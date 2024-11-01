<?php
/*
Uninstall "Time goes by"
@since 1.0
*/

if(!defined('ABSPATH') && !defined('WP_UNINSTALL_PLUGIN')) { exit(); }

delete_option('time_goes_by_timezone');
