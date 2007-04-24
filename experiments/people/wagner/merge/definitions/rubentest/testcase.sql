SET FOREIGN_KEY_CHECKS=0;

-- --------------------------------------------------------
-- --------------------------------------------------------

DROP TABLE IF EXISTS `mmessage`;
CREATE TABLE `mmessage` (
  `message_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `body` text NOT NULL,
  `valid_from` datetime DEFAULT NULL,
  `expire_at` datetime NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `author_id` int(11) NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `recipient_id` (`recipient_id`),
  KEY `author_id` (`author_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4;

INSERT INTO `mmessage` VALUES (1, 'bla',     'bla 1', NULL, '2007-03-05 12:11:58', 1, 2);
INSERT INTO `mmessage` VALUES (2, 'blub',    'bla 1', NULL, '2007-03-05 12:15:50', 2, 2);
INSERT INTO `mmessage` VALUES (3, 'schmick', 'bla 1', NULL, '2007-03-05 12:15:50', 3, 2);

-- --------------------------------------------------------

DROP TABLE IF EXISTS `mperson`;
CREATE TABLE `mperson` (
  `person_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4;

INSERT INTO `mperson` VALUES (1, 'Peter');
INSERT INTO `mperson` VALUES (2, 'Paul');
INSERT INTO `mperson` VALUES (3, 'Mary');

-- --------------------------------------------------------

ALTER TABLE `mmessage`
  ADD CONSTRAINT `author_of_message` FOREIGN KEY (`author_id`) REFERENCES `mperson` (`person_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `mmessage`
  ADD CONSTRAINT `recipient_of_message` FOREIGN KEY (`recipient_id`) REFERENCES `mperson` (`person_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

-- --------------------------------------------------------
-- --------------------------------------------------------

DROP TABLE IF EXISTS `ncolor`;
CREATE TABLE `ncolor` (
  `color_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `colortype_id` int(11) NOT NULL ,
  PRIMARY KEY (`color_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=5;

INSERT INTO `ncolor` VALUES (1, 'lightgreen', 1);
INSERT INTO `ncolor` VALUES (2, 'darkgreen',  1);
INSERT INTO `ncolor` VALUES (3, 'lightgray',  2);
INSERT INTO `ncolor` VALUES (4, 'brown',      3);

-- --------------------------------------------------------

DROP TABLE IF EXISTS `ncolortype`;
CREATE TABLE `ncolortype` (
  `colortype_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`colortype_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4;

INSERT INTO `ncolortype` VALUES (1, 'green');
INSERT INTO `ncolortype` VALUES (2, 'gray');
INSERT INTO `ncolortype` VALUES (3, 'brown');

-- --------------------------------------------------------

DROP TABLE IF EXISTS `ntexture`;
CREATE TABLE `ntexture` (
  `texture_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `color_id` int(11) NOT NULL,
  PRIMARY KEY (`texture_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=4;

INSERT INTO `ntexture` VALUES (1, 'street', 3);
INSERT INTO `ntexture` VALUES (2, 'gras',   2);
INSERT INTO `ntexture` VALUES (3, 'castle', 4);

-- --------------------------------------------------------

DROP TABLE IF EXISTS `nmappoint`;
CREATE TABLE `nmappoint` (
  `coord_x` int(11) NOT NULL,
  `coord_y` int(11) NOT NULL,
  `texture_id` int(11) NOT NULL,
  PRIMARY KEY (`coord_x`, `coord_y`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;

INSERT INTO `nmappoint` VALUES ( 1, 2, 1);
INSERT INTO `nmappoint` VALUES ( 2, 4, 2);
INSERT INTO `nmappoint` VALUES (10, 1, 3);

-- --------------------------------------------------------

DROP TABLE IF EXISTS `nmobileObject`;
CREATE TABLE `nmobileObject` (
  `object_id` int(11) NOT NULL AUTO_INCREMENT,
  `coord_x` int(11) NOT NULL,
  `coord_y` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`object_id`),
  KEY `coords` (`coord_x`, `coord_y`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=7;

INSERT INTO `nmobileObject` VALUES (1, 2, 4, 'tree');
INSERT INTO `nmobileObject` VALUES (2, 2, 4, 'tree');
INSERT INTO `nmobileObject` VALUES (3, 1, 2, 'car');
INSERT INTO `nmobileObject` VALUES (4, 10, 1, 'person1');
INSERT INTO `nmobileObject` VALUES (5, 10, 1, 'person2');
-- INSERT INTO `mobileObject` VALUES (6, 2, 2, 'Puppy-Poo');

-- --------------------------------------------------------

ALTER TABLE `nmobileObject`
  ADD CONSTRAINT `mappoint_for_mob` FOREIGN KEY `coords` (`coord_x`, `coord_y`) REFERENCES `nmappoint` (`coord_x`, `coord_y`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `nmappoint`
  ADD CONSTRAINT `texture_for_mappoint` FOREIGN KEY (`texture_id`) REFERENCES `ntexture` (`texture_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `ntexture`
  ADD CONSTRAINT `color_for_texture` FOREIGN KEY (`color_id`) REFERENCES `ncolor` (`color_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

ALTER TABLE `ncolor`
  ADD CONSTRAINT `colortype_for_color` FOREIGN KEY (`colortype_id`) REFERENCES `ncolortype` (`colortype_id`)
  ON DELETE CASCADE
  ON UPDATE CASCADE;

-- --------------------------------------------------------
-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS=1;
