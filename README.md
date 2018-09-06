# Larvela eCommerce Framework

# What is it

Larvela is a Web Based eCommerce Shopping Cart built on a Laravel 5.4+ Framework. It is designed to be fast and light weight with a GT metrics score of 95% and an AA rating.

Larvela was initially aimed at small Magento sites who wish to move to an inexpensive easy to develop eCommerce platform with an easy to use templating environment (using Laravel Blade Templates).

Support for v5.5 and v5.6 are in the works and code migration should occur shortly.


# Status

## Is it Production Ready

Larvela is running in production environments but is still considered Beta until the current round of code refactoring is being completed. Contributors to the Payment Gateway code are welcome as this is a current weakness.

## Support

Limited email support is available until a Production release is available. If you have skills in using composer and installing a working Laravel deployment then you should have no issues in getting the code implemented and working once all the views are released.


# Architecture

Larvela has been designed from the ground up to scale to huge system capacities if needed, that includes multi-server front ends, multi-server backends using Queuing and a host of features that the Laravel framework provides to achieve this.

It is recommended that the Redis cache is installed and configured for the Session Support and Queue capabilites in all deployments. While Memcached is also available, it does not provide a Message Queue/ Job Queue capability which is used extensively in Larvela. You are free to implement a third party Queuing solution but this may not be supportable over time. 

Larvela has been implemented from the ground up to support multi work queues and session caching using Redis so this is the basic recomendation for all implementations.

## Templating

Larvela uses the Laravel Blade template system to provide a very clean environment for designers to create your store. Fixed and date based Theming is supported so you can implement changes in themes automatically at various festive times of the year. The theme design is based on: https://z900collector.wordpress.com/2017/08/29/laravel-5-implementing-themes/

Larvela uses the Laravel Blootstrap v3 in the Blade templating. The Blade templating is also implemented for Mailable eMail templating which has proven to work well during Alpha and Beta Production testing.

## Multi-Store / Multi-Site

Larvela supports an unlimted number of stores on the same host. This requires an environment variable be presented by the web server (usually set in your site specific web-host configuration file). Each store can be Themed independantly, this allows you to build language specific versions of your site or any type of variant you desire.
```
 SetEnv STORE_CODE EUSTORE
 SetEnv STORE_ENV PROD
```
More info can be found in the knowledgebase article: <a href="https://larvela.org/kb?2017103101">Vhost Variable for Multiple Store Support</a>

# Product Types

As of July 2018 there are 4 types of products defined in the Beta release with more planned:

* Basic Product - Physical, shipable product with a quantity in stock that decrements as customers purchase them until the quantity reaches zero. When the quantity hits zero the system will email the store owner and tell them. When the product level is increased all subscribed customers will be notified via the BackInStock notification system.

* Parent Product - Used to group related basic products together by attribute (size, colour etc) - Beta code being developed and due for release in August, uses definable attributes like Size, Colour etc to build SKU's from Basic Products, See Knowledgebase Article:

## Not yet fully implemented

* Virtual Unlimited Product - Digital style product such as an eBook where you can sell them forever.

* Virtual Limited Produyct - Digital style product where there is a fixed quantity that can be sold, such as tickets to an event.

## Being Developed

* Pack Products - Think of this as a related group of Basic products but you need to be a set quantity from a range, you pick the items to make up the pack. I.e 6-pack of Beer, you pick the selected range of brands to put in the pack.

* Grouped Products - Like a Pack Product, but you can fill your cart using a range of basic (related by SKU) products, i.e. shirt, pants, socks and shoes as a package, but you get to pick the colours and size to then get a discounted price for buying all the items as a "Group".



# Administration Backend

A comprehensive backend has existed since the alpha release. It provides:

* Product Management.
* Category Management.
* SEO and page control.
* Settings and Configuration for multi stores support.
* Customer Management.
* Order review and management.
* System Settings.
* and more!


# Payments

Support for Paypal has been built into the DEMO site, more work being done on Payment Gateways

# Business Automation

The Larvela software includes a range of advanced Marketing Automation features such as:

- New User Welcome Program
- Subscriptions Handling
- Transactional Workflow Programs
- Post Purchase Programs
- Product Replenishment Program.

Features are being enhanced in each release, to match what you would find in high end eCommerce systems. Review the app/Jobs and app/Mail code for information on whats been implemented.

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell at taylor@laravel.com. All security vulnerabilities will be promptly addressed.

## License

The Larvela framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT). The Copyright is held by Present & Future Holdings Pty Ltd, a Proprietary Limitited Company registered in Brisbane, Australia.

# Knowledgebase

The Knowledgebase is slowly being updated, you can view it by visiting https://larvela.org/


# Instalation and Configuration

## Installation

*Being Finalized **

The install process has become increadibly simple!

Using <b>git clone</b>, you an download a copy of the current release into a directory created for you, edit the .env file, run <b>composer install</b>, set file ownership, generate an APP KEY, run the migration, then the seed and fire up the site!

Once fired up the auot installer runs and collects some details about you and the store, configures itself and your in business.

The install process is documented here: <a href="https://larvela.org/kb?2017-0002">https://larvela.org/kb?2017-0002</a>. Its being actively updated as the release date approaches and any additional requirements will be posted in the <a href="https://larvela.org/kb">Knowledgebase</>

### Installation for Development, User Acceptance Testing and Production

Typically you would create a "dev", "uat" and "prod" environment so you can develp, test and release to production...
To install into a directory called "dev"
```
git clone https://github.com/offgridengineering/Larvela.git dev
composer install
```

## Configuration

 TODO - Outline auto installer actions....

### Composer additions

 TODO - Still to do, outline how to add extra packages.

### app.php configuration:

 Incomlpete

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

