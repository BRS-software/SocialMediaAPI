<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brs\SocialMediaAPI\Model;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 */
class Post
{
    protected $id;
    protected $title;
    protected $content;
    protected $date;
    protected $userName;
    protected $rawData;
    protected $postUrl;
    protected $imgUrl;

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setDate($date)
    {
        $this->date = date('Y-m-d H:i:s', strtotime($date));
        return $this;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setUserName($userName)
    {
        $this->userName = $userName;
        return $this;
    }

    public function getUserName()
    {
        return $this->userName;
    }

    public function setRawData(array $rawData)
    {
        $this->rawData = $rawData;
        return $this;
    }

    public function getRawData()
    {
        return $this->rawData;
    }

    public function setPostUrl($postUrl)
    {
        $this->postUrl = $postUrl;
        return $this;
    }

    public function getPostUrl()
    {
        return $this->postUrl;
    }

    public function setImgUrl($imgUrl)
    {
        $this->imgUrl = $imgUrl;
        return $this;
    }

    public function getImgUrl()
    {
        return $this->imgUrl;
    }
}