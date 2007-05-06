create table progress (
  bz_id           integer primary key,
  description     varchar(200)
);
create table author (
  author_id       integer primary key not null,

  username        varchar(32) not null,
  password        varchar(50) not null,
  realname        varchar(50) not null,
  email           varchar(255)
);
create table page (
  page_id         integer primary key not null,

  bz_id           integer references progress(bz_id) not null default 20000,
  author_id       integer references author(author_id) not null,
  
  title           varchar(255) not null,
  description     text null,
  permalink       varchar(255) null,

  sequence        integer not null,
  published       datetime null,

  lastchange      datetime not null,
  changedby       varchar(50)
);

create table picture (
  picture_id      integer primary key not null,
  page_id         integer references page(page_id) not null,
  filename        varchar(255) not null,
  author_id       integer references author(author_id) not null
);

create table tag (
  page_id         integer references page(page_id) not null,
  tag             varchar(50) not null
);

create table comment (
  comment_id      integer primary key not null,
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

-- primary keys
create unique index pk_author on author(author_id);
create unique index pk_page on page(page_id);
create unique index pk_picture on picture(picture_id);
create unique index pk_comment on comment(comment_id);

create unique index i_seq on page(sequence);
create index i_page_published on page(published);

create unique index  i_author_username on author(username);

create unique index i_picture_pagefile on picture(page_id, filename);
create index i_picture_page on picture(page_id);

create index i_tag_tag on tag(tag);
create index i_tag_page on tag(page_id);

create index i_comment_page on comment(page_id);

insert into progress (bz_id, description) values (500, "new");
insert into progress (bz_id, description) values (20000, "done");
insert into progress (bz_id, description) values (30001, "delete");
insert into progress (bz_id, description) values (40000, "deleted");
