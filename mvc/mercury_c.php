<?php

class mercury_c extends Controller
{
    function head_y($action) {
    }

    function tail_y() {
        if (!MVC::$layout)
            return;
        $y = parent::tail_y();
        $this->k_static = [[], ["~/m/dev.js", "~/m/mercury.js"], ["~/m/dev.css", "~/m/mercury.css"]];
        return $y + ['menu' => [
            'db' => 'Database',
            'ctrl' => 'Controllers',
            'mod' => 'Models',
            'wizard' => 'Wizard',
        ]];
    }

    function a_ware() {
        jump('_mercury?db');
    }

    function a_db() {
        return [
            'e_tables' => $this->m_merc->tables(),
        ];
    }

    function j_create() {
        $this->m_merc->create($_POST['tbl']);
        return $this->a_db();
    }

    function j_drop() {
        $this->m_merc->drop($_POST['tbl']);
        return $this->a_db();
    }

    function j_sort() {
        $this->m_merc->sort($_GET['tbl'], $_POST['sort'][0], $_POST['sort'][1]);
        return $this->a_db();
    }

    function j_dc() { # drop column
        $this->m_merc->dc($_GET['tbl'], $_GET['n']);
        return $this->a_db();
    }

    function j_alter() {
        return $this->a_db();
    }

    function a_ctrl() {
    }

    function a_mod() {
    }

    function a_wizard() {
        return [
            'qq' => 11,
//$this->m_merc->test(),
        ];
    }
}
