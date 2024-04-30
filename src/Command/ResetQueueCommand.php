<?php declare(strict_types=1);

namespace Topdata\TopdataQueueHelperSW6\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Topdata\TopdataQueueHelperSW6\Helper\CliStyle;
use Topdata\TopdataQueueHelperSW6\Service\QueueService;

/**
 * aka "topdata:queue-helper:reset-queue"
 *
 * 04/2024 created
 */
class ResetQueueCommand extends Command
{
    protected static $defaultName = 'topdata:queue-helper:reset-queue';
    protected static $defaultDescription = 'truncate tbl enqueue, also sets status of all scheduled tasks with status="queues" to "scheduled"';

    protected CliStyle $cliStyle;
    private QueueService $queueService;

    public function __construct(
        QueueService $queueService
    )
    {
        parent::__construct();
        $this->queueService = $queueService;
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

        // ---- delete rows from enqueue
        $numDeleted = $this->queueService->deleteFromEnqueue();
        $this->cliStyle->info("Deleted $numDeleted rows from tbl enqueue");

        // ---- set status of all scheduled tasks with status="queues" to "scheduled"
        $numUpdated = $this->queueService->updateScheduledTasksStatusFromQueueToScheduled();
        $this->cliStyle->info("Updated $numUpdated rows in scheduled_task from status='queued' to status='scheduled'");

        $this->cliStyle->success("==== DONE ====");

        return Command::SUCCESS;
    }


}

