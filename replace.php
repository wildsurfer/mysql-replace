<?php
if(isset($_GET['replace'])){
//config
if (!defined('SEARCH')) define('SEARCH',$_GET['old_host']);
if (!defined('REPLACE')) define('REPLACE',$_GET['new_host']);
$db_options = array(
    'host'     => $_GET['db_host'],
    'username' => $_GET['db_user'],
    'password' => $_GET['db_pass'],
    'dbname'   => $_GET['db_db']
);
$db_prefix = $_GET['db_prefix'];
// nothing to config below
set_time_limit (60);
require_once ('Zend/Db/Adapter/Pdo/Mysql.php');
if (isset($_GET['dry_run']) && $_GET['dry_run'] == 0)
    define('DRY_RUN',false);
else
    define('DRY_RUN',true);
$db = new Zend_Db_Adapter_Pdo_Mysql($db_options);
$tables = $db->listTables();
$html_tables = array();
foreach ($tables as $table) {
    if (!$db_prefix || preg_match("/^{$db_prefix}/",$table)) {
        $pkey = null;
        $columns = $db->describeTable($table);
        foreach ($columns as $column => $info) {
            if (!$pkey && isset($info['PRIMARY'])) $pkey = $column;
            $result = $db->fetchAll("SELECT `{$pkey}`,`{$column}` FROM `{$table}` WHERE `{$column}` like (?)", '%'.SEARCH.'%');
            foreach ($result as $r) {
                $where = $db->quoteInto($pkey.' = ?', $r[$pkey]);
                $str = $r[$column];
                $data = @unserialize($str);
                if ($str === 'b:0;' || $data !== false) {
                    array_walk_recursive($data,'replaceUrl');
                    $str_new = serialize($data);
                    $html_tables[$table][$column][] = array(
                        'from' => $str,
                        'to' => $str_new
                    );
                    if (!DRY_RUN) $db->update($table,array($column => $str_new),$where);
                } else {
                    $a = array();
                    $a['from'] = $str;
                    replaceUrl($str);
                    $a['to'] = $str;
                    $html_tables[$table][$column][] = $a;
                    if (!DRY_RUN) $db->update($table,array($column => $str),$where);
                }
            }
        }
    }
}
/**
 * TODO: short description.
 *
 * @return TODO
 * @author Ivan Kuznetsov <kuzma.wm@gmail.com>
 * @since  2013-02-18
 */
function replaceUrl(&$val) {
    if(is_string($val)){
        $val = str_replace(SEARCH,REPLACE,$val);
    }
}
/**
 * TODO: short description.
 *
 * @param string $str
 *
 * @return TODO
 * @author Ivan Kuznetsov <kuzma.wm@gmail.com>
 * @since  2013-02-18
 */
function hilight ($str) {
    $str = htmlentities($str);
    $s = array(
        SEARCH,
        REPLACE
    );
    $r = array(
        '<span class="search">'.SEARCH.'</span>',
        '<span class="replace">'.REPLACE.'</span>'
    );
    return str_replace($s,$r,$str);
}
// output result in html
echo <<<STYLE
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }
    table td {
        white-space: -moz-pre-wrap !important;  /* Mozilla, since 1999 */
        white-space: -pre-wrap;      /* Opera 4-6 */
        white-space: -o-pre-wrap;    /* Opera 7 */
        white-space: pre-wrap;       /* css-3 */
        word-wrap: break-word;       /* Internet Explorer 5.5+ */
        word-break: break-all;
        white-space: normal;
        border: 1px solid black;
        padding: 2px 5px;
    }
    span.search {
        font-weight :bold;
        color: blue;
    }
    span.replace {
        font-weight :bold;
        color: red;
    }
    a.save {
        font-size: 2em;
        color: green;
        margin: 2em 0em;
        display: block;
    }
</style>
STYLE;
foreach ($html_tables as $tkey => $table) {
    echo("<h1>{$tkey}</h1>");
    echo '<table>';
    echo "<tr><th>From</th><th>To</th></tr>";
    foreach ($table as $ckey => $column) {
        echo "<tr><th colspan=\"2\">{$ckey}</th></tr>";
        foreach ($column as $row) {
            $from = hilight($row['from']);
            $to = hilight($row['to']);
            echo "<tr><td>{$from}</td><td>{$to}</td></tr>";
        }
    }
    echo('</table>');
}

if ($html_tables && DRY_RUN) echo('<a class="save" href="?dry_run=0&old_host='.$_GET['old_host'].'&new_host='.$_GET['new_host'].'&db_host='.$_GET['db_host'].'&db_user='.$_GET['db_user'].'&db_pass='.$_GET['db_pass'].'&db_db='.$_GET['db_db'].'&db_prefix='.$_GET['db_prefix'].'">Save</a>');


}else{
	echo "Bad Request!!!";
}
