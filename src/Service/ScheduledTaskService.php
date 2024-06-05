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
     * explanation of the SQL query:
     *
     * status = 'running': Filters tasks that are currently running.
     * next_execution_time < NOW() - INTERVAL run_interval SECOND: Checks if the next_execution_time is overdue based on the run_interval.
     * last_execution_time > next_execution_time: Ensures last_execution_time is not greater than next_execution_time.
     * updated_at < NOW() - INTERVAL (run_interval * 2) SECOND: Checks if the updated_at is older than twice the run_interval. This is a heuristic to identify tasks that should have been updated recently but were not.
     *
     * You can adjust the multiplier (in this case, 2) to fit your specific needs.
     *
     * overdue_score: Adds 1 if next_execution_time is overdue.
     * last_exec_score: Adds 1 if last_execution_time is greater than next_execution_time.
     * outdated_score: Adds 1 if updated_at is outdated.
     * certainty_score: Sums the individual scores to get a total certainty score.
     *
     * 06/2024 created (ChatGPT)
     */
    public function findZombies(): array
    {
        $SQL = "
            SELECT
                LOWER(HEX(id)) AS id,
                name,
                scheduled_task_class,
                run_interval,
                status,
                last_execution_time,
                next_execution_time,
                created_at,
                updated_at,

           CASE
               WHEN next_execution_time < UTC_TIMESTAMP() - INTERVAL run_interval SECOND THEN 1 ELSE 0 END AS overdue_score,
           CASE
               WHEN last_execution_time > next_execution_time THEN 1 ELSE 0 END AS last_exec_score,
           CASE
               WHEN updated_at < UTC_TIMESTAMP() - INTERVAL (run_interval * 2) SECOND THEN 1 ELSE 0 END AS outdated_score,
           (
               CASE
                    WHEN next_execution_time < UTC_TIMESTAMP() - INTERVAL run_interval SECOND THEN 1 ELSE 0 END +
               CASE
                   WHEN last_execution_time > next_execution_time THEN 1 ELSE 0 END +
               CASE
                    WHEN updated_at < UTC_TIMESTAMP() - INTERVAL (run_interval * 2) SECOND THEN 1 ELSE 0 END
            ) AS certainty_score                
                
                
            FROM
                scheduled_task
            WHERE
                status = 'running'
                AND (
                    next_execution_time < UTC_TIMESTAMP() - INTERVAL run_interval SECOND
                    OR last_execution_time > next_execution_time
                    OR updated_at < UTC_TIMESTAMP() - INTERVAL (run_interval * 2) SECOND
                )
            ORDER BY scheduled_task_class ASC, id ASC
        ";

        $rows = $this->connection->executeQuery($SQL)->fetchAllAssociative();

        return $rows;
    }

}