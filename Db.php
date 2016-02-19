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
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Configuration;

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
            'driver' => 'pdo_sqlite',
            'path' => getcwd(),
        ]);
        $this->options = $resolver->resolve($options);

        $this->conn = DriverManager::getConnection([
            'driver' => $this->options['driver'],
            'path' => $this->options['path'] . '/social-media-api.db'
        ], new Configuration());

        $this->makeDbStructure();
    }

    public function getEntityManager()
    {
        if (null === $this->entityManager) {
            $isDevMode = true;
            $config = Setup::createAnnotationMetadataConfiguration([__DIR__ . '/Model'], $isDevMode);
            $this->entityManager = EntityManager::create($this->conn, $config);
        }
        return $this->entityManager;
    }

    private function makeDbStructure()
    {
        $tablesExists = $this->conn->executeQuery('SELECT COUNT(*) FROM sqlite_master')->fetchColumn();
        if ($tablesExists < 1) {
            $this->conn->executeQuery("
                CREATE TABLE IF NOT EXISTS social_media_api_logs (
                    id integer primary key,
                    status varchar(16) not null,
                    errorMessage text,
                    apiName varchar(64) not null,
                    importName varchar(64) not null,
                    date datetime default current_timestamp,
                    externalId varchar(100) not null,
                    localId varchar(100),
                    UNIQUE (status, apiName, importName, localId),
                    UNIQUE (status, errorMessage, apiName, importName, externalId) ON CONFLICT REPLACE
            )")->fetchAll();

            $this->conn->executeQuery("
                CREATE TABLE IF NOT EXISTS social_media_api_params (
                    id integer primary key,
                    apiName varchar(64) not null,
                    importName varchar(64) not null,
                    key varchar(32) not null,
                    value varchar(256) not null,
                    UNIQUE (apiName, importName, key)
            )")->fetchAll();
        }
    }
}