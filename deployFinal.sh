#! /usr/bin/env bash

# Recupera los parametros de entrada
ALIAS=$1
REPO=$2
BRANCH=$3
DB_NAME=$4
DB_USER=$5
DB_PASS=$6
DOMAIN=$7

function creaFolder() {
    path="/var/www/$ALIAS"

    if [ ! -d "$path" ]; then
        mkdir -p "$path"
    else
        echo "===> La carpeta ya existe, eliminando."

        if [[ -n "$path" && "$path" == /var/www/* ]]; then
            rm -rf "$path"
        else
            exit 1
        fi

        mkdir -p "$path"
    fi
}

function cloneRepo() {
    git clone -b "$BRANCH" "$REPO" "$path" || {
        echo "===> Error al clonar el repositorio."
        exit 1
    }

    if [ -d "$path/storage" ]; then
        sudo chown -R deploy:www-data "$path/storage"
    fi

    if [ -d "$path/bootstrap/cache" ]; then
        sudo chown -R deploy:www-data "$path/bootstrap/cache"
    fi

    if [ -d "$path/vendor" ]; then
        sudo chown -R deploy:www-data "$path/vendor"
    fi

    cd "$path" || exit 1

    if [ ! -f ".env" ]; then
        cp .env.example .env || {
            echo "===> No existe el archivo .env.example"
            exit 1
        }
    fi

    composer install || {
        echo "===> Error en composer"
        exit 1
    }

    php artisan key:generate --force
}

function creaDatabase() {
    DB_NAME=$(echo "$DB_NAME" | xargs) #quita espacios

    local db_exists

    db_exists=$(mysql -u "$DB_USER" -p"$DB_PASS" -N -B -e "SHOW DATABASES LIKE '$DB_NAME';" 2>/dev/null)

    if [ "$db_exists" = "$DB_NAME" ]; then
        mysql -u "$DB_USER" -p"$DB_PASS" <<SQL
DROP DATABASE IF EXISTS \`$DB_NAME\`;
SQL

        if [ $? -ne 0 ]; then
            echo "No se pudo eliminar la base de datos '$DB_NAME'"
            exit 1
        fi
    fi

    mysql -u "$DB_USER" -p"$DB_PASS" <<SQL
CREATE DATABASE \`$DB_NAME\`
CHARACTER SET utf8mb4
COLLATE utf8mb4_spanish2_ci;
SQL

    if [ $? -ne 0 ]; then
        echo "Error al crear la base de datos"
        exit 1
    fi

    sed -i "s/^DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
    sed -i "s/^DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
    sed -i "s/^DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

    php artisan migrate || {
        echo "===> Error al migrar"
        exit 1
    }
}

function configureSite() {
    local NGINX_SITE
    local NGINX_ENABLED

    NGINX_SITE="/etc/nginx/sites-available/$ALIAS"
    NGINX_ENABLED="/etc/nginx/sites-enabled/$ALIAS"

    if [ -f "$NGINX_SITE" ] || [ -L "$NGINX_ENABLED" ]; then
        sudo rm -f "$NGINX_SITE"
        sudo rm -f "$NGINX_ENABLED"
    fi

    sudo tee "$NGINX_SITE" > /dev/null <<EOF
server {
	listen 80;
	listen [::]:80;

	server_name $DOMAIN;

	root $path/public;
	index index.php index.html;

	access_log /var/log/nginx/${ALIAS}_access.log;
	error_log /var/log/nginx/${ALIAS}_error.log;

	client_max_body_size 100M;

	location / {
		try_files \$uri \$uri/ /index.php?\$query_string;
	}

	location ~ \.php$ {
		include snippets/fastcgi-php.conf;
		fastcgi_pass unix:/run/php/php8.3-fpm.sock;
	}

	location ~ /\.ht {
		deny all;
	}
}
EOF

    sudo ln -s "$NGINX_SITE" "/etc/nginx/sites-enabled/$ALIAS"

    sudo nginx -t || {
        echo "La configuración de Nginx es inválida"
        exit 1
    }

    sudo systemctl reload nginx || {
        echo "No se pudo recargar Nginx"
        exit 1
    }
}

function validateDomain() {
    local TIMES=10
    local COUNT=1

    while [ $COUNT -le $TIMES ]; do
    	HTTP_STATUS=$(curl -s -o /dev/null -w "%{http_code}" -H "Host: $DOMAIN" http://127.0.0.1)

    	if [ "$HTTP_STATUS" = 200 ] || [ "$HTTP_STATUS" = "302" ]; then
    		echo "Nginx responde correctamente para $DOMAIN"
    	else
    		echo "Nginx respondio con código $HTTP_STATUS para $DOMAIN"
    	fi

    	echo "===> Validando DNS del dominio..."
    	SERVER_IP=$(curl -4 -s ifconfig.me)
    	DOMAIN_IP=$(dig +short "$DOMAIN" | tail -n 1)

    	if [ -z "$DOMAIN_IP" ]; then
    		echo "El dominio $DOMAIN aún no resuelve en DNS"
    	elif [ "$DOMAIN_IP" != "$SERVER_IP" ]; then
    		echo "El dominio $DOMAIN resuelve a $DOMAIN_IP, pero este servidor es $SERVER_IP"
    		echo "Aún no conviene ejecutar CertBot"
    	else
    		echo "El dominio responde y apunta a este servidor"

    		secureDomain
    		break
    	fi

    	echo "Intento: $COUNT / $TIMES "
    	sleep 20
    	COUNT=$((COUNT + 1))
    done
}

function secureDomain() {
	echo "===> Validando si el dominio ya tiene certificado SSL..."

	if sudo certbot certificates 2>/dev/null | grep -q "Domains: .*\\b$DOMAIN\\b"; then
		echo "El dominio $DOMAIN ya tiene certificado emitido."
		sed -i "s#^APP_URL=.*#APP_URL=https://$DOMAIN#" .env
		return
	fi

	echo "===> Ejecutando CertBot para $DOMAIN"
	sudo certbot --nginx -d "$DOMAIN" --non-interactive --agree-tos -m "mfdzmirabal@gmail.com" --redirect || {
		echo "No se pudo configurar SSL con Certbot"
		return
	}

	echo "SSL configurado correctamente para $DOMAIN"
	sed -i "s#^APP_URL=.*#APP_URL=https://$DOMAIN#" .env
}

function compileFrontEnd() {
    local fileName
    local metodo

    sed -i "s/^APP_ENV=.*/APP_ENV=production/" .env
    sed -i "s/^APP_DEBUG=.*/APP_DEBUG=false/" .env
    sed -i "s#^APP_URL=.*#APP_URL=https://$DOMAIN#" .env

    fileName="$path/vite.config.js"

    if [ -f "$fileName" ]; then
        metodo="vite";
    else
        metodo="mix";
    fi

    if [ "$metodo" = "vite" ]; then
        npm install || exit 1
        npm run build || exit 1
    else
        sudo fallocate -l 2G /swapfile
        sudo chmod 600 /swapfile
        sudo mkswap /swapfile
        sudo swapon /swapfile

        npm install || exit 1
        npm run prod || {
            sudo swapoff /swapfile
            sudo rm /swapfile

            exit 1
        }

        sudo swapoff /swapfile
        sudo rm /swapfile
    fi

    php artisan storage:link

    php artisan cache:clear
    php artisan config:clear
    php artisan optimize:clear
    php artisan view:clear
}

function configureCron() {
    echo "===> Configurando CRON para el Schedule..."
    # Evita duplicados: busca si ya existe el cron para este path, si no, lo añade
    (sudo crontab -u deploy -l 2>/dev/null | grep -v "$path/artisan schedule:run"; echo "* * * * * php $path/artisan schedule:run >> /dev/null 2>&1") | sudo crontab -u deploy - balance
}

function configureSupervisor() {
    echo "===> Configurando Supervisor para Jobs..."
    CONF_FILE="/etc/supervisor/conf.d/${ALIAS}.conf"

    sudo tee "$CONF_FILE" > /dev/null <<EOF
[program:${ALIAS}-worker]
process_name=%(program_name)s_%(process_num)02d
command=php $path/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deploy
numprocs=2
redirect_stderr=true
stdout_logfile=$path/storage/logs/worker.log
stopwaitsecs=3600
EOF

    sudo supervisorctl reread
    sudo supervisorctl update
    sudo supervisorctl start "${ALIAS}-worker:*"
}

function automatico() {
    creaFolder
    cloneRepo
    creaDatabase
    configureSite
    compileFrontEnd
    configureCron
    configureSupervisor
    validateDomain
}

function manual() {
    echo "=== FUNCION NO IMPLEMENTADA ==="
}

export NVM_DIR="/home/deploy/.nvm"

[ -s "$NVM_DIR/nvm.sh" ] && \. "$NVM_DIR/nvm.sh"

nvm use default

# Valida si será automático o manual
if [ -z "$ALIAS" ]; then
    echo "===> Inicia proceso manualmente."
    manual
else
    echo "===> Inicia proceso automático."
    automatico
fi
