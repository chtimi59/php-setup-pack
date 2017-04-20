# setup
Setup tools for php web site

This should be use as submodule for other projects
```
git submodule add https://github.com/chtimi59/php-setup-pack.git setup
```

in your project the following aborescence tree is expected:
```
\project
   \ libs   - external libs
   \ setup  - this submodule
   conf.php - the generated file will layed here
              and should be include in your project
```

#Setting up...
*setup.conf* should be setup according your needs

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
- Will test your MYSQL Database credentials
- Will configure your database with *setup.sql*, that means that this file should be update according your needs

## features-user
Note: this requiere **features-db** to be ON
- Will add a user table in your database

## features-admin
Note: this requiere **features-db** and **features-user** to be ON
- Will insert admin user in user's table in your database

## features-mail
Note: this requiered to add **PHPMailer** in your project libs folder
```
git submodule add https://github.com/PHPMailer/PHPMailer.git libs/PHPMailer
```
- Will test smtp credentials by sending you an email

#Usage
From there normally you just open http://yoururl/setup and follow the instruction.

At this end a *conf.php* file should be generated.





