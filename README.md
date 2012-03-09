# Google Geolocation Bundle for Symfony2

## Overview

A Symfony 2 bundle for the
[Google Geocoding API](http://code.google.com/apis/maps/documentation/geocoding/)
service.
 
## Requirements

 * PHP 5.3+

## Dependancies

 * [Buzz](https://github.com/kriswallsmith/Buzz)

## Installation

1. Add bundle and Buzz library dependancy to `vendor` dir:

    * Using vendors script

        Add the following to the `deps` file:

            [Buzz]
                git=git://github.com/kriswallsmith/Buzz.git
                target=/Buzz

            [GoogleGeolocationBundle]
                git=git://github.com/dsyph3r/GoogleGeolocationBundle.git
                target=/bundles/Google/GeolocationBundle

        Run the vendors script:

            $ php bin/vendors install

    * Using git submodules:

            $ git submodule add git://github.com/kriswallsmith/Buzz.git vendor/Buzz
            $ git submodule add git://github.com/dsyph3r/GoogleGeolocationBundle.git vendor/bundles/Google/GeolocationBundle

2. Add the Google and Network namespace to your autoloader:

        // app/autoload.php
        $loader->registerNamespaces(array(
            // ..
            'Buzz'      => __DIR__.'/../vendor/Buzz/lib',
            'Google'    => __DIR__.'/../vendor/bundles',
        ));

3. Add bundle to application kernel:

        // app/ApplicationKernel.php
        public function registerBundles()
        {
            return array(
                // ...
                new Google\GeolocationBundle\GoogleGeolocationBundle(),
            );
        }

## Usage

The bundle provides a service available via the ``google_geolocation.geolocation_api``
identifier.

To retrieve the service from the container:

    $geo = $this->get('google_geolocation.geolocation_api');

### Basic usage

To find an address:

    $geolocationApi = $this->get('google_geolocation.geolocation_api');
    $location = $geolocationApi->locateAddress("Wales, UK");

    if ($location->getMatches() > 0)
    {
        $matches = json_decode($location->getResult(), true);
        
        // Get address components [city, country, postcode, etc] for 1st match
        $components = $location->getAddressComponents(0);
        
        // Get LatLng for 2nd match
        $latLng = $location->getLatLng(1);
    }

### Additional Usage

The service can be used in 2 ways:

 1. Without the caching layer (default)
 2. With the caching layer

#### Without the caching layer

The service is configured by default to not use the caching layer.

#### With the caching layer

The caching layer provides a cache of previous requests made to the Google Geocoding API
to reduce the number of requests required for the service. It also allows limiting of
requests made to the service. Both of these features are useful if you heavily use
the Google Geocoding API.

To enable the use of the caching layer you need to configure the service. Update
the config at `app/config/config.yml` with the following:

```
services:
    google_geolocation.geolocation_api:
        class:      %google_geolocation.geolocation_api.class%
        calls:
            - [ setEntityManager, [ @doctrine.orm.entity_manager ] ]
            - [ setDailyLimit, [ %google_geolocation.geolocation_api.daily_limit% ] ]
            - [ setCacheLifetime, [ %google_geolocation.geolocation_api.cache_lifetime% ] ]
```

The defaults for the paramaters are as follows:

```
google_geolocation.geolocation_api.daily_limit: 2500    # Daily requests
google_geolocation.geolocation_api.cache_lifetime: 24   # Hours
``` 

### Clearing the cache

The cache should be cleaned periodically to comply with the Google terms
of service (see below)

Run the following task:

    $ php app/console google:geolocation:clean-cache

## TODO
 
 * Explore use of Zend Caching layer instead of DB - Zend caching provides a SQLite option which would be useful
   for some projects.
   
## Google Terms of Service

Please respect the
[terms of service](http://code.google.com/apis/maps/terms.html) (TOS)
specified by Google for use of the Geocoding API.

The Geocoding API service must only be used in conjunction with a Google Map.
The caching feature provided by the bundle is for temporary caching use in order
to enhance the user experience when using Geocoding (This is permitted by the
TOS). You should run the clean cache task periodically to clean up the cache
values. The lifetime of each Geocoding result can be set via the paramater 
`google_geolocation.geolocation_api.cache_lifetime`. By default this is set to 24 hours
