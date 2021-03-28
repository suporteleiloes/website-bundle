<?php


namespace SL\WebsiteBundle\Services;


use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManagerInterface;

class DatabaseOperationsService
{
    private $em;
    /** @var Connection */
    private $connection;

    const FOREIGN_KEY_CHECK_DISABLED = 0;
    const FOREIGN_KEY_CHECK_ENABLED = 1;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        $this->connection = $em->getConnection();
    }

    /**
     * @throws \Exception
     */
    public function clearAllTables () {
        try {
            $tables = $this->connection->getSchemaManager()->listTables();

            $this->setForeignKeyCheck(self::FOREIGN_KEY_CHECK_DISABLED);

            foreach ($tables as $table) {
                $this->truncateTable($table->getName());
            }

            $this->setForeignKeyCheck(self::FOREIGN_KEY_CHECK_ENABLED);

        } catch (\Exception | \Error | \Throwable $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * @param $tableName
     * @throws DBALException
     */
    private function truncateTable ($tableName) {

        $query = sprintf('TRUNCATE TABLE %s', $tableName);

        $this->connection->executeQuery($query, array(), array());
    }

    /**
     * @param $status
     * @throws DBALException
     */
    private function setForeignKeyCheck($status) {
        $query = sprintf("SET FOREIGN_KEY_CHECKS = $status;");

        $this->connection->executeQuery($query, array(), array());
    }
}