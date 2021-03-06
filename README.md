PHP-HouseOA 1.0
===============

PHP-HouseOA项目是本人为某小型公司开发的系统，该公司主要经营范围为新房交易，目前任在运行。本项目主要包含新房项目展示站点和后台管理站点两个系统，如果稍微对技术有一定水平的人，直接配置之后就能正常使用。

这次开源出部分功能，主要是为了同行之间的交流。

## 

### 访问路径
 + 后端：../PHP-HouseOA/public/index
 + 前端：../PHP-HouseOA/public/html/

### 主要使用技术 

 + 前端框架：h-ui
 + 后端框架：ThinkPHP 5
 + 数据库：MySql 
 
### 测试环境配置

 + php 7.1.1
 + mysql 5.5.52

### 目录结构

项目使用ThinkPHP5框架进行开发，所以主结构文件目录在此就不多做介绍，具体可以查看ThinkPHP5技术文档。以下介绍的功能目录结构：

~~~
PHP-HouseOA
├─app           应用目录
│  ├─api        接口模块
│  ├─base       基础功能模块
│  ├─common     公共模块
│  ├─customer   客户模块
│  ├─db         多数据库配置模块
│  ├─extra      配置文件模块
│  ├─finance    财务模块
│  ├─index      主要入口模块
│  ├─item       新房维护模块
│  ├─report     统计报表模块
│  ├─search     公共查询模块
│  ├─sysActiom  系统自动任务模块
│  ├─warehouse  数据仓库模块
│  ├─web        新房发布模块
│  └─work       工作业务模块
│
├─extend        扩展功能目录
│
├─public        WEB目录（对外访问目录）
│  ├─index.php  入口文件
│  └─html       公司官网目录
~~~

从文件目录上看，此项目使用的是模块化开发，一切访问都通过index模块进行，由index模块分别调用其他业务模块从而实现相关功能。
常用且不经常变化的数据基本都通过缓存实现。

##

### 新房项目展示站点
公司官网，功能包括：

 + 公司介绍
 + 新房项目展示
 + 客户在线报名

### 后台管理站点
管理后台，功能包括：

 + 基础数据维护
 + 用户及用户权限控制
 + 新房项目维护及发布
 + 客户信息录入及日常跟进
 + 企业收支管理 [不在开源系统中体现]
 + 日常业务管理 [不在开源系统中体现]
 + 交易业务管理 [不在开源系统中体现]
 + 发票业务管理 [不在开源系统中体现]
 + 基础统计报表 [不在开源系统中体现]

##

### 特别说明

关于项目中使用的技术基本都是基础技术，仅为实用而已。

### 联系方式

本项目主要用于学习和交流，本人联系方式如下：

 + 微信：SamUncle7
 + 邮箱：bingchuan0203@163.com
 + QQ：1097218457
 + Q群：已满 [如有需要，以后再建]