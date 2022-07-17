<?php

class mercury_c extends Controller
{
    function head_y($action) {
    }

    function tail_y() {
        if (!MVC::$layout)
            return;
        $y = parent::tail_y();
        $this->k_static = [[], ["~/dev.js", "~/mercury.js"], ["~/dev.css", "~/mercury.css"]];
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

    function j_alter() {
        return $this->a_db();
    }

    function j_create() {
        return $this->a_db();
    }

    function j_drop() {
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
