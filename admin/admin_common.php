<?php

require_once dirname( dirname( __FILE__ ) ) . '/' . 'common.php';
require_once( WEBIM_PATH . 'admin/functions.admin_helper.php' );
require_once( WEBIM_PATH . 'admin/schema.php' );

/** get $im_template_files */
require_once( WEBIM_PATH . 'admin_interface.php' );

$im_config_file = WEBIM_PATH . 'config.php';
$im_config_sample_file = WEBIM_PATH . 'config_sample.php';
$im_template = '<script type="text/javascript" src="webim/custom.js.php"></script>';

?>


