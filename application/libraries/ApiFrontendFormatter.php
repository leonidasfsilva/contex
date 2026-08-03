<?php

defined('BASEPATH') or exit('No direct script access allowed');

class ApiFrontendFormatter
{
    public static function decimal($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = trim((string) $value);

        if (preg_match('/^-?\d+$/', $value)) {
            return $value . '.00';
        }

        if (preg_match('/^-?\d+\.\d$/', $value)) {
            return $value . '0';
        }

        return $value;
    }

    public static function date($value)
    {
        if ($value === null || $value === '' || $value === '0000-00-00') {
            return null;
        }

        $timestamp = strtotime((string) $value);

        return $timestamp === false ? null : date('Y-m-d', $timestamp);
    }

    public static function dateTime($value)
    {
        if ($value === null || $value === '' || strpos((string) $value, '0000-00-00') === 0) {
            return null;
        }

        $date = new DateTime((string) $value);

        return $date->format(DateTime::ATOM);
    }

    public static function boolean($value)
    {
        return (bool) $value;
    }
}
