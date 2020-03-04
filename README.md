# Fryske Oranjekoeke
The simple MVC PHP Framework.

## Authors
* Sander Tuinstra - Developer - [SanderT2001](https://github.com/SanderT2001)

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
<IfModule mod_rewrite.c>
 RewriteEngine on
 RewriteBase /fryske-koekn/fryske-oranjekoeke/app-skeleton/public/
 RewriteRule ^$ /fryske-koekn/fryske-oranjekoeke/app-skeleton/public/index.php [L]
</IfModule>
```
