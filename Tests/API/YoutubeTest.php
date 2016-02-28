<?php

namespace Brs\SocialMediaAPI\Tests\API;

use Brs\SocialMediaAPI\Tests\Helper;
use Brs\Stdlib\Assert;

class YoutubeTest extends \PHPUnit_Framework_TestCase
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
        Assert::has($GLOBALS, ['YOUTUBE_USER_NAME']);

        $api = new \Brs\SocialMediaAPI\API\Youtube([
            // 'verbose' => true,
            'clientOptions' => Helper::getYoutubeClientOptions(),
        ]);

        $counter = 0;
        $api->importPosts(function ($post) use (&$counter) {
            $counter++;
            return md5(microtime());
        }, [
            'fromDate' => $GLOBALS['YOUTUBE_IMPORT_POSTS_FROM'],
        ]);
        $this->assertEquals($GLOBALS['YOUTUBE_NUMBER_OF_POSTS'], $counter);

        $counter2 = 0;
        $api->importPosts(function ($post) use (&$counter2) {
            $counter2++;
            return md5(microtime());
        });
        $this->assertEquals(0, $counter2);
    }

}