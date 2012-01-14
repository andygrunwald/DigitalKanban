DigitalKanban
========================

1) About DigitalKanban
--------------------------------

Hey, welcome to DigitalKanban. But what is it?
DigitalKanban is a small digital Kanban board with multiple users, multiple boards and some drag and drop features for tickets and columns.

DigitalKanban was created for a small project at my university and one of my wishes was to try Symfony2. So here we are. This is my first project created with Symfony2. So be patient with me and drop some hints to improve some parts of this application. This project could be a example for other Symfony2 starter as i was. So, have look and try it. If you found some bugs or or have an issue, create a bug ticket here and i will try to help you ;)

This project is based on [Symfony2](http://symfony.com/), [jQuery](http://jquery.com/), [HTML5Boilerplate](html5boilerplate.com) and there sub projects. 

PS: You should use a 'newer' browser like Google Chrome or Mozilla Firefox (up to date please), because DigitalKanban make use of some HTML5 and CSS3 features. Thanks ;)

2) Installation
--------------------------------

* Get a copy of this application
* Target your vhost to the web/ directory (document root). In this example your vhost is named ``digitalkanban.local``
* For shell commands you must change the directory to main leben (where is ``app/``, ``bin/``, ``Database/`` and so on)
* Call [http://digitalkanban.local/config.php](http://digitalkanban.local/config.php) via webbrowser to check for all (web) requirements (if a test will not pass, configure your server to pass this tests)
* Call ``php app/check.php`` via shell to check for all (cli) requirements (if a test will not pass, configure your server to pass this tests)
* Open ``app/config/parameters.ini`` and edit database settings to match your personal settings (``database_host``, ``database_port``, ``database_name``, ``database_user``, ``database_password``)
* Set up the database: You can chose between two different ways. 1. via a complete MySQL dump or 2. via Symfony2 cli. The prefered way is via Symfony2 cli, because by this way you can check if Symfony2 has access to your database ;)
* Setting up database via a MySQL dump
	* Login to you MySQL-Server via command ``mysql -u###USER### -p`` (replace ``###USER###`` with YOUR MySQL user for example ``root``)
	* Create database via command ``CREATE DATABASE ###DATABASENAME###;`` (Please use for ``###DATABASENAME###`` the setting from ``app/config/parameters.ini`` -> ``database_name``. Default is ``digital_kanban``)
	* Exit MySQL via command ``exit``
	* Import the MySQL dump via command ``mysql -u###USER### -p ###DATABASENAME### < Database/digital_kanban.sql`` (for example ``mysql -uroot -p digital_kanban < Database/digital_kanban.sql``)
* Setting up database via Symfony2
	* Call ``php app/console doctrine:database:create`` via shell to create the database
		If the result was successful you will get the output<br />
		``Created database for connection named digital_kanban``
	* Call ``php app/console doctrine:schema:update --force`` via shell to set up the database schema
		If the result was successful you will get the output:<br />
		``Updating database schema... 
		Database schema updated successfully! "15" queries were executed``
	* Call ``php app/console doctrine:fixtures:load`` via shell to fill up the database with sample data
		If the result was successful you will get the output:<br />
		``purging database``<br />
		``loading DigitalKanban\BaseBundle\DataFixtures\ORM\FixtureLoader``
* Call [http://digitalkanban.local/](http://digitalkanban.local/) via webbrowser and enjoy the application

3) Logins
--------------------------------
At first you will be asked for a login. Login with username/password or email/password.<br />
Here are some logins:

Username: *john*<br />
Email: *john@example.com*<br />
Password: *admin*<br />
Role: Administrator<br />
Active: Yes

Username: *max*<br />
Email: *max@mustermann.de*<br />
Password: *user*<br />
Role: User<br />
Active: Yes

Username: *dieter*<br />
Email: *dieter@google.de*<br />
Password: *user*<br />
Role: User<br />
Active: No

User: *markus*<br />
Email: *markus@yahoo.de*<br />
Password: *user*<br />
Role: Administrator<br />
Active: Yes

User: *daniel*<br />
Email: *daniel@web.de*<br />
Password: *user*<br />
Role: User<br />
Active: Yes

4) Problems / Issues / Help
--------------------------------
If you got a blank page while starting the application, open *web/app.php* and replace

    $kernel = new AppKernel('prod', false);

with

    $kernel = new AppKernel('prod', true);

reload the page, have a look which error occur and try to solve it on your own.<br />
If you can not find a solution, drop me a short message in a ticket or something like this. I will try to help you.

5) Hints and more informations
--------------------------------
You can use the github wiki for more informations about DigitalKanban.<br />
If there are useful informations and hints, you will find them in the wiki.

6) Have fun
--------------------------------
Enjoy this little application and i will be happy for feedback!