-- MySQL dump 10.13  Distrib 5.7.17, for macos10.12 (x86_64)
--
-- Host: localhost    Database: house_oa_warehouse
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
-- Table structure for table `x_customer`
--

DROP TABLE IF EXISTS `x_customer`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x_customer` (
  `id` char(36) NOT NULL COMMENT 'ID',
  `name` varchar(64) NOT NULL DEFAULT '' COMMENT '客户姓名',
  `phone` varchar(64) NOT NULL DEFAULT '' COMMENT '客户电话',
  `address` varchar(128) NOT NULL DEFAULT '' COMMENT '地址',
  `note` varchar(256) NOT NULL DEFAULT '' COMMENT '备注',
  `sign` varchar(64) NOT NULL DEFAULT '' COMMENT '来源渠道标识',
  `itemid` varchar(64) NOT NULL DEFAULT '' COMMENT '项目ID',
  `itemname` varchar(255) NOT NULL DEFAULT '' COMMENT '项目',
  `device` varchar(64) NOT NULL DEFAULT '' COMMENT '设备',
  `brand` varchar(64) NOT NULL DEFAULT '' COMMENT '设备品牌',
  `ip` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP',
  `isp` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP归属(运营商)',
  `country` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP归属(国)',
  `province` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP归属(省)',
  `city` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP归属(市)',
  `district` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP归属(区)',
  `ctime` int(10) unsigned DEFAULT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户表(各渠道提交)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `x_customer_other`
--

DROP TABLE IF EXISTS `x_customer_other`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `x_customer_other` (
  `id` char(36) NOT NULL COMMENT 'ID',
  `thisweburl` mediumtext NOT NULL COMMENT '当前网址',
  `fromweburl` mediumtext NOT NULL COMMENT '上一级网址',
  `useragent` mediumtext NOT NULL COMMENT '浏览器及设备信息'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='客户表(其它信息)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `z_visit`
--

DROP TABLE IF EXISTS `z_visit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `z_visit` (
  `id` char(36) NOT NULL COMMENT 'ID',
  `item` varchar(255) NOT NULL DEFAULT '' COMMENT '项目',
  `ctime` int(10) unsigned DEFAULT NULL COMMENT '访问时间',
  `ip` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP',
  `isp` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP归属(运营商)',
  `country` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP归属(国)',
  `province` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP归属(省)',
  `city` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP归属(市)',
  `district` varchar(64) NOT NULL DEFAULT '' COMMENT 'IP归属(区)',
  `searchengines` varchar(64) NOT NULL DEFAULT '' COMMENT '搜索引擎',
  `keyword` varchar(255) NOT NULL DEFAULT '' COMMENT '搜索词',
  `device` varchar(64) NOT NULL DEFAULT '' COMMENT '设备',
  `brand` varchar(64) NOT NULL DEFAULT '' COMMENT '设备品牌',
  `os` varchar(64) NOT NULL DEFAULT '' COMMENT '设备系统',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '网页',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='访问记录表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `z_visit_other`
--

DROP TABLE IF EXISTS `z_visit_other`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `z_visit_other` (
  `id` char(36) NOT NULL COMMENT 'ID',
  `thisurl` varchar(255) NOT NULL DEFAULT '' COMMENT '访问页面',
  `fromurl` varchar(4096) NOT NULL DEFAULT '' COMMENT '来源页面',
  `useragent` varchar(1024) NOT NULL DEFAULT '' COMMENT 'Http包头'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='访问记录表-附表';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-03-25 15:57:57
