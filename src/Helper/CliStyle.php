<?php

declare(strict_types=1);

namespace Topdata\TopdataQueueHelperSW6\Helper;

use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Topdata\TopdataQueueHelperSW6\Util\AnsiColor;

/**
 * 05/2022 created (copied from art-experiments).
 *
 * @version 2023-08-15
 */
class CliStyle extends SymfonyStyle
{
    /**
     * wrapper to have an instance of input and output
     * 04/2022 created.
     */
    public function __construct(InputInterface $input, OutputInterface $output)
    {
        parent::__construct($input, $output);
    }

    /**
     * 04/2023 created
     *
     * @param mixed $val
     * @return false|string
     */
    private static function _nonScalarToStringForTable($val)
    {
        return json_encode($val, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }


    /**
     * same like SymfonyStyle's table but with optional headerTitle and footerTitle
     *
     * 07/2023 created.
     */
    public function table(array $headers, array $rows, ?string $headerTitle = null, ?string $footerTitle = null)
    {
        $tbl = $this->createTable()
            ->setHeaders($headers)
            ->setRows($rows);

        if ($headerTitle) {
            $tbl->setHeaderTitle($headerTitle);
        }

        if ($footerTitle) {
            $tbl->setFooterTitle($footerTitle);
        }

        $tbl->render();

        $this->newLine();
    }


    /**
     * Formats a horizontal table.
     *
     * same like SymfonyStyle's table but with optional headerTitle and footerTitle
     *
     * 07/2023 created.
     */
    public function horizontalTable(array $headers, array $rows, ?string $headerTitle = null, ?string $footerTitle = null): void
    {
        $tbl = $this->createTable()
            ->setHorizontal(true)
            ->setHeaders($headers)
            ->setRows($rows);

        if ($headerTitle) {
            $tbl->setHeaderTitle($headerTitle);
        }

        if ($footerTitle) {
            $tbl->setFooterTitle($footerTitle);
        }

        $tbl->render();

        $this->newLine();
    }


    /**
     * 03/2022 TODO: some color as parameter would be nice (see UtilFormatter for ascii tables as alternative to symfony's table)
     * 11/2020 created.
     */
    public function dictAsHorizontalTable(?array $dict, ?string $title = null): void
    {
        if (!$dict) {
            $this->warning('dictAsHorizontalTable' . ($title ? (' ' . $title) : '') . ' - $dict is empty');

            return;
        }

        $values = array_values($dict);
        // UtilDebug::d($values);
        foreach ($values as $idx => $val) {
            if (!is_scalar($val)) {
                $values[$idx] = self::_nonScalarToStringForTable($val);
            }
        }

        $this->horizontalTable(array_keys($dict), [$values], $title);
    }

    /**
     * factory method.
     *
     * 01/2022 created
     *
     * @return self
     */
    public static function createQuiet()
    {
        return new self(new ArgvInput(), new NullOutput());
    }

    /**
     * factory method.
     *
     * 01/2022 created
     *
     * @return self
     */
    public static function create()
    {
        // return new self(new ArgvInput(), new StreamOutput(fopen('php://stdout', 'w')));
        return new self(new ArgvInput(), new ConsoleOutput());
    }

    /**
     * 05/2021 created.
     */
    public function done(?string $msg = "DONE")
    {
        $this->success("==== $msg ====");
    }

    /**
     * 01/2023 created.
     */
    public function fail(): void
    {
        $this->error('!!!!!!!!!!!!!!!! FAIL !!!!!!!!!!!!!!!!');
    }

    public function red(string $msg, $bNewLine = true)
    {
        $this->write(AnsiColor::colorText($msg, 'red'), $bNewLine);
    }

    public function green(string $msg, $bNewLine = true)
    {
        $this->write(AnsiColor::colorText($msg, 'green'), $bNewLine);
    }

    public function blue(string $msg, $bNewLine = true)
    {
        $this->write(AnsiColor::colorText($msg, 'blue'), $bNewLine);
    }

    public function yellow(string $msg, $bNewLine = true)
    {
        $this->write(AnsiColor::colorText($msg, 'yellow'), $bNewLine);
    }

    public function cyan(string $msg, $bNewLine = true)
    {
        $this->write(AnsiColor::colorText($msg, 'cyan'), $bNewLine);
    }

    public function magenta(string $msg, $bNewLine = true)
    {
        $this->write(AnsiColor::colorText($msg, 'magenta'), $bNewLine);
    }

    public function red_bg(string $msg, $bNewLine = true)
    {
        $this->write(AnsiColor::colorText($msg, 'red_bg'), $bNewLine);
    }

    public function green_bg(string $msg, $bNewLine = true)
    {
        $this->write(AnsiColor::colorText($msg, 'green_bg'), $bNewLine);
    }

    public function blue_bg(string $msg, $bNewLine = true)
    {
        $this->write(AnsiColor::colorText($msg, 'blue_bg'), $bNewLine);
    }

    public function yellow_bg(string $msg, $bNewLine = true)
    {
        $this->write(AnsiColor::colorText($msg, 'yellow_bg'), $bNewLine);
    }

    public function cyan_bg(string $msg, $bNewLine = true)
    {
        $this->write(AnsiColor::colorText($msg, 'cyan_bg'), $bNewLine);
    }

    public function magenta_bg(string $msg, $bNewLine = true)
    {
        $this->write(AnsiColor::colorText($msg, 'magenta_bg'), $bNewLine);
    }

    /**
     * 04/2022 created.
     *
     * @param string[] $arr
     */
    public function list(array $arr)
    {
        $args = [];
        foreach ($arr as $key => $item) {
            $args[] = [(string)($key + 1) => $item];
        }
        $this->definitionList(...$args);
    }

    /**
     * 03/2023 created
     *
     * @param array $dicts numeric array of dicts
     */
    public function listOfDictsAsTable(array $dicts, ?string $title = null): void
    {
        if (empty($dicts)) {
            $this->warning("listOfDictsAsTable - list is empty");
            return;
        }

        $this->table(array_keys($dicts[0]), $dicts, $title);
    }


    /**
     * Lets the user type some confirmation string (eg the domain name of a shop the command is going to operate on)
     * if the entered string does not match the expected $confirmation string, the script EXITS with code 77
     * 05/2023 created
     *
     * @param string $confirmationString
     * @param string $info
     */
    public function confirmSecureOrDie(string $confirmationString, string $info = null)
    {
        if ($info) {
            $this->writeln("<info>$info</info>");
        }

        // ---- ask user to confirm by typing some text
        $response = $this->askQuestion(new Question("To continue, type <question>$confirmationString</question>", null));

        if ($response !== $confirmationString) {
            $this->error("expected: $confirmationString, got: $response ... exiting");
            exit(77);
        }
    }

}
