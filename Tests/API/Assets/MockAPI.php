<?php

namespace Brs\SocialMediaAPI\Tests\API\Assets;

use Brs\SocialMediaAPI\API\AbstractAPI;


class MockAPI extends AbstractAPI
{
    public $apiName;

    public function getPostsRaw($fromDate)
    {
        $someTime = 1455703224;
        $fromTs = strtotime($fromDate);
        $data = [
            [
                'id' => 1,
                'date' => date('Y-m-d H:i:s', $someTime - 3 * 86400),
                'text' => 'post 1'
            ],
            [
                'id' => 2,
                'date' => date('Y-m-d H:i:s', $someTime - 2 * 86400),
                'text' => 'post 2'
            ],
            [
                'id' => 3,
                'date' => date('Y-m-d H:i:s', $someTime),
                'text' => 'post 3'
            ],
        ];
        $result = [];
        foreach ($data as $v) {
            if (strtotime($v['date']) >= $fromTs) {
                $result[] = $v;
            }
        }
        return $result;
    }

    protected function createPost(array $rawData)
    {
        $post = new \Brs\SocialMediaAPI\Model\Post();
        $post
            ->setRawData($rawData)
            ->setId($rawData['id'])
            ->setContent($rawData['text'])
            ->setDate($rawData['date'])
        ;
        return $post;
    }

    protected function createClient()
    {
        return new StdClass;
    }


    // switch methods to public - for testing
    public function getParam($key, $default = null)
    {
        return parent::getParam($key, $default);
    }

    public function setParam($key, $value)
    {
        return parent::setParam($key, $value);
    }
}