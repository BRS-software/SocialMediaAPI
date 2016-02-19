<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brs\SocialMediaAPI\Model;

/**
 * @Table(name="social_media_api_logs")
 * @Entity(repositoryClass="Brs\SocialMediaAPI\Model\LogRepository")
 * @author Tomasz Borys (tobo) <t.borys@brs-software.pl>
 */
class Log
{
    const STATUS_SUCCESS = 'success';
    const STATUS_FAIL = 'fail';

    /** @Id @Column(type="integer") @GeneratedValue **/
    protected $id;

    /** @Column(type="string") **/
    protected $status;

    /** @Column(type="string") **/
    protected $errorMessage;

    /** @Column(type="string") **/
    protected $apiName;

    /** @Column(type="string") **/
    protected $importName;

    /** @Column(type="string") **/
    protected $date;

    /** @Column(type="string") **/
    protected $externalId;

    /** @Column(type="string") **/
    protected $localId;

    public function __construct()
    {
        $this->date = date('Y-m-d H:i:s');
    }

    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }

    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

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

    public function setExternalId($externalId)
    {
        $this->externalId = $externalId;
        return $this;
    }

    public function getExternalId()
    {
        return $this->externalId;
    }

    public function setLocalId($localId)
    {
        $this->localId = $localId;
        return $this;
    }

    public function getLocalId()
    {
        return $this->localId;
    }
}