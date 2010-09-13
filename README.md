WebIM For PHPWind
================================================================

为PHPWind提供的在线及时聊天插件.

升级
-----------------------------

*	直接覆盖webim目录即可

安装
-----------------------------

为了和bbs同步历史记录,安装时会添加历史记录数据库到ucenter数据,请确保ucenter数据库在您所安装的服务器并且可以链接.

首先将下载文件解压到UChome根目录

	.
	|-- webim
	|   |-- README.md
	|   |-- static

给与安装文件权限

	chmod 777 webim
	chmod -R 777 webim/install/

###线上安装

1.	浏览器打开webim安装页面。例： uchome地址(http://www.uc.com/home/index.php) -> webim安装地址(http://www.uc.com/home/webim/install.php)

2.	配置域名，apikey确认

3.	安装完成


###手动安装

1.	配置WebIM，将`webim/install/config.php`复制到`webim/config.php`，配置相应参数;
2.	安装数据库，修改`webim/install/install.sql`中`@charset`为uchome中配置的`UC_DBCHARSET`，修改`webim_`为uchome中配置`UC_DBTABLEPRE`加`webim_`，在uchome中导入此数据库;

3.	加载WebIM配置，在UChome配置文件(home/config.php)中添加`@include_once('webim/config.php');`;

4.	加入模版，将`webim/install/webim_uchome.htm`复制到模版目录内，默认模版目录问`home/template/default/`
5.	加载模版，在模版footer文件`home/template/default/footer.htm`中`</body>`之前添加`<!--{template webim_uchome}-->`;

6.	清除uchome模版缓存，删除`home/data/tpl_cache/`中的所有文件;

卸载
--------------------------------------------

###线上卸载

浏览器访问 /webim/uninstall.php

