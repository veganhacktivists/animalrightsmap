# Animal Rights Map

[![](https://img.shields.io/badge/community-discord-black?style=flat-square&labelColor=000&color=7289da)](https://discord.com/channels/829144774929940550/829184713394487326)
[![](https://img.shields.io/badge/sponsor-patreon-black?style=flat-square&labelColor=000&color=ff424d)](https://patreon.com/veganhacktivists)
[![](https://img.shields.io/badge/trello-vh--playground-black?style=flat-square&labelColor=000&color=026aa7)](https://trello.com/b/J3JW43mY/vh-playground)
[![](https://img.shields.io/badge/website-animalrightsmap.org-black?style=flat-square&labelColor=000&color=ff0097)](https://animalrightsmap.org)

With over 2,500 groups, browse the largest collection of animal rights activist
groups all located in one single map! Filter and learn more information on which
vegan activist organizations you‘d like to join in your area. If you don’t see a
group that you know should be there, email us! None in your area? Start your
own!

## Setup

Requires PHP 8.4 and [Composer](https://getcomposer.org).

```
composer install
cp .env.example .env
php -S localhost:8000 -t public
```

`MAILGUN_API_KEY` is only needed by the group submission form. Run the tests
with `vendor/bin/phpunit`.
