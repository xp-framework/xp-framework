/*
 * Database scheme for planet-xp's syndicate
 *
 * $Id$
 */

create database syndicate
use syndicate

/* Administrative tables */
create table syndicate (
  syndicate_id                int primary key auto_increment,
  name                        varchar(255) not null,
  administrator               varchar(255) null
) Type=InnoDB

create table feedsettings (
  feed_id                     int primary key auto_increment,
  url                         varchar(255) not null,
  lastcheck                   datetime null,
  nextcheck                   datetime null,
  checkinterval               int
) Type=InnoDB

create table syndicate_feed_matrix (
  syndicate_id                int,
  feed_id                     int
) Type=InnoDB
alter table syndicate_feed_matrix add constraint primary key (syndicate_id, feed_id)
alter table syndicate_feed_matrix add foreign key (syndicate_id) references syndicate (syndicate_id)
alter table syndicate_feed_matrix add foreign key (feed_id) references feedsettings (feed_id)
/* Data tables */
create table feed (
  feed_id                     int not null,
  title                       varchar(255) not null,
  link                        varchar(255) not null,
  description                 text null,
  lastchange                  datetime null,
  changedby                   varchar(50) null
) Type=InnoDB
alter table feed add constraint foreign key (feed_id) references feedsettings(feed_id)

create table feeditem (
  feeditem_id                 int primary key auto_increment,
  feed_id                     int not null,
  title                       varchar(255) not null,
  description                 text null,
  link                        varchar(255) null,
  author                      varchar(255) not null
) Type=InnoDB
alter table feeditem add constraint foreign key (feed_id) references feedsettings(feed_id)
