-- MySQL dump 10.13  Distrib 5.7.17, for macos10.12 (x86_64)
--
-- Host: localhost    Database: house_oa
-- ------------------------------------------------------
-- Server version	5.6.35

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `tp5_action`
--

DROP TABLE IF EXISTS `tp5_action`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_action` (
  `atype` varchar(36) DEFAULT NULL COMMENT '类型',
  `source` varchar(20) DEFAULT NULL COMMENT '数据来源',
  `ip` varchar(20) DEFAULT NULL COMMENT 'IP地址',
  `description` varchar(200) DEFAULT NULL COMMENT '描述',
  `cid` char(36) DEFAULT NULL COMMENT '录入用户ID',
  `ctime` datetime DEFAULT NULL COMMENT '录入时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_attachment`
--

DROP TABLE IF EXISTS `tp5_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_attachment` (
  `id` char(36) NOT NULL COMMENT '附件ID',
  `linkid` char(36) DEFAULT NULL COMMENT '关联ID',
  `linktable` varchar(50) DEFAULT NULL COMMENT '关联表',
  `suffix` varchar(50) DEFAULT NULL COMMENT '后缀',
  `oldname` varchar(50) DEFAULT NULL COMMENT '源文件名',
  `newname` varchar(50) DEFAULT NULL COMMENT '新文件名',
  `path` varchar(200) DEFAULT NULL COMMENT '路径',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='附件';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_change_rule`
--

DROP TABLE IF EXISTS `tp5_change_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_change_rule` (
  `id` char(36) NOT NULL COMMENT '资产类别ID',
  `status` tinyint(4) DEFAULT '0' COMMENT '状态',
  `atype` char(36) DEFAULT '0' COMMENT '变更规则类型',
  `islike` tinyint(4) DEFAULT '0' COMMENT '是否模糊匹配',
  `oldid` char(50) DEFAULT NULL COMMENT '原类型ID',
  `newid` char(50) DEFAULT NULL COMMENT '新类型ID',
  `description` varchar(2000) DEFAULT NULL COMMENT '规则',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据变更规则';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_city`
--

DROP TABLE IF EXISTS `tp5_city`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_city` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `pcode` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(4) DEFAULT '0' COMMENT '态状',
  `description` varchar(200) DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='行政区域地州市信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_customer`
--

DROP TABLE IF EXISTS `tp5_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_customer` (
  `id` char(36) NOT NULL COMMENT 'ID',
  `code` varchar(20) DEFAULT '' COMMENT '编号',
  `name` varchar(50) DEFAULT '' COMMENT '姓名',
  `atype` char(36) DEFAULT '' COMMENT '类型',
  `btype` varchar(36) DEFAULT '' COMMENT '客户池',
  `itemid` char(36) DEFAULT '' COMMENT '项目ID',
  `source` char(36) DEFAULT '' COMMENT '来源',
  `level` char(36) DEFAULT '' COMMENT '客户等级',
  `sex` tinyint(4) DEFAULT '0' COMMENT '性别',
  `tel` varchar(50) DEFAULT '' COMMENT '电话',
  `email` varchar(100) DEFAULT '' COMMENT '邮箱',
  `qq` varchar(20) DEFAULT '' COMMENT '微QQ',
  `wechat` varchar(50) DEFAULT '' COMMENT '微信',
  `description` varchar(200) DEFAULT '' COMMENT '描述',
  `orgid` char(36) DEFAULT '' COMMENT '所属组织',
  `aid` char(36) DEFAULT '' COMMENT '归属用户ID',
  `cid` char(36) DEFAULT '' COMMENT '录入用户ID',
  `ctime` datetime DEFAULT NULL COMMENT '录入时间',
  `uid` char(36) DEFAULT '' COMMENT '最后修改用户ID',
  `utime` datetime DEFAULT NULL COMMENT '最后修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_district`
--

DROP TABLE IF EXISTS `tp5_district`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_district` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `pid` int(11) NOT NULL DEFAULT '0',
  `pcode` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(4) DEFAULT '0' COMMENT '态状',
  `description` varchar(200) DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='行政区域县区信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_followup`
--

DROP TABLE IF EXISTS `tp5_followup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_followup` (
  `id` char(36) NOT NULL COMMENT 'ID',
  `code` varchar(20) DEFAULT '' COMMENT '编号',
  `atype` char(36) DEFAULT '' COMMENT '类型',
  `hid` char(36) DEFAULT '' COMMENT '客户ID',
  `orgid` char(36) DEFAULT '' COMMENT '所属组织',
  `fid` char(36) DEFAULT '' COMMENT '跟进用户ID',
  `ftime` datetime DEFAULT NULL COMMENT '跟进时间',
  `is_update` tinyint(4) DEFAULT '2' COMMENT '是否允许修改 1允许 2不允许',
  `description` varchar(200) DEFAULT '' COMMENT '描述',
  `cid` char(36) DEFAULT '' COMMENT '录入用户ID',
  `ctime` datetime DEFAULT NULL COMMENT '录入时间',
  `uid` char(36) DEFAULT '' COMMENT '最后修改用户ID',
  `utime` datetime DEFAULT NULL COMMENT '最后修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户跟进信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_icon`
--

DROP TABLE IF EXISTS `tp5_icon`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_icon` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `code` varchar(50) DEFAULT NULL COMMENT '编码',
  `name` varchar(50) DEFAULT NULL COMMENT '名称',
  `pid` int(11) DEFAULT '0' COMMENT 'PID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=302 DEFAULT CHARSET=utf8 COMMENT='ICON';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_item`
--

DROP TABLE IF EXISTS `tp5_item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_item` (
  `id` char(36) NOT NULL COMMENT '项目ID',
  `status` tinyint(4) DEFAULT '0' COMMENT '态状',
  `itemtype` char(36) DEFAULT NULL COMMENT '类型',
  `itemname` varchar(50) DEFAULT NULL COMMENT '名称',
  `provinceid` int(11) DEFAULT '0' COMMENT '省ID',
  `cityid` int(11) DEFAULT '0' COMMENT '市ID',
  `districtid` int(11) DEFAULT '0' COMMENT '区域ID',
  `address` varchar(200) DEFAULT NULL COMMENT '地址',
  `developer` varchar(100) DEFAULT NULL COMMENT '开发商',
  `url` varchar(400) DEFAULT '',
  `description` varchar(200) DEFAULT NULL COMMENT '描述',
  `cid` char(36) DEFAULT NULL COMMENT '录入用户ID',
  `ctime` datetime DEFAULT NULL COMMENT '录入时间',
  `uid` char(36) DEFAULT NULL COMMENT '最后修改用户ID',
  `utime` datetime DEFAULT NULL COMMENT '最后修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_item_other`
--

DROP TABLE IF EXISTS `tp5_item_other`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_item_other` (
  `id` char(36) DEFAULT NULL COMMENT '项目ID',
  `area` varchar(128) DEFAULT NULL COMMENT '销售面积',
  `price` varchar(128) DEFAULT NULL COMMENT '均价',
  `type1` mediumtext NOT NULL COMMENT '类型',
  `type2` mediumtext NOT NULL COMMENT '销售情况',
  `type3` mediumtext NOT NULL COMMENT '性质',
  `type4` mediumtext NOT NULL COMMENT '装修标准',
  `note1` mediumtext NOT NULL COMMENT '优惠政策',
  `note2` mediumtext NOT NULL COMMENT '介绍',
  `note3` mediumtext NOT NULL COMMENT '优势',
  `note4` mediumtext NOT NULL COMMENT '详情',
  `note5` mediumtext NOT NULL COMMENT '配套',
  `note6` mediumtext NOT NULL COMMENT '交通'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='项目信息-详细';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_menu`
--

DROP TABLE IF EXISTS `tp5_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_menu` (
  `id` char(36) NOT NULL COMMENT '菜单ID',
  `pid` char(36) DEFAULT '0' COMMENT '上级菜单ID',
  `status` tinyint(4) DEFAULT '0' COMMENT '态状',
  `msort` int(5) DEFAULT '0' COMMENT '序排ID',
  `menuname` varchar(20) DEFAULT NULL COMMENT '模块名称',
  `menuicon` varchar(50) DEFAULT NULL COMMENT '菜单图标',
  `menuurl` varchar(100) DEFAULT NULL COMMENT '连接地址',
  `description` varchar(200) DEFAULT NULL COMMENT '描述',
  `cid` char(36) DEFAULT NULL COMMENT '录入用户ID',
  `ctime` datetime DEFAULT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_organizational`
--

DROP TABLE IF EXISTS `tp5_organizational`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_organizational` (
  `id` char(36) NOT NULL COMMENT 'ID',
  `code` varchar(20) DEFAULT NULL COMMENT '编号',
  `name` varchar(50) DEFAULT NULL COMMENT '名称',
  `status` tinyint(4) DEFAULT '0' COMMENT '态状',
  `pid` char(36) DEFAULT NULL COMMENT '父ID',
  `sign` int(5) DEFAULT '0' COMMENT '标识 9客户归属团队',
  `msort` int(5) DEFAULT '0' COMMENT '序排ID',
  `description` varchar(200) DEFAULT NULL COMMENT '描述',
  `cid` char(36) DEFAULT NULL COMMENT '录入用户ID',
  `ctime` datetime DEFAULT NULL COMMENT '录入时间',
  `uid` char(36) DEFAULT NULL COMMENT '最后修改用户ID',
  `utime` datetime DEFAULT NULL COMMENT '最后修改时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='组织信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_power`
--

DROP TABLE IF EXISTS `tp5_power`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_power` (
  `id` char(36) NOT NULL COMMENT '特殊权限ID',
  `code` varchar(50) DEFAULT NULL COMMENT '编码',
  `name` varchar(50) DEFAULT NULL COMMENT '名称',
  `status` tinyint(4) DEFAULT '0' COMMENT '态状',
  `description` varchar(200) DEFAULT NULL COMMENT '描述',
  `cid` char(36) DEFAULT NULL COMMENT '录入用户ID',
  `ctime` datetime DEFAULT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='特殊权限信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_province`
--

DROP TABLE IF EXISTS `tp5_province`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_province` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` tinyint(4) DEFAULT '0' COMMENT '态状',
  `description` varchar(200) DEFAULT '' COMMENT '描述',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8 COMMENT='省份信息表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_role`
--

DROP TABLE IF EXISTS `tp5_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_role` (
  `id` char(36) NOT NULL COMMENT '角色ID',
  `status` tinyint(4) DEFAULT '0' COMMENT '态状',
  `rolename` varchar(50) DEFAULT NULL COMMENT '名称',
  `description` varchar(200) DEFAULT NULL COMMENT '描述',
  `cid` char(36) DEFAULT NULL COMMENT '录入用户ID',
  `ctime` datetime DEFAULT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_role_menu`
--

DROP TABLE IF EXISTS `tp5_role_menu`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_role_menu` (
  `roleid` char(36) DEFAULT '' COMMENT '角色ID',
  `menuid` char(36) DEFAULT '' COMMENT '菜单ID',
  `a` tinyint(4) DEFAULT '0' COMMENT '增权限',
  `d` tinyint(4) DEFAULT '0' COMMENT '删权限',
  `s` tinyint(4) DEFAULT '0' COMMENT '查权限',
  `e` tinyint(4) DEFAULT '0' COMMENT '改权限'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_role_power`
--

DROP TABLE IF EXISTS `tp5_role_power`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_role_power` (
  `roleid` char(36) DEFAULT '' COMMENT '角色ID',
  `powerid` char(36) DEFAULT '' COMMENT '特殊权限ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色特殊权限信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_user`
--

DROP TABLE IF EXISTS `tp5_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_user` (
  `id` char(36) NOT NULL COMMENT '菜单ID',
  `status` tinyint(4) DEFAULT '0' COMMENT '态状',
  `usercode` varchar(20) DEFAULT NULL COMMENT '编号',
  `username` varchar(50) DEFAULT NULL COMMENT '姓名',
  `password` char(32) DEFAULT NULL COMMENT '密码',
  `nickname` varchar(50) DEFAULT NULL COMMENT '昵称',
  `teamid` char(36) DEFAULT NULL COMMENT '团队',
  `tel` varchar(50) DEFAULT NULL COMMENT '电话',
  `intime` datetime DEFAULT NULL COMMENT '入职时间',
  `outtime` datetime DEFAULT NULL COMMENT '离职时间',
  `description` varchar(200) DEFAULT NULL COMMENT '描述',
  `isdelete` tinyint(4) DEFAULT '0' COMMENT '是否删除 1正常 2删除',
  `cid` char(36) DEFAULT NULL COMMENT '录入用户ID',
  `ctime` datetime DEFAULT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_user_org`
--

DROP TABLE IF EXISTS `tp5_user_org`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_user_org` (
  `userid` char(36) DEFAULT '' COMMENT '用户ID',
  `orgid` char(36) DEFAULT '' COMMENT '组织ID',
  `atype` tinyint(4) DEFAULT '1' COMMENT '类型'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_user_other`
--

DROP TABLE IF EXISTS `tp5_user_other`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_user_other` (
  `userid` char(36) DEFAULT NULL COMMENT '用户ID',
  `idcard` varchar(50) DEFAULT NULL COMMENT '身份证',
  `sex` tinyint(4) DEFAULT '0' COMMENT '性别',
  `birthday` datetime DEFAULT NULL COMMENT '生日',
  `email` varchar(100) DEFAULT NULL COMMENT '邮箱',
  `qq` varchar(20) DEFAULT NULL COMMENT '微QQ',
  `wechat` varchar(50) DEFAULT NULL COMMENT '微信',
  `bank` varchar(50) DEFAULT NULL COMMENT '开户行',
  `accountholder` varchar(50) DEFAULT NULL COMMENT '开户人',
  `bankaccount` varchar(50) DEFAULT NULL COMMENT '开户账号',
  `note` varchar(200) DEFAULT NULL COMMENT '备注',
  `lastip` varchar(45) DEFAULT NULL COMMENT '最后登录IP',
  `lasttime` datetime DEFAULT NULL COMMENT '最后登录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户其它信息';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_user_role`
--

DROP TABLE IF EXISTS `tp5_user_role`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_user_role` (
  `userid` char(36) DEFAULT '' COMMENT '用户ID',
  `roleid` char(36) DEFAULT '' COMMENT '角色ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tp5_wordbook`
--

DROP TABLE IF EXISTS `tp5_wordbook`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tp5_wordbook` (
  `id` char(36) NOT NULL COMMENT '数据字典ID',
  `pid` char(36) DEFAULT '0' COMMENT '上级数据字典ID',
  `name` varchar(50) DEFAULT '' COMMENT '内容',
  `status` tinyint(4) DEFAULT '0' COMMENT '态状',
  `msort` int(5) DEFAULT '0' COMMENT '序排ID',
  `temp1` tinyint(4) DEFAULT '0' COMMENT '弹性值',
  `description` varchar(200) DEFAULT '' COMMENT '描述',
  `cid` char(36) DEFAULT '' COMMENT '录入用户ID',
  `ctime` datetime DEFAULT NULL COMMENT '录入时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='数据字典';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-03-25 15:57:12
