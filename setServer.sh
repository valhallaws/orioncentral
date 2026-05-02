#!/bin/bash

# Variables de configuración
USER_DEPLOY="deploy"
DB_ADMIN_USER="dbadmin"
DB_ROOT_PASS="sistemas"
DB_ADMIN_PASS=$(openssl rand -base64 12)
PHP_VERSION="8.3"

echo "--- Iniciando Configuración VPS Ubuntu 26.04 ---"

# 1. Actualización y Paquetería Básica
echo "===> Actualizando sistema..."
apt update && apt upgrade -y
apt install -y nginx git curl unzip software-properties-common mariadb-server certbot python3-certbot-nginx dnsutils ufw

echo "===> Instalando PHP v${PHP_VERSION}..."
# PHP 8.3
add-apt-repository ppa:ondrej/php -y
apt update
apt install -y php${PHP_VERSION} php${PHP_VERSION}-fpm php${PHP_VERSION}-mysql php${PHP_VERSION}-xml php${PHP_VERSION}-mbstring php${PHP_VERSION}-curl php${PHP_VERSION}-zip php${PHP_VERSION}-bcmath php${PHP_VERSION}-gd php${PHP_VERSION}-soap

echo "===> Instalando composer..."
# Composer
cd /tmp
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

echo "===> Instalando WKHTMLTOPDF..."
# 2. WKHTMLTOPDF
wget https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-3/wkhtmltox_0.12.6.1-3.jammy_amd64.deb
apt install -y ./wkhtmltox_0.12.6.1-3.jammy_amd64.deb
rm wkhtmltox_0.12.6.1-3.jammy_amd64.deb

echo "===> Configurando Firewall..."
ufw allow OpenSSH
ufw allow 80
ufw allow 443
ufw allow 3306
ufw --force enable

echo "===> Creando usuario deploy"
# 3. Usuario Deploy (Sudo sin pass y SSH)
useradd -m -s /bin/bash $USER_DEPLOY
usermod -aG sudo $USER_DEPLOY
usermod -aG www-data $USER_DEPLOY
echo "$USER_DEPLOY ALL=(ALL) NOPASSWD:ALL" >> /etc/sudoers.d/90-deploy-user

mkdir -p /home/$USER_DEPLOY/.ssh
touch /home/$USER_DEPLOY/.ssh/authorized_keys
chown -R $USER_DEPLOY:$USER_DEPLOY /home/$USER_DEPLOY/.ssh
chmod 700 /home/$USER_DEPLOY/.ssh
chmod 600 /home/$USER_DEPLOY/.ssh/authorized_keys

# 4. Directorio /var/www con ACL
mkdir -p /var/www
chown -R www-data:www-data /var/www
chmod -R 775 /var/www
setfacl -R -m u:$USER_DEPLOY:rwx /var/www
setfacl -dR -m u:$USER_DEPLOY:rwx /var/www

echo "===> Instalando NVM para deploy..."
# 5. NVM para usuario Deploy
sudo -u $USER_DEPLOY -i bash <<EOF
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.7/install.sh | bash
source ~/.bashrc
nvm install --lts
EOF

# 6. MariaDB: Seguridad y Acceso Remoto
echo "Configurando MariaDB..."
# Asegurar root y limpiar
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED BY '$DB_ROOT_PASS';"
mysql -u root -p$DB_ROOT_PASS -e "DELETE FROM mysql.user WHERE User='';"
mysql -u root -p$DB_ROOT_PASS -e "DROP DATABASE IF EXISTS test;"

# Habilitar escucha externa
sed -i 's/bind-address.*/bind-address = 0.0.0.0/' /etc/mysql/mariadb.conf.d/50-server.cnf

# Crear accesos remotos (%) y locales
mysql -u root -p$DB_ROOT_PASS -e "CREATE USER 'root'@'%' IDENTIFIED BY '$DB_ROOT_PASS';"
mysql -u root -p$DB_ROOT_PASS -e "GRANT ALL PRIVILEGES ON *.* TO 'root'@'%' WITH GRANT OPTION;"
mysql -u root -p$DB_ROOT_PASS -e "CREATE USER 'orion'@'%' IDENTIFIED BY 'Mirabal2010_!';"
mysql -u root -p$DB_ROOT_PASS -e "GRANT ALL PRIVILEGES ON *.* TO 'orion'@'%' WITH GRANT OPTION;"
mysql -u root -p$DB_ROOT_PASS -e "CREATE USER '$DB_ADMIN_USER'@'%' IDENTIFIED BY '$DB_ADMIN_PASS';"
mysql -u root -p$DB_ROOT_PASS -e "GRANT ALL PRIVILEGES ON *.* TO '$DB_ADMIN_USER'@'%' WITH GRANT OPTION;"
mysql -u root -p$DB_ROOT_PASS -e "FLUSH PRIVILEGES;"

systemctl restart mariadb
systemctl restart nginx

mysql_tzinfo_to_sql /usr/share/zoneinfo | mariadb -u root -p"sistemas" mysql

sudo apt install micro

sudo apt install supervisor

sudo systemctl enable supervisor
sudo systemctl start supervisor

sudo sed -i 's/#ClientAliveInterval.*/ClientAliveInterval 60/' /etc/ssh/sshd_config
sudo sed -i 's/#ClientAliveCountMax.*/ClientAliveCountMax 120/' /etc/ssh/sshd_config

# Reiniciar SSH para aplicar cambios
sudo systemctl restart ssh

echo "----------------------------------------------------"
echo "¡LISTO! Configuración finalizada."
echo "DB Admin User: $DB_ADMIN_USER | Pass: $DB_ADMIN_PASS"
echo "DB Root Pass: $DB_ROOT_PASS (Acceso remoto habilitado)"
echo "----------------------------------------------------"
