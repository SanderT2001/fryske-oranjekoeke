# Fryske Oranjekoeke
The simple MVC PHP Framework.

## Authors
* Sander Tuinstra - Developer - [SanderT2001](https://github.com/SanderT2001)
* Yvar Nanlohij   - Slave     - [Yvar-School](https://github.com/Yvar-School)

## Build with
* PHP 7.3.11

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
