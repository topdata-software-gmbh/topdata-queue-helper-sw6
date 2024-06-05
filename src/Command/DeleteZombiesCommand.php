<?php declare(strict_types=1);

namespace Topdata\TopdataQueueHelperSW6\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Topdata\TopdataQueueHelperSW6\Helper\CliStyle;
use Topdata\TopdataQueueHelperSW6\Service\QueueService;

/**
 * aka "topdata:queue-helper:delete-dead-running-jobs"
 *
 * 06/2024 created
 */
class DeleteZombiesCommand extends Command
{
    protected static $defaultName = 'topdata:queue-helper:delete-zombies';
    protected static $defaultDescription = 'detects zombie jobs (jobs with status="running" but started long time ago) and deletes them (after confirmation)';

    protected CliStyle $cliStyle;
    private QueueService $queueService;



    public function __construct(
        ScheduleTaskService $scheduleTaskService
    )
    {
        parent::__construct();
        $this->scheduleTaskService = $scheduleTaskService;
    }


    protected function configure(): void
    {
    }

    /**
     * ==== MAIN ====
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // ---- init ----
        $this->cliStyle = new CliStyle($input, $output);

        // ---- set status of all scheduled tasks with status="queues" to "scheduled"
        $numUpdated = $this->queueService->updateScheduledTasksStatusFromQueueToScheduled();
        $this->cliStyle->info("Updated $numUpdated rows in scheduled_task from status='queued' to status='scheduled'");

        $this->cliStyle->success("==== DONE ====");

        return Command::SUCCESS;
    }


}

