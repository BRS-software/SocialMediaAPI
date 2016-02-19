<?php

namespace Brs\SocialMediaAPI\Tests\API;

use Brs\SocialMediaAPI\Tests\Helper;
use Brs\Stdlib\Assert;

class FacebookTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Helper::resetDb();
    }

    public function tearDown()
    {
        Helper::resetDb();
    }

    public function testImportPostFromDateNow()
    {
        Assert::has($GLOBALS, ['FACEBOOK_IMPORT_POSTS_FROM', 'FACEBOOK_NUMBER_OF_POSTS']);

        $api = new \Brs\SocialMediaAPI\API\Facebook([
            // 'verbose' => true,
            'clientOptions' => Helper::getFacebookClientOptions(),
        ]);

        $counter = 0;
        $api->importPosts(function ($post) use (&$counter) {
            $counter++;
            return md5(microtime());
        }, [
            'fromDate' => $GLOBALS['FACEBOOK_IMPORT_POSTS_FROM'],
        ]);
        $this->assertEquals($GLOBALS['FACEBOOK_NUMBER_OF_POSTS'], $counter);

        $counter2 = 0;
        $api->importPosts(function ($post) use (&$counter2) {
            $counter2++;
            return md5(microtime());
        });
        $this->assertEquals(0, $counter2);
    }

}