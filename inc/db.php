<?php
$db->sqliteCreateFunction('regexp',
    function ($pattern, $data, $delimiter = '~', $modifiers = 'isuS')
    {
        if (isset($pattern, $data) === true)
        {
            return (preg_match(sprintf('%1$s%2$s%1$s%3$s', $delimiter, $pattern, $modifiers), $data) > 0);
        }

        return null;
    }
);
