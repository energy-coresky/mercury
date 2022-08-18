<?php

class m_db extends Model_m
{
    function head_y() {
        $name = 'main' == $this->d_merc_db ? '' : $this->d_merc_db;
        $dd = SQL::open($name);
        $this->q = $dd->quote;
        $this->lite = $dd->name == 'SQLite3';
        return $dd;
    }

    function cut_pref($tbl) {
        return substr($tbl, strlen($this->dd->pref));
    }

    function struct($tbl, &$new = false, &$orig = false) {
        $ary = $this->dd->_struct($this->cut_pref($tbl));
        if (false === $new)
            return $ary;
        if (false !== $orig)
            $orig = $ary;
        $keys = [];
        $struct = array_map(function ($v) use (&$keys) {
            if ($v[3])
                $keys[] = $v[3];
            return $v[2];
        }, $ary);
        $list = $this->dd->_tables();
        for ($i = 0; in_array($new = $tbl . $i, $list, true); $i++);
        return array_merge($struct, $keys);
    }

    function sql(...$in) {
        $query = (string)call_user_func_array('qp', [-2 => $this, -1 => 1] + $in);
        if ($this->w_save_migration)
            Plan::mem_a("migration_" . substr(NOW, 0, 10) . '.sql', "\n" . escape($query));
        return $this->dd->sql(SQL::NO_PARSE + 1, $query);
    }

    function info($tbl) {
        $cnt = $this->dd->sqlf("+select count(*) from `$tbl`");
        $show = $this->lite
            ? $this->create($tbl, true)
            : pos($this->dd->sqlf('@show create table `' . $tbl . "`"));
        return "rows=$cnt\n$show";
    }

    function create($tbl, $return = false) {
        $struct = $this->struct($tbl, $new);
        $tbl = $return ? $this->q . $tbl . $this->q : $this->q . $new . $this->q;
        $query = "CREATE TABLE $tbl (\n  " . implode(",\n  ", $struct) . "\n)";
        return $return ? $query : $this->sql($query);
    }

    function drop($tbl) {
        $this->sql("DROP TABLE $this->q$tbl$this->q");
    }

    function rename($tbl, $to) {
        $this->sql("ALTER TABLE $this->q$tbl$this->q RENAME TO $this->q$to$this->q");
    }

    function sort($tbl, $from, $to) {
        $struct = $this->struct($tbl, $tmp);
        $q = $this->q;
        array_splice($struct, $to, 0, array_splice($struct, $from, 1));

        $this->sql("ALTER TABLE $q$tbl$q RENAME TO $q$tmp$q");
        $this->sql("CREATE TABLE $q$tbl$q (\n  " . implode(",\n  ", $struct) . "\n)");
        $sql = $this->dd->sqlf("SELECT * FROM $q$tmp$q");
        for (; $row = $sql->one(); $this->sql("INSERT INTO $q$tbl$q @@", $row)) {
            $keys = array_keys($row);
            $vals = array_values($row);
            array_splice($keys, $to, 0, array_splice($keys, $from, 1));
            array_splice($vals, $to, 0, array_splice($vals, $from, 1));
            $row = array_combine($keys, $vals);
        }
        $this->sql("DROP TABLE $q$tmp$q");
    }

    function ac($tbl, $to, $src_t, $from) { # add column
        $src = $this->struct($src_t, $_, $orig);
        $src = array_splice($src, $from, 1);
        $src = $src[$col = key($src)];
        $dst = $this->struct($tbl, $tmp);
        $columns = array_keys($dst);
        for ($i = ''; in_array($new = $col . $i, $columns, true); (int)$i++);
        $q = $this->q;
        $src = "$q$new$q" . substr($src, 2 * strlen($q) + strlen($col));
        array_splice($dst, ++$to, 0, $src);

        $this->sql("ALTER TABLE $q$tbl$q RENAME TO $q$tmp$q");
        $this->sql("CREATE TABLE $q$tbl$q (\n  " . implode(",\n  ", $dst) . "\n)");
        $sql = $this->dd->sqlf("SELECT * FROM $q$tmp$q");
        for (; $row = $sql->one(); $this->sql("INSERT INTO $q$tbl$q @@", $row)) {
            $keys = array_keys($row);
            $vals = array_values($row);
            array_splice($keys, $to, 0, $new);
            array_splice($vals, $to, 0, [$orig[$col][1]]);
            $row = array_combine($keys, $vals);
        }
        $this->sql("DROP TABLE $q$tmp$q");
    }

    function dc($tbl, $n) { # drop column
        $struct = $this->struct($tbl, $new);
        $q = $this->q;
        array_splice($struct, $n, 1);
        $this->sql("ALTER TABLE $q$tbl$q RENAME TO $q$new$q");
        $this->sql("CREATE TABLE $q$tbl$q (\n  " . implode(",\n  ", $struct) . "\n)");
        $sql = $this->dd->sqlf("SELECT * FROM $q$new$q");
        for (; $row = $sql->one(); $this->sql("INSERT INTO $q$tbl$q @@", $row))
            array_splice($row, $n, 1);
        $this->sql("DROP TABLE $q$new$q");
    }

    function rc($tbl, $from, $to) { # rename column
        $q = $this->q;
        if ($this->lite)
            return $this->sql("ALTER TABLE $q$tbl$q RENAME COLUMN $q$from$q TO $q$to$q");
        $struct = $this->struct($tbl, $_);
        $def = substr($struct[$from], 2 * strlen($q) + strlen($from));
        $this->sql("ALTER TABLE $q$tbl$q CHANGE $q$from$q $q$to$q$def");
    }

    function tables($struct = true) {
        $list = $this->dd->_tables();
        return [
            'row_c' => function () use (&$list, $struct) {
                if (!$list)
                    return false;
                return [
                    'table' => $tbl = array_shift($list),
                    'col' => $struct ? $this->struct($tbl) : $this->t_fs->fromdb($tbl),
                ];
            },
        ];
    }
}
