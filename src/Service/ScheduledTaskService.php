<?php

namespace Topdata\TopdataQueueHelperSW6\Service;


use Doctrine\DBAL\Connection;

/**
 * 06/2024 created
 */
class ScheduledTaskService
{
    private Connection $connection;
    private DatabaseHelperService $databaseHelperService;

    public function __construct(
        Connection $connection,
        DatabaseHelperService $databaseHelperService,
    )
    {
        $this->connection = $connection;
        $this->databaseHelperService = $databaseHelperService;
    }

    /**
     * 04/2024 created
     * 06/2024 moved from QueueService to ScheduledTaskService
     */
    public function getScheduledTasks(?string $search = null): array
    {
        $rows = $this->databaseHelperService->fetchRows('scheduled_task', ['id', 'created_at', 'updated_at'], ['scheduled_task_class' => 'ASC']);

        return $this->databaseHelperService->_filterRows($rows, $search);
    }


    /**
     * set status of all scheduled tasks with status="queues" to "scheduled"
     *
     * 04/2024 created
     * 06/2024 moved from QueueService to ScheduledTaskService
     *
     * @return int number of updated rows
     */
    public function updateScheduledTasksStatusFromQueueToScheduled(): int
    {
        return (int)$this->connection->executeStatement("
            UPDATE scheduled_task
            SET status = 'scheduled'
            WHERE status = 'queued'
        ");
    }


}