# Fryske Oranjekoeke
The simple MVC PHP Framework.

## Build with
* NGINX 1.16
* PHP 7.3.11
* MySQL 8.0

## Start using
* To create a project, execute the `create-project.sh` file which will guide you through the process of creating a amazing project with the Framework.
```sh
$ ./create-project.sh
```
* Now your App should be created and you are ready to start using _Fryske Oranjekoeke_. If you are using _NGINX_, make sure you have the correct Rewrite Rules set (noted below).If you are using _APACHE_, the `.htaccess` file is already present in: `yourproject/public/.htaccess`.
* To make sure _Fryske Oranjekoeke_ will be loading everything it needs, tell _Fryske Oranjekoeke_ if this App is an API or just a normal MVC App in `yourproject/config/config.ini` under `runtime` -> `is_api`.

## Webserver Rewrite Rules
### NGINX Rewrite Rules
```sh
location /
{
    # If the request was made to a JS/CSS File, don't rewrite.
    if ($request_uri ~* \.(?:css|js|map|jpe?g|png)$) {
        break;
    }

    rewrite ^/(.*) /app/public/index.php last;
}
```

### Apache Rewrite Rules
```sh
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /public/ [L]
```
