# Fryske Oranjekoeke
The simple MVC PHP Framework.

## Authors
* Sander Tuinstra - Developer - [SanderT2001](https://github.com/SanderT2001)
* Yvar Nanlohij   - Slave     - [Yvar-School](https://github.com/Yvar-School)

## Build with
* PHP 7.3.11

## Start using
* CD into your project directory and make sure the following subdirectories are present.
```sh
$ mkdir -p vendor/sandert2001
```
* Then CD into the above sandert2001 vendor directory and clone this repository there.
```sh
$ git clone https://github.com/SanderT2001/fryske-oranjekoeke.git
```
* Then make the `create-project.sh` file executable (if that is not already the case).
```sh
$ chmod +x ./create-project.sh
```
* After that, execute the `create-project.sh` file in order to create the App.
```
$ ./create-project.sh
```
* Now your App should be created and you are ready to start using _Fryske Oranjekoeke_. If you are using _NGINX_, make sure you have the correct Rewrite Rules set (noted below).If you are using _APACHE_, the `.htaccess` file is already present in: `yourproject/public/.htaccess`.
* To make sure _Fryske Oranjekoeke_ will be loading everything it needs, tell _Fryske Oranjekoeke_ if this App is an API or just a normal MVC App in `yourproject/config/config.ini` under `runtime` -> `is_api`.

## NGINX Rewrite Rules
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

## Apache Rewrite Rules
```sh
RewriteEngine On
RewriteBase /
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ /public/ [L]
```
