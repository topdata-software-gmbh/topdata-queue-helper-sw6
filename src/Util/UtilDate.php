<?php

namespace Topdata\TopdataQueueHelperSW6\Util;

/**
 * UtilDate - Utility class for date conversions
 *
 * Provides static methods for handling date conversions.
 */
class UtilDate
{
    /**
     * Converts a date string to a \DateTime object.
     *
     * This method handles date strings with or without milliseconds.
     *
     * @param string $dateString The date string to convert.
     * @return \DateTime The converted \DateTime object.
     * @throws \Exception If the date string is invalid.
     */
    public static function dateTimeFromString(string $dateString): \DateTime
    {
        // Check if the date string contains milliseconds
        if (strpos($dateString, '.') !== false) {
            $format = 'Y-m-d H:i:s.u';
        } else {
            $format = 'Y-m-d H:i:s';
        }

        // Create a DateTime object from the date string
        return \DateTime::createFromFormat($format, $dateString);
    }
}