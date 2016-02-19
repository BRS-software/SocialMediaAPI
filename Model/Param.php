<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brs\SocialMediaAPI\Model;

/**
 * @Table(name="social_media_api_params")
 * @Entity
 * @author Tomasz Borys (tobo) <t.borys@brs-software.pl>
 */
class Param
{
    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /** @Column(type="string") **/
    protected $apiName;

    /** @Column(type="string") **/
    protected $importName;

    /** @Column(type="string") **/
    protected $key;

    /** @Column(type="string") **/
    protected $value;

    public function setApiName($name)
    {
        $this->apiName = $name;
        return $this;
    }

    public function setImportName($importName)
    {
        $this->importName = $importName;
        return $this;
    }

    public function getImportName()
    {
        return $this->importName;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }
}