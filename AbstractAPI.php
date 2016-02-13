<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brs\SocialMediaAPI;

use Closure;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 */
abstract class AbstractAPI implements APIInterface
{
    const DEFAULT_IMPORT_NAME = 'default';

    protected $options;
    private $client;
    private $db;

    abstract public function getLastPostsRaw();
    abstract protected function createPost(array $rawData);
    abstract protected function createClient();

    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    public function getApiName()
    {
        $classNameWithNamespace = get_class($this);
        return substr($classNameWithNamespace, strrpos($classNameWithNamespace, '\\')+1);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'importName' => self::DEFAULT_IMPORT_NAME,
            'verbose' => true,
            'clientOptions' => [],
            'db' => [],
        ));
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
        $logRepo = $this->getDb()->getEntityManager()->getRepository('Brs\SocialMediaAPI\Model\Log');

        foreach ($this->getLastPostsRaw() as $v) {
            $post = $this->createPost($v);
            $imported = $logRepo->wasImportedWithSuccess($this->getApiName(), $this->options['importName'], $post->getId());
            if (! $imported) {
                try {
                    $localId = $importPostFn($post);
                    if (empty($localId)) {
                        throw new Exception('Local ID is empty. Maybe you forgotten return this value from the import closure?');
                    }
                    $logRepo->addSuccessImport($this->getApiName(), $this->options['importName'], $post->getId(), $localId);
                    $this->printLog('OK - Post %s has been imported with success under local ID %s', $post->getId(), $localId);

                } catch (\Exception $e) {
                    $logRepo->addFailImport($this->getApiName(), $this->options['importName'], $post->getId(), $e->getMessage());
                    $this->printLog('ERROR - Import post %s fail: %s', $post->getId(), $e->getMessage());
                }
            } else {
                $this->printLog('OK - Post %s was already imported to local ID %s', $post->getId(), $imported->getLocalId());
            }
        }
    }

    protected function printLog($msg)
    {
        if ($this->options['verbose']) {
            call_user_func_array('printf', func_get_args());
            print "\n";
        }
    }
}