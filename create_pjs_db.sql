--
-- phpjobseeker
--
-- Copyright (C) 2009 Kevin Benton - kbenton at bentonfam dot org
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License along
-- with this program; if not, write to the Free Software Foundation, Inc.,
-- 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
-- 
-- 

-- -----------------------------------------------------------------------------------
-- version
-- -----------------------------------------------------------------------------------
create table version
     (
       versionValue          varchar(255) not null default ''
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     ) engine='MyISAM';
-- This is the only DML in this file but it really needs to be here since it's the\
-- version of the data layout.
insert version (versionValue) value ('0.0PA1');

-- -----------------------------------------------------------------------------------
-- applicationStatus
-- -----------------------------------------------------------------------------------
create table applicationStatus
     (
       applicationStatusId   int unsigned not null auto_increment primary key
     , statusValue           varchar(50) not null
     , isActive              boolean not null default 1
     , sortKey               smallint(3) unsigned not null default 100
     , style                 varchar(4096) not null default ''
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     ) engine='MyISAM';
insert into applicationStatus (applicationStatusId, isActive, statusValue, sortKey) values
       ( 1, 1, 'FOUND'        , 10)
     , ( 2, 1, 'CONTACTED'    , 20)
     , ( 3, 1, 'APPLIED'      , 30)
     , ( 4, 1, 'INTERVIEWING' , 40)
     , ( 5, 1, 'FOLLOWUP'     , 50)
     , ( 6, 1, 'CHASING'      , 60)
     , ( 7, 1, 'NETWORKING'   , 70)
     , ( 8, 0, 'UNAVAILABLE'  , 999)
     , ( 9, 0, 'INVALID'      , 999)
     , (10, 0, 'DUPLICATE'    , 999)
     , (11, 0, 'CLOSED'       , 999)
     ;
update applicationStatus set created = updated;

-- -----------------------------------------------------------------------------------
-- company
-- -----------------------------------------------------------------------------------
create table company
     (
       companyId             int unsigned not null auto_increment primary key
     , isAnAgency            boolean not null default 0
     , agencyCompanyId       int unsigned null default null comment 'When isAnAgency is false, this points to the company ID of the agency'
     , companyName           varchar(100) not null default ''
     , companyAddress1       varchar(255) not null default ''
     , companyAddress2       varchar(255) not null default ''
     , companyCity           varchar(60) not null default ''
     , companyState          char(2) not null default 'XX'
     , companyZip            int(5) unsigned null default null
     , companyPhone          int unsigned null default null
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     ) engine='MyISAM';

-- -----------------------------------------------------------------------------------
-- contact
-- -----------------------------------------------------------------------------------
create table contact
     (
       contactId             int unsigned not null auto_increment primary key
     , contactCompanyId      int unsigned not null default 0
     , contactName           varchar(255)
     , contactEmail          varchar(255)
     , contactPhone          int unsigned not null
     , contactAlternatePhone int unsigned null default null
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     ) engine='MyISAM';

-- -----------------------------------------------------------------------------------
-- job
-- -----------------------------------------------------------------------------------
create table job
     (
       jobId                 int unsigned not null auto_increment primary key
     , primaryContactId      int unsigned null default null
     , companyId             int unsigned null default null
     , applicationStatusId   int unsigned not null
     , lastStatusChange      datetime not null default 0
     , urgency               enum('high', 'medium', 'low') not null default 'low'
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     , nextActionDue         datetime not null default 0
     , nextAction            varchar(255) not null default ''
     , positionTitle         varchar(255) not null default ''
     , location              varchar(255) not null default ''
     , url                   varchar(4096) not null default ''
     ) engine='MyISAM';

-- -----------------------------------------------------------------------------------
-- jobKeywordMap
-- -----------------------------------------------------------------------------------
create table jobKeywordMap (
       jobId     int unsigned not null
     , keywordId int unsigned not null
     , created   timestamp not null default 0
     , updated   timestamp not null default current_timestamp
                 on update current_timestamp
     , primary key jobKeywordMapIdx ( jobId, keywordId )
     ) engine='MyISAM';

-- -----------------------------------------------------------------------------------
-- keyword
-- -----------------------------------------------------------------------------------
create table keyword
     (
       keywordId             int unsigned not null auto_increment primary key
     , keywordValue          varchar(255) not null
     , sortKey               smallint(3) not null default 0
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     , unique index valueIdx (keywordValue)
     ) engine='MyISAM';

-- -----------------------------------------------------------------------------------
-- note
-- -----------------------------------------------------------------------------------
create table note
     (
       noteId                int unsigned not null auto_increment primary key
     , appliesToTable        enum('job', 'company', 'contact', 'keyword', 'search')
     , appliesToId           int unsigned not null
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     , note                  text not null
     , fulltext index noteIdx (note)
     , index appliesTo (appliesToTable, appliesToId, created)
     ) engine='MyISAM';


-- -----------------------------------------------------------------------------------
-- search
-- -----------------------------------------------------------------------------------
create table search
     (
       searchId              int unsigned not null auto_increment primary key
     , engineName            varchar(255) not null default ''
     , searchName            varchar(255) not null default ''
     , url                   varchar(4096) not null default ''
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     ) engine='MyISAM';