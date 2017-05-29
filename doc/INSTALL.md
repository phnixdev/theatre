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
