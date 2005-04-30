/*
 * Database structure for uska.de
 *
 * $Id$
 */

create table team (
  team_id                     int auto_increment primary key,
  name                        varchar(255) not null
) Type=InnoDB

create table player (
  player_id                   int auto_increment primary key,
  player_type_id              int not null,
  team_id                     int not null,
  
  firstname                   varchar(50) not null,
  lastname                    varchar(50) not null,
  username                    varchar(20) null,
  
  password                    varchar(60) null,
  email                       varchar(255) null,
  
  position                    int null,
  created_by                  int null,
  
  lastchange                  datetime not null,
  changedby                   varchar(50) not null
) Type=InnoDB
alter table player add unique index (username)
alter table player add foreign key (created_by) references player(player_id)
alter table player add foreign key (team_id) references team(team_id)

create table event_type (
  event_type_id               int primary key,
  description                 varchar(50) not null
) Type=InnoDB
insert into event_type values (1, "Training")
insert into event_type values (2, "Turnier")
insert into event_type values (3, "Sonstiges")

create table event (
  event_id                    int auto_increment primary key,
  team_id                     int not null,
  event_type_id               int not null
  
  name                        varchar(255) not null,
  description                 varchar(1023) null,
  
  target_date                 datetime not null,
  deadline                    datetime null,
  
  max_attendees               int null,
  req_attendees               int null,
  allow_guests                int default 1,
  
  lastchange                  datetime not null,
  changedby                   varchar(50) not null
) Type=InnoDB
alter table event add index (target_date)
alter table event add foreign key (team_id) references team(team_id)
alter table event add foreign key (event_type_id) references event_type(event_type_id)

create table event_attendee (
  event_id                    int not null,
  player_id                   int not null,
  
  attends                     int not null,
  offers_seats                int not null,
  needs_driver                int not null,
  
  lastchange                  datetime not null,
  changedby                   varchar(50) not null
) Type=InnoDB
alter table event_attendee add foreign key (event_id) references event(event_id)
alter table event_attendee add foreign key (player_id) references player(player_id)
alter table event_attendee add index (event_id)
alter table event_attendee add index (player_id)
alter table event_attendee add unique index (event_id, player_id)

create table event_points (
  event_id                    int not null,
  player_id                   int not null,
  
  points                      int null
) Type=InnoDB
alter table event_points add foreign key (event_id) references event(event_id)
alter table event_points add foreign key (player_id) references player(player_id)
alter table event_points add index (player_id)

create table permission (
  permission_id               int auto_increment primary key,
  name                        varchar(50) not null
) Type=InnoDB
insert into permission values (1, "create_player")
insert into permission values (2, "create_event")
insert into permission values (3, "create_news")

create table plane_right_matrix (
  permission_id               int not null,
  player_id                   int not null
) Type=InnoDB
alter table plane_right_matrix add index (player_id)
alter table plane_right_matrix add unique index (permission_id, player_id)
alter table plane_right_matrix add foreign key (permission_id) references permission(permission_id)
alter table plane_right_matrix add foreign key (player_id) references player(player_id)
