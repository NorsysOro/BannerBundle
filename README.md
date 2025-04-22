NorsysBannerExtension v1.0
==============================

What is the NorsysBannerExtension
--------------------

This bundle allow you to manage on or more banner and display them in the frontend according to your configuration. The aim is to be able to display messages to your customers, and decide who sees what, and when.

System Requirements
-------------------

Please see the OroCommerce online documentation for the complete list of [system requirements](https://doc.oroinc.com/backend/setup/system-requirements/).

This bundle has been tested on both the Community and Enterprise editions for the 4.2 and 5.0 versions

## 📦 Installation

To include this bundle in your OroCommerce project using Composer, follow the steps below.

### 1. Require the Bundle
```bash
composer require norsysoro/bannerbundle:dev-main
```
### 2. Enable the Bundle
Register the bundle in your bundles.php file if not auto-registered:
```php
return [
    // Other bundles...
    Norsys\BannerBundle\NorsysOroBannerBundle::class => ['all' => true],
];
```
### 3. Clear cache and install assets
```bash
php bin/console cache:clear
php bin/console oro:assets:install
php bin/console oro:platform:update --force --skip-search-reindexation --skip-download-translations --skip-translations
```
