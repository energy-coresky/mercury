<?php

class m_fs extends Model_m
{
    static $view;
    static $mvc;
    static $ok = [];
    static $tables = [];
    static $ctr;

    function fromdb($tbl) {
        $tbl = $this->m_db->cut_pref($tbl);
        self::$tables[] = $tbl;
        $tpl = ['c_%s.php', '_%s.jet', 't_%s.php', 'm_%s.php'];
        $list = [];
        $can_hide = 1;
        foreach ($tpl as $i => $t) {
            $fn = sprintf($t, $tbl);
            if ($ok = 1 == $i ? in_array($fn, self::$view) : in_array($fn, self::$mvc))
                self::$ok[] = $fn;
            $can_hide &= ~(int)$ok;
            $list[] = [$fn, $ok];
        }
        return [$list, $can_hide];
    }

    function tables(&$hidden) {
        self::$view = array_map('basename', Plan::view_b(['main', '*.jet']));
        self::$mvc = array_map('basename', Plan::_b(['main', 'mvc/*.php']));

        $h = $this->d_merc_hidden;
        $hidden = $h ? explode(' ', $h) : [];

        return $this->m_db->tables(false) + [
            'func' => function ($tbl, $ary) use ($hidden) {
                return in_array($tbl, $hidden);
            },
            'after_c' => function () {
                $fixed_name = ['c_main.php', 'common_c.php', 'default_c.php'];
                $ctr = array_map('basename', Plan::_b(['main', 'mvc/c_*.php']));
                $t = array_map($func = function ($v) {
                    if (in_array($v = basename($v), self::$ok))
                        return '';
                    return 'c_' . substr($v, 2);
                }, Plan::_b(['main', 'mvc/t_*.php']));
                $m = array_map($func, Plan::_b(['main', 'mvc/m_*.php']));
                $ctr = array_merge($ctr, $t, $m, $fixed_name);
                sort($ctr);
                self::$ctr = array_diff(array_unique($ctr), self::$ok + [-1 => '']);
            },
        ];
    }

    function mvc() {
        return [
            'row_c' => function () {
                if (!self::$ctr)
                    return false;
                $col = [[$ctr = array_shift(self::$ctr), in_array($ctr, self::$mvc)]];
                self::$ok[] = $ctr;
                $short = preg_replace("/^(c_)?(\w+?)(_c)?\.php$/", '$2', $ctr);
                foreach (['_%s.jet', 't_%s.php', 'm_%s.php'] as $i => $t) {
                    if ('_' != $ctr[1] && in_array($short, self::$tables)) {
                        $col[] = ['-', 0];
                        continue;
                    }
                    $fn = sprintf($t, $short);
                    if ($ok = !$i ? in_array($fn, self::$view) : in_array($fn, self::$mvc))
                        self::$ok[] = $fn;
                    $col[] = [$fn, $ok];
                }
                return [
                    'table' => '-',
                    'col' => [$col, 0],
                ];
            },
            'after_c' => function () {
                self::$mvc = array_diff(self::$mvc, self::$ok);
                self::$view = array_diff(self::$view, self::$ok);
            },
        ];
    }

    function view() {
        
    }
}
    /*public $list = [
        ['-', 'error.jet'],
        ['-', 'y_desktop.jet'],
    ];
*/
