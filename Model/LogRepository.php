<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brs\SocialMediaAPI\Model;

use Doctrine\ORM\EntityRepository;
// use Doctrine\ORM\Mapping as ORM;

/**
 * @author Tomasz Borys (tobo) <t.borys@brs-software.pl>
 */
class LogRepository extends EntityRepository
{
    public function wasImportedWithSuccess($apiName, $importName, $postId)
    {
        $result = $this->findOneBy([
            'status' => Log::STATUS_SUCCESS,
            'apiName' => $apiName,
            'importName' => $importName,
            'postId' => $postId,
        ]);
        return null !== $result ? $result : false;
    }

    public function addSuccessImport($apiName, $importName, $postId, $localId)
    {
        // $check = $this->findOneBy([
        //     'status' => Log::STATUS_SUCCESS,
        //     'apiName' => $apiName,
        //     'importName' => $importName,
        //     'localId' => $localId,
        // ]);
        // dbg($check);

        $log = new Log();
        $log
            ->setStatus(Log::STATUS_SUCCESS)
            ->setApiName($apiName)
            ->setImportName($importName)
            ->setPostId($postId)
            ->setLocalId($localId)
        ;
        $this->_em->persist($log);
        $this->_em->flush();
        return $log;
    }

    public function addFailImport($apiName, $importName, $postId, $errorMessage)
    {
        $log = new Log();
        $log
            ->setStatus(Log::STATUS_FAIL)
            ->setErrorMessage($errorMessage)
            ->setApiName($apiName)
            ->setImportName($importName)
            ->setPostId($postId)
        ;
        $this->_em->persist($log);
        $this->_em->flush();
        return $log;
    }
}