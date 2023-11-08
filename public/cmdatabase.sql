-- MySQL dump 10.13  Distrib 5.5.62, for Linux (aarch64)
--
-- Host: localhost    Database: www_ssladmin_cm
-- ------------------------------------------------------
-- Server version	5.5.62-log

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
-- Table structure for table `cm_admin`
--

DROP TABLE IF EXISTS `cm_admin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `username` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `nickname` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '昵称',
  `password` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '头像',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '电子邮箱',
  `loginfailure` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '失败次数',
  `logintime` int(10) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '登录IP',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(59) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Session标识',
  `status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='管理员表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_admin`
--

LOCK TABLES `cm_admin` WRITE;
/*!40000 ALTER TABLE `cm_admin` DISABLE KEYS */;
INSERT INTO `cm_admin` VALUES (1,'admin','Admin','345b21df14de73f5ab9593ee8350ee6e','5gx1lg','/static/images/avatar.png','admin@admin.com',0,1600298414,'123.196.11.216',1492186163,1601174630,'04e8afb9-646c-4a41-8452-cd6330c3232b','normal');
/*!40000 ALTER TABLE `cm_admin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_auth_group`
--

DROP TABLE IF EXISTS `cm_auth_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_auth_group` (
  `id` int(8) unsigned NOT NULL AUTO_INCREMENT,
  `createtime` int(11) NOT NULL,
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` varchar(30) DEFAULT 'normal' COMMENT '状态',
  `title` varchar(255) NOT NULL DEFAULT '' COMMENT '角色组',
  `rules` text COMMENT '权限',
  `remark` text COMMENT '备注',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='角色组管理';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_auth_group`
--

LOCK TABLES `cm_auth_group` WRITE;
/*!40000 ALTER TABLE `cm_auth_group` DISABLE KEYS */;
INSERT INTO `cm_auth_group` VALUES (1,1601170339,1601172768,'normal','超级管理员','536,556,553,554,555,537,557,538,541,547,548,549,550,542,','超级管理员'),(4,1601170436,1601181256,'normal','插件管理员','0,536,538,541,547,548,549,550,542,','这里是备注');
/*!40000 ALTER TABLE `cm_auth_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_auth_group_access`
--

DROP TABLE IF EXISTS `cm_auth_group_access`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_auth_group_access` (
  `uid` mediumint(8) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  `createtime` int(11) DEFAULT '0' COMMENT '添加时间',
  `updatetime` int(11) DEFAULT '0' COMMENT '修改时间',
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  KEY `uid` (`uid`),
  KEY `group_id` (`group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_auth_group_access`
--

LOCK TABLES `cm_auth_group_access` WRITE;
/*!40000 ALTER TABLE `cm_auth_group_access` DISABLE KEYS */;
INSERT INTO `cm_auth_group_access` VALUES (1,1,0,0),(2,4,0,0);
/*!40000 ALTER TABLE `cm_auth_group_access` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_auth_rule`
--

DROP TABLE IF EXISTS `cm_auth_rule`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL DEFAULT '1',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(50) NOT NULL DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) NOT NULL DEFAULT 'fa-circle-o' COMMENT '图标',
  `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为菜单',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(10) DEFAULT NULL COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) NOT NULL DEFAULT 'normal' COMMENT '状态',
  `auth_open` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE,
  KEY `pid` (`pid`),
  KEY `weigh` (`weigh`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COMMENT='节点表';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_auth_rule`
--

LOCK TABLES `cm_auth_rule` WRITE;
/*!40000 ALTER TABLE `cm_auth_rule` DISABLE KEYS */;
INSERT INTO `cm_auth_rule` VALUES (1,1,0,'admin/Auth','权限管理','fa-group','','',1,1601167936,NULL,1,'normal',1),(2,1,1,'admin/index','人员管理','fa-group','','',1,NULL,1601167961,2,'normal',1),(3,1,1,'AuthGroup/index','角色管理','fa-group','','',1,1601168043,NULL,100,'normal',1),(4,1,6,'AuthRule/add','菜单添加','fa-add','','这里是备注',0,1601122750,NULL,100,'normal',1),(5,1,6,'AuthRule/edit','菜单编辑','fa-edit','','',0,1601122878,1601123339,100,'normal',1),(6,1,1,'AuthRule/index','菜单管理','fa-bars','','',1,NULL,1601167951,1,'normal',1),(7,1,0,'general.Config/index','系统设置','fa-gear','','',1,NULL,NULL,16,'normal',1),(8,1,0,'index/welcome','控制台','fa-dashboard','','',1,NULL,NULL,0,'normal',1),(9,1,12,'ssl_ca/add','添加','','','',0,1636198018,1636198018,0,'normal',1),(10,1,12,'ssl_ca/del','删除','','','',0,1636198018,1636198018,0,'normal',1),(11,1,12,'ssl_ca/edit','编辑 ','','','',0,1636198018,1636198018,0,'normal',1),(12,1,0,'ssl_ca/index','签发机构','fa-book','','',1,1636198018,1636198163,0,'normal',1),(13,1,16,'ssl_cert/add','添加','','','',0,1636215412,1636215412,0,'normal',1),(14,1,16,'ssl_cert/del','删除','','','',0,1636215412,1636215412,0,'normal',1),(15,1,16,'ssl_cert/edit','编辑 ','','','',0,1636215412,1636215412,0,'normal',1),(16,1,0,'ssl_cert/index','签发证书','fa-book','','',1,1636215412,1636215459,0,'normal',1);
/*!40000 ALTER TABLE `cm_auth_rule` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_config`
--

DROP TABLE IF EXISTS `cm_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量名',
  `group` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '分组',
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量标题',
  `tip` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `value` text COLLATE utf8mb4_unicode_ci COMMENT '变量值',
  `content` text COLLATE utf8mb4_unicode_ci COMMENT '变量字典数据',
  `rule` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '扩展属性',
  `setting` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '配置',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='系统配置';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_config`
--

LOCK TABLES `cm_config` WRITE;
/*!40000 ALTER TABLE `cm_config` DISABLE KEYS */;
INSERT INTO `cm_config` VALUES (1,'name','basic','站点名称','请填写站点名称','string','自签证书管理器','','required','class=\"layui-input\"',NULL),(2,'beian','basic','备案号','粤ICP备15000000号-1','string','','','','class=\"layui-input\"',NULL),(3,'digest_alg','certificate','加密方式','','string','sha256','{\"1\":\"\"}','','class=\"layui-input\"',NULL),(4,'private_key_bits','certificate','加密字节','','string','2048','{\"1\":\"\"}','','class=\"layui-input\"',NULL),(5,'private_key_type','certificate','加密类型','','string','OPENSSL_KEYTYPE_RSA','{\"1\":\"\"}','','class=\"layui-input\"',NULL),(6,'randstr','certificate','字符组合','','string','abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789','{\"1\":\"\"}','','class=\"layui-input\"',NULL),(7,'serialbit','certificate','序列位数','','string','1','{\"1\":\"\"}','','class=\"layui-input\"',NULL),(8,'document','certificate','存放路径','','string','/document','{\"1\":\"\"}','','class=\"layui-input\"',NULL),(9,'crt','certificate','机构链接','','string','http://www.btpanel.cm/ssl/BTPanelCMInnRootCA.crt','{\"1\":\"\"}','','class=\"layui-input\"',NULL),(10,'crl','certificate','吊销链接','','string','http://www.btpanel.cm/ssl/BTPanelCMInnRootCA.crl','{\"1\":\"\"}','','class=\"layui-input\"',NULL),(11,'CPS','certificate','CPS链接','','string','http://www.btpanel.cm/BTPanelCMInnRootCACPS','{\"1\":\"\"}','','class=\"layui-input\"',NULL);
/*!40000 ALTER TABLE `cm_config` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_ssl_ca`
--

DROP TABLE IF EXISTS `cm_ssl_ca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_ssl_ca` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '机构编号',
  `ssl_ca_id` int(11) NOT NULL COMMENT '签发机构',
  `serial` text NOT NULL COMMENT '序列编号',
  `name` text NOT NULL COMMENT '公共名称',
  `endtime` int(11) NOT NULL COMMENT '过期时间',
  `switch` text NOT NULL COMMENT '机构开关',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签发机构';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_ssl_ca`
--

LOCK TABLES `cm_ssl_ca` WRITE;
/*!40000 ALTER TABLE `cm_ssl_ca` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_ssl_ca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cm_ssl_cert`
--

DROP TABLE IF EXISTS `cm_ssl_cert`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cm_ssl_cert` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '证书编号',
  `ssl_ca_id` int(11) NOT NULL COMMENT '签发机构',
  `serial` text NOT NULL COMMENT '序列编号',
  `name` text NOT NULL COMMENT '申请域名',
  `endtime` int(11) NOT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签发证书';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cm_ssl_cert`
--

LOCK TABLES `cm_ssl_cert` WRITE;
/*!40000 ALTER TABLE `cm_ssl_cert` DISABLE KEYS */;
/*!40000 ALTER TABLE `cm_ssl_cert` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'www_ssladmin_cm'
--

--
-- Dumping routines for database 'www_ssladmin_cm'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2021-11-07  2:29:19
