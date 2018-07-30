# Larvela eCommerce Framework

# What is it?

Larvela is an eCommerce shopping web application built on a Laravel 5.3 framework. It is designed to be fast and light weight with a GT metrics score of 95% and an AA rating.

Larvela is being aimed at small Magento sites who wish to move to an inexpensive easy to develop eCommerce platform with an easy to use templating environment (using Laravel Blade Templates).

# Status

# Is it Production Ready?

# Architecture

## Templating

## Multi-Store / Multi-Site

## Horizontal Scaling

# Administration Backend

# Payments

# Business Automation

The Larvela software includes a range of advanced Marketing Automation features such as:

- New User Welcome Program
- Subscriptions Handling
- Transactional Workflow Programs
- Post Purchase Programs
- Product Replenishment Program.

Features are being enhanced in each release, to match what you would find in high end eCommerce systems.

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Larvela framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT). The Copyright is held by Present & Future Holdings Pty Ltd, a Proprietary Limitited Company registered in Brisbane, Australia.

# Knowledgebase

The Knowledgebase is slowly being updated, you can view it by visiting https://larvela.org/


# Instalation and Configuration

## Installation

Please review the Laravel 5.3 Installation Guide, once v5.3 installed, ensure the composer.json file is configured as per the repo file. 

Also the directories from the repo can be copied into place until an installation program is finalized.

## Configuration

app.php configuration

Add the following to the end of the "providers" array:

 Illuminate\Bus\BusServiceProvider::class,
 App\Providers\AppServiceProvider::class,
 App\Providers\AuthServiceProvider::class,
 App\Providers\EventServiceProvider::class,
 App\Providers\RouteServiceProvider::class,
 Collective\Html\HtmlServiceProvider::class,
 Illuminate\Notifications\NotificationServiceProvider::class,
 App\Providers\ThemeServiceProvider::class,
 Barryvdh\Snappy\ServiceProvider::class,

Add the following lines to the end of the "aliases" array:

 'Form' => Collective\Html\FormFacade::class,
 'Html' => Collective\Html\HtmlFacade::class,
 'Input' => Illuminate\Support\Facades\Input::class,
 'StoreHelper'=> App\Helpers\StoreHelper::class,
 'Notification' => Illuminate\Support\Facades\Notification::class,
 'PDF' => Barryvdh\Snappy\Facades\SnappyPdf::class,
 'SnappyImage' => Barryvdh\Snappy\Facades\SnappyImage::class,
 ],

