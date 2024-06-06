<?php declare(strict_types=1);

namespace Topdata\TopdataQueueHelperSW6\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Topdata\TopdataQueueHelperSW6\Helper\CliStyle;
use Topdata\TopdataQueueHelperSW6\Service\QueueService;
use Topdata\TopdataQueueHelperSW6\Service\ScheduledTaskService;

/**
 * 04/2024 created
 */
class ScheduledTaskListCommand extends Command
{
    protected static $defaultName = 'topdata:queue-helper:scheduled-task:list';
    protected static $defaultDescription = 'Print list of scheduled tasks';

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
        $this->addOption('search', 's', InputOption::VALUE_REQUIRED, 'filter the rows by this search term');
    }

    /**
     * ==== MAIN ====
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // ---- init ----
        $this->cliStyle = new CliStyle($input, $output);
        $search = $input->getOption('search');

        $this->cliStyle->listOfDictsAsTable($this->scheduledTaskService->getScheduledTasks($search), "Scheduled Tasks");

        $this->cliStyle->success("==== DONE ====");

        return Command::SUCCESS;
    }


}

