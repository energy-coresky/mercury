<?php

class t_merc extends Model_t
{
    function __construct() {
        $this->tbl = $_GET['tbl'] ?? $_POST['tbl'];
        $this->table = substr($this->tbl, strlen(SKY::$dd->pref));
        parent::__construct();
    }

#    function head_y() {
#        return $this->edd;
#    }

    function alter() {
        $tables = SKY::$dd->_tables();
    }

    function ins() {
        $this->insert([
            'title' => '',
            'md' => '',
            '!dt_c' => '$now',
            '!dt_u' => '$now',
        ]);
    }

    function save() {
        $this->update([
            'md' => $_POST['s'],
            '!dt_u' => '$now',
        ], $_POST['id']);
        return md($_POST['s']);
    }
}
