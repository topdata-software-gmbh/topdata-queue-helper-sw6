<?php

namespace Topdata\TopdataQueueHelperSW6\Service;


use Doctrine\DBAL\Connection;
use Topdata\TopdataQueueHelperSW6\Util\UtilDate;

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


    /**
     * private helper
     *
     * 06/2024 created
     */
    private function _isZombie(mixed $row)
    {
        if($row['status'] != 'running') {
            return false;
        }
        $lastExecutionUTC = UtilDate::dateTimeFromString($row['last_execution_time']);
        $nextExecutionUTC = UtilDate::dateTimeFromString($row['next_execution_time']);
        $runIntervalSeconds = (int)$row['run_interval'];

        dump($row, $lastExecutionUTC, $nextExecutionUTC, $runIntervalSeconds);

        return true;
    }



    /**
     * 06/2024 created
     */
    public function findZombies(): array
    {
        $rows = $this->databaseHelperService->fetchRows('scheduled_task', ['created_at', 'updated_at'], ['scheduled_task_class' => 'ASC']);

        $zombies = [];
        foreach ($rows as $row) {

            if ($this->_isZombie($row)) {
                $zombies[] = $row;
            }
        }

        return $zombies;
    }

}