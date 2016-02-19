<?php

use Brs\SocialMediaAPI\Tests\Helper;
use Brs\SocialMediaAPI\Tests\API\Assets;

class AbstractTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Helper::resetDb();
    }

    public function tearDown()
    {
        Helper::resetDb();
    }

    public function testSetParam()
    {
        $mock = new Assets\MockAPI();
        $paramName = 'test_param';
        $paramValue = 'test_val';

        $this->assertNull($mock->getParam($paramName));
        $mock->setParam($paramName, $paramValue);
        $this->assertEquals($mock->getParam($paramName), $paramValue);

        $mock2 = new Assets\MockAPI2();
        $this->assertNull($mock2->getParam($paramName));
    }

    /**
     * @expectedException Brs\SocialMediaAPI\Exception
     */
    public function testImportPostsFailInvalidClass()
    {
        $mock = new Assets\MockAPICreatePostFail;
        $mock->importPosts(function ($post) {});
    }

    public function testImportPosts()
    {
        $mock = new Assets\MockAPI;
        $mock->importPosts(function ($post, $i, $api) {
            $data = $api->getPostsRaw('1970-01-01')[$i];
            $this->assertEquals($data['id'], $post->getId());
            $this->assertEquals($data['text'], $post->getContent());
            $this->assertEquals($data['date'], $post->getDate());
            return md5(microtime());
        });
    }

    public function testImportPostsRepeat()
    {
        $mock = new Assets\MockAPI;
        $counter1 = 0;
        $mock->importPosts(function ($post, $i, $api) use (&$counter1) {
            $counter1++;
            return md5(microtime());
        }, [
            'fromDate' => '1970-01-01',
        ]);
        $this->assertEquals(3, $counter1);

        $mock = new Assets\MockAPI;
        $counter2 = 0;
        $mock->importPosts(function ($post, $i, $api) use (&$counter2) {
            $counter2++;
            return md5(microtime());
        }, [
            'fromDate' => '1970-01-01',
        ]);
        $this->assertEquals(0, $counter2);
    }

    public function testSaveYoungestPostData()
    {
        $mock = new Assets\MockAPI();

        $mock->importPosts(function ($post, $i, $api) {
            return md5(microtime());
        });

        $rawData = $mock->getPostsRaw('1970-01-01');
        $this->assertEquals($rawData[2]['date'], $mock->getParam($mock::PARAM_IMPORT_FROM_DATE));
    }
}