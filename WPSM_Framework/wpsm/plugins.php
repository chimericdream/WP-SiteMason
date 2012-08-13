<?php
global $wpsm_plugins;

foreach ($wpsm_plugins as $p) {
    if (check_wpsm_plugin($p['option_name']) &&
            check_wpsm_plugin_dependencies($p['option_name'])) {
        require_once dirname(__FILE__) .'/plugins/' . $p['file'] . '.php';
    }
}

function check_wpsm_plugin_dependencies($plugin_name)
{
    global $wpsm_plugins;
    if (empty($wpsm_plugins[$plugin_name]['depends'])) {
        return true;
    }

    foreach ($wpsm_plugins[$plugin_name]['depends'] as $dep) {
        if (!check_wpsm_plugin($dep)) {
            return false;
        }
    }

    return true;
} //end check_wpsm_plugin_dependencies

function check_wpsm_plugin($plugin_name)
{
    if ((bool) get_option($plugin_name) == true) {
        return true;
    }

    return false;
} //end check_wpsm_plugin_dependencies