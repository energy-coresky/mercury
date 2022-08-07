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
            return $this->nref ? null : MVC::instance()->set($this->a_db());

        $y = parent::tail_y();
        $this->k_static = [[], ["~/m/dev.js", "~/m/mercury.js"], ["~/m/dev.css", "~/m/mercury.css"]];
        return $y + ['menu' => [
            'db=' . ($this->d_merc_db ?: 'main') => 'Databases',
            'fs' => 'Files',
            'wizard' => 'Wizard',
        ]];
    }

    function a_ware() {
        jump('_mercury?db=' . ($this->d_merc_db ?: 'main'));
    }

    # === DATABASES ==================================================================
    function a_db() {
        $list = ['main' => 0] + SKY::$databases;
        unset($list['driver'], $list['pref'], $list['dsn'], $list['']);
        $this->ajax or $this->d_merc_db = $this->_2 ?: 'main';
        return [
            'e_tables' => $this->m_db->tables(),
            'databases' => array_keys($list),
            'driver' => $this->m_db->dd()->name,
        ];
    }

    function j_info() {
        $dd = $this->m_db->dd();
        $x = 'MySQLi' == $dd->name
            ? pos($dd->sqlf('@show create table `' . $_GET['tbl'] . "`"))
            : $this->m_db->create($_GET['tbl'], true);
        echo tag($x, '', 'pre');
        return $this->nref = true;
    }

    function j_mig() {
        $this->w_save_migration = $this->_3;
        return $this->nref = true;
    }

    function j_rename() {
        $this->m_db->rename($_GET['tbl'], $_GET['to']);
    }

    function j_create() {
        $this->m_db->create($_POST['tbl']);
    }

    function j_drop() {
        $this->m_db->drop($_POST['tbl']);
    }

    function j_sort() { # sort columns
        $this->m_db->sort($_GET['tbl'], $_POST['sort'][0], $_POST['sort'][1]);
    }

    function j_ac() { # add column
        $this->m_db->ac($_GET['tbl'], $_GET['n'], $_POST['tbl'], $_POST['n']);
    }

    function j_dc() { # drop column
        $this->m_db->dc($_GET['tbl'], $_GET['n']);
    }

    function j_rc() { # rename column
        $this->m_db->rc($_GET['tbl'], $_POST['from'], $_POST['to']);
    }

    function default_j() {
        $ary = explode('.', $this->_1, 3);
        MVC::body("$ary[0].$ary[1]");
        $this->nref = true;
        return $this->{"s_$ary[1]"}($ary[2] ?? 0);
    }
    # === FILES ======================================================================
    function a_fs() {
        return [
            'e_mvc' => $this->m_fs->mvc(),
            'e_tables' => $this->m_fs->tables($hidden),
            'hidden' => $hidden,
        ];
    }

    function s_show($tbl) {
        return $this->s_hide($tbl, 1);
    }

    function s_hide($tbl, $show = 0) {
        $h = $this->d_merc_hidden;
        $h = $h ? explode(' ', $h) : [];
        $show ? ($h = array_diff($h, [$tbl])) : ($h[] = $tbl);
        $this->d_merc_hidden = implode(' ', $h);
        SKY::d('merc_hidden', implode(' ', $h));
        return $this->a_fs();
    }


    # === WIZARD =====================================================================
    function a_wizard() {
        return [
            'qq' => 11,
//$this->m_db->test(),
        ];
    }
}
