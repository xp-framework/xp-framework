SET FOREIGN_KEY_CHECKS=0;

-- --------------------------------------------------------
-- --------------------------------------------------------

DROP TABLE IF EXISTS `job`;
CREATE TABLE `job` (
  `job_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `valid_from` datetime DEFAULT NULL,
  `expire_at` datetime NOT NULL,
  `person_id` int(11) NOT NULL,
  PRIMARY KEY (`job_id`),
  KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4;

INSERT INTO `job` VALUES (1, 'bla',     NULL, '2007-03-05 12:11:58', 1);
INSERT INTO `job` VALUES (2, 'blub',    NULL, '2007-03-05 12:15:50', 3);
INSERT INTO `job` VALUES (3, 'schmick', NULL, '2007-03-05 12:15:50', 3);

-- --------------------------------------------------------

DROP TABLE IF EXISTS `toilette`;
CREATE TABLE `toilette` (
  `toilette_id`int(11) NOT NULL AUTO_INCREMENT,
  `person_id`  int(11),
  PRIMARY KEY (`toilette_id`),
  UNIQUE KEY `person_id` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4;

INSERT INTO `toilette` VALUES (1, 1);
INSERT INTO `toilette` VALUES (2, 2);
INSERT INTO `toilette` VALUES (3, 3);

-- --------------------------------------------------------

DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
  `person_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4;

INSERT INTO `person` VALUES (1, 'Peter');
INSERT INTO `person` VALUES (2, 'Paul');
INSERT INTO `person` VALUES (3, 'Mary');

-- --------------------------------------------------------

ALTER TABLE `job`
  ADD CONSTRAINT `person_for_job` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `toilette`
  ADD CONSTRAINT `person_on_toilette` FOREIGN KEY (`person_id`) REFERENCES `person` (`person_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

-- --------------------------------------------------------
-- --------------------------------------------------------

DROP TABLE IF EXISTS `color`;
CREATE TABLE `color` (
  `color_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `colortype_id` int(11) NOT NULL ,
  PRIMARY KEY (`color_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=5;

INSERT INTO `color` VALUES (1, 'lightgreen', 1);
INSERT INTO `color` VALUES (2, 'darkgreen',  1);
INSERT INTO `color` VALUES (3, 'lightgray',  2);
INSERT INTO `color` VALUES (4, 'brown',      3);

-- --------------------------------------------------------

DROP TABLE IF EXISTS `colortype`;
CREATE TABLE `colortype` (
  `colortype_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`colortype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4;

INSERT INTO `colortype` VALUES (1, 'green');
INSERT INTO `colortype` VALUES (2, 'gray');
INSERT INTO `colortype` VALUES (3, 'brown');

-- --------------------------------------------------------

DROP TABLE IF EXISTS `texture`;
CREATE TABLE `texture` (
  `texture_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color_id` int(11) NOT NULL,
  PRIMARY KEY (`texture_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4;

INSERT INTO `texture` VALUES (1, 'street', 3);
INSERT INTO `texture` VALUES (2, 'gras',   2);
INSERT INTO `texture` VALUES (3, 'castle', 4);

-- --------------------------------------------------------

DROP TABLE IF EXISTS `mappoint`;
CREATE TABLE `mappoint` (
  `coord_x` int(11) NOT NULL,
  `coord_y` int(11) NOT NULL,
  `texture_id` int(11) NOT NULL,
  PRIMARY KEY (`coord_x`, `coord_y`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

INSERT INTO `mappoint` VALUES ( 1, 2, 1);
INSERT INTO `mappoint` VALUES ( 2, 4, 2);
INSERT INTO `mappoint` VALUES (10, 1, 3);

-- --------------------------------------------------------

DROP TABLE IF EXISTS `mobileObject`;
CREATE TABLE `mobileObject` (
  `object_id` int(11) NOT NULL AUTO_INCREMENT,
  `coord_x` int(11) NOT NULL,
  `coord_y` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`object_id`),
  KEY `coords` (`coord_x`, `coord_y`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=7;

INSERT INTO `mobileObject` VALUES (1, 2, 4, 'tree');
INSERT INTO `mobileObject` VALUES (2, 2, 4, 'tree');
INSERT INTO `mobileObject` VALUES (3, 1, 2, 'car');
INSERT INTO `mobileObject` VALUES (4, 10, 1, 'person1');
INSERT INTO `mobileObject` VALUES (5, 10, 1, 'person2');
-- INSERT INTO `mobileObject` VALUES (6, 2, 2, 'Puppy-Poo');

-- --------------------------------------------------------

ALTER TABLE `mobileObject`
  ADD CONSTRAINT `mappoint_for_mob` FOREIGN KEY `coords` (`coord_x`, `coord_y`) REFERENCES `mappoint` (`coord_x`, `coord_y`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `mappoint`
  ADD CONSTRAINT `texture_for_mappoint` FOREIGN KEY (`texture_id`) REFERENCES `texture` (`texture_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `texture`
  ADD CONSTRAINT `color_for_texture` FOREIGN KEY (`color_id`) REFERENCES `color` (`color_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `color`
  ADD CONSTRAINT `colortype_for_color` FOREIGN KEY (`colortype_id`) REFERENCES `colortype` (`colortype_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

-- --------------------------------------------------------
-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS=1;
