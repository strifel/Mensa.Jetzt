# Mensa.JETZT

Do you struggle to coordinate the timings of your canteen visits with friends?

Worry no longer! Mensa.Jetzt will provide you with an easy to use canteen coordination interface.

Just select the time and enter your name and you will be shown on a list of canteen goers, where everyone else
can find you and coordinate the canteen going efforts.

## Installation
This tool is written in php so you can just drop it on your webserver (provided you still have one with php installed).
You need to create a folder in the parent folder of you webroot named `data` and grant permissions to the webserver user. You do not need a database. Standard php extensions and php 5.4 should do the job.

### Configuration
Most things are only configurable in the source code.
You can/should configure oauth for user management.
Just copy the `config.php.default` to `config.php` and change the values accordingly.

## WHY PHP???
I wanted to just quickly write a small tool, mostly for shitposting with the domain name.
The initial version had just 100 lines `index.php` so php seemed to be the easiest choice.

### Ok, but why no database?
I felt like sqlite was to much of a hassle for such a small project.
Than I added features...