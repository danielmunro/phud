-- MySQL dump 10.13  Distrib 5.1.49, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: mud
-- ------------------------------------------------------
-- Server version	5.1.49-1ubuntu8.1

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
-- Table structure for table `abilities`
--

DROP TABLE IF EXISTS `abilities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `abilities` (
  `name` varchar(32) NOT NULL,
  `percent` int(3) NOT NULL,
  `fk_actor_id` int(10) unsigned NOT NULL,
  `actor_type` varchar(32) NOT NULL,
  `type` int(1) unsigned NOT NULL,
  UNIQUE KEY `fk_actor_id` (`fk_actor_id`,`actor_type`,`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `abilities`
--

LOCK TABLES `abilities` WRITE;
/*!40000 ALTER TABLE `abilities` DISABLE KEYS */;
INSERT INTO `abilities` VALUES ('Kick',100,1,'User',1),('Dodge',100,1,'User',1),('Shield_Block',100,1,'User',1),('Bash',100,1,'User',1),('Armor',100,1,'User',2),('Cure_Light',100,1,'User',2),('Magic_Missile',100,1,'User',2),('Berserk',100,1,'User',1),('Kick',100,1,'Mob',1),('Dodge',50,1,'Mob',1);
/*!40000 ALTER TABLE `abilities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `affects`
--

DROP TABLE IF EXISTS `affects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `affects` (
  `fk_id` int(10) unsigned NOT NULL,
  `affect` varchar(32) NOT NULL,
  `fk_table` varchar(32) NOT NULL,
  `skill` varchar(32) DEFAULT NULL,
  `spell` varchar(32) DEFAULT NULL,
  `level` int(3) DEFAULT NULL,
  PRIMARY KEY (`fk_table`,`fk_id`,`affect`),
  UNIQUE KEY `fk_user_id` (`fk_id`,`affect`),
  UNIQUE KEY `fk_table` (`fk_table`,`fk_id`,`affect`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `affects`
--

LOCK TABLES `affects` WRITE;
/*!40000 ALTER TABLE `affects` DISABLE KEYS */;
INSERT INTO `affects` VALUES (2,'glow','items',NULL,NULL,NULL),(3,'glow','items',NULL,NULL,NULL),(4,'glow','items',NULL,NULL,NULL),(5,'glow','items',NULL,NULL,NULL),(25,'glow','items',NULL,NULL,NULL),(29,'glow','items',NULL,NULL,NULL);
/*!40000 ALTER TABLE `affects` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doors`
--

DROP TABLE IF EXISTS `doors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `doors` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `short_desc` varchar(32) NOT NULL,
  `long_desc_room1` varchar(255) NOT NULL,
  `fk_unlock_item_id` int(10) unsigned DEFAULT NULL,
  `fk_room1_id` int(10) unsigned NOT NULL,
  `fk_room2_id` int(10) unsigned NOT NULL,
  `direction1` varchar(5) NOT NULL,
  `direction2` varchar(5) NOT NULL,
  `disposition` enum('locked','closed','open') DEFAULT NULL,
  `nouns` varchar(255) NOT NULL,
  `hidden` tinyint(1) NOT NULL DEFAULT '0',
  `hidden_show_command` varchar(255) DEFAULT NULL,
  `hidden_action` varchar(255) NOT NULL,
  `fk_hidden_item_id` int(10) unsigned DEFAULT NULL,
  `reload_ticks` int(1) NOT NULL DEFAULT '5',
  `default_disposition` enum('locked','closed','open') NOT NULL DEFAULT 'closed',
  `long_desc_room2` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doors`
--

LOCK TABLES `doors` WRITE;
/*!40000 ALTER TABLE `doors` DISABLE KEYS */;
INSERT INTO `doors` VALUES (1,'a large steel grate','A large, rusted steel grate is here in the floor. The slats are too narrow to see through, and there is a handle to open it.',NULL,5,6,'down','up','closed','grate',1,'move rug','You move a tattered rug to reveal a steel grate.',14,3,'closed','A large, rusted steel grate in the ceiling lets ribbons of light in from the bar upstairs.'),(2,'a steel grate','A steel grate sits here partially covered in dirt.',NULL,45,55,'down','up','closed','steel grate',0,NULL,'',NULL,5,'closed','A steel grate leads up to the daylight.');
/*!40000 ALTER TABLE `doors` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `environment`
--

DROP TABLE IF EXISTS `environment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `environment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `environment_type` int(10) unsigned NOT NULL,
  `command` varchar(255) NOT NULL,
  `fk_table` varchar(64) NOT NULL,
  `fk_table_id` int(10) unsigned NOT NULL,
  `fk_room_id` int(10) unsigned NOT NULL,
  `message` varchar(255) NOT NULL,
  `look_describe` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
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
-- Table structure for table `equipped`
--

DROP TABLE IF EXISTS `equipped`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `equipped` (
  `fk_user_id` int(10) unsigned NOT NULL,
  `position_id` int(2) unsigned NOT NULL,
  `fk_item_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `fk_user_id` (`fk_user_id`,`position_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `equipped`
--

LOCK TABLES `equipped` WRITE;
/*!40000 ALTER TABLE `equipped` DISABLE KEYS */;
INSERT INTO `equipped` VALUES (2,15,39);
/*!40000 ALTER TABLE `equipped` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventories`
--

DROP TABLE IF EXISTS `inventories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `fk_table` varchar(32) NOT NULL,
  `fk_table_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventories`
--

LOCK TABLES `inventories` WRITE;
/*!40000 ALTER TABLE `inventories` DISABLE KEYS */;
INSERT INTO `inventories` VALUES (1,'users',1),(2,'bag',1),(7,'room',1),(6,'room',5),(8,'room',2),(9,'users',0),(10,'users',0),(11,'users',0),(12,'users',0),(13,'users',0),(14,'users',0),(15,'users',2),(16,'users_eq',2),(17,'users',2),(18,'users_eq',1),(19,'users',1),(20,'users',1),(21,'users',1),(22,'users',1),(25,'shop',1),(26,'shop',2),(27,'shop',3),(28,'shop',4),(29,'shop',5);
/*!40000 ALTER TABLE `inventories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `short_desc` varchar(255) NOT NULL,
  `long_desc` varchar(255) NOT NULL,
  `nouns` varchar(255) NOT NULL,
  `value` int(11) NOT NULL,
  `weight` float(4,1) NOT NULL,
  `item_condition` int(3) NOT NULL,
  `item_type` varchar(64) NOT NULL,
  `can_own` tinyint(1) NOT NULL,
  `equipment_type` int(2) unsigned DEFAULT NULL,
  `verb` varchar(64) NOT NULL,
  `nourishment` int(1) DEFAULT NULL,
  `fk_inv_inside_id` int(10) unsigned NOT NULL,
  `fk_inventory_id` int(10) unsigned NOT NULL,
  `thirst` int(1) DEFAULT NULL,
  `fk_door_unlock_id` int(10) unsigned DEFAULT NULL,
  `weapon_type` int(2) unsigned DEFAULT NULL,
  `hit_roll` int(4) unsigned DEFAULT NULL,
  `dam_roll` int(4) unsigned DEFAULT NULL,
  `ac_slash` int(3) DEFAULT NULL,
  `ac_bash` int(3) DEFAULT NULL,
  `ac_pierce` int(3) DEFAULT NULL,
  `ac_magic` int(3) DEFAULT NULL,
  `affects` varchar(255) NOT NULL,
  `damage_type` int(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,'a wooden torch','a wooden torch is here.','wooden torch',1,1.0,0,'6',1,0,'',NULL,1,0,NULL,0,NULL,NULL,NULL,0,0,0,0,'',NULL),(2,'a wooden torch','a wooden torch is here.','wooden torch',1,1.0,0,'6',1,0,'',NULL,18,0,NULL,0,NULL,NULL,NULL,0,0,0,0,'',NULL),(3,'a wooden torch','a wooden torch is here.','wooden torch',1,1.0,0,'6',1,0,'',NULL,1,0,NULL,0,NULL,NULL,NULL,0,0,0,0,'',NULL),(4,'a wooden torch','a wooden torch is here.','wooden torch',1,1.0,0,'6',1,0,'',NULL,1,0,NULL,0,NULL,NULL,NULL,0,0,0,0,'',NULL),(5,'a wooden torch','a wooden torch is here.','wooden torch',1,1.0,0,'6',1,0,'',NULL,1,0,NULL,0,NULL,NULL,NULL,0,0,0,0,'',NULL),(6,'a sub issue sword','a sub issue sword is here.','sub sword',100,4.0,0,'5',1,NULL,'slash',NULL,1,0,NULL,0,1,1,2,NULL,NULL,NULL,NULL,'',1),(21,'a pair of sub issue gloves','a pair of sub issue gloves are here.','sub gloves',20,5.0,0,'6',1,7,'',NULL,26,0,NULL,0,NULL,NULL,NULL,-5,-5,-5,0,'',NULL),(20,'a sub issue shield','a sub issue shield is here.','sub shield',20,5.0,0,'6',1,12,'',NULL,26,0,NULL,0,NULL,NULL,NULL,-5,-5,-5,0,'',NULL),(19,'a sub issue mace','a sub issue mace is here.','sub mace',100,4.0,0,'5',1,NULL,'pound',NULL,18,0,NULL,0,3,1,2,NULL,NULL,NULL,NULL,'',3),(18,'a sub issue dagger','a sub issue dagger is here.','sub dagger',100,4.0,0,'5',1,NULL,'stab',NULL,25,0,NULL,0,6,1,2,NULL,NULL,NULL,NULL,'',2),(17,'a sub issue mace','a sub issue mace is here.','sub mace',100,4.0,0,'5',1,NULL,'pound',NULL,25,0,NULL,0,3,1,2,NULL,NULL,NULL,NULL,'',3),(16,'a sub issue sword','a sub issue sword is here.','sub sword',100,4.0,0,'5',1,NULL,'slash',NULL,25,0,NULL,0,1,1,2,NULL,NULL,NULL,NULL,'',1),(22,'a sub issue belt','a sub issue belt is here.','sub belt',20,5.0,0,'6',1,10,'',NULL,26,0,NULL,0,NULL,NULL,NULL,-5,-5,-5,0,'',NULL),(23,'a sub issue helmet','a sub issue helmet is here.','sub helmet',20,5.0,0,'6',1,4,'',NULL,26,0,NULL,0,NULL,NULL,NULL,-5,-5,-5,0,'',NULL),(24,'a pair of sub issue boots','a pair of sub issue boots are here.','sub boots',20,5.0,0,'6',1,6,'',NULL,26,0,NULL,0,NULL,NULL,NULL,-5,-5,-5,0,'',NULL),(25,'a wooden torch','a wooden torch is here.','wooden torch',1,1.0,0,'6',1,0,'',NULL,27,0,NULL,0,NULL,NULL,NULL,0,0,0,0,'',NULL),(26,'a small leather canteen','a small leather canteen is here','leather canteen',5,1.0,0,'4',1,NULL,'',NULL,29,0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL),(27,'a small leather canteen','a small leather canteen is here','leather canteen',5,1.0,0,'4',1,NULL,'',NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'',NULL),(28,'a wooden torch','a wooden torch is here.','wooden torch',1,1.0,0,'6',1,0,'',NULL,1,0,NULL,0,NULL,NULL,NULL,0,0,0,0,'',NULL),(29,'a wooden torch','a wooden torch is here.','wooden torch',1,1.0,0,'6',1,0,'',NULL,1,0,NULL,0,NULL,NULL,NULL,0,0,0,0,'',NULL);
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mobs`
--

DROP TABLE IF EXISTS `mobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(120) NOT NULL,
  `noun` varchar(255) NOT NULL,
  `long` varchar(255) NOT NULL,
  `auto_flee` tinyint(1) NOT NULL DEFAULT '0',
  `unique` tinyint(1) NOT NULL DEFAULT '0',
  `area` varchar(255) NOT NULL,
  `movement_speed` int(3) NOT NULL,
  `respawn_time` int(3) NOT NULL,
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
  `race` enum('Human','Elf','Faerie','Ogre','Undead') DEFAULT NULL,
  `fk_room_id` int(10) unsigned NOT NULL,
  `level` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mobs`
--

LOCK TABLES `mobs` WRITE;
/*!40000 ALTER TABLE `mobs` DISABLE KEYS */;
INSERT INTO `mobs` VALUES (1,'a town crier','town crier','You see a town crier before you.',0,0,'temple midgaard',27,1,17,19,18,17,19,0,0,20,20,100,100,100,100,0,0,0,'Human',3,1),(2,'the zombified remains of the mayor','zombie corpse mayor','The partially decomposed, moaning zombie corpse of the mayor of Midgaard stands before you.',0,1,'temple midgaard',14,5,20,15,15,21,13,0,0,20,20,100,100,100,100,0,0,0,'Undead',3,1),(3,'a snail','snail','A snail is desperately trying to get out of your way.',0,0,'temple_arena',20,1,17,19,18,17,19,0,0,10,10,100,100,100,100,0,0,0,'Human',53,1),(4,'a snail','snail','A snail is desperately trying to get out of your way.',0,0,'temple_arena',20,1,17,19,18,17,19,0,0,10,10,100,100,100,100,0,0,0,'Human',28,1),(5,'a snail','snail','A snail is desperately trying to get out of your way.',0,0,'temple_arena',20,1,17,19,18,17,19,0,0,10,10,100,100,100,100,0,0,0,'Human',35,1),(6,'a snail','snail','A snail is desperately trying to get out of your way.',0,0,'temple_arena',20,1,17,19,18,17,19,0,0,10,10,100,100,100,100,0,0,0,'Human',42,1),(7,'a fox','fox','A fox is eying you from a distance.',0,0,'temple_arena',50,2,17,19,18,17,19,0,0,15,15,100,100,100,100,0,0,0,'Human',31,3),(8,'a fox','fox','A fox is eying you from a distance.',0,0,'temple_arena',50,2,17,19,18,17,19,0,0,15,15,100,100,100,100,0,0,0,'Human',50,3),(9,'a fox','fox','A fox is eying you from a distance.',0,0,'temple_arena',50,2,17,19,18,17,19,0,0,15,15,100,100,100,100,0,0,0,'Human',31,3),(10,'a boar','boar','A boar getting ready to charge!',0,0,'temple_arena',50,2,17,19,18,17,19,0,0,20,20,100,100,100,100,0,0,0,'Human',27,3),(11,'a boar','boar','A boar getting ready to charge!',0,0,'temple_arena',50,2,17,19,18,17,19,0,0,20,20,100,100,100,100,0,0,0,'Human',39,3),(12,'a giant gray rat','gray rat','A giant rat is here, staring nefariously at you.',0,0,'midgaard_dungeon',30,2,17,19,18,17,19,0,0,25,25,100,100,100,100,0,0,0,'Human',60,6),(13,'a giant gray rat','gray rat','A giant rat is here, staring nefariously at you.',0,0,'midgaard_dungeon',30,2,17,19,18,17,19,0,0,25,25,100,100,100,100,0,0,0,'Human',63,6),(14,'a giant gray rat','gray rat','A giant rat is here, staring nefariously at you.',0,0,'midgaard_dungeon',30,2,17,19,18,17,19,0,0,25,25,100,100,100,100,0,0,0,'Human',64,6),(15,'a giant gray rat','gray rat','A giant rat is here, staring nefariously at you.',0,0,'midgaard_dungeon',30,2,17,19,18,17,19,0,0,25,25,100,100,100,100,0,0,0,'Human',70,6),(16,'a giant gray rat','gray rat','A giant rat is here, staring nefariously at you.',0,0,'midgaard_dungeon',30,2,17,19,18,17,19,0,0,25,25,100,100,100,100,0,0,0,'Human',68,6),(17,'a giant gray rat','gray rat','A giant rat is here, staring nefariously at you.',0,0,'midgaard_dungeon',30,2,17,19,18,17,19,0,0,25,25,100,100,100,100,0,0,0,'Human',73,6),(18,'a giant gray rat','gray rat','A giant rat is here, staring nefariously at you.',0,0,'midgaard_dungeon',30,2,17,19,18,17,19,0,0,25,25,100,100,100,100,0,0,0,'Human',61,6),(19,'a giant gray rat','gray rat','A giant rat is here, staring nefariously at you.',0,0,'midgaard_dungeon',30,2,17,19,18,17,19,0,0,25,25,100,100,100,100,0,0,0,'Human',74,6),(20,'a giant gray rat','gray rat','A giant rat is here, staring nefariously at you.',0,0,'midgaard_dungeon',30,2,17,19,18,17,19,0,0,25,25,100,100,100,100,0,0,0,'Human',73,6),(21,'a giant hissing black rat','hissing black rat','A giant hissing black rat is here, ready to defend the nest!',0,0,'midgaard_rat_nest',0,2,17,19,18,17,19,0,0,45,45,100,100,100,100,0,0,0,'Human',83,10),(22,'a giant hissing black rat','hissing black rat','A giant hissing black rat is here, ready to defend the nest!',0,0,'midgaard_rat_nest',0,2,17,19,18,17,19,0,0,45,45,100,100,100,100,0,0,0,'Human',85,10),(23,'a giant hissing black rat','hissing black rat','A giant hissing black rat is here, ready to defend the nest!',0,0,'midgaard_rat_nest',0,2,17,19,18,17,19,0,0,45,45,100,100,100,100,0,0,0,'Human',85,10),(24,'a giant hissing black rat','hissing black rat','A giant hissing black rat is here, ready to defend the nest!',0,0,'midgaard_rat_nest',0,2,17,19,18,17,19,0,0,45,45,100,100,100,100,0,0,0,'Human',87,10),(25,'a giant hissing black rat','hissing black rat','A giant hissing black rat is here, ready to defend the nest!',0,0,'midgaard_rat_nest',0,2,17,19,18,17,19,0,0,45,45,100,100,100,100,0,0,0,'Human',87,10);
/*!40000 ALTER TABLE `mobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rooms`
--

DROP TABLE IF EXISTS `rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rooms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `north` int(10) unsigned NOT NULL,
  `south` int(10) unsigned NOT NULL,
  `east` int(10) unsigned NOT NULL,
  `west` int(10) unsigned NOT NULL,
  `up` int(10) unsigned NOT NULL,
  `down` int(10) unsigned NOT NULL,
  `area` varchar(64) NOT NULL,
  `visibility` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=90 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rooms`
--

LOCK TABLES `rooms` WRITE;
/*!40000 ALTER TABLE `rooms` DISABLE KEYS */;
INSERT INTO `rooms` VALUES (1,'Temple Fountain','A majestic fountain temple lies in the middle of an extensive and beautiful walkway.',2,3,0,0,0,0,'temple',1),(2,'Temple Courtyard','The walkway opens up to a large courtyard, basking in the open sun.',54,3,16,0,0,0,'temple',1),(3,'Temple Entrance','A grand archway separates the foyer from the busy market street.',2,4,0,0,0,0,'temple',1),(4,'The Market Square','Roads full of busy travellers stretch in every direction. To the north is an awe-inspiring temple.',3,14,10,11,0,0,'midgaard',1),(5,'Cleric\'s Bar','A dim bar is here, lit by wall candles dancing in the breeze.',0,11,0,0,0,6,'temple',1),(6,'Gambling Room','The large, unfinished basement of the Cleric\'s bar has given way to gambling rings.',0,0,0,0,5,0,'temple',1),(7,'Puragtory','',0,0,0,0,0,0,'temple',1),(8,'Entrance to the Temple Arena','A large training arena lies before you.',27,36,54,31,0,0,'temple_arena',1),(9,'','',0,0,0,0,0,0,'',1),(10,'Main Street','A wide busy road, lined with merchants.',12,15,58,4,0,0,'midgaard',1),(11,'Main Street','A wide busy road, lined with merchants.',5,57,4,56,0,0,'midgaard',1),(12,'Midgaard Weaponsmith','A large room with various implements of destruction adorning the walls.',0,10,0,0,0,0,'midgaard',1),(13,'','',0,0,0,0,0,0,'',1),(14,'Common Grounds','Cobblestone roads lead in all directions.',4,76,78,77,0,0,'midgaard',1),(15,'Midgaard Bank','A clean, simple room with a vault behind a counter on the far wall.',10,0,0,0,0,0,'midgaard',1),(16,'Temple Medidation Room','A sense of relaxation flows through you.',0,0,0,2,0,0,'temple',1),(17,'','',0,0,0,0,0,0,'',1),(27,'Temple Arena','A large training arena lies before you.',28,8,0,30,0,0,'temple_arena',1),(28,'Temple Arena','A large training arena lies before you.',0,27,0,29,0,0,'temple_arena',1),(29,'Temple Arena','A large training arena lies before you.',0,30,28,39,0,0,'temple_arena',1),(30,'Temple Arena','A large training arena lies before you.',29,31,27,42,0,0,'temple_arena',1),(31,'Temple Arena','A large training arena lies before you.',30,35,8,45,0,0,'temple_arena',1),(32,'','',8,0,0,0,0,0,'',1),(33,'Temple Arena','A large training arena lies before you.',8,34,0,0,0,0,'temple_arena',1),(34,'Temple Arena','A large training arena lies before you.',33,0,0,0,0,0,'temple_arena',1),(35,'Temple Arena','A large training arena lies before you.',31,38,36,48,0,0,'temple_arena',1),(36,'Temple Arena','A large training arena lies before you.',8,37,0,35,0,0,'temple_arena',1),(37,'Temple Arena','A large training arena lies before you.',36,0,0,38,0,0,'temple_arena',1),(38,'Temple Arena','A large training arena lies before you.',35,0,37,51,0,0,'temple_arena',1),(39,'Temple Arena','A large training arena lies before you.',0,42,29,40,0,0,'temple_arena',1),(40,'Temple Arena','A large training arena lies before you.',0,43,39,41,0,0,'temple_arena',1),(41,'Temple Arena','A large training arena lies before you.',0,44,40,0,0,0,'temple_arena',1),(42,'Temple Arena','A large training arena lies before you.',39,45,30,43,0,0,'temple_arena',1),(43,'Temple Arena','A large training arena lies before you.',40,46,42,44,0,0,'temple_arena',1),(44,'Temple Arena','A large training arena lies before you.',41,47,43,0,0,0,'temple_arena',1),(45,'Center of the Temple Arena','A large training arena lies before you.',42,48,31,46,0,55,'temple_arena',1),(46,'Temple Arena','A large training arena lies before you.',43,49,45,47,0,0,'temple_arena',1),(47,'Temple Arena','A large training arena lies before you.',44,50,46,0,0,0,'temple_arena',1),(48,'Temple Arena','A large training arena lies before you.',45,51,35,49,0,0,'temple_arena',1),(49,'Temple Arena','A large training arena lies before you.',46,52,48,50,0,0,'temple_arena',1),(50,'Temple Arena','A large training arena lies before you.',47,53,49,0,0,0,'temple_arena',1),(51,'Temple Arena','A large training arena lies before you.',48,0,38,52,0,0,'temple_arena',1),(52,'Temple Arena','A large training arena lies before you.',49,0,51,53,0,0,'temple_arena',1),(53,'Temple Arena','A large training arena lies before you.',50,0,52,0,0,0,'temple_arena',1),(54,'Temple Courtyard','The walkway opens up to a large courtyard, basking in the open sun.',0,2,0,8,0,0,'temple',1),(55,'Entrance to the Temple Arena Dungeon','A small grate in the ceiling lets in enough light to see a small staircase going down.',0,0,0,0,45,60,'midgaard_dungeon',0),(56,'Main Street','A wide busy road, lined with merchants.',0,0,11,81,0,0,'midgaard',1),(57,'Midgaard Armorsmith','A wide room with armaments lining the walls.',11,0,0,0,0,0,'midgaard',1),(58,'Main Street','A wide busy road, lined with merchants.',59,0,82,10,0,0,'midgaard',1),(59,'Midgaard Grocer and General Store','Adventuring supplies litter disorganized shelves in this cramped general store.',0,58,0,0,0,0,'midgaard',1),(60,'Temple Arena Dungeon','A low stone archway is on the north wall, in a cold and damp cobblestone room.',61,0,0,0,55,0,'midgaard_dungeon',1),(61,'Temple Arena Dungeon','A small and dark hallway extends in various directions.',0,60,63,62,0,0,'midgaard_dungeon',1),(62,'Temple Arena Dungeon','A small and dark hallway extends in various directions.',0,0,61,64,0,0,'midgaard_dungeon',1),(63,'Temple Arena Dungeon','A small and dark hallway extends in various directions.',0,0,65,61,0,0,'midgaard_dungeon',1),(64,'Temple Arena Dungeon','A small and dark hallway extends in various directions.',0,66,62,0,0,0,'midgaard_dungeon',1),(65,'Temple Arena Dungeon','A small and dark hallway extends in various directions.',0,74,0,63,0,0,'midgaard_dungeon',1),(66,'Temple Prison','A small and dark hallway extends in various directions.',64,67,0,0,0,0,'midgaard_dungeon',1),(67,'Temple Prison','A small and dark hallway extends in various directions.',66,70,69,68,0,0,'midgaard_dungeon',1),(68,'Prison Cell','A dark, cramped room.',0,0,67,0,0,0,'midgaard_dungeon',1),(69,'Prison Cell','A small and dark hallway extends in various directions.',0,0,0,67,0,0,'midgaard_dungeon',1),(70,'Temple Prison','A small and dark hallway extends in various directions.',67,73,72,71,0,0,'midgaard_dungeon',1),(71,'Prison Cell','A small and dark hallway extends in various directions.',0,0,70,0,0,0,'midgaard_dungeon',1),(72,'Prison Cell','A small and dark hallway extends in various directions.',0,0,0,70,0,0,'midgaard_dungeon',1),(73,'Prison Cell','A small and dark hallway extends in various directions.',70,0,0,0,0,0,'midgaard_dungeon',1),(74,'Temple Arena Dungeon','A small and dark hallway extends in various directions.',65,75,0,0,0,0,'midgaard_dungeon',1),(75,'Temple Arena Dungeon','A small and dark hallway extends in various directions. There is a hole in the ground.',74,0,0,0,0,83,'midgaard_dungeon',1),(76,'Midgaard Park','Cobblestone roads lead in all directions.',14,0,0,0,0,0,'midgaard',1),(77,'Common Grounds','Cobblestone roads lead in all directions.',0,0,14,0,0,0,'midgaard',1),(78,'Common Grounds','Cobblestone roads lead in all directions.',79,0,0,14,0,0,'midgaard',1),(79,'Midgaard Pub and Inn','A large counter separates a busy tavern from the bartender/innkeeper.',0,78,0,0,80,0,'midgaard',1),(80,'Midgaard Inn Rooms','You feel rested and rejuvinated.',0,0,0,0,0,79,'midgaard',1),(81,'Market Street','A wide busy road, lined with merchants.',0,0,56,0,0,0,'midgaard',1),(82,'Market Street','A wide busy road, lined with merchants.',88,89,0,58,0,0,'midgaard',1),(88,'Market Street','A wide busy road, lined with merchants.',0,82,0,0,0,0,'midgaard',1),(83,'Rat Nest','A hole in the ground has led to filthy rat burrow.',0,84,0,0,75,0,'midgaard_rat_nest',1),(84,'A disgusting rat burrow.','A hole in the ground has led to filthy rat burrow.',83,86,85,0,0,0,'midgaard_rat_nest',1),(85,'A disgusting rat burrow.','A hole in the ground has led to filthy rat burrow.',0,0,0,84,0,0,'midgaard_rat_nest',1),(86,'A disgusting rat burrow.','A hole in the ground has led to filthy rat burrow.',84,0,0,87,0,0,'midgaard_rat_nest',1),(87,'A disgusting rat burrow.','A hole in the ground has led to filthy rat burrow.',0,0,86,0,0,0,'midgaard_rat_nest',1),(89,'Market Street','A wide busy road, lined with merchants.',82,0,0,0,0,0,'midgaard',1);
/*!40000 ALTER TABLE `rooms` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `shopkeepers`
--

DROP TABLE IF EXISTS `shopkeepers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `shopkeepers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(120) NOT NULL,
  `noun` varchar(255) NOT NULL,
  `long` varchar(255) NOT NULL,
  `gold` int(4) unsigned NOT NULL,
  `silver` int(5) unsigned NOT NULL,
  `copper` int(5) unsigned NOT NULL,
  `race` enum('Human','Elf','Faerie','Ogre','Undead') NOT NULL,
  `fk_room_id` int(10) unsigned NOT NULL,
  `level` int(3) NOT NULL,
  `list_item_message` varchar(255) NOT NULL,
  `no_item_message` varchar(255) NOT NULL,
  `not_enough_money_message` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `shopkeepers`
--

LOCK TABLES `shopkeepers` WRITE;
/*!40000 ALTER TABLE `shopkeepers` DISABLE KEYS */;
INSERT INTO `shopkeepers` VALUES (1,'Erog the blacksmith','erog blacksmith','A large ogre stands before you with a giant smelting iron by his side.',0,0,0,'Ogre',12,1,'Here\'s what I have in stock now','I\'m not selling that','Come back when you have more money'),(2,'Halek the armorsmith','halek armorsmith','A cautious looking elf stands before you.',0,0,0,'Elf',57,1,'Here\'s what I have in stock now','I\'m not selling that','Come back when you have more money'),(3,'Alfred the store clerk','alfred clerk','Alfred smiles and offers you to look around.',0,0,0,'Human',59,1,'Here\'s what I have in stock now','I\'m not selling that','Come back when you have more money'),(4,'The banker','banker','The banker sits behind a counter, reading ledgers.',0,0,0,'Human',15,1,'Here\'s what I have in stock now','I\'m not selling that','Come back when you have more money'),(5,'Annir the bartender','annir bartender','Annir, the faerie bartender buzzes before you.',0,0,0,'Faerie',79,1,'Here\'s what I have in stock now','I\'m not selling that','Come back when you have more money');
/*!40000 ALTER TABLE `shopkeepers` ENABLE KEYS */;
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
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `race` enum('Human','Elf','Faerie','Ogre','Undead') DEFAULT NULL,
  `_class` enum('Warrior') DEFAULT NULL,
  `fk_room_id` int(10) unsigned NOT NULL,
  `level` int(3) NOT NULL,
  `experience` int(10) unsigned NOT NULL,
  `exp_per_level` int(10) unsigned NOT NULL,
  `nourishment` int(3) NOT NULL,
  `thirst` int(3) NOT NULL,
  `discipline` enum('Crusader','Barbarian','Wizard','Rogue') DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Dan','f3b5a11fa6c78370ac0f0b31b30c44849b058718',20,15,15,21,13,0,0,20,20,100,100,10,100,0,0,793,'Undead',NULL,61,1,585,0,0,0,'Barbarian'),(2,'dan2','2e60ff0c8179070eac9516680b9fddfefda04a10',16,20,23,17,23,0,0,20,20,100,100,100,100,0,0,1000,'Elf',NULL,3,1,0,0,0,0,'Rogue');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2011-04-24 11:02:25
