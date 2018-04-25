AMP Cache Update
================

A simple PHP Class to update Google AMP Cache

Installation
------------

    composer require ennexa/amp-update-cache:dev-master

Usage
----------

To purge the cache for [https://www.prokerala.com/health/?amp=1](https://www.prokerala.com/health/?amp=1)

```
$var = new Ennexa\AmpCache\Update(PATH_TO_YOUR_PRIVATE_KEY); // Make sure your private key is outside document root

$status = $var->purge('https://www.prokerala.com/health/?amp=1');
```
