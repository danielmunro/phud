-- MySQL dump 10.11
--
-- Host: localhost    Database: mud
-- ------------------------------------------------------
-- Server version	5.0.83-0ubuntu3

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
-- Table structure for table `doors`
--

DROP TABLE IF EXISTS `doors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doors` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `short_desc` varchar(32) NOT NULL,
  `long_desc_room1` varchar(255) NOT NULL,
  `fk_unlock_item_id` int(10) unsigned default NULL,
  `fk_room1_id` int(10) unsigned NOT NULL,
  `fk_room2_id` int(10) unsigned NOT NULL,
  `direction1` varchar(5) NOT NULL,
  `direction2` varchar(5) NOT NULL,
  `disposition` enum('locked','closed','open') default NULL,
  `nouns` varchar(255) NOT NULL,
  `hidden` tinyint(1) NOT NULL default '0',
  `hidden_show_command` varchar(255) default NULL,
  `hidden_action` varchar(255) NOT NULL,
  `fk_hidden_item_id` int(10) unsigned default NULL,
  `reload_ticks` int(1) NOT NULL default '5',
  `default_disposition` enum('locked','closed','open') NOT NULL default 'closed',
  `long_desc_room2` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doors`
--

LOCK TABLES `doors` WRITE;
/*!40000 ALTER TABLE `doors` DISABLE KEYS */;
INSERT INTO `doors` VALUES (1,'a large steel grate','A large, rusted steel grate is here in the floor. The slats are too narrow to see through, and there is a handle to open it.',NULL,5,6,'down','up','closed','grate',1,'move rug','You move a tattered rug to reveal a steel grate.',14,3,'closed','A large, rusted steel grate in the ceiling lets ribbons of light in from the bar upstairs.');
/*!40000 ALTER TABLE `doors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `environment`
--

DROP TABLE IF EXISTS `environment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `environment` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `environment_type` int(10) unsigned NOT NULL,
  `command` varchar(255) NOT NULL,
  `fk_table` varchar(64) NOT NULL,
  `fk_table_id` int(10) unsigned NOT NULL,
  `fk_room_id` int(10) unsigned NOT NULL,
  `message` varchar(255) NOT NULL,
  `look_describe` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `environment`
--

LOCK TABLES `environment` WRITE;
/*!40000 ALTER TABLE `environment` DISABLE KEYS */;
INSERT INTO `environment` VALUES (1,1,'move rug','doors',1,5,'A drab rug is laid out over a corner of the bar.','A plain, drab-looking rug is in the corner. It looks like something might be underneath. It can be moved.');
/*!40000 ALTER TABLE `environment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventories`
--

DROP TABLE IF EXISTS `inventories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `fk_table` varchar(32) NOT NULL,
  `fk_table_id` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventories`
--

LOCK TABLES `inventories` WRITE;
/*!40000 ALTER TABLE `inventories` DISABLE KEYS */;
INSERT INTO `inventories` VALUES (1,'users',1),(2,'bag',1),(7,'room',1),(6,'room',5),(8,'room',2);
/*!40000 ALTER TABLE `inventories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `short_desc` varchar(255) NOT NULL,
  `long_desc` varchar(255) NOT NULL,
  `nouns` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  `weight` float(4,1) NOT NULL,
  `item_condition` int(3) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `can_own` tinyint(1) NOT NULL,
  `equipment_position` varchar(24) NOT NULL,
  `verb` varchar(64) NOT NULL,
  `nourishment` int(1) default NULL,
  `fk_inv_inside_id` int(10) unsigned NOT NULL,
  `fk_inventory_id` int(10) unsigned NOT NULL,
  `thirst` int(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,'a small leather satchel','A small leather satchel lies here, looking perfect to store items in.','small leather satchel',32,2.0,100,'bag',1,'0','',0,1,2,NULL),(14,'a tattered rug','A tattered rug is here, worn by ages of use. It looks like it can be moved.','tattered rug',0,0.0,0,'rug',0,'','',NULL,6,0,NULL),(16,'a modest fountain','A small, marble fountain lies in the middle of the courtyard.','marble fountain',0,0.0,0,'fountain',0,'','',NULL,7,0,5),(35,'a map of Old Thalos','a map of Old Thalos','map thalos',1,0.0,100,'map',1,'hands','',NULL,2,0,NULL),(22,'a flask','A bottomless flask of water.','flask water',50,1.0,100,'drink',1,'','',NULL,1,0,5),(27,'a pumpkin pie','A delicious pumpkin pie lies here.','pumpkin pie',4,1.0,100,'food',1,'','',5,1,0,NULL),(28,'a pumpkin pie','A delicious pumpkin pie lies here.','pumpkin pie',4,1.0,100,'food',1,'','',5,1,0,NULL),(30,'a map of Midgaard','a map of Midgaard','map midgaard',1,0.0,100,'map',1,'hands','',NULL,2,0,NULL),(31,'a pumpkin pie','A delicious pumpkin pie lies here.','pumpkin pie',4,1.0,100,'food',1,'','',5,1,0,NULL),(32,'a pumpkin pie','A delicious pumpkin pie lies here.','pumpkin pie',4,1.0,100,'food',1,'','',5,1,0,NULL),(33,'a pumpkin pie','A delicious pumpkin pie lies here.','pumpkin pie',4,1.0,100,'food',1,'','',5,1,0,NULL),(34,'a pumpkin pie','A delicious pumpkin pie lies here.','pumpkin pie',4,1.0,100,'food',1,'','',5,1,0,NULL);
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `skills`
--

DROP TABLE IF EXISTS `skills`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `skills` (
  `skill` varchar(32) NOT NULL,
  `user_alias` varchar(32) NOT NULL,
  `percent` int(3) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `skills`
--

LOCK TABLES `skills` WRITE;
/*!40000 ALTER TABLE `skills` DISABLE KEYS */;
/*!40000 ALTER TABLE `skills` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tick`
--

DROP TABLE IF EXISTS `tick`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tick` (
  `next_tick` int(10) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tick`
--

LOCK TABLES `tick` WRITE;
/*!40000 ALTER TABLE `tick` DISABLE KEYS */;
/*!40000 ALTER TABLE `tick` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `alias` varchar(32) NOT NULL,
  `pass` varchar(128) NOT NULL,
  `str` int(2) unsigned NOT NULL,
  `int` int(2) unsigned NOT NULL,
  `wis` int(2) unsigned NOT NULL,
  `con` int(2) unsigned NOT NULL,
  `dex` int(2) unsigned NOT NULL,
  `vit` int(2) unsigned NOT NULL,
  `wil` int(2) unsigned NOT NULL,
  `hp` int(4) unsigned NOT NULL,
  `max_hp` int(4) unsigned NOT NULL,
  `mana` int(4) unsigned NOT NULL,
  `max_mana` int(4) unsigned NOT NULL,
  `movement` int(4) unsigned NOT NULL,
  `max_movement` int(4) unsigned NOT NULL,
  `gold` int(4) unsigned NOT NULL,
  `silver` int(5) unsigned NOT NULL,
  `copper` int(5) unsigned NOT NULL,
  `race` enum('Human') default NULL,
  `_class` enum('Warrior') default NULL,
  `fk_room_id` int(10) unsigned NOT NULL,
  `level` int(3) NOT NULL,
  `experience` int(10) unsigned NOT NULL,
  `exp_per_level` int(10) unsigned NOT NULL,
  `nourishment` int(3) NOT NULL,
  `thirst` int(3) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `world`
--

DROP TABLE IF EXISTS `world`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `world` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `north` int(10) unsigned NOT NULL,
  `south` int(10) unsigned NOT NULL,
  `east` int(10) unsigned NOT NULL,
  `west` int(10) unsigned NOT NULL,
  `up` int(10) unsigned NOT NULL,
  `down` int(10) unsigned NOT NULL,
  `area` varchar(64) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `world`
--

LOCK TABLES `world` WRITE;
/*!40000 ALTER TABLE `world` DISABLE KEYS */;
INSERT INTO `world` VALUES (1,'Temple Fountain','A majestic fountain temple lies in the middle of an extensive and beautiful walkway.',2,3,0,0,0,0,'temple'),(2,'Temple Courtyard','The walkway opens up to a large courtyard, basking in the open sun.',0,1,0,8,0,0,'temple'),(3,'Temple Entrance','A grand archway separates the foyer from the busy market street.',1,4,5,0,0,0,'temple'),(4,'The Market Square','Roads full of busy travellers stretch in every direction. To the north is an awe-inspiring temple.',3,0,0,0,0,0,'temple'),(5,'Cleric\'s Bar','A dim bar is here, lit by wall candles dancing in the breeze.',0,0,0,3,0,6,'temple'),(6,'Gambling Room','The large, unfinished basement of the Cleric\'s bar has given way to gambling rings.',0,0,0,0,5,0,'temple'),(7,'Puragtory','',0,0,0,0,0,0,'temple'),(8,'Entrance to the Temple Arena','A large training arena lies before you.',0,0,2,0,0,0,'temple_arena');
/*!40000 ALTER TABLE `world` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2010-04-06 17:57:03
