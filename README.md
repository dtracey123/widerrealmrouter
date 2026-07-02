# widerrealm-router

**THIS PROJECT IS ABANDONED, IT WAS BUILT AS A LEARNING EXERCISE DO NOT USE IN PRODUCTION!**

A simple fast router built for web applications and APIs.

To install

`composer require widerrealm/widerrealm-router`

Basic Setup

index.php
```php
require_once(__DIR__ . './../vendor/autoload.php');

$routes = new Routes();

$routes->register();
// execute route if it's found.
$routes->execute();
// 404 if no routes are found
$routes->error_handler()->route_not_found();
```
routes.php
```php
<?php
use Widerrealm\Router\Router;

class Routes extends Router {
	public function register() {
    		$this->get('/', fn() => "Hello World!");
			$this->get('/name/{name}', fn($name) => "Hello " . $name);
			// Only accepts int values in the URL if any other datatype is typed it returns a 404.
			$this->get('/id/{int:id}', fn($id) => "Hello " . get_username($id);
	}
}
```
.htaccess
```apache
Header always set Access-Control-Allow-Origin "*"
Header always set Access-Control-Allow-Methods "POST, GET, DELETE, PUT, PATCH, OPTIONS"
Header always set Access-Control-Max-Age "1000"
Header always set Access-Control-Allow-Headers "x-requested-with, Content-Type, origin, authorization, accept, client-security-token"

Options +FollowSymLinks
# Remove Indexes
Options -Indexes

<IfModule mod_rewrite.c> 
    RewriteEngine On
    RewriteBase /

    # Deliver the folder directly if it exists on the server
    RewriteCond %{REQUEST_FILENAME} !-d

    # Enable COORS by returning 200 when the request is OPTIONS
    RewriteCond %{REQUEST_METHOD} OPTIONS
    RewriteRule ^(.*)$ $1 [R=200,L]
    # Deliver the file directly if it exists on the server
    RewriteCond %{REQUEST_FILENAME} !-f

    # Push every request to index.php
    RewriteRule ^(.*)$ index.php [QSA]
</IfModule>

# Deny direct access to all PHP & server config files.
<Files ~ "\.(php|htaccess|env|conf)$">  
    Order Allow,Deny 
    Deny from all 
</Files>

# Allow direct access to index.php
<Files ~ "index.php$">  
    Order allow,deny
    Allow from all
</Files>
```
