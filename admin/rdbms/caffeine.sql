-- CAFFEINE RDBMS (Sybase)
--
-- $Id$

-- Create database
-- Size: 10 Megabyte
use master
go
create database CAFFEINE on default = 10
go

-- Create table news
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
