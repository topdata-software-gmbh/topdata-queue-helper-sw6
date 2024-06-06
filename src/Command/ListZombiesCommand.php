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
class ListZombiesCommand extends Command
{
    protected static $defaultName = 'topdata:queue-helper:list-zombies';
    protected static $defaultDescription = 'list zombie jobs';

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

        $this->cliStyle->success("==== DONE ====");

        return Command::SUCCESS;
    }

    /**
     * prints the zombies in scheduled_task as a table
     *
     * 06/2024 created
     */

}

