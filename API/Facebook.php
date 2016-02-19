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

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 */
class Facebook extends AbstractAPI
{
    protected function configureClientOptions(OptionsResolver $resolver)
    {
        $resolver
            // ->setDefaults([])
            ->setRequired(['app_id', 'app_secret', 'access_token', 'profile_id'])
            ->setAllowedTypes('app_id', ['string', 'integer'])
            ->setAllowedTypes('profile_id', ['string', 'integer'])
            ->setAllowedTypes('app_secret', 'string')
            ->setAllowedTypes('access_token', 'string')
        ;
    }

    public function getPostsRaw($fromDate)
    {
        $fromTs = strtotime($fromDate);
        $pack = 1;
        $untilTs = time();
        $result = [];

        while (true) {
            $this->printLog('Getting data from facebook API, pack %d: ', $pack++);
            $packData = $this->getPack($untilTs);
            $this->printLog("OK\n");

            foreach ($packData as $k => $v) {
                $postDate = $v->getField('created_time')->format('Y-m-d H:i:s');
                $postDateTs = strtotime($postDate);
                if ($postDateTs >= $fromTs) {
                    $result[] = $v;
                } else {
                    break 2;
                }
                // $this->printLog("%02d. %s - %s\n", $k, $postDate, substr($v->getField('message'), 0, 25));
            }

            $untilTs = $postDateTs;
        }
        return $result;
    }

    protected function createPost($rawData)
    {
        $post = new Post();
        $post
            ->setRawData($rawData->asArray())
            ->setId($rawData->getField('id'))
            ->setContent($rawData->getField('message'))
            ->setDate($rawData->getField('created_time')->format('Y-m-d H:i:s'))
            ->setUserName($rawData->getField('admin_creator')->getField('name'))
            ->setPostUrl(sprintf('https://www.facebook.com/%s/posts/%s', $this->clientOptions['profile_id'], explode('_', $rawData->getField('id'))[1]))
            //->setPostUrl($rawData->getField('link'))
            ->setImgUrl($rawData->getField('full_picture'))
        ;
        return $post;
    }

    protected function createClient()
    {
        return new \Facebook\Facebook([
            'default_graph_version' => 'v2.5',
            'app_id' => $this->clientOptions['app_id'],
            'app_secret' => $this->clientOptions['app_secret'],
            'default_access_token' => $this->clientOptions['access_token'],
        ]);
    }

    private function getPack($untilTs)
    {
        // selecting fields to the response
        // https://developers.facebook.com/tools/explorer
        $query['fields'] = 'full_picture,message,created_time,is_published,admin_creator,link';
        $query['until'] = $untilTs;
        $query['limit'] = 25;

        // $response = $fb->get($this->clientOptions['profile_id'] . '/posts');
        $response = $this->getClient()->get($this->clientOptions['profile_id'] . '/posts?' . http_build_query($query));
        return $response->getGraphEdge();
    }
}
