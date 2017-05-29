Installation
============

- Clone Symfony Theatre application repository:

        git clone https://github.com/phnixdev/theatre

- Install [Composer](http://getcomposer.org) globally.

- Set the following shell variables to:

        THEATRE_ENV=prod|test|dev
        THEATRE_DEBUG=0|1

- Install application dependencies running the following command
 from the application folder:

        composer install --no-dev

    if you're on production, on you developing machine use:
    
        composer install

- Setup your Webserver, at the momemnt Apache is prefered:

        <VirtualHost *:80>
            DocumentRoot /your/path/to/theatre/web
            ServerName  theatre.dev
            ServerAlias theatre.test
            ServerAlias theatre.prod

            <Directory your/path/to/theatre/web>
                AllowOverride All
                Order allow,deny
                allow from all

                RewriteEngine On
                RewriteCond %{REQUEST_FILENAME} -f
                RewriteRule ^ - [L]
                RewriteRule ^(.*)$ app.php [QSA,L]
            </Directory>
        </VirtualHost>

    **This is just a template and not for production use.**
