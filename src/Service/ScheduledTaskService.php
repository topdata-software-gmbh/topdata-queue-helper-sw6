<?php

namespace Topdata\TopdataQueueHelperSW6\Service;


use Doctrine\DBAL\Connection;

/**
 * 06/2024 created
 */
class ScheduledTaskService
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
     */
    public function getScheduledTasks(?string $search = null): array
    {
        $rows = $this->fetchRows('scheduled_task', ['id', 'created_at', 'updated_at'], ['scheduled_task_class' => 'ASC']);

        return $this->_filterRows($rows, $search);
    }


}