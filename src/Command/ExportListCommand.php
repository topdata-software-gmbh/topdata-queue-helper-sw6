<?php declare(strict_types=1);

namespace Topdata\TopdataQueueHelperSW6\Command;

use ErrorException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Topdata\TopdataQueueHelperSW6\Helper\CliStyle;
use Topdata\TopdataQueueHelperSW6\Service\ExportHelperService;
use Topdata\TopdataQueueHelperSW6\Service\QueueService;
use Topdata\TopdataQueueHelperSW6\Util\UtilDebug;

/**
 * 04/2024 created
 */
class ExportListCommand extends Command
{
    protected static $defaultName = 'topdata:queue-helper:export:list';
    protected static $defaultDescription = 'Print list of exports (sales channel of type "product comparison")';

    private CliStyle $cliStyle;

    public function __construct(
        private QueueService        $queueService,
        private ExportHelperService $exportHelperService,
    )
    {
        parent::__construct();
    }


    protected function configure(): void
    {
        $this->addOption('search', 's', InputOption::VALUE_REQUIRED, 'filter the rows by this search term');
        $this->addOption('all', 'a', InputOption::VALUE_NONE, 'show all exports, not just the active ones');
    }

    /**
     * ==== MAIN ====
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        // ---- init ----
        $this->cliStyle = new CliStyle($input, $output);
        $search = $input->getOption('search');
        $bAll = (bool)$input->getOption('all');

        // ---- main
        $rows = $this->exportHelperService->getExports(bAll: $bAll);
        // UtilDebug::dd($rows);
        $this->cliStyle->listOfDictsAsTable($rows, 'Exports');

        $this->cliStyle->success("==== DONE ====");

        return Command::SUCCESS;
    }


}

