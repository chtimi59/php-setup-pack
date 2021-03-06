# PHP Setup Pack
A basic setup tool for your php webSite project

# Motivation
It's ofen a pain to deploy a website on a server, when it contents different credentials for mail, database.
This little tool gives the possibility to setup and also tests that stuffs.

At the end of the process (if tests pass), a 'conf.php' file is generated. That means that your index.php can starts like this:

```php
<?php if(!@include("conf.php")) { echo("Setup missing"); die(); }

[...]

```

and conf.php will contains all the following definitions:
```php
$GLOBALS['CONFIG']['debug']       /* which is true if server is on localhost */
$GLOBALS['CONFIG']['base_url']    /* base url specified, ex: 'http://mywebsite/main' */
$GLOBALS['CONFIG']['root_path']   /* root path, ex: 'C:\wamp64\www\mywebsite\main' */

/* if using db */
$GLOBALS['CONFIG']['sql_host']    /* sql host, ex: localhost */
$GLOBALS['CONFIG']['sql_login']   /* sql login, ex: root */
$GLOBALS['CONFIG']['sql_isPW']    /* sql needs a password */
$GLOBALS['CONFIG']['sql_pw']      /* sql password */
$GLOBALS['CONFIG']['sql_db']      /* sql database used */

/* if using user */
$GLOBALS['CONFIG']['user_table']  /* user table name in db */

/* if using admin */
$GLOBALS['CONFIG']['admin_email'] /* admin email */
$GLOBALS['CONFIG']['admin_uuid']  /* admin uuid */

/* if using email */
$GLOBALS['CONFIG']['smtp_host']   /* smtp host address */
$GLOBALS['CONFIG']['smtp_port']   /* smtp host port */
$GLOBALS['CONFIG']['smtp_login']  /* smtp login */
$GLOBALS['CONFIG']['smtp_isPW']   /* smtp needs a password */
$GLOBALS['CONFIG']['smtp_pw']     /* smtp password */
$GLOBALS['CONFIG']['smtp_secure'] /* none,ssl,tsl */
$GLOBALS['CONFIG']['smtp_auth']   /* true if secure is not 'none' */
$GLOBALS['CONFIG']['smtp_email']  /* smtp relative email */
```


# Setting up your project

This git repo, should actually be used as a git's submodule in your project.

The following arborescence tree is expected:

```
project_dir\
   libs\      - external libs
   setup\     - this submodule
   
   setup.conf - setup configuration file
   setup.sql  - database script
   users.sql  - database script
   
   conf.php   - the generated file will layed here
                and should be include in your project
```

Hence, to add this submodule, write:
```
git submodule add https://github.com/chtimi59/php-setup-pack.git setup
```

Once, it's done, create a project_dir/*setup.conf* according your needs:

```json
{
    "title": "your_Project_Name",
    "features": {
        "db"    : true,
        "user"  : true,
        "admin" : true,
        "mail"  : true
    }
}    
```
Note: if not present, project_dir/setup/*setup.conf* default will be used instead

## "title"
Just here for convenience

## feature "db"
- Will ask for MYSQL Database credentials
- Will test them
- Will configure your database with *setup.sql*

That means that you should have create a project_dir/**setup.sql** file.

if not present, project_dir/setup/*setup.sql* default will be used instead

## "user"
Note: this requiered **db** to be ON
- Will ask for a user-table name
- Will configure your database with *users.sql*

Ok, here again you should have create a project_dir/**users.sql** file.

if not present, project_dir/setup/*users.sql* default will be used instead

## "admin"
Note: this requiered **db** and **user** to be ON
- Will simply insert admin user in user's table

## "mail"
Note: this requiered to add **PHPMailer** in your project libs folder
```
git submodule add https://github.com/PHPMailer/PHPMailer.git libs/PHPMailer
pushd libs/PHPMailer
git checkout v5.2.23
popd
git add libs/PHPMailer
git commit -m "moved submodule to libs/PHPMailer"
git push
```
- Will ask for SMTP credentials
- Will test them
- Will sending you back an email

# Usage
You just open http://yoururl/setup and follow the instruction:

![alt tag](https://raw.githubusercontent.com/chtimi59/php-setup-pack/master/docs/page1.png)

And then click on **generate**

If all tests pass you should get the following view and a *conf.php* file is generated for you.

![alt tag](https://raw.githubusercontent.com/chtimi59/php-setup-pack/master/docs/page2.png)





