<?php

namespace Brs\SocialMediaAPI\Tests\API\Assets;

use Brs\SocialMediaAPI\API\AbstractAPI;


class MockAPICreatePostFail extends AbstractAPI
{
    public $apiName;

    public function getPostsRaw($fromDate)
    {
        return [
            [],
        ];
    }

    protected function createPost(array $rawData)
    {
        return new \StdClass;
    }

    protected function createClient()
    {
        return new \StdClass;
    }
}