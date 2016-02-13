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
    protected $text;
    protected $rawData;

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

    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    public function getText()
    {
        return $this->text;
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
}