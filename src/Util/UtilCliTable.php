<?php
namespace Topdata\TopdataQueueHelperSW6\Util;

use Topdata\TopdataQueueHelperSW6\Helper\CliStyle;

/**
 * 06/2024 created
 */
class UtilCliTable
{

    public static function printZombiesTable(CliStyle $cliStyle, array $rows): void
    {
        if(count($rows) === 0) {
            $cliStyle->info("No zombies found");

            return;
        }

        // Prepare table headers and rows
        $headers = [
            // 'ID',
            'Name',
            'Task Class',
            'Run Interval',
            'Status',
            'Last Execution Time',
            'Next Execution Time',
//            'Created At',
//            'Updated At',
//            'Overdue Score',
//            'Last Exec Score',
//            'Outdated Score',
            'Certainty Score'
        ];
        $tableRows = [];

        foreach ($rows as $row) {
            $tableRows[] = [
                // $row['id'],
                $row['name'],
                $row['scheduled_task_class'],
                $row['run_interval'],
                $row['status'],
                $row['last_execution_time'],
                $row['next_execution_time'],
//                $row['created_at'],
//                $row['updated_at'],
                $row['overdue_score'] . '+' . $row['last_exec_score'] . '+' . $row['outdated_score'] . ' = ' . $row['certainty_score']
            ];
        }

        $cliStyle->table($headers, $tableRows, "Zombie Jobs", "Total: " . count($rows));
    }


}