-- CAFFEINE RDBMS (Sybase)
--
-- $Id$

-- Create database
-- Size: 10 Megabyte
use master
go
create database CAFFEINE on default = 10
go

-- News
use CAFFEINE
go

create table news (
  news_id           numeric(10)     identity                primary key,
  caption           varchar(255)                            not null,
  link              varchar(255)                            null,
  body              text                                    null,
  created_at        smalldatetime   default getdate()       not null,
  lastchange        smalldatetime   default getdate()       not null,
  changedby         varchar(255)    default suser_name()    not null,
  bz_id             numeric(10)     default 500             not null
)
go

-- Add login / user and grant select
sp_addlogin "news", "enieffac"
go

sp_adduser news
go
grant select on news to news
go

-- People, groups and logins
use CAFFEINE
go

create table person (
  person_id         numeric(10)     identity                primary key,
  firstname         varchar(255)                            not null,
  lastname          varchar(255)                            not null,
  email             varchar(255)                            not null
)
go
create nonclustered index I_email on person (email)
go

create table account (
  account_id        numeric(10)     identity                primary key,
  person_id         numeric(10)                             not null,
  username          varchar(255)                            not null,
  password          varchar(255)                            not null,
  created_at        smalldatetime   default getdate()       not null,
  lastchange        smalldatetime   default getdate()       not null,
  changedby         varchar(255)    default suser_name()    not null,
  bz_id             numeric(10)     default 500             not null
)
go
alter table account add constraint FK_account_person (person_id) references person (person_id)
go

create table group (
  group_id          numeric(10)                             not null,
  account_id        numeric(10)                             not null,
  name              varchar(255)                            not null
)
go
alter table group add constraint PK_group primary key clustered (group_id, account_id)
go
alter table group add constraint FK_group_account (account_id) references account (account_id)
go
alter table group add unique nonclustered (name)
go

-- Permissions
use CAFFEINE
go

create table permission (
  permission_id     numeric(10)                             not null,
  name              varchar(30)                             not null
)
go

create table acl (
  group_id          numeric(10)                             not null,
  permission_id     numeric(10)                             not null
)
go
alter table acl add constraint PK_acl primary key clustered (account_id, permission_id)
go
alter table acl add constraint FK_acl_account (account_id) references account (account_id)
go
alter table acl add constraint FK_acl_permission (permission_id) references permission (permission_id)
go

-- EOF
