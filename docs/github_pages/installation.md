# Installation

[Back to summary](../index.md)

## Prepare your work environment 
### Prerequisites
* [![WampServer](https://img.shields.io/badge/WampServer-v3.2.0-F70094)](https://www.wampserver.com/) OR [![PHP](https://img.shields.io/badge/PHP-%3E%3D7.4.7-7377AD)](https://www.php.net/manual/fr/install.php) + [![MySQL](https://img.shields.io/badge/MySQL-v8.0.19-DF6900)](https://dev.mysql.com/downloads/mysql/#downloads)
* [![Git](https://img.shields.io/badge/Git-v2.27-E94E31)](https://git-scm.com/download)
* [![SymfonyCLI](https://img.shields.io/badge/Symfony-v4.20-000000)](https://symfony.com/download)
* [![Composer](https://img.shields.io/badge/Composer-v1.10.13-5F482F)](https://getcomposer.org/download)
* [![Nodes](https://img.shields.io/badge/Nodejs-v14.5.0-026E00)](https://nodejs.org)

**Download** and **install** all prerequisites tools.

## Set up the Project
Think to [**fork**](https://docs.github.com/en/github/getting-started-with-github/fork-a-repo) the project and read the [contribution guide](contr.html).

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
composer install
```

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