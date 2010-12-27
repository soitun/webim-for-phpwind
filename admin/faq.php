<?php

require_once( dirname( __FILE__ ) . '/' . 'admin_common.php' );
require_once( 'markdown.php' );

$text = file_get_contents( '../FAQ.md' );
$html =  Markdown( $text );
$html = preg_replace( '/<h1.*?h1>/i', "", $html );

echo webim_header( '常见问题' );
echo webim_menu( 'faq' );
echo '<div id="content">';
echo $html;
$html_template = htmlspecialchars( $im_template );
$html_queries = htmlspecialchars( $im_queries );
$html_template_files = htmlspecialchars( implode( ", ", $im_template_files ) );
$html_body = htmlspecialchars( '</body>' );
$db_name = $_IMC['dbname'];
echo <<<EOF
<h2 id="install">WebIM安装时做了那些操作?</h2>
<p class="notice">如果出现安装失败或者需要手动安装可手动执行下面三个步骤</p>
<ol>
<li><p>在模板文件( $html_template_files  )中的{$html_body}标签之前插入如下代码:</p>
<pre>$html_template</pre></li>
<li><p>在数据库{$db_name}中创建数据库</p>
<pre>$html_queries</pre></li>
<li><p>根据( $im_config_sample_file ) 说明创建配置文件 ( $im_config_file ) </p></li>
</ol>
EOF;
echo '</div>';

echo webim_footer();

?>

