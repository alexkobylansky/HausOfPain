#!/bin/bash

DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd $DIR/..

# Installing basic PHP components
if [ ! -f ".env" ]

then
# this variant is for new installations

    cp .env.example .env

    read -p "Enter MySQL database name: " DBNAME
    read -p "Enter MySQL database user: " DBUSER
    read -p "Enter MySQL database pass: " DBPASS

    while ! MYSQL_PWD=$DBPASS mysql -u $DBUSER $DBNAME -e ";" ; do
        echo "Can't connect, please provide correct information"
        read -p "Enter MySQL database name: " DBNAME
        read -p "Enter MySQL database user: " DBUSER
        read -p "Enter MySQL database pass: " DBPASS
    done

    sed -i -- "s/DB_DATABASE=laravel/DB_DATABASE=$DBNAME/g" .env
    sed -i -- "s/DB_USERNAME=root/DB_USERNAME=$DBUSER/g" .env
    sed -i -- "s/DB_PASSWORD=/DB_PASSWORD=$DBPASS/g" .env

    read -p "Enter SMTP Mail Host: " MAILHOST
    read -p "Enter SMTP Mail Port: " MAILPORT
    read -p "Enter SMTP Mail Username: " MAILUSER
    read -p "Enter SMTP Mail Password: " MAILPASS

    sed -i -- "s/MAIL_HOST=smtp.mailtrap.io/MAIL_HOST=$MAILHOST/g" .env
    sed -i -- "s/MAIL_PORT=2525/MAIL_HOST=$MAILPORT/g" .env
    sed -i -- "s/MAIL_USERNAME=null/MAIL_USERNAME=$MAILUSER/g" .env
    sed -i -- "s/MAIL_PASSWORD=null/MAIL_PASSWORD=$MAILPASS/g" .env

    read -p "Enter Vimeo Client ID: " VIMEOCLIENT
    read -p "Enter Vimeo App Secret: " VIMEOSECRET
    read -p "Enter Vimeo Access Token: " VIMEOACCESS

    sed -i -- "s/VIMEO_CLIENT=/VIMEO_CLIENT=$VIMEOCLIENT/g" .env
    sed -i -- "s/VIMEO_SECRET=/VIMEO_SECRET=$VIMEOSECRET/g" .env
    sed -i -- "s/VIMEO_ACCESS=/VIMEO_ACCESS=$VIMEOACCESS/g" .env

    read -p "Enter Admin Panel login email: " ADMINMAIL

    if [ -f "php" ]
    then

        if [ ! -f "composer.phar" ]
        then
            ./php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
            ./php composer-setup.php
            ./php -r "unlink('composer-setup.php');"
        fi

        ./php composer.phar install
        ./php artisan package:discover --ansi

    else

        composer install
        php artisan package:discover --ansi

    fi

    # installing new node.js dependencies (if any)
    npm install

    # make migration and seed
    if [ -f "php" ]
    then
        ./php artisan migrate:refresh --seed
    else
        php artisan migrate:refresh --seed

    fi

    # generating application key
    if [ -f "php" ]
    then
        ./php artisan key:generate
    else
        php artisan key:generate

    fi

    # install admin panel Voyager
    if [ -f "php" ]
    then
        ./php artisan voyager:install
        ./php artisan voyager:admin $ADMINMAIL --create
    else
        php artisan voyager:install
        php artisan voyager:admin $ADMINMAIL --create
    fi

    # build Javascript assets
    npm run dev

else
# this is the variant for updating existing installations

    # installing new PHP components (if any)
    if [ -f "php" ]
    then
        ./php composer.phar install
        ./php artisan package:discover --ansi
    else
        composer install
        php artisan package:discover --ansi
    fi

    # installing new node.js dependencies (if any)
    npm install

    # updating migrations
    if [ -f "php" ]
    then
        ./php artisan migrate
    else
        php artisan migrate
    fi

    # build Javascript assets
    npm run dev

fi
