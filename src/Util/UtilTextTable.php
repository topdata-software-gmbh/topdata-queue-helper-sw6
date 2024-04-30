<?php


namespace Topdata\TopdataQueueHelperSW6\Util;

/**
 * 09/2023 created
 */
class UtilTextTable
{

    private static function mb_str_pad(string $string, int $length, string $strPag = ' ')
    {
        return $string . str_repeat($strPag, $length - mb_strlen($string));
    }

    const VERTICAL_LINE = '|';
    /**
     * @var array|mixed
     */
    private static array $columnWidths;

    public static function renderTextTable($data)
    {
        if (empty($data)) {
            return "No data to display.";
        }

        // ---- Determine column widths based on the length of the keys and values
        self::_determineColumnWidths($data);

        // ---- HEADER
        $table = self::_renderSeparatorRow('-');
        $table .= self::_renderTableRow(array_combine(array_keys(self::$columnWidths), array_keys(self::$columnWidths)));
        $table .= self::_renderSeparatorRow('-');

        // ---- BODY
        foreach ($data as $row) {
            if (is_string($row)) {
                if (str_starts_with($row, '-')) {
                    $table .= self::_renderSeparatorRow('-');
                } elseif (str_starts_with($row, '=')) {
                    $table .= self::_renderSeparatorRow('=');
                }
            } else {
                $table .= self::_renderTableRow($row);
            }
        }

        // ---- FOOTER
        $table .= self::_renderSeparatorRow();

        return $table;
    }

    /**
     * Determine column widths based on the length of the keys and values
     * 09/2023 created
     *
     * @param $data
     * @return void
     */
    private static function _determineColumnWidths($data): void
    {
        self::$columnWidths = [];
        foreach ($data as $row) {
            if(is_string($row)) {
                // eg '--------'
                continue;
            }
            foreach ($row as $key => $value) {
                $keyLength = mb_strlen($key);
                $valueLength = mb_strlen($value);
                self::$columnWidths[$key] = max(self::$columnWidths[$key] ?? 0, $keyLength, $valueLength);
            }
        }
    }

    private static function _renderSeparatorRow(string $ch = '-'): string
    {
        $str = self::VERTICAL_LINE;
        foreach (self::$columnWidths as $width) {
            $str .= str_repeat($ch, $width) . self::VERTICAL_LINE;
        }
        $str .= "\n";

        return $str;
    }

    private static function _renderTableRow(mixed $row): string
    {
        $str = self::VERTICAL_LINE;
        foreach ($row as $key => $value) {
            $str .= self::mb_str_pad($value, self::$columnWidths[$key]) . self::VERTICAL_LINE;
        }
        $str .= "\n";

        return $str;
    }

}