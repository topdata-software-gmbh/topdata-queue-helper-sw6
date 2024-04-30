<?php declare(strict_types=1);

namespace Topdata\TopdataQueueHelperSW6\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Topdata\TopdataQueueHelperSW6\Helper\CliStyle;
use Topdata\TopdataQueueHelperSW6\Service\QueueService;

/**
 * only for development
 *
 * 04/2024 created
 */
class DebugQueueCommand extends Command
{
    protected static $defaultName = 'topdata:queue-helper:debug-queue';
    protected static $defaultDescription = 'Print debug information about the queue';

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

        $this->cliStyle->listOfDictsAsTable($this->queueService->getMessageQueueStats(), "Message Queue Stats");
        // $this->cliStyle->listOfDictsAsTable($this->queueService->getEnqueue(), "Enqueue");
//        $this->cliStyle->listOfDictsAsTable($this->queueService->getScheduledTasks(), "Scheduled Tasks");


        $this->cliStyle->success("==== DONE ====");

        return Command::SUCCESS;
    }


}

