<?php

/**
 * (c) BRS software - Tomasz Borys <t.borys@brs-software.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Brs\SocialMediaAPI;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;

/**
 * @author Tomasz Borys <t.borys@brs-software.pl>
 */
class Db
{
    private $importName;
    private $options;
    private $conn;
    private $entityManager;

    public function __construct(array $options)
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([

        ]);
        $this->options = $resolver->resolve($options);

        $config = new \Doctrine\DBAL\Configuration();
        $connectionParams = array(
            'driver' => 'pdo_sqlite',
            'path' => 'social-media-api.db'
        );
        $this->conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        $this->makeDbStructure();
    }

    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $isDevMode = true;
            $config = Setup::createAnnotationMetadataConfiguration(array(__DIR__."/Model"), $isDevMode);
            $this->entityManager = EntityManager::create($this->conn, $config);
        }
        return $this->entityManager;
    }

    private function makeDbStructure()
    {
        $tablesExists = $this->conn->executeQuery('SELECT COUNT(*) FROM sqlite_master')->fetchColumn();
        if ($tablesExists < 1) {
            $this->conn->executeQuery("
                CREATE TABLE IF NOT EXISTS logs (
                    id integer primary key,
                    status varchar(16) not null,
                    errorMessage text,
                    apiName varchar(100) not null,
                    importName varchar(100) not null,
                    date datetime default current_timestamp,
                    postId varchar(100) not null,
                    localId varchar(100),
                    UNIQUE (status, apiName, importName, localId),
                    UNIQUE (status, errorMessage, apiName, importName, postId) ON CONFLICT REPLACE
            )")->fetchAll();
        }
    }
}