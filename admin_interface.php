<?php

/**
 * Provide array $im_template_files for insert webim_template
 * Provide array $im_template_cache_dir for clear template cache
 *
 */

$im_template_cache_dir = dirname( dirname(__FILE__) ) . '/data/tplcache';
$im_template_files = array();
$template_dir = array();
$template_dir[] = dirname( dirname(__FILE__) ) . '/template';
$template_dir[] = dirname( dirname(__FILE__) ) . '/u/themes';
$template_dir[] = dirname( dirname(__FILE__) ) . '/mode/o';
$template_dir[] = dirname( dirname(__FILE__) ) . '/mode/area';

foreach( $template_dir as $dir ) {
	foreach( webim_scan_subdir( $dir ) as $k => $v ) {
		$d = $dir.DIRECTORY_SEPARATOR.$v;
		$f = $d.DIRECTORY_SEPARATOR.'footer.htm';
		if( file_exists( $f ) ){
			$im_template_files[] = $f;
		}
	}
}

unset( $template_dir );

