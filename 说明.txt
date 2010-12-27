WebIM For PHPWind
================================================================

为PHPWind提供的在线及时聊天插件，更新内容请查看 CHANGELOG.md


需求
-----------------------------

*	MySQL版本不低于4.1.2
*	需要PHP版本不低于4.3
*	PHP访问外部网络，WebIM连接时需要访问WebIM服务器, 请确保您的php环境是否可连接外部网络, 设置php.ini中`allow_url_fopen=ON`.


升级
-----------------------------

1.	覆盖新版内容到webim目录，浏览器打开webim管理地址( phpwind地址/webim/ )会自动执行升级脚本


安装
-----------------------------

首先将下载文件解压到PHPWind根目录

	.
	|-- webim
	|   |-- README.md
	|   |-- static

给与安装目录可写权限

	chmod 777 webim

1.	浏览器打开webim安装页面。例： PHPWind地址(http://www.phpwind.com/home/) -> webim安装地址(http://www.phpwind.com/home/webim/)

2.	配置域名，apikey确认

3.	安装完成


im服务器
---------------------------

国内

服务器: im.webim20.cn 端口: 8000
服务器: im.webim20.cn 端口: 80

国外

服务器: im.webim.us 端口: 8000
服务器: im.webim.us 端口: 80

