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

/* Data tables */
create table feed (
  feed_id                     int primary key auto_increment,
  url                         varchar(255) not null,
  lastcheck                   datetime null,
  nextcheck                   datetime null,
  checkinterval               int null,
  bz_id                       int not null default 500,
  author                      varchar(255) null,

  /* data columns */
  title                       varchar(255) not null,
  link                        varchar(255) not null,
  description                 text null,
  published                   datetime null,
  
  lastchange                  datetime null,
  changedby                   varchar(50) null
) Type=InnoDB

create table syndicate_feed_matrix (
  syndicate_id                int,
  feed_id                     int
) Type=InnoDB

alter table syndicate_feed_matrix add unique index (syndicate_id, feed_id)
alter table syndicate_feed_matrix add foreign key (syndicate_id) references syndicate (syndicate_id)
alter table syndicate_feed_matrix add foreign key (feed_id) references feed (feed_id)

create table feeditem (
  feeditem_id                 int primary key auto_increment,
  feed_id                     int not null,
  
  title                       varchar(255) not null,
  content                     text null,
  link                        varchar(255) null,
  author                      varchar(255) null,
  published                   datetime not null,
  guid                        varchar(255) not null,
  lastchange                  datetime not null,
  changedby                   varchar(50) not null
) Type=InnoDB
alter table feeditem add constraint foreign key (feed_id) references feed (feed_id)
alter table feeditem add unique index (link)
alter table feeditem add index (published)

create table progress (
  bz_id                       int primary key,
  description                 varchar(100) not null
) Type=InnoDB

insert into progress values (500, 'New')
insert into progress values (10000, 'Initialize')
insert into progress values (20000, 'Done')
insert into progress values (21000, 'Broken')
insert into progress values (30000, 'Locked')
insert into progress values (30001, 'Delete')
insert into progress values (40000, 'Deleted')

create table authormapping (
  feed_id                     int null,
  author_from                 varchar(255) not null,
  author_to                   varchar(255) not null
) Type=InnoDB
alter table authormapping add constraint foreign key (feed_id) references feed (feed_id)
