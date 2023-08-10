# Mensa.JETZT

Do you struggle to coordinate the timings of your canteen visits with friends?

Worry no longer! Mensa.Jetzt will provide you with an easy to use canteen coordination interface.

Just select the time and enter your name and you will be shown on a list of canteen goers, where everyone else
can find you and coordinate the canteen going efforts.

## Installation
This tool is written in php so you can just drop it on your webserver (provided you still have one with php installed).
You need to create a folder in the parent folder of you webroot named `data` and grant permissions to the webserver user. You do not need a database. Standard php extensions and php 5.4 should do the job.

## Configuration
Some things are only configurable in the source code.
You can/should configure oauth for user management. Most strings are configurable.
Just copy the `config.php.default` to `config.php` and change the values accordingly.

### Multi-Domain-Support
Multipage support is implemented, but a bit cursed.
Just write a config.php like:
```php
<?php
$oauth_client_id = "";
$oauth_client_secret = "";
$oauth_token_url = "";
$oauth_userinfo_url = "";


if ($_SERVER['HTTP_HOST'] == "mensa.jetzt") {
    $canteen_types = ["OnlyOneTypeHere"]; // Hides Type Selector
    $times = ["11:30 Uhr", "11:45 Uhr", "12:00 Uhr", "12:15 Uhr", "12:30 Uhr", "12:45 Uhr", "13:00 Uhr", "13:15 Uhr", "13:30 Uhr", "13:45 Uhr", "14:00 Uhr", "14:15 Uhr"];
    $default_time = "12:00 Uhr";
    $dataDirectory = "../data1";
    $endHour = 15;
    $pageTitle = "Mensa.Jetzt";
    $oauth_redirect_url = "https://mensa.jetzt/oauth.php";
    $lang_overview_sentence = "in der Mensa";
    $lang_also_there_sentence = "Du bist %word auch in der Mensa?";
    $lang_change_time_sentence = "Du bist %word doch wann anders in der Mensa?";
} else if ($_SERVER['HTTP_HOST'] == "2.mensa.jetzt") {
    $canteen_types = ["A", "B", "C"];
    $times = ["OnlyOneTimeHere"]; // Hides time selector
    $default_time = "OnlyOneTimeHere";
    $dataDirectory = "../data2";
    $endHour = 23;
    $oauth_redirect_url = "https://2.mensa.jetzt/oauth.php";
    $pageTitle = "Mensa.Jetzt 2";
    $lang_overview_sentence = "in der Mensa (2)";
    $lang_also_there_sentence = "Du bist %word auch in der Mensa (2)?";
    $lang_change_time_sentence = "Du bist %word doch wann anders in der Mensa (2)?";
}

$oauth_request_url = "https://yourOauthDomain/oauth/authorize?client_id=".$oauth_client_id."&redirect_uri=".$oauth_redirect_url."&response_type=code&scope=openid+profile";
```
## WHY PHP???
I wanted to just quickly write a small tool, mostly for shitposting with the domain name.
The initial version had just 100 lines `index.php` so php seemed to be the easiest choice.

### Ok, but why no database?
I felt like sqlite was to much of a hassle for such a small project.
Than I added features...