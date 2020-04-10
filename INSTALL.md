# Out of the box install
Here are the basic setup items which all need to be done, which is probably
exactly what you need if you already have an Ubuntu PHP box, which you need to
upgrade to PHP 7.4, install missing extensions, and get services up. The only
dependencies for Pioneers of Plymouth are some php extensions, stylus
(which requires npm), composer, one composer package which is DBAL
(this is set to a specific version in the composer file), and zbee/UserSystem,
which is included and modified.

Pioneers of Plymouth is written for PHP 7.4. If you do not have PHP 7.4
installed then you can follow
[this guide](https://computingforgeeks.com/how-to-install-php-on-ubuntu/)
or here are some highlight commands:

```
$ sudo apt-get update
$ sudo apt install software-properties-common
$ sudo add-apt-repository ppa:ondrej/php
$ sudo apt-get update
$ sudo apt install php7.4 libapache2-mod-php7.4
$ sudo apt install php7.4-{bcmath,bz2,cgi,cli,common,dba,dev,enchant,fpm,gd,gmp,imap,interbase,intl,json,ldap,mbstring,odbc,opcache,pgsql,phpdbg,pspell,readline,snmp,soap,sqlite3,sybase,tidy,xml,xmlrpc,xsl,zip,memcached,mysql}
$ sudo a2enmod php7.4
```

If you already had PHP then you'll need to disable your other version, replace
the Xs with what your version was:

```
$ sudo a2dismod phpX.X
```

You will probably also want to copy your old `php.ini` over to 7.4 if you
already had another version of PHP installed:

```
$ sudo cp /etc/php/X.X/apache2/php.ini /etc/php/7.4/apache2/php.ini
```

The additional php7.4-[packages] are what I needed to run Pioneers of Plymouth
as well as other software - you could reduce this list, but this will definitely 
give you a solution.

The next installation you'll need is memcache, which can be installed similarly:

```
$ sudo apt install php7.4-memcache
$ sudo a2enmod php7.4-memcache
$ sudo service start memcached
```

The first thing configuration you'll need to update is for allowing .htaccess
files. Add this to the apache2 config for your directory: 

```
<Directory /path/to/pioneersOfPlymouth>
  Options Indexes FollowSymLinks
  AllowOverride All
  Require all granted
</Directory>
```

Next you'll want to be sure to compile the stylus included for styling. Navigate
to the `/web/assets/stylus` folder and run the following:

```
$ npm install stylus -g
$ stylus main.styl -o ../css/main.css -c
```

Then you'll want to install the [composer](https://getcomposer.org/download/)
packages in the `/game/` directory:
```
$ composer install
```

When you setup your Pioneers of Plymouth database in your MySQL
database, you can use [the DDL](database_setup.sql) to generate the
tables. However, make sure the default engine is set to InnoDB, or alter
the DDL to set each table to be InnoDB manually.
This is required as foreign key constraints are in use.