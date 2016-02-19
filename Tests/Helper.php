<?php

namespace Brs\SocialMediaAPI\Tests;

use Brs\Stdlib\Assert;

class Helper
{
    public static function resetDB()
    {
        $dbFile = 'social-media-api.db';
        if (file_exists($dbFile)) {
            unlink($dbFile);
        }
    }

    public function getTwitterClientOptions()
    {
        Assert::has($GLOBALS, [
            'TWITTER_OAUTH_ACCESS_TOKEN',
            'TWITTER_OAUTH_ACCESS_TOKEN_SECRET',
            'TWITTER_CONSUMER_KEY',
            'TWITTER_CONSUMER_SECRET',
        ]);
        return [
            'oauth_access_token' => $GLOBALS['TWITTER_OAUTH_ACCESS_TOKEN'],
            'oauth_access_token_secret' => $GLOBALS['TWITTER_OAUTH_ACCESS_TOKEN_SECRET'],
            'consumer_key' => $GLOBALS['TWITTER_CONSUMER_KEY'],
            'consumer_secret' => $GLOBALS['TWITTER_CONSUMER_SECRET'],
        ];
    }

    public function getFacebookClientOptions()
    {
        Assert::has($GLOBALS, [
            'FACEBOOK_APP_ID',
            'FACEBOOK_APP_SECRET',
            'FACEBOOK_ACCESS_TOKEN',
            'FACEBOOK_PROFILE_ID',
        ]);
        return [
            'app_id' => $GLOBALS['FACEBOOK_APP_ID'],
            'app_secret' => $GLOBALS['FACEBOOK_APP_SECRET'],
            'access_token' => $GLOBALS['FACEBOOK_ACCESS_TOKEN'],
            'profile_id' => $GLOBALS['FACEBOOK_PROFILE_ID'],
        ];
    }
}