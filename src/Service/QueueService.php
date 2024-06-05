<?php

namespace Topdata\TopdataQueueHelperSW6\Service;


use Doctrine\DBAL\Connection;

/**
 * 04/2024 created
 */
class QueueService
{
    private Connection $connection;
    private DatabaseHelperService $databaseHelperService;

    public function __construct(
        Connection            $connection,
        DatabaseHelperService $databaseHelperService,
    )
    {
        $this->connection = $connection;
        $this->databaseHelperService = $databaseHelperService;
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
        return $this->databaseHelperService->fetchRows('message_queue_stats', ['id', 'created_at', 'updated_at']);
    }

    /**
     * 04/2024 created
     */
    public function getEnqueue(?string $search = null): array
    {
        return $this->databaseHelperService->_filterRows($this->databaseHelperService->fetchRows('enqueue', ['id', 'created_at', 'updated_at']), $search);
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

}