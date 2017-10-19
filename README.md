# Larvela
Larvela - Simple, Scalable, Fast - A Laravel 5 based eCommerce Framework for online merchants.

# Status
Larvela is currently in Beta and code is being finalized for release, the larvela.org web site is being developed and will go live when the code is ready to be released.

# What is it
Larvela is a Laravel 5 web application for small businesses that wish to move away from Magento to a simpler template driven
eCommerce platform. Larvela provides a fully function business automation system to handle order processing, notifications, subscriptions and post purchasing. There is a comprehensive administration backend which allows for product, image, customer, SEO and multistore support.

# Is it in Production

Despite the "Beta" release, It is currenlty being used for the last 12 months in a number of production web site which are trialling the code and actively reporting issues.

# Product Types

There are 4 types of products defined in the Beta release with more planned:

* Basic Product - Physical, shipable product with a quantity in stock that decrements as customers purchase them until the quantity reaches zero. When the quantity hits zero the system will email the store owner and tell them.

* Parent Product - Used to group related basic products together by attribute (size, colour etc)

* Virtual Unlimited Product - Digital style product such as an eBook where you can sell them forever.

* Virtual Limited Produyct - Digital style product where there is a fixed quantity that can be sold, such as tickets to an event.


# Templating

Larvela uses the Laravel Blade template system to provide a very clean environment for designers to create your store.
Fixed and date based Theming is supported so you can implement changes in themes automatically at various festive times of the year. The theme design is based on: https://z900collector.wordpress.com/2017/08/29/laravel-5-implementing-themes/

# Multi Store

Larvela supports an unlimted number of stores on the same host. This requires an environment variable be presented by the web server (usually set in your site specific web-host configuration file). Each store can be Themed independantly, this allows you to build language specific versions of your site or any type of variant you desire.

```
 SetEnv STORE_CODE EUSTORE
 SetEnv STORE_ENV PROD
```
# Administration

An administration backend is provided to enable you to manage all aspects of the store.

MORE TO COME....
