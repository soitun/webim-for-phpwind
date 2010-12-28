<?php

/**
 * @author Hidden <zzdhidden@gmail.com>
 *
 */

function webim_only_for_admin() {
	global $im_is_admin;
	if ( ! $im_is_admin ) {
		$msg = '<div class="box"><div class="box-c">请用管理员帐号登录管理。</div></div>';
		include_once( '_error.php' );
		exit();
	}
}

function webim_header( $title = "" ) {
	global $im_version;
	return <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>WebIM $title</title>
		<link href="base.css" media="all" type="text/css" rel="stylesheet" />
	</head>
	<body>
		<h1>WebIM $im_version $title</h1>
		<div id="wrap">
EOF;
}

function webim_menu( $current ) {
	return '
		<div id="menu">
		<ul>
		<li ' . ( $current == 'index' ? 'class="current"' : '' ) . '><a href="index.php">基本配置</a></li>
		<li ' . ( $current == 'themes' ? 'class="current"' : '' ) . '><a href="themes.php">主题选择</a></li>
		<li ' . ( $current == 'histories' ? 'class="current"' : '' ) . '><a href="histories.php">聊天记录</a></li>
		<li class="sep"></li>
		<li ' . ( $current == 'faq' ? 'class="current"' : '' ) . '><a href="faq.php">常见问题</a></li>
		<li ' . ( $current == 'changelog' ? 'class="current"' : '' ) . '><a href="changelog.php">更新日志</a></li>
		<li ' . ( $current == 'uninstall' ? 'class="current"' : '' ) . '><a href="uninstall.php">卸载WebIM</a></li>
		<li ' . ( $current == 'install' ? 'class="current"' : '' ) . '><a href="in.php">重新安装</a></li>
		</ul>
		</div>';
}

function webim_footer() {
	return <<<EOF
		</div>
		<div id="footer"><p><a href="http://www.webim20.cn" target="_blank">© 2010 NextIM</a></p></div>
	</body>
</html>
EOF;
}

function webim_unwritable_log($paths, $truncate_size = 0, $html = true){
	$head = "无可写权限";
	$desc = "下面这些文件或目录需要可写权限才能继续，请修改这些文件权限为777";
	$markup = "";
	if($html){
		$markup .= '<div class="box"><h3>'.$head.'</h3><div class="box-c"><p class="box-desc">'.$desc.'</p><ul>';
		foreach($paths as $k => $v){
			$markup .= "<li>".substr($v, $truncate_size)."</li>";
		}
		$markup .= '</ul></div></div>';
	}else{
		$markup .= "\n".$desc."\n";
		$markup .= "---------------------------------\n";
		foreach($paths as $k => $v){
			$markup .= substr($v, $truncate_size)."\n";
		}
		$markup .= "---------------------------------\n\n";
	}
	return $markup;
}


function webim_update_db() {
	global $im_queries;
	webim_dbDelta( $im_queries );
}

function webim_update_config( $save = true ) {
	global $_IMC, $im_config_file, $im_config_sample_file;
	$old = $_IMC;
	include( $im_config_sample_file );
	$new = $_IMC;

	if( $old ) {
		foreach( $old as $k => $v ){
			if( isset( $new[$k] ) && $k != 'version' && $k != 'enable' ) {
				$new[$k] = $v;
			}
		}
	}
	foreach( $new as $k => $v ) {
		if( isset( $new[$k] ) && $k != 'version' && $k != 'enable' ) {
			$gp = webim_gp( $k );
			if( !is_null( $gp ) ) {
				$new[ $k ] = $gp;
			}
		}
	}
	if ( $save ) {
		$markup = "<?php\n\$_IMC = ".var_export($new, true).";\n";
		file_put_contents( $im_config_file, $markup );
	}
	$_IMC = $new;
}

function webim_clean_cache(){
	global $im_template_cache_dir;
	//delete cache files
	webim_clean_dir( $im_template_cache_dir );
}

function webim_insert_template() {
	global $im_template_files, $im_template;
	foreach($im_template_files as $k => $v) {
		$html = file_get_contents( $v );
		// Remove old version
		// Insert with {template webim} Before version 3.2
		$html = preg_replace('/<\!--\{template\swebim[^>]+>/i', "", $html);
		$html = preg_replace('/<script[^w>]+webim[^>]+><\/script>/i', "", $html);
		list($html, $foot) = explode("</body>", $html);
		$inc_markup = $im_template;
		$html .= $inc_markup."</body>".$foot;
		file_put_contents($v, $html);
	}
}

function webim_remove_template() {
	global $im_template_files;
	foreach($im_template_files as $k => $v) {
		$html = file_get_contents( $v );
		// Remove old version
		// Insert with {template webim} Before version 3.2
		$html = preg_replace('/<\!--\{template\swebim[^>]+>/i', "", $html);
		$html = preg_replace('/<script[^w>]+webim[^>]+><\/script>/i', "", $html);
		file_put_contents($v, $html);
	}
}

function webim_select_unwritable_path( $include_templates = false ) {
	global $im_config_file, $im_template_files;
	$paths = array();
	if( file_exists( $im_config_file ) ){
		$paths[] = $im_config_file;
	}else{
		$paths[] = WEBIM_PATH;
	}
	if ( $include_templates ) {
		$paths = array_merge( $paths, $im_template_files );
	}
	$p = array();
	foreach($paths as $k => $v){
		if(!is_writable($v)){
			$p[] = $v;
		}
	}
	return $p ? $p : false;
}

/**
 * 
 * @param
 * @return
 *
 */

function webim_report_install() {
}

/**
 * From wordpress
 * {@internal Missing Short Description}}
 *
 * {@internal Missing Long Description}}
 *
 * @since unknown
 *
 * @param unknown_type $queries
 * @param unknown_type $execute
 * @return unknown
 */
function webim_dbDelta($queries, $execute = true) {
	global $imdb;

	// Separate individual queries into an array
	if ( !is_array($queries) ) {
		$queries = explode( ';', $queries );
		if ('' == $queries[count($queries) - 1]) array_pop($queries);
	}

	$cqueries = array(); // Creation Queries
	$iqueries = array(); // Insertion Queries
	$for_update = array();

	// Create a tablename index for an array ($cqueries) of queries
	foreach($queries as $qry) {
		if (preg_match("|CREATE TABLE ([^ ]*)|", $qry, $matches)) {
			$cqueries[trim( strtolower($matches[1]), '`' )] = $qry;
			$for_update[$matches[1]] = 'Created table '.$matches[1];
		} else if (preg_match("|CREATE DATABASE ([^ ]*)|", $qry, $matches)) {
			array_unshift($cqueries, $qry);
		} else if (preg_match("|INSERT INTO ([^ ]*)|", $qry, $matches)) {
			$iqueries[] = $qry;
		} else if (preg_match("|UPDATE ([^ ]*)|", $qry, $matches)) {
			$iqueries[] = $qry;
		} else {
			// Unrecognized query type
		}
	}

	// Check to see which tables and fields exist
	if ($tables = $imdb->get_col('SHOW TABLES;')) {
		// For every table in the database
		foreach ($tables as $table) {
			// If a table query exists for the database table...
			if ( array_key_exists(strtolower($table), $cqueries) ) {
				// Clear the field and index arrays
				$cfields = $indices = array();
				// Get all of the field names in the query from between the parens
				preg_match("|\((.*)\)|ms", $cqueries[strtolower($table)], $match2);
				$qryline = trim($match2[1]);

				// Separate field lines into an array
				$flds = explode("\n", $qryline);

				//echo "<hr/><pre>\n".print_r(strtolower($table), true).":\n".print_r($cqueries, true)."</pre><hr/>";

				// For every field line specified in the query
				foreach ($flds as $fld) {
					// Extract the field name
					preg_match("|^([^ ]*)|", trim($fld), $fvals);
					$fieldname = trim( $fvals[1], '`' );

					// Verify the found field name
					$validfield = true;
					switch (strtolower($fieldname)) {
					case '':
						case 'primary':
							case 'index':
								case 'fulltext':
									case 'unique':
										case 'key':
											$validfield = false;
											$indices[] = trim(trim($fld), ", \n");
											break;
					}
					$fld = trim($fld);

					// If it's a valid field, add it to the field array
					if ($validfield) {
						$cfields[strtolower($fieldname)] = trim($fld, ", \n");
					}
				}

				// Fetch the table column structure from the database
				$tablefields = $imdb->get_results("DESCRIBE {$table};");

				// For every field in the table
				foreach ($tablefields as $tablefield) {
					// If the table field exists in the field array...
					if (array_key_exists(strtolower($tablefield->Field), $cfields)) {
						// Get the field type from the query
						preg_match("|".$tablefield->Field." ([^ ]*( unsigned)?)|i", $cfields[strtolower($tablefield->Field)], $matches);
						$fieldtype = $matches[1];

						// Is actual field type different from the field type in query?
						if ($tablefield->Type != $fieldtype) {
							// Add a query to change the column type
							$cqueries[] = "ALTER TABLE {$table} CHANGE COLUMN {$tablefield->Field} " . $cfields[strtolower($tablefield->Field)];
							$for_update[$table.'.'.$tablefield->Field] = "Changed type of {$table}.{$tablefield->Field} from {$tablefield->Type} to {$fieldtype}";
						}

						// Get the default value from the array
						//echo "{$cfields[strtolower($tablefield->Field)]}<br>";
						if (preg_match("| DEFAULT '(.*)'|i", $cfields[strtolower($tablefield->Field)], $matches)) {
							$default_value = $matches[1];
							if ($tablefield->Default != $default_value) {
								// Add a query to change the column's default value
								$cqueries[] = "ALTER TABLE {$table} ALTER COLUMN {$tablefield->Field} SET DEFAULT '{$default_value}'";
								$for_update[$table.'.'.$tablefield->Field] = "Changed default value of {$table}.{$tablefield->Field} from {$tablefield->Default} to {$default_value}";
							}
						}

						// Remove the field from the array (so it's not added)
						unset($cfields[strtolower($tablefield->Field)]);
					} else {
						// This field exists in the table, but not in the creation queries?
					}
				}

				// For every remaining field specified for the table
				foreach ($cfields as $fieldname => $fielddef) {
					// Push a query line into $cqueries that adds the field to that table
					$cqueries[] = "ALTER TABLE {$table} ADD COLUMN $fielddef";
					$for_update[$table.'.'.$fieldname] = 'Added column '.$table.'.'.$fieldname;
				}

				// Index stuff goes here
				// Fetch the table index structure from the database
				$tableindices = $imdb->get_results("SHOW INDEX FROM {$table};");

				if ($tableindices) {
					// Clear the index array
					unset($index_ary);

					// For every index in the table
					foreach ($tableindices as $tableindex) {
						// Add the index to the index data array
						$keyname = $tableindex->Key_name;
						$index_ary[$keyname]['columns'][] = array('fieldname' => $tableindex->Column_name, 'subpart' => $tableindex->Sub_part);
						$index_ary[$keyname]['unique'] = ($tableindex->Non_unique == 0)?true:false;
					}

					// For each actual index in the index array
					foreach ($index_ary as $index_name => $index_data) {
						// Build a create string to compare to the query
						$index_string = '';
						if ($index_name == 'PRIMARY') {
							$index_string .= 'PRIMARY ';
						} else if($index_data['unique']) {
							$index_string .= 'UNIQUE ';
						}
						$index_string .= 'KEY ';
						if ($index_name != 'PRIMARY') {
							$index_string .= $index_name;
						}
						$index_columns = '';
						// For each column in the index
						foreach ($index_data['columns'] as $column_data) {
							if ($index_columns != '') $index_columns .= ',';
							// Add the field to the column list string
							$index_columns .= $column_data['fieldname'];
							if ($column_data['subpart'] != '') {
								$index_columns .= '('.$column_data['subpart'].')';
							}
						}
						// Add the column list to the index create string
						$index_string .= ' ('.$index_columns.')';
						if (!(($aindex = array_search($index_string, $indices)) === false)) {
							unset($indices[$aindex]);
							//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br />Found index:".$index_string."</pre>\n";
						}
						//else echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">{$table}:<br /><b>Did not find index:</b>".$index_string."<br />".print_r($indices, true)."</pre>\n";
					}
				}

				// For every remaining index specified for the table
				foreach ( (array) $indices as $index ) {
					// Push a query line into $cqueries that adds the index to that table
					$cqueries[] = "ALTER TABLE {$table} ADD $index";
					$for_update[$table.'.'.$fieldname] = 'Added index '.$table.' '.$index;
				}

				// Remove the original table creation query from processing
				unset($cqueries[strtolower($table)]);
				unset($for_update[strtolower($table)]);
			} else {
				// This table exists in the database, but not in the creation queries?
			}
		}
	}

	$allqueries = array_merge($cqueries, $iqueries);
	if ($execute) {
		foreach ($allqueries as $query) {
			//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($query, true)."</pre>\n";
			$imdb->query($query);
		}
	}

	return $for_update;
}

function webim_scan_subdir( $dir ){
	$d = dir( $dir."/" );
	$dn = array();
	while ( false !== ( $f = $d->read() ) ) {
		if(is_dir($dir."/".$f) && $f!='.' && $f!='..') $dn[]=$f;
	}
	$d->close();
	return $dn;
}

function webim_clean_dir( $dir ){
	if(!file_exists($dir)){
		return ;
	}
	$directory = dir($dir);
	while($entry = $directory->read()) {
		$filename = $dir.DIRECTORY_SEPARATOR.$entry;
		if(is_file($filename)) {
			@unlink($filename);
		}
	}
	$directory->close();
}

?>
