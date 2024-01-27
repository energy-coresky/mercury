<?php

class mercury_c extends Controller
{
    function head_y($action) {
        $vars = Plan::cfg_gq('mercury_vars');
        SKY::ghost('w', $vars, function ($s) {
            Plan::cfg_p(['mercury', 'mercury_vars'], $s);
        });
        #require Plan::_obj(0)->path . "/mvc/merc.php";
        #new Merc;
    }

    function tail_y() {
        if (!MVC::$layout)
            return $this->nref ? null : MVC::instance()->set($this->a_db());

        $y = parent::tail_y();
        Plan::tail('', '~/m/mercury.js', '~/m/mercury.css');
        return $y + ['menu' => [
            'wizard' => 'Wizard',
            'db=' . ($this->d_merc_db ?: 'main::core') => 'Databases',
            'fs' => 'Files',
        ]];
    }

    function a_ware() {
        jump('_mercury?db=' . ($this->d_merc_db ?: 'main'));
    }

    # === DATABASES ==================================================================
    function a_db() {
        $this->fly or $this->d_merc_db = $this->_2 ?: 'main::core';
        return [
            'e_tables' => $this->m_db->tables(),
            'driver' => $this->m_db->dd()->name,
        ];
    }

    function j_info() {
        echo tag($this->m_db->info($_GET['tbl']), '', 'pre');
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
        $in = explode('.', $this->_1);
        MVC::body("$in[0].$in[1]");
        $this->nref = true;
        $r = call_user_func_array([$this->t_fs, "j_$in[1]"], array_pad(array_slice($in, 2), 5, ''));
        return null === $r ? $this->a_fs() : $r;
    }
    # === FILES ======================================================================
    function a_fs() {
        return [
            'e_mvc' => $this->t_fs->mvc(),
            'e_view' => $this->t_fs->view(),
            'e_other' => $this->t_fs->other(),
            'e_tables' => $this->t_fs->tables($hidden),
            'hidden' => $hidden,
        ];
    }


    # === WIZARD =====================================================================
    function a_wizard() {
        return [
            'qq' => 11,
//$this->m_db->test(),
        ];
    }
}
