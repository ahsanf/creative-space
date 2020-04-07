<?php
defined('MOODLE_INTERNAL') || die;

$plugin->version   = 2013050100; // The current module version (Date: YYYYMMDDXX)
$plugin->requires  = 2013050100; // Requires this Moodle version
$plugin->component = 'theme_genesis'; // Full name of the plugin (used for diagnostics)
$plugin->dependencies = array(
    'theme_canvas'  => 2013050100,
);