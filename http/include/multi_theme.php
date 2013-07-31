<?php
$theme = array('Default' => '',
    'Blue(simple)' => '<link href="style/style.blue.css" title="Blue(simple)" rel="oj_theme stylesheet" type="text/css"/>',
    'Green(simple)' => '<link href="style/style.green.css" title="Green(simple)" rel="oj_theme stylesheet" type="text/css" />',
    'Purple(simple)' => '<link href="style/style.purple.css" title="Purple(simple)" rel="oj_theme stylesheet" type="text/css" />'
    );
$cur_theme = 'Default';

if(!empty($_COOKIE['oj_info'])) {
    $theme_arr = explode(';', $_COOKIE['oj_info']);
    foreach($theme_arr as $val) {
        $kv = explode('=', $val, 2);
        if (count($kv) >= 2) {
            if ( trim(urldecode($kv[0])) == 'oj_theme') {
                $cur_theme = urldecode($kv[1]);
                break;
            }
        }
    }
}

if (isset($theme[$cur_theme]))
    echo $theme[$cur_theme];
?>