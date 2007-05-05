<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses(
    'util.PropertyManager',
    'rdbms.ConnectionManager'
  );

  // {{{ main
  $pm= PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../etc');

  $cm= ConnectionManager::getInstance();
  $cm->configure($pm->getProperties('database'));
  
  $db= $cm->getByHost('pxl', 0);
  $db->query('
    create table author (
      author_id       integer primary key,
      
      username        varchar(32) not null unique,
      password        varchar(50) not null,
      realname        varchar(50) not null,
      email           varchar(255)
    );
  ');
  
  $db->query('
    create table page (
      page_id         integer primary key,

      title           varchar(255) not null,
      description     text null,
      author_id       integer references author(author_id) not null,
      
      sequence        integer,
      published       datetime null,
      
      lastchange      datetime not null,
      changedby       varchar(50)
    );
    
    create index i_page_published on page(published)
    create index i_page_is_published on page(is_published)
    create index i_seq on page(sequence)
  ');
  
  $db->query('
    create table picture (
      picture_id      integer primary key,
      page_id         integer references page(page_id) not null,
      filename        varchar(255) not null,
      author_id       integer references author(author_id) not null
    );
    create index i_picture_page on picture(page_id);
  ');

  $db->query('
    create table tag (
      page_id         integer references page(page_id) not null,
      tag             varchar(50) not null
    );
    create index i_tag_tag on tag(tag);
  ');
  
  $db->query('
    create table progress (
      bz_id           integer primary key,
      description     varchar(200)
    );
    
    insert into progress (bz_id, description) values (500, "new");
    insert into progress (bz_id, description) values (20000, "done");
    insert into progress (bz_id, description) values (30001, "delete");
    insert into progress (bz_id, description) values (40000, "deleted");
  ');
  
  $db->query('
    create table comment (
      comment_id      integer primary key,
      comment_type_id integer not null default 0,
      
      page_id         integer references page(page_id) not null,
      bz_id           integer references progress(bz_id) not null,
      title           varchar(255),
      body            text,
      url             text,
      commented_at    datetime not null,
      
      author          varchar(255),
      email           varchar(255)
    );
    create index i_comment_page on comment(page_id);
  ');


  // }}}

?>
