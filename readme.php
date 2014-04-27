<?php
/**
 * startupphp
 * @author zhao.binyan
 * @link https://github.com/itbdw/startupphp.git
 * @since 2014-01-11
 */

/*

StartUp PHP

小型项目的启动代码，为你建好了几个常用的文件夹和文件。
特点：无framework代码，最核心的代码就在入口文件！
使用的时候直接复制一份即可。

目的是快速创建高效的站点，因此尽可能的把代码控制权交给了开发人员。

鉴于使用此结构的目的是快速开发小网站，方便转移，减少外部依赖，没有考虑主从等问题，多种数据库问题，甚至把db（sqlite）都放在程序目录了。因此网站部署人员要格外注意权限问题。

个人建议结合 jQuery 和 BootStrap 快速开发。
我们的目标是：没有美工，一样可行。

示例链接：
http://centos/sqlite/?c=demodir_sub
http://centos/sqlite/?c=index
http://centos/sqlite/?c=index&a=index

赵彬言
http://yungbo.com
itbudaoweng@gmail.com
2014年1月11日

*/


/*
v1.0.1
2014-04-26
限制
文件名不可以包含下划线
自动加载规则示例 Model_Admin_User => model/admin/user.php => model/admin_user.php => model_admin_user.php


v1.0
基础结构的搭建

集成了 jQuery 和 BootStrap，版本分别为

 * jQuery JavaScript Library v1.10.2
 * http://jquery.com/
 *
 * Bootstrap v3.0.3 (http://getbootstrap.com)
 * Copyright 2013 Twitter, Inc.

2014年1月11日


*/