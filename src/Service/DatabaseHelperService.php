<?php

namespace Topdata\TopdataQueueHelperSW6\Service;


use Doctrine\DBAL\Connection;

/**
 * 06/2024 created
 */
class DatabaseHelperService
{
    private Connection $connection;

    public function __construct(
        Connection $connection
    )
    {
        $this->connection = $connection;
    }

    /**
     * 04/2024 created
     * 06/2024 moved from QueueService to ScheduledTaskService
     *
     * @return string[] the list of column names, filtered
     */
    private function getTableColumnNamesExcept(string $tableName, array $excludedColumns = []): array
    {
        $keys = array_keys($this->connection->getSchemaManager()->listTableColumns($tableName));

        return array_diff($keys, $excludedColumns);
    }


    /**
     * 04/2024 created
     * 06/2024 moved from QueueService to DatabaseHelperService
     *
     * @param string $tbl eg 'message_queue_stats'
     * @param string[] $exclude eg ['id', 'created_at', 'updated_at']
     * @param array $sort eg ['scheduled_task_class' => 'ASC', 'id' => 'ASC']
     */
    public function fetchRows(string $tbl, array $exclude, array $sort = ['id' => 'ASC']): array
    {
        $columnNames = $this->getTableColumnNamesExcept($tbl, $exclude);

        $arrOrderBy = [];
        foreach ($sort as $key => $value) {
            $arrOrderBy[] = "$key $value";
        }
        $sql = "SELECT " . implode(", ", $columnNames) . " FROM $tbl ORDER BY " . implode(", ", $arrOrderBy);

        return $this->connection->executeQuery($sql)->fetchAllAssociative();
    }


    /**
     * helper
     *
     * 04/2024 created
     * 04/2024 moved from QueueService to DatabaseHelperService
     */
    public static function _filterRows(array $rows, ?string $search): array
    {
        $ret = [];
        foreach ($rows as $row) {
            if ($search === null || stripos(json_encode($row), $search) !== false) {
                $ret[] = $row;
            }
        }

        return $ret;
    }

}