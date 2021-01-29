# Environnements

[Back to summary](../index.md)

Symfony applications come with a file called ``.env`` located at the project root directory. This file is used to define the value of environment variables and it’s explained in detail later in this article.

Open the ``.env`` file (or better, the .env.local file if you created one), go to the line **17** and edit the value of the **APP_ENV** variable to change the environment in which the application runs. There are several different environments:
* `dev`: development environment
* `test`: test environment
* `prod`: production environment

```dotenv
# .env (or .env.local)

APP_ENV=prod
```

This value is used both for the web and for the console commands. However, you can override it for commands by setting the **APP_ENV** value before running them:

```dotenv
# Use the environment defined in the '.env' file
 php bin/console command_name

# Ignore the '.env' file & run this command in production
 APP_ENV=prod php bin/console command_name
```
For more info visit the [link](https://symfony.com/doc/4.4/configuration.html#configuration-environments "Environments configuration")

## Development environment
It is not necessary to create a file for each environment. But if you need diffrent configuration in the same time, then it should be named `.env.dev.local`. and just copy the content from the file ``.env``.

By default, no third-party service (mail, database, ...) is used, if you need something specific, add its configuration here.

## Test Environment
It is essential to create the `.env.test.local` file to ensure the proper functioning of the tests, you can use this example as a basis:
```dotenv
# Necessary if you want to run system tests
# define your env variables for the test env here
KERNEL_CLASS='App\Kernel'
APP_SECRET='$ecretf0rt3st'
SYMFONY_DEPRECATIONS_HELPER=999999
PANTHER_APP_ENV=panther
# Use SQLite
DATABASE_URL=sqlite:///%kernel.cache_dir%/test.db

# if you want Use MYSQL uncomment next line & comment the previous one
#DATABASE_URL=mysql://root:@127.0.0.1:3306/p_8_test
```

## Production environment
You have 2 options, create the `.env.prod.local` file or add your environment variables in the configuration of your virtual host:
```dotenv
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7"
```

>Don't forget to configure other environment variables as needed, like [MAILER_DSN](https://symfony.com/doc/4.4/mailer.html "Sending Emails with Mailer").

[Next step](try_application.html "Try the application")