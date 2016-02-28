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
class Youtube extends AbstractAPI
{
    protected function configureClientOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setRequired(['user_name'])
            ->setAllowedTypes('user_name', 'string')
        ;
    }

    public function getPostsRaw($fromDate)
    {
        $fromTs = strtotime($fromDate);
        $pack = 1;
        $result = [];

        while (true) {
            $this->printLog('Getting data from youtube, pack %d: ', $pack++);
            $xmlString = file_get_contents('https://www.youtube.com/feeds/videos.xml?user=vivitarofficial');
            $this->printLog("OK\n");

            $xmlString = str_replace(['yt:', 'media:'], '', $xmlString);
            $xml = simplexml_load_string($xmlString);
            $json = json_encode($xml);
            $packData = json_decode($json, true);
            // dbgd($packData);

            foreach ($packData['entry'] as $k => $v) {
                $postDateTs = strtotime($v['published']);
                if ($postDateTs >= $fromTs) {
                    $result[] = $v;
                } else {
                    break 2;
                }
            }
            break; // not supported older posts for this moment
        }
        return $result;
    }

    protected function createPost($rawData)
    {
        $post = new Post();
        $post
            ->setRawData($rawData)
            ->setId($rawData['id'])
            ->setContent($rawData['group']['description'])
            ->setDate(date('Y-m-d H:i:s', strtotime($rawData['published'])))
            ->setUserName($rawData['author']['name'])
            ->setPostUrl($rawData['link']['@attributes']['href'])
            ->setImgUrl(sprintf('http://img.youtube.com/vi/%s/0.jpg', $rawData['videoId']))
        ;
        return $post;
    }

    protected function createClient()
    {
        throw new Exception('Youtube client does not exists');
    }
}