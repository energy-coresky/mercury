<?php

class m_fs extends Model_m
{
    static $view;
    static $mvc;
    static $ok = [];
    static $tables = [];
    static $ctr;
    static $jets;

    function fromdb($tbl) {
        self::$tables[] = $tbl = $this->m_db->cut_pref($tbl);
        $list = [];
        $can_hide = 1;
        foreach (['c_%s.php', '_%s.jet', 't_%s.php', 'm_%s.php'] as $i => $t) {
            self::$ok[] = $fn = sprintf($t, $tbl);
            $exist = 1 == $i ? in_array($fn, self::$view) : in_array($fn, self::$mvc);
            $can_hide &= ~(int)$exist;
            $list[] = [$fn, $exist];
        }
        return [$list, $can_hide];
    }

    function tables(&$hidden) {
        self::$view = array_map('basename', Plan::view_b(['main', '*.jet']));
        self::$mvc = array_map('basename', Plan::_b(['main', 'mvc/*.php']));

        $h = $this->d_merc_hidden;
        $hidden = $h ? explode(' ', $h) : [];

        return $this->m_db->tables(false) + [
            'after_c' => function () {
                $ctr = array_map(function ($v) {
                    if (in_array($v = basename($v), self::$ok) || '_' != $v[1])
                        return '';
                    return 'c_' . substr($v, 2);
                }, self::$mvc) + [888 => 'c_main.php', 'common_c.php', 'default_c.php'];
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
                    self::$ok[] = $fn = sprintf($t, $short);
                    $col[] = [$fn, !$i ? in_array($fn, self::$view) : in_array($fn, self::$mvc)];
                }
                return [
                    'table' => '-',
                    'col' => [$col, 0],
                ];
            },
            'after_c' => function () {
                self::$view = array_diff(self::$view, self::$ok);
                self::$jets = self::$view + [-3 => 'y_desktop.jet', -2 => 'error.jet', -1 => 'pagination.jet'];
                sort(self::$jets);
                self::$jets = array_chunk(array_unique(self::$jets), 5);
            },
        ];
    }

    function view() {
        return [
            'row_c' => function () {
                if (!self::$jets)
                    return false;
                $ary = array_pad(array_shift(self::$jets), 5, '-');
                return [
                    'row' => array_map(function ($v) {
                        return [$v, in_array($v, self::$view)];
                    }, $ary),
                ];
            },
        ];
    }

    function other() {
        $list = array_map($func = function ($v) {
            return 'w3/' . basename($v);
        }, Plan::_b(['main', 'w3/*']));
        $list += [99 => 'mvc/jet.php', 'mvc/jet.let', 'w3/app.php', 'cron.php'];
        $list = array_chunk(array_unique($list), 5);
        return [
            'row_c' => function () use (&$list) {
                if (!$list)
                    return false;
                $ary = array_pad(array_shift($list), 5, '-');
                return [
                    'row' => array_map(function ($v) {
                        return [$v, Plan::_t(['main', $v])];
                    }, $ary),
                ];
            },
        ];
    }


    function j_hide($show, $tbl) {
        $h = $this->d_merc_hidden;
        $h = $h ? explode(' ', $h) : [];
        $show ? ($h = array_diff($h, [$tbl])) : ($h[] = $tbl);
        $this->d_merc_hidden = implode(' ', $h);
        SKY::d('merc_hidden', implode(' ', $h));
    }

    function gen() {
        
    }

    function j_new() {
    }

    function j_back() {
    }

    function j_open($type, $x, $fn, $ext) {
        $app = 'jet' != $ext;
        if ('' === $fn) {
            $tpl = [
                'c_<strong>file</strong>.php',
                '_<strong>file</strong>.jet',
                't_<strong>file</strong>.php',
                'm_<strong>file</strong>.php',
                '<strong>file</strong>.jet',
                '<strong>file</strong>.php',
            ];
            $h1 = 'Create new:&nbsp;`' . $tpl[$type] . '`';
            $code = '';
        } else {
            $fn = "$fn.$ext";
            if ($app && 'p' != $x)
                $fn = "mvc/$fn";
            $test = $app ? Plan::_t(['main', $fn]) : Plan::view_t(['main', $fn]);
            $h1 = $test ? "Existent: `$fn`" : "Not existent: `$fn`";
            $code = $test ? file_get_contents($test) : '';
        }
        return [
            'html' => $app ? Display::php($code) : Display::jet($code),
            'h1' => $h1,
            'c' => 'C' == $h1[0] ? 'y' : ('E' == $h1[0] ? 'g' : 'r'),
        ];
    }
}
