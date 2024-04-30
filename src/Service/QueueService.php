<?php

namespace Topdata\TopdataQueueHelperSW6\Service;


use Doctrine\DBAL\Connection;

/**
 * 04/2024 created
 */
class QueueService
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
     */
    public function getScheduledTasks(?string $search = null): array
    {
        $rows = $this->fetchRows('scheduled_task', ['id', 'created_at', 'updated_at'], ['scheduled_task_class' => 'ASC']);

        return $this->_filterRows($rows, $search);
    }


//
//    /**
//     * TODO
//     * 04/2024 created
//     */
//    public function queueSingleTask()
//    {
//
//        $criteria = $this->buildCriteriaForAllScheduledTask();
//        $context = Context::createDefaultContext();
//        $tasks = $this->scheduledTaskRepository->search($criteria, $context)->getEntities();
//
//        if (\count($tasks) === 0) {
//            return;
//        }
//
//        // Tasks **must not** be queued before their state in the database has been updated. Otherwise,
//        // a worker could have already fetched the task and set its state to running before it gets set to
//        // queued, thus breaking the task.
//        /** @var ScheduledTaskEntity $task */
//        foreach ($tasks as $task) {
//            $this->scheduledTaskRepository->update([
//                [
//                    'id'     => $task->getId(),
//                    'status' => ScheduledTaskDefinition::STATUS_QUEUED,
//                ],
//            ], $context);
//            $this->queueTask($task);
//        }
//    }


    /**
     * 04/2024 created
     */
    public function getMessageQueueStats(): array
    {
        return $this->fetchRows('message_queue_stats', ['id', 'created_at', 'updated_at']);
    }

    /**
     * 04/2024 created
     */
    public function getEnqueue(?string $search = null): array
    {
        return $this->_filterRows($this->fetchRows('enqueue', ['id', 'created_at', 'updated_at']), $search);
    }

    /**
     * 04/2024 created
     */
    private function getTableColumnNamesExcept(string $tableName, array $excludedColumns = []): array
    {
        $keys = array_keys($this->connection->getSchemaManager()->listTableColumns($tableName));

        return array_diff($keys, $excludedColumns);
    }

    /**
     * 04/2024 created
     *
     * @param string $tbl eg 'message_queue_stats'
     * @param string[] $exclude eg ['id', 'created_at', 'updated_at']
     * @param array $sort eg ['scheduled_task_class' => 'ASC', 'id' => 'ASC']
     */
    private function fetchRows(string $tbl, array $exclude, array $sort = ['id' => 'ASC']): array
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
     * private helper
     *
     * 04/2024 created
     */
    private function _filterRows(array $rows, ?string $search): array
    {
        $ret = [];
        foreach ($rows as $row) {
            if ($search === null || stripos(json_encode($row), $search) !== false) {
                $ret[] = $row;
            }
        }

        return $ret;
    }

    public function countEnqueue(): int
    {
        $sql = "SELECT COUNT(*) FROM enqueue";

        return (int)$this->connection->executeQuery($sql)->fetchNumeric()[0];
    }

    public function deleteFromEnqueue(): int
    {
        return (int)$this->connection->executeStatement("DELETE FROM enqueue");
    }

    /**
     * set status of all scheduled tasks with status="queues" to "scheduled"
     *
     * 04/2024 created
     * @return int number of updated rows
     */
    public function updateScheduledTasksStatusFromQueueToScheduled(): int
    {
        return (int) $this->connection->executeStatement("
            UPDATE scheduled_task
            SET status = 'scheduled'
            WHERE status = 'queued'
        ");
    }

}