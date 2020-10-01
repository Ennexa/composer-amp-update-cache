AMP Cache Update
================

A simple PHP Class to update Google AMP Cache

Installation
------------

    composer require ennexa/amp-update-cache:dev-master

Usage
----------

To purge the cache for [https://www.prokerala.com/health/?amp=1](https://www.prokerala.com/health/?amp=1)

```php
$var = new Ennexa\AmpCache\Update(PATH_TO_YOUR_PRIVATE_KEY); // Make sure your private key is outside document root

$status = $var->purge('https://www.prokerala.com/health/?amp=1');
```

The script will purge the cache url from all the caches listed in AMP Project's [official caches list](https://cdn.ampproject.org/caches.json).

If you want to purge only Google's AMP cache, you can filter the list as below

```php
$updater = new Ennexa\AmpCache\Update(PATH_TO_YOUR_PRIVATE_KEY);
$updater->setCache(array_filter($updater->getCache(), function($cache) {
    return 'google' === $cache->id;
}));
$status = $updater->purge('https://www.prokerala.com/health/?amp=1');
```
