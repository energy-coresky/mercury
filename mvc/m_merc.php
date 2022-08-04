<?php

class m_merc extends Model_m
{
    function struct($tbl, &$new = false) {
        $orig = SKY::$dd->_struct(substr($tbl, strlen(SKY::$dd->pref)));
        if (false === $new)
            return $orig;
        $keys = [];
        $struct = array_map(function ($v) use (&$keys) {
            if ($v[3])
                $keys[] = $v[3];
            return $v[2];
        }, $orig);
        $list = SKY::$dd->_tables();
        for ($i = 0; in_array($new = $tbl . $i, $list); $i++);
        return array_merge($struct, $keys);
    }

    function create($tbl) {
        $struct = $this->struct($tbl, $new);
        $q = SKY::$dd->quote;
        sqlf("CREATE TABLE $q$new$q (\n  " . implode(",\n  ", $struct) . "\n)");
    }

    function drop($tbl) {
        $q = SKY::$dd->quote;
        sqlf("DROP TABLE $q$tbl$q");
    }

    function sort($tbl, $from, $to) {
        $struct = $this->struct($tbl, $new);
        $q = SKY::$dd->quote;
        $deleted = array_splice($struct, $from, 1);
        array_splice($struct, $to, 0, $deleted);
        sqlf("DROP TABLE $q$tbl$q");
        sqlf("CREATE TABLE $q$tbl$q (\n  " . implode(",\n  ", $struct) . "\n)");
    }

    function dc($tbl, $n) {
        $struct = $this->struct($tbl, $new);
        $q = SKY::$dd->quote;
        array_splice($struct, $n, 1);
        sqlf("DROP TABLE $q$tbl$q");
        sqlf("CREATE TABLE $q$tbl$q (\n  " . implode(",\n  ", $struct) . "\n)");
    }

    function tables() {
        $list = SKY::$dd->_tables();
        return [
            'row_c' => function () use (&$list) {
                if (!$list)
                    return false;
                return [
                    'table' => $tbl = array_shift($list),
                    'col' => $this->struct($tbl),
                ];
            },
        ];
    }
}
