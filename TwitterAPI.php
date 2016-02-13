<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brs\SocialMediaAPI;

use TwitterAPIExchange;
use Brs\SocialMediaAPI\Model\Post;

/**
 * To create keys for application go to: https://apps.twitter.com or https://dev.twitter.com/apps
 * @author Tomasz Borys <t.borys@brs-software.pl>
 */
class TwitterAPI extends AbstractAPI
{
    public function getLastPostsRaw()
    {
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getfield = '?screen_name=VivitarOfficial&max_id=697808584613220352';
        $requestMethod = 'GET';

        $jsonData = $this->getClient()->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        $data = json_decode($jsonData, true);
        // foreach (json_decode($x, true) as $k => $v) {
        //     printf("%02d. %s | %s | %s\n", $k, $v['id'], $v['created_at'], $v['text']);
        // }

        // dbgd(json_decode($x, true));
        // dbgd(json_decode($x, true)[6]['entities']);
        return $data;
    }

    protected function createPost(array $rawData)
    {
        $post = new Post();
        $post
            ->setRawData($rawData)
            ->setId($rawData['id_str'])
            ->setText($rawData['text'])
        ;
        return $post;
    }

    protected function createClient()
    {
        return new TwitterAPIExchange($this->options['clientOptions']);
    }

}