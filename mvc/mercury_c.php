<?php

class mercury_c extends Controller
{
    function head_y($action) {
        $vars = Plan::cfg_gq('mercury_vars');
        SKY::ghost('w', $vars, function ($s) {
            Plan::cfg_p(['mercury', 'mercury_vars'], $s);
        });
    }

    function tail_y() {
        if (!MVC::$layout)
            return;
        $y = parent::tail_y();
        $this->k_static = [[], ["~/m/dev.js", "~/m/mercury.js"], ["~/m/dev.css", "~/m/mercury.css"]];
        return $y + ['menu' => [
            'db=' . ($this->d_merc_db ?: 'main') => 'Databases',
            'ctrl' => 'Controllers',
            'mod' => 'Models',
            'wizard' => 'Wizard',
        ]];
    }

    function a_ware() {
        jump('_mercury?db=' . ($this->d_merc_db ?: 'main'));
    }

    function a_db() {
        $list = ['main' => 0] + SKY::$databases;
        unset($list['driver'], $list['pref'], $list['dsn'], $list['']);
        $this->ajax or $this->d_merc_db = $this->_2 ?: 'main';
        return [
            'e_tables' => $this->m_merc->tables(),
            'databases' => array_keys($list),
            'driver' => $this->m_merc->dd()->name,
        ];
    }

    function j_info() {
        $dd = $this->m_merc->dd();
        $x = 'MySQLi' == $dd->name
            ? pos($dd->sqlf('@show create table `' . $_GET['tbl'] . "`"))
            : $this->m_merc->create($_GET['tbl'], true);
        echo tag($x, '', 'pre');
    }

    function j_mig() {
        $this->w_save_migration = $this->_3;
        return true;
    }

    function j_rename() {
        $this->m_merc->rename($_GET['tbl'], $_GET['to']);
        return $this->a_db();
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

    function j_ac() { # drop column
        $this->m_merc->ac($_GET['tbl'], $_GET['n'], $_POST['tbl'], $_POST['n']);
        return $this->a_db();
    }

    function j_dc() { # drop column
        $this->m_merc->dc($_GET['tbl'], $_GET['n']);
        return $this->a_db();
    }

    function j_rc() { # rename column
        $this->m_merc->rc($_GET['tbl'], $_POST['from'], $_POST['to']);
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
