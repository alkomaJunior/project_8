# Try the Application

[Back to summary](../index.md)

## Test the application Locally

>It is necessary to have installed the [symfony binary](https://symfony.com/download).

```shell
# Start the server and display the log messages in the console
symfony server:start
 
# Start the server in the background
symfony server:start [-d]
```
* Start your Database server

* Optional: If you want manage external ``css`` & ``javascript`` ressources, run one of the command below [more info](https://symfony.com/doc/4.4/frontend/encore/simple-example.html#configuring-encore-webpack)

Compile the files once in the development environment:
```npm
npm run dev-server
```

Activate automatic compilation:
```shell
npm run watch
```

Compile the files for production:
```shell
npm run build
```

* Do not forget to start your database server !!!

* Navigate to [localhost:8000](http://localhost:8000)

## Test the application online

[![ToDo&Co](https://img.shields.io/badge/ToDo&Co-yellow.svg)](https://todolist.it-bigboss.de/ "Manage your tasks")

## User accounts
Use one of the accounts below to login:

Username | Password | Role  | authorized actions
:------- | :------- | :-----| :--------
 admin   |   admin  | ADMIN | ``Modify own profile``, ``Create & Edit users``, ``Create, Edit Tasks``, ``Delete anonymous Tasks``, ``Delete own Tasks``
 user    |   user   | USER  | ``Modify own profile``,  ``Create, Edit Tasks``, ``Delete own Tasks``

[Next step](tests.html "Run Tests")
