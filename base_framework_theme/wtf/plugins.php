<?php
global $wtf_plugins;

foreach ($wtf_plugins as $p) {
    if (check_wtf_plugin($p['option_name']) &&
            check_wtf_plugin_dependencies($p['option_name'])) {
        require_once dirname(__FILE__) .'/plugins/' . $p['file'] . '.php';
    }
}

function check_wtf_plugin_dependencies($plugin_name)
{
    global $wtf_plugins;
    if (empty($wtf_plugins[$plugin_name]['depends'])) {
        return true;
    }

    foreach ($wtf_plugins[$plugin_name]['depends'] as $dep) {
        if (!check_wtf_plugin($dep)) {
            return false;
        }
    }

    return true;
} //end check_wtf_plugin_dependencies

function check_wtf_plugin($plugin_name)
{
    if ((bool) get_option($plugin_name) == true) {
        return true;
    }

    return false;
} //end check_wtf_plugin_dependencies