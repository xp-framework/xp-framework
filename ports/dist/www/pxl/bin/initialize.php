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
      
      lastchange      datetime not null,
      published       datetime null,
      is_published    integer null
    );
    create index i_page_published on page(published)
    create index i_page_is_published on page(is_published)
  ');
  
  $db->query('
    create table picture (
      picture_id      integer primary key,
      page_id         integer references page(page_id) not null,
      title           text null,
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

  // }}}

?>
