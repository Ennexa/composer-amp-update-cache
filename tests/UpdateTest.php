<?php

/**
 * @internal
 * @coversNothing
 */
class UpdateTest extends PHPUnit\Framework\TestCase
{
    /**
     * Check syntax
     *
     * Make sure that there are not syntax errors
     */
    public function testIsThereAnySyntaxError()
    {
        $var = new Ennexa\AmpCache\Update(__DIR__ . '/amp-update-key.pem');
        $this->assertTrue(is_object($var));
        unset($var);
    }

    /**
     * Test Purge
     *
     * Purge a sample url
     */
    public function testPurge()
    {
        $var = new Ennexa\AmpCache\Update(__DIR__ . '/amp-update-key.pem');
        $this->assertTrue($var->purge('https://www.prokerala.com/news/articles/a826335.html?amp=1'));

        unset($var);
    }

    /**
     * Test Purge
     *
     * Purge a sample url
     */
    public function testPurgeAll()
    {
        $var = new Ennexa\AmpCache\Update(__DIR__ . '/amp-update-key.pem');
        $this->assertTrue($var->purgeAll(['https://www.prokerala.com/news/articles/a826335.html?amp=1']));

        unset($var);
    }
}
