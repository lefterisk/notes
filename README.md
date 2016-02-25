### Required

- PHP 5.6
- MySQL 5.6
- ElasticSearch 1.6.0
- SASS (for UI dev)
- Gulp (for UI dev)
- Bower (for UI dev)

#####local.php
Copy and rename `config/autoload/local.php.dist` as `config/autoload/local.php` nd fill in your DB credentials

```
'db' => array(
    'username' => 'DB_USERNAME',
    'password' => 'DB_PASSWORD',
    'dsn'      => 'mysql:dbname=YOUR_DATABASE;host=YOURHOST',
),
```

Install DB from file provided `notes.sql` the schema should be self explanatory

### Apache setup

To setup apache, setup a virtual host to point to the public/ directory of the
project and you should be ready to go! It should look something like below:
```
    <VirtualHost *:80>
        ServerName notes.localhost
        DocumentRoot /path/to/notes/public
        <Directory /path/to/notes/public>
            DirectoryIndex index.php
            AllowOverride All
            Order allow,deny
            Allow from all
            <IfModule mod_authz_core.c>
            Require all granted
            </IfModule>
        </Directory>
    </VirtualHost>

```

Once all is installed and ready to go the user for the login is 'lefteris.kokkonas@gmail.com' and password is 'password'. Enjoy

###Front end

 Tech stack used:
 * npm for server dependencies (Gulp, Bower etc)
 * Bower for frontend dependencies
 * Gulp for compiling all vendor dependencies, app code, scss files etc

 The building process is run by gulp. The gulp file `gulpfile.js` contains distinctive tasks such as:
 * `vendor-js`: Compiles, concats, minifies all vendor js dependencies
 * `vendor-css`: Compiles, concats, minifies all vendor css dependencies
 * `app-js`: Compiles, concats, minifies all app specific js
 * `app-scss`: Compiles, concats, minifies all app specific css
 * `default`: Runs all the tasks above in sequence once
 * `watch-app-js`: Fires up the watcher for the app specific js files
 * `watch-app-css`: Fires up the watcher for the app specific css files
 * `watch-init`: Fires up both file watchers

 ####Proposed sequence to run prior start of development
 Assuming `npm`, `bower` are already installed globally
 * `npm install`
 * `bower install`
 * `gulp watch-init`

 ###Dependencies
 * Angular
 * Bootstrap sass
 * Angular ui (bootstrap port for angular)
 * Toastr (fancy notifications)

