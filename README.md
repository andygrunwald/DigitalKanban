README
======

What is DigitalKanban?
-----------------

Hey, welcome to DigitalKanban. But what is it?
DigitalKanban is a small digital Kanban board with multiple users, multiple boards and some drag and drop features for tickets and columns.

DigitalKanban was created for a small project at my university and one of my wishes was to try Symfony2. So here we are. This is my first project created with Symfony2. So be patient with me and drop some hints to improve some parts of this application. This project could be a example for other Symfony2 starter as i was. So, have look and try it. If you found some bugs or or have an issue, create a bug ticket here and i will try to help you ;)

This project is based on [Symfony2](http://symfony.com/), [jQuery](http://jquery.com/), [HTML5Boilerplate](html5boilerplate.com) and there sub projects.

PS: You should use a 'newer' browser like Google Chrome or Mozilla Firefox (up to date please), because DigitalKanban make use of some HTML5 and CSS3 features. Thanks ;)

Requirements
------------
DigitalKanban is a normal Symfony2 application.
Have a look at [Requirements for running Symfony2](http://symfony.com/doc/2.0/reference/requirements.html)

Installation
------------

* Get a copy of this application
* Target your vhost to the web/ directory (document root). In this example your vhost is named ``digitalkanban.local``
* For shell commands you have to change the directory to main application root (level on ``app/``, ``bin/`` and so on)
* Call ``php bin/vendors install`` via shell
* Call [http://digitalkanban.local/config.php](http://digitalkanban.local/config.php) via webbrowser to check for all (web) requirements (if a test will not pass, configure your server to pass this tests)
* Call ``php app/check.php`` via shell to check for all (cli) requirements (if a test will not pass, configure your server to pass this tests)
* Copy ``app/config/parameters.ini.dist`` to ``app/config/parameters.ini`` and edit database settings to match your personal settings
* Setting up database (incl. dummy data) with the following shell commands
	* ``php app/console doctrine:database:create``
	* ``php app/console doctrine:schema:update --force``
	* ``php app/console doctrine:fixtures:load``
* Call [http://digitalkanban.local/](http://digitalkanban.local/) via webbrowser and enjoy the application

Logins
------------
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

Problems / Issues / Help
------------
If you got a blank page while starting the application, open *web/app.php* and replace

    $kernel = new AppKernel('prod', false);

with

    $kernel = new AppKernel('prod', true);

reload the page, have a look which error occur and try to solve it on your own.
If you can not find a solution, drop me a short message in a ticket or something like this. I will try to help you.
You can also use the github wiki for more informations about DigitalKanban.

Have fun
------------
Enjoy this little application and i will be happy for feedback!