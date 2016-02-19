<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brs\SocialMediaAPI\API;

use Closure;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Brs\SocialMediaAPI\Db;
use Brs\SocialMediaAPI\Model\Post;
use Brs\SocialMediaAPI\Model\Param;
use Brs\SocialMediaAPI\Exception;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 */
abstract class AbstractAPI implements APIInterface
{
    const DEFAULT_IMPORT_NAME = 'default';
    const PARAM_IMPORT_FROM_DATE = 'importFromDate';

    protected $options;
    protected $clientOptions;
    private $client;
    private $db;

    abstract public function getPostsRaw($fromDate);
    abstract protected function createPost($rawData);
    abstract protected function createClient();
    abstract protected function configureClientOptions(OptionsResolver $resolver);

    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);

        $clientOptionsResolver = new OptionsResolver();
        $this->configureClientOptions($clientOptionsResolver);
        try {
            $this->options['clientOptions'] = $clientOptionsResolver->resolve($options['clientOptions']);

        } catch (MissingOptionsException $e) {
            throw new Exception(sprintf(
                'The %s client has a problem with options: %s', $this->getApiName(), $e->getMessage()
            ));
        }
        $this->clientOptions = $this->options['clientOptions']; // short way to the client options
    }

    final public function getApiName()
    {
        $classNameWithNamespace = get_class($this);
        return substr($classNameWithNamespace, strrpos($classNameWithNamespace, '\\')+1);
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'importName' => self::DEFAULT_IMPORT_NAME,
                'verbose' => false,
                'clientOptions' => [],
                'db' => [],
            ])
            ->setRequired([
                'importName',
                'clientOptions',
                'db',
            ])
            ->setAllowedTypes('importName', 'string')
            ->setAllowedTypes('verbose', 'boolean')
            ->setAllowedTypes('clientOptions', 'array')
            ->setAllowedTypes('db', 'array')
        ;
    }

    public function getClient()
    {
        if (null === $this->client) {
            $this->client = $this->createClient();
        }
        return $this->client;
    }

    private function getDb()
    {
        if (null === $this->db) {
            $this->db = new Db($this->options['db']);
        }
        return $this->db;
    }

    public function importPosts(Closure $importPostFn, array $options = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults(array(
            'fromDate' => $this->getParam(self::PARAM_IMPORT_FROM_DATE, date('Y-m-d', time() - 86400)), // default from last day
        ));
        $options = $resolver->resolve($options);

        $logRepo = $this->getDb()->getEntityManager()->getRepository('Brs\SocialMediaAPI\Model\Log');
        $rawPosts = $this->getPostsRaw($options['fromDate']);
        $youngestImportedPost = null;

        foreach ($rawPosts as $i => $v) {
            $post = $this->createPost($v);
            if (! ($post instanceof Post)) {
                throw new Exception(
                    sprintf('Unexpected class of object returned from %s::createPost(). Expectation Brs\SocialMediaAPI\Model\Post', self::class)
                );
            }
            $imported = $logRepo->wasImportedWithSuccess($this->getApiName(), $this->options['importName'], $post->getId());
            if (! $imported) {
                try {
                    $localId = $importPostFn($post, $i, $this);
                    if (empty($localId)) {
                        throw new Exception('Local ID is empty. Maybe you forgotten return this value from the import closure?');
                    }
                    $postTs = strtotime($post->getDate());
                    if (false === $postTs) {
                        throw new Exception('Date of post "%s" is invalid', $post->getDate());
                    }
                    if (empty($youngestImportedPost) || strtotime($youngestImportedPost->getDate()) < $postTs) {
                        $youngestImportedPost = $post;
                    }

                    $logRepo->addSuccessImport($this->getApiName(), $this->options['importName'], $post->getId(), $localId);
                    $this->printLog("OK - Post %s has been imported with success under local ID %s\n", $post->getId(), $localId);

                } catch (\Exception $e) {
                    $logRepo->addFailImport($this->getApiName(), $this->options['importName'], $post->getId(), $e->getMessage());
                    $this->printLog("ERROR - Import post %s fail: %s\n", $post->getId(), $e->getMessage());
                }
            } else {
                $this->printLog("OK - Post %s was already imported to local ID %s\n", $post->getId(), $imported->getLocalId());
            }
        }

        if (!empty($youngestImportedPost)) {
            $this->setParam(self::PARAM_IMPORT_FROM_DATE, $youngestImportedPost->getDate());
        }
    }

    protected function printLog($msg)
    {
        if ($this->options['verbose']) {
            call_user_func_array('printf', func_get_args());
            flush(); ob_flush();
        }
    }

    protected function getParam($key, $default = null)
    {
        $val = $this->getParamEntity($key);
        return $val !== null ? $val->getValue() : $default;
    }

    protected function setParam($key, $value)
    {
        $val = $this->getParamEntity($key);
        if ($val === null) {
            $val = new Param();
            $val
                ->setApiName($this->getApiName())
                ->setImportName($this->options['importName'])
                ->setKey($key)
            ;
        }
        $val->setValue($value);
        $em = $this->getDb()->getEntityManager();
        $em->persist($val);
        $em->flush();
        return $this;
    }

    private function getParamEntity($key)
    {
        $paramRepo = $this->getDb()->getEntityManager()->getRepository('Brs\SocialMediaAPI\Model\Param');
        return $paramRepo->findOneBy([
            'apiName' => $this->getApiName(),
            'importName' => $this->options['importName'],
            'key' => $key,
        ]);
    }
}
