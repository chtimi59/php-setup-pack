# PHP Setup Pack
A basic setup tool for your php webSite project

# Motivation
It's ofen a pain to deploy a website on a server, when it contents different credentials for mail, database.
This little tool gives the possibility to set up and tests that stuffs.

At the end of the process (if tests pass), a 'conf.php' file is generated. That means that your index.php can looks like this:

```php
<?php
if(!@include("conf.php")) {
    echo "Setup missing";
    error_log("Setup missing");
    die();
}

[...]

```

conf.php wills contains:
```
$GLOBALS['CONFIG']
```

A global array for your credentials/configuration.


# Setting up your project

This git repo, should actually be used as a git's submodule in your project.

The following aborescence tree is expected:

```
project_dir/
   /libs   - external libs
   /setup  - this submodule
   conf.php - the generated file will layed here
              and should be include in your project
```

Hence, to add this submodule, write:
```
git submodule add https://github.com/chtimi59/php-setup-pack.git setup
```

Once, it's done, project_dir/setup/*setup.conf* should be setup according your needs

```json
{
    "title": "your_Project_Name",
    "features": {
        "db"    : true,
        "user"  : true,
        "admin" : true,
        "mail"  : true,        
    }
}    
```

## Title
Just here for convignence

## features-db
- Will ask for MYSQL Database credentials
- Will test then
- Will configure your database with *setup.sql*

Ok, That means that you should update project_dir/setup/*setup.sql* according your needs.

## features-user
Note: this requiered **features-db** to be ON
- Will ask for a user-table name
- Will configure your database with *user.sql*

Ok, here again you can update project_dir/setup/*user.sql*

**Some fields are mandatory!**

## features-admin
Note: this requiere **features-db** and **features-user** to be ON
- Will insert admin user in user's table

## features-mail
Note: this requiered to add **PHPMailer** in your project libs folder
```
git submodule add https://github.com/PHPMailer/PHPMailer.git libs/PHPMailer
```
- Will test smtp credentials by sending you an email

#Usage
You just open http://yoururl/setup and follow the instruction.

At this end a *conf.php* file should be generated.







