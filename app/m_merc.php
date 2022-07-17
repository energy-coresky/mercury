<?php

class m_merc extends Model_m
{
    function tables() {
        $list = SKY::$dd->_tables();
        return [
            'row_c' => function () use (&$list) {
                if (!$list)
                    return false;
                return [
                    'table' => $table = array_shift($list),
                    'col' => SKY::$dd->_struct(substr($table, strlen(SKY::$dd->pref))),
                ];
            },
        ];
    }
}
