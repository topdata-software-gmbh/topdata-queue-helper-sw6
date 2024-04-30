<?php declare(strict_types=1);

namespace Topdata\TopdataQueueHelperSW6\Command;

use ErrorException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Topdata\TopdataQueueHelperSW6\Helper\CliStyle;
use Topdata\TopdataQueueHelperSW6\Service\QueueService;

/**
 * 04/2024 created
 */
class EnqueueListCommand extends Command
{
    protected static $defaultName = 'topdata:queue-helper:enqueue:list';
    protected static $defaultDescription = 'Print list of enqueues';

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
        $this->addOption('search', 's', InputOption::VALUE_REQUIRED, 'filter the rows by this search term');
        $this->addOption('count-only', 'c', InputOption::VALUE_NONE, 'count only the rows');
    }

    /**
     * ==== MAIN ====
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // ---- init ----
        $this->cliStyle = new CliStyle($input, $output);
        $search = $input->getOption('search');
        $bCountOnly = (bool)$input->getOption('count-only');

        // ---- main

        if($bCountOnly) {
            $count = $this->queueService->countEnqueue();
            $this->cliStyle->writeln("Number of rows in tbl enqueue: <info>$count</info>");
        } else {
            $rows = $this->queueService->getEnqueue($search);
//        foreach($rows as &$row) {
//            echo "\n\n" . $row['body'] . "\n\n";
//            $row['body'] = unserialize($row['body'], ['allowed_classes' => true]);
//        }
            $this->cliStyle->listOfDictsAsTable($rows, "Enqueue");
        }

        $this->cliStyle->success("==== DONE ====");

        return Command::SUCCESS;
    }


}

