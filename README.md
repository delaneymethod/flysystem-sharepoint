# Flysystem adapter for the Sharepoint API

[![Latest Version on Packagist](https://img.shields.io/packagist/v/delaneymethod/flysystem-sharepoint.svg?style=flat-square)](https://packagist.org/packages/delaneymethod/flysystem-sharepoint)
[![Total Downloads](https://img.shields.io/packagist/dt/delaneymethod/flysystem-sharepoint.svg?style=flat-square)](https://packagist.org/packages/delaneymethod/flysystem-sharepoint)

This package contains a [Flysystem](https://flysystem.thephpleague.com/) adapter for Sharepoint 2013 REST API. Under the hood, the [Sharepoint 2013 REST API](https://www.dropbox.com/developers/documentation/http/overview) is used.

## Installation

You can install the package via composer:

``` bash
composer require delaneymethod/flysystem-sharepoint
```


## Usage

The first thing you need to do is get an authorisation token from Sharepoint. Sharepoint has made this very easy. You can register a new App within your Sharepoint Site that can be used to generate a client ID and Secret. You'll find more info at [Authorizing REST API calls against SharePoint Site](http://spshell.blogspot.co.uk/2015/03/sharepoint-online-o365-oauth.html). 

You can read the whole article for additional knowledge but the first step is the only step you're interested in for our flysystem-sharepoint adapter to work.

With an authorisation token you can instantiate a `DelaneyMethod\Sharepoint\Client`.

``` php
use League\Flysystem\Filesystem;
use DelaneyMethod\Sharepoint\Client as SharepointClient;
use DelaneyMethod\FlysystemSharepoint\SharepointAdapter;

$siteName = 'YOUR_TEAM_SITE_NAME';
$siteUrl = 'https://YOUR_SITE.sharepoint.com';
$publicUrl = 'https://YOUR_SITE.sharepoint.com/:i:/r/sites/YOUR_TEAM_SITE_NAME/Shared%20Documents'
$clientId = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';
$verify = false; // See http://docs.guzzlephp.org/en/stable/request-options.html#verify
$accessToken = 'YOUR_ACCESS_TOKEN';

$client = new SharepointClient($siteName, $siteUrl, $publicUrl, $clientId, $clientSecret, $verify, $accessToken);
			
$adapter = new SharepointAdapter($client);

$filesystem = new Filesystem($adapter, ['url' => $publicUrl]);
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email hello@delaneymethod.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
