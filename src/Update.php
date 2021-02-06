<?php

namespace Ennexa\AmpCache;

/*
*  Amp Cache Update
*
*  Class to request updation of Google AMP Cache
*
*/
use GuzzleHttp\Client;

class Update
{
    private $guzzleClient;

    private static $arContentType = [
        'c' => 'Document',
        'i' => 'Image',
        'r' => 'Resource',
    ];

    private $cacheList;

    public function __construct($keyFilePath)
    {
        if (!file_exists($keyFilePath)) {
            throw new \Exception('Private key not found');
        }

        $this->keyFilePath = $keyFilePath;
        $this->guzzleClient = new Client();
    }

    /**
     * Set Caches
     *
     * Set a list of AMP Caches with the same structure as what you get from https://cdn.ampproject.org
     *
     * @param array $cacheList List of caches in the format of https://cdn.ampproject.org
     */
    public function setCache($cacheList)
    {
        $this->cacheList = $cacheList;
    }

    /**
     * Purge
     *
     * Request AMP CDNs to purge the cache for the specified url
     *
     * @param string $url         Updated url
     * @param char   $contentType Content type to update
     *
     * @return bool
     */
    public function purge($url, $contentType = 'c')
    {
        $timestamp = time();
        $info = parse_url($url);

        $host = strtr($info['host'], '.', '-');

        $url = "{$info['host']}{$info['path']}" . urlencode(isset($info['query']) ? "?{$info['query']}" : '');

        $ampCachePath = "/update-cache/{$contentType}/" . ('https' === $info['scheme'] ? 's/' : '');
        $ampCachePath .= "{$url}?amp_action=flush&amp_ts={$timestamp}";

        $privateKey = openssl_pkey_get_private('file://' . $this->keyFilePath);
        openssl_sign($ampCachePath, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        openssl_free_key($privateKey);

        $signature = self::base64encode($signature);
        $status = true;

        foreach ($this->getCaches() as $cache) {
            $ampCacheBase = "https://{$host}.{$cache->updateCacheApiDomainSuffix}";
            $response = $this->guzzleClient->get("{$ampCacheBase}{$ampCachePath}&amp_url_signature={$signature}");
            $status = $status && ('OK' === (string)$response->getBody());
        }

        return $status;
    }

    /**
     * Purge All
     *
     * Convenience method to purge multiple urls
     *
     * @param string $url         Updated url
     * @param char   $contentType Content type to update
     *
     * @return bool
     */
    public function purgeAll(array $arFile, $contentType = 'c')
    {
        $status = true;
        foreach ($arFile as $url) {
            $status = $status && $this->purge($url, $contentType);
        }

        return $status;
    }

    /**
     * Base64 Encode
     *
     * Creates url-safe base64 encoded string
     *
     * @param string $string String to encode
     *
     * @return string
     */
    private static function base64encode($string)
    {
        return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($string));
    }

    /**
     * Get Caches
     *
     * Get a list of AMP Caches from cdn.ampproject.org
     *
     * @param void
     *
     * @return string
     */
    public function getCache()
    {
        if (is_null($this->cacheList)) {
            $response = $this->guzzleClient->get('https://cdn.ampproject.org/caches.json');
            $body = $response->getBody();

            $data = json_decode($body);
            if ($data) {
                $this->cacheList = $data->caches;
            }
        }

        return $this->cacheList;
    }
}
