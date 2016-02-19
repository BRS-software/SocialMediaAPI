<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brs\SocialMediaAPI\API;

use TwitterAPIExchange;
use Brs\SocialMediaAPI\Model\Post;
use Brs\Stdlib\Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 */
class Twitter extends AbstractAPI
{
    protected function configureClientOptions(OptionsResolver $resolver)
    {
        $resolver
            // ->setDefaults([])
            ->setRequired(['oauth_access_token', 'oauth_access_token_secret', 'consumer_key', 'consumer_secret'])
            ->setAllowedTypes('oauth_access_token', 'string')
            ->setAllowedTypes('oauth_access_token_secret', 'string')
            ->setAllowedTypes('consumer_key', 'string')
            ->setAllowedTypes('consumer_secret', 'string')
        ;
    }


    public function getPostsRaw($fromDate)
    {
        $fromTs = strtotime($fromDate);
        $maxId = null;
        $result = [];
        $pack = 1;
        while (true) {
            $this->printLog('Getting data from twitter API, pack %d: ', $pack++);
            $packData = $this->getPack($maxId);
            $this->printLog("OK\n");

            foreach ($packData as $k => $v) {
                if (strtotime($v['created_at']) >= $fromTs) {
                    $result[] = $v;
                } else {
                    break 2;
                }
                // $this->printLog("%02d. %s:%s - %s\n", $k, $v['user']['id'], $v['created_at'], $v['text']);
            }

            $maxId = $v['id_str'];
        }
        return $result;
    }

    protected function createPost($rawData)
    {
        $post = new Post();
        $post
            ->setRawData($rawData)
            ->setId($rawData['id_str'])
            ->setContent($rawData['text'])
            ->setDate($rawData['created_at'])
            ->setUserName($rawData['user']['name'])
            ->setPostUrl('https://twitter.com/xxx/status/' . $rawData['id_str'])
        ;
        if (! empty($rawData['entities']) && ! empty($rawData['entities']['media']) && ! empty($rawData['entities']['media'][0])) {
            $post->setImgUrl($rawData['entities']['media'][0]['media_url']);
        }
        return $post;
    }

    protected function createClient()
    {
        return new TwitterAPIExchange($this->clientOptions);
    }

    private function getPack($maxId = null)
    {
        $getfield['user_id'] = $this->getUserId();
        if ($maxId !== null) {
            $getfield['max_id'] = $maxId;
        }
        $jsonData = $this->getClient()
            ->setGetfield('?' . http_build_query($getfield))
            ->buildOauth('https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET')
            ->performRequest()
        ;
        return json_decode($jsonData, true);
    }

    private function getUserId()
    {
        return explode('-', $this->clientOptions['oauth_access_token'])[0];
    }
}