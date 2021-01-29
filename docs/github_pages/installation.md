# Installation

[Back to summary](../index.md)

## Prepare your work environment
### Prerequisites
**Download** and **install** all prerequisites tools.

* [![WampServer](https://img.shields.io/badge/WampServer-v3.2.0-F70094)](https://www.wampserver.com/) OR [![PHP](https://img.shields.io/badge/PHP-%3E%3D7.4.7-7377AD)](https://www.php.net/manual/fr/install.php) + [![MySQL](https://img.shields.io/badge/MySQL-v8.0.19-DF6900)](https://dev.mysql.com/downloads/mysql/#downloads) + [![Apache](https://img.shields.io/badge/Apache-v2.4.43-B72046)](https://httpd.apache.org/download.cgi)
* [![Git](https://img.shields.io/badge/Git-v2.27-E94E31)](https://git-scm.com/download)
* [![SymfonyCLI](https://img.shields.io/badge/Symfony-v4.4-000000)](https://symfony.com/download)
* [![Composer](https://img.shields.io/badge/Composer-v1.10.13-5F482F)](https://getcomposer.org/download)
* [![Nodes](https://img.shields.io/badge/Nodejs-v14.5.0-026E00)](https://nodejs.org)

#### Optimizing Performance (Optional)
Symfony Application is fast, right out of the box. However, you can make it faster if you optimize your servers, then your code will run faster. Just change some values in your file ``php.ini``.

```shell
[PHP]

;;;;;;;;;;;;;;;;;;;
; About php.ini   ;
;;;;;;;;;;;;;;;;;;;

opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
realpath_cache_size=4096K
realpath_cache_ttl=600
opcache.preload=/PATH/TO/YOUR/app/config/preload.php
```

More details:
[**performance**](https://symfony.com/doc/current/performance.html "Symfony doc: performance")
[**opcache**](https://symfony.com/blog/php-preloading-and-symfony "Symfony doc: Set up preloader")

To set up your ``php.ini`` easily use the command **sed** in your unix terminal:

```
shell
# for exemple activate opache
sudo sed -i 's,^;opcache.enable=.*$,opcache.enable=1,' /PATH/TO/YOUR/FILE/php.ini
```

## Set up the Project
Think to [**fork**](https://docs.github.com/en/github/getting-started-with-github/fork-a-repo) the project and read the [contribution guide](contrib.html).

### Retrieve the project sources
```shell
git clone https://github.com/<your-username>/<repo-name>
```

### Install dependencies

1. In your terminal change the working directory to the project folder:
```shell
cd <repo-name>
```

2. Install **composer** dependencies:

```shell
# Devolopment environment
composer install

# Production environment with optimized server
composer install --no-dev --optimize-autoloader --classmap-authoritative --apcu-autoloader
```

> More info about [composer Optimization](https://getcomposer.org/doc/articles/autoloader-optimization.md)

3. Install **npm** dependencies:
```shell 
npm install
```

### Initialize the databases:
1. Edit the variable ***DATABASE_URL*** at the line ``28`` on the file **```./.env```** with your database details.
 
 > [More info about how you Configure the Database in Symfony?](https://symfony.com/doc/current/doctrine.html#configuring-the-database)
 
2. Run ***WampServer*** (Or run Mysql separately, if you don't use Wamp).

3. Create the application **database**: 
```shell 
php bin/console doctrine:database:create
```

4. Create the Database script Tables/Schema:
```shell
php bin/console make:migration
```

5. Add tables in the Database:
```shell 
php bin/console doctrine:migrations:migrate --no-interaction
```

6. Load the initial data into the application database:
```shell 
php bin/console doctrine:fixtures:load -n
```

[Next step](environments.html "Environments")