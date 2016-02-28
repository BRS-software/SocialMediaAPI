<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brs\SocialMediaAPI\API;

use Brs\SocialMediaAPI\Model\Post;
use Brs\Stdlib\Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Brs\SocialMediaAPI\Exception;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 */
class Instagram extends AbstractAPI
{
    protected function configureClientOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['client_id', 'client_secret', 'user_id', 'access_token'])
            ->setAllowedTypes('client_id', 'string')
            ->setAllowedTypes('client_secret', 'string')
            ->setAllowedTypes('user_id', ['string', 'integer'])
            ->setAllowedTypes('access_token', 'string')
        ;
    }

    public function getPostsRaw($fromDate)
    {
        $fromTs = strtotime($fromDate);
        $pack = 1;
        $result = [];

        $url = sprintf('https://api.instagram.com/v1/users/%s/media/recent/?access_token=%s', $this->clientOptions['user_id'], $this->clientOptions['access_token']);

        while (true) {
            $this->printLog('Getting data from instagram API, pack %d: ', $pack++);
            $data = file_get_contents($url);
            $packData = json_decode($data, true);
            $this->printLog("OK\n");

            foreach ($packData['data'] as $k => $v) {
                if ($v['created_time'] >= $fromTs) {
                    $result[] = $v;
                } else {
                    break 2;
                }
            }

            if (empty($packData['pagination']['next_url'])) {
                break;
            }

            $url = $packData['pagination']['next_url'];
        }
        return $result;
    }

    protected function createPost($rawData)
    {
        $post = new Post();
        $post
            ->setRawData($rawData)
            ->setId($rawData['id'])
            ->setContent($rawData['caption']['text'])
            ->setDate(date('Y-m-d H:i:s', $rawData['created_time']))
            ->setUserName($rawData['user']['username'])
            ->setPostUrl($rawData['link'])
            ->setImgUrl($rawData['images']['standard_resolution']['url'])
        ;
        return $post;
    }

    protected function createClient()
    {
        throw new Exception('Instagram client does not exists');
    }
}