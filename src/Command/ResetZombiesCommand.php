<?php declare(strict_types=1);

namespace Topdata\TopdataQueueHelperSW6\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Topdata\TopdataQueueHelperSW6\Helper\CliStyle;
use Topdata\TopdataQueueHelperSW6\Service\QueueService;
use Topdata\TopdataQueueHelperSW6\Service\ScheduledTaskService;
use Topdata\TopdataQueueHelperSW6\Util\UtilCliTable;

/**
 * aka "topdata:queue-helper:delete-dead-running-jobs"
 *
 * 06/2024 created
 */
class ResetZombiesCommand extends Command
{
    protected static $defaultName = 'topdata:queue-helper:reset-zombies';
    protected static $defaultDescription = 'detects zombie jobs (jobs with status="running" but started long time ago) and resets the status to "scheduled" (after confirmation)';

    protected CliStyle $cliStyle;
    private ScheduledTaskService $scheduledTaskService;


    public function __construct(
        ScheduledTaskService $scheduledTaskService
    )
    {
        parent::__construct();
        $this->scheduledTaskService = $scheduledTaskService;
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

        $zombies = $this->scheduledTaskService->findZombies();
        UtilCliTable::printZombiesTable($this->cliStyle, $zombies);

        // ---- nothing to do
        if(count($zombies) === 0) {
            $this->cliStyle->success("No zombies found");

            return Command::SUCCESS;
        }

        // ---- ask for confirmation and do it!
        if($this->cliStyle->confirm("Do you want to reset these zombies?")) {
            $this->scheduledTaskService->deleteZombies($zombies);
            $this->cliStyle->success("Zombies deleted");
        } else {
            $this->cliStyle->success("Zombies not deleted");
        }

        $this->cliStyle->success("==== DONE ====");

        return Command::SUCCESS;
    }

    /**
     * prints the zombies in scheduled_task as a table
     *
     * 06/2024 created
     */


}

