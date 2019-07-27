<div align="center"><img src="https://rugaard.github.io/packages/pollen/logo.jpg"></div>

# 🇩🇰🤧 Pollen measurements from Astma-Allergi Denmark

<a href="https://github.com/rugaard/pollen/releases"><img src="https://img.shields.io/github/release/rugaard/pollen.svg"></a>
<a href="https://travis-ci.org/rugaard/pollen"><img src="https://travis-ci.org/rugaard/pollen.svg?branch=master"></a>
<a href="https://codeclimate.com/github/rugaard/pollen"><img src="https://img.shields.io/codeclimate/coverage/rugaard/pollen.svg"></a>
<a href="https://codeclimate.com/github/rugaard/pollen"><img src="https://img.shields.io/codeclimate/maintainability/rugaard/pollen.svg"></a>
<a href="https://creativecommons.org/licenses/by-nc-nd/4.0/"><img src="https://img.shields.io/static/v1.svg?labelColor=5f5f5f&label=license&color=43897a&message=CC%20BY-NC-ND"></a>

Astma-Allergi Denmark does unfortunately not offer an official API for the latest pollen measurements in Denmark.

This package is (in some form) a workaround for that. It collects the latest measurements from the official Astma-Allergi Denmark website and turns it into structured data objects.

The returned data shows the measured pollen between **13:00** _(1:00 PM)_ yesterday and **13:00** _(1:00 PM)_ present day. Everyday at **16:00** _(4:00 PM)_ the latest measurements are being published.

## ⚠️ Disclaimer
Since Astma-Allergi Denmark is an independent union, with a very little government funding, this package is made available under a very strict license, which prohibits any use other than personal.

If you wish to use the pollen measurements commercially, you should contact Astma-Allergi Denmark directly and support them by buying the data instead. The payment goes directly to the maintenance and further development of their Pollen measurement service.

For more info about a commercial license, [visit their official website](https://hoefeber.astma-allergi.dk/pollenfeed).

## 📖 Table of contents

* [Installation](#-installation)
    * [Laravel](#laravel)
* [Usage](#%EF%B8%8F-usage)
    * [Pollen Client](#pollen-client)
    * [Methods](#methods)
        * [Get measurements](#get-measurements)
* [Pollen stations](#-pollen-stations)
* [Frequently Asked Questions (FAQ)](#-frequently-asked-questions-faq)
    * [What is this `Tightenco\Collect\Support\Collection` class and how does it work?](#what-is-this-tightencocollectsupportcollection-class-and-how-does-it-work)
* [Donating to Astma-Allergi Denmark](#-donating-to-astma-allergi-denmark)
* [License](#-license)

## 📦 Installation
You can install the package via [Composer](https://getcomposer.org/), by using the following command:
```shell
composer require rugaard/pollen
```

### Laravel
This package comes with a out-of-the-box Service Provider for the [Laravel](http://laravel.com) framework.
If you're using a newer version of Laravel (`>= 5.5`) then the service provider will be loaded automatically.

Are you using an older version, then you need to manually add the service provider to the `config/app.php` file:
```php
'providers' => [
    Rugaard\Pollen\Providers\Laravel\ServiceProvider::class,
]
```

## ⚙️ Usage

First thing you need to do, is to instantiate the `Pollen` client
```php
# Instantiate the Pollen client.
$pollen = new \Rugaard\Pollen\Pollen;
```

Once you've done that, you're able to request the latest measurements from one of the [supported pollen stations](#-pollen-stations):
```php
# Copenhagen pollen station.
$measurements = $pollen->get('copenhagen');
```

### Pollen client

The Pollen client which handles the requests to Astma-Allergi Denmark.

```php
new Pollen(?Client $httpClient);
```

| Parameter | Type | Default | Description |
| :--- | :--- | :---: | :--- |
| `$httpClient` | `\GuzzleHttp\ClientInterface` | `null` | Replace the default underlying HTTP Client |

### Methods

#### Get measurements.

Get latest pollen measurements from a specific pollen station.

```php
get(string $stationCode);
```

| Parameter | Type | Description |
| :--- | :--- | :--- |
| `$stationCode` | `string` | Code of station. [Supported pollen stations.](#-pollen-stations) |

_**Note**: The returned data shows the measured pollen between 13:00 (1:00 PM) yesterday and 13:00 (1:00 PM) present day. The measurements are updated everyday at 16:00 (4:00 PM)._

## 🏛 Pollen stations

Currently there only exists two Pollen stations in Denmark. 

| ID | Name | Code | Region |
| :---: | :--- | :--- | :---: |
| 48 | København | `copenhagen` | East |
| 49 | Viborg | `viborg` | West |

## 🗣 Frequently Asked Questions (FAQ)

#### What is this `Tightenco\Collect\Support\Collection` class and how does it work?

All data is returned within a `Tightenco\Collect\Support\Collection` class. The class is a port of the popular `Collection` class from [Laravel](https://laravel.com).

Please refer to [Laravel](https://laravel.com)'s detailed documentation, to learn more about how you work with a `Collection`:<br>
[https://laravel.com/docs/master/collections](https://laravel.com/docs/master/collections)

## 💰 Donating to Astma-Allergi Denmark

To help Astma-Allergi Denmark maintain and further develop the Pollen measurement service.<br>
Please consider [sending them a donation](https://www.astma-allergi.dk/stoetos).

## 🚓 License
This package is licensed under a [Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 (CC BY-NC-ND 4.0)](https://creativecommons.org/licenses/by-nc-nd/4.0/).

<a rel="license" href="https://creativecommons.org/licenses/by-nc-nd/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-nd/4.0/88x31.png" /></a>