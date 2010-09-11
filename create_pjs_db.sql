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
     ) engine='InnoDB';
-- This is nearly the only DML in this file but it really needs to be here since
-- it's the version of the data layout.
insert version (versionValue) value ('0.0PA2');
commit;

-- -----------------------------------------------------------------------------------
-- applicationStatus
-- -----------------------------------------------------------------------------------
create table applicationStatus
     (
       applicationStatusId   int unsigned not null auto_increment
     , statusValue           varchar(50) not null
     , isActive              boolean not null default 1
     , sortKey               smallint(3) unsigned not null default 100
     , style                 varchar(4096) not null default ''
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     , primary key pk_applicationStatusId ( applicationStatusId )
     ) engine='InnoDB';

-- -----------------------------------------------------------------------------------
-- applicationStatusSummary
-- -----------------------------------------------------------------------------------
create table applicationStatusSummary
     (
       applicationStatusId int unsigned not null /* No auto_increment: foreign key */
     , statusCount         int unsigned not null default 0
     , created             timestamp not null default 0
     , updated             timestamp not null default current_timestamp
                           on update current_timestamp
     , primary key pk_applicationStatusId ( applicationStatusId )
     , foreign key fk_applicationStatusId ( applicationStatusId )
        references applicationStatus ( applicationStatusId )
                on delete cascade
                on update cascade
     ) engine='InnoDB';

-- -----------------------------------------------------------------------------------
-- company
-- -----------------------------------------------------------------------------------
create table company
     (
       companyId             int unsigned not null auto_increment
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
     , primary key pk_companyId ( companyId )
     , foreign key fk_agencyCompanyId ( companyId )
        references company ( companyId )
                on delete cascade
                on update cascade
     ) engine='InnoDB';

-- -----------------------------------------------------------------------------------
-- contact
-- -----------------------------------------------------------------------------------
create table contact
     (
       contactId             int unsigned not null auto_increment
     , contactCompanyId      int unsigned not null default 0
     , contactName           varchar(255)
     , contactEmail          varchar(255)
     , contactPhone          int unsigned not null
     , contactAlternatePhone int unsigned null default null
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     , primary key pk_contactId ( contactId )
     , foreign key fk_contactCompanyId ( contactCompanyId )
        references company ( companyId )
                on delete cascade
                on update cascade
     ) engine='InnoDB';
commit;

-- -----------------------------------------------------------------------------------
-- job
-- -----------------------------------------------------------------------------------
create table job
     (
       jobId                 int unsigned not null auto_increment
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
     , primary key pk_jobId ( jobId )
     , foreign key fk_primaryContactId ( primaryContactId )
        references contact ( contactId )
                on delete cascade
                on update cascade
     , foreign key fk_companyId ( companyId )
        references company ( companyId )
                on delete cascade
                on update cascade
     , foreign key fk_applicationStatusId ( applicationStatusId )
        references applicationStatus ( applicationStatusId )
                on delete cascade
                on update cascade
     ) engine='InnoDB';

-- -----------------------------------------------------------------------------------
-- keyword
-- -----------------------------------------------------------------------------------
create table keyword
     (
       keywordId             int unsigned not null auto_increment
     , keywordValue          varchar(255) not null
     , sortKey               smallint(3) not null default 0
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     , primary key pk_keywordId ( keywordId )
     , unique index valueIdx (keywordValue)
     ) engine='InnoDB';

-- -----------------------------------------------------------------------------------
-- note
-- -----------------------------------------------------------------------------------
create table note
     (
       noteId                int unsigned not null auto_increment
     , appliesToTable        enum('job', 'company', 'contact', 'keyword', 'search')
     , appliesToId           int unsigned not null
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     , note                  text not null
     , primary key pk_noteId ( noteId )
     , index appliesTo (appliesToTable, appliesToId, created)
     ) engine='InnoDB';

-- -----------------------------------------------------------------------------------
-- search
-- -----------------------------------------------------------------------------------
create table search
     (
       searchId              int unsigned not null auto_increment
     , engineName            varchar(255) not null default ''
     , searchName            varchar(255) not null default ''
     , url                   varchar(4096) not null default ''
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     , primary key pk_searchId ( searchId )
     ) engine='InnoDB';

-- -----------------------------------------------------------------------------------
-- jobKeywordMap (constraints require job and keyword to exist first.
-- -----------------------------------------------------------------------------------
create table jobKeywordMap (
       jobId     int unsigned not null
     , keywordId int unsigned not null
     , created   timestamp not null default 0
     , updated   timestamp not null default current_timestamp
                 on update current_timestamp
     , primary key jobKeywordMapIdx ( jobId, keywordId )
     , foreign key fk_jobId ( jobId )
        references job ( jobId )
                on delete cascade
                on update cascade
     , foreign key fk_keywordId ( keywordId )
        references keyword ( keywordId )
                on delete cascade
                on update cascade
     ) engine='InnoDB';

-- -----------------------------------------------------------------------------------
-- Triggers
-- -----------------------------------------------------------------------------------
delimiter $$
create trigger applicationStatusAfterInsertTrigger
 after insert
    on applicationStatus
   for each row
 begin
       insert applicationStatusSummary
            ( applicationStatusId
            , statusCount
            , created
            , updated
            )
       values
            ( NEW.applicationStatusId
            , 0
            , NOW()
            , NOW()
            );
   end $$

create trigger jobAfterInsertTrigger
 after insert
    on job
   for each row
 begin
       update applicationStatusSummary
           as jss
          set jss.statusCount = jss.statusCount + 1
        where jss.applicationStatusId = NEW.applicationStatusId;
   end $$

create trigger jobAfterUpdateTrigger
 after update
    on job
   for each row
 begin
	     if OLD.applicationStatusId <> NEW.applicationStatusId
	   then
            update applicationStatusSummary
                as jss
               set jss.statusCount = jss.statusCount + 1
             where jss.applicationStatusId = NEW.applicationStatusId;
            update applicationStatusSummary
                as jss
               set jss.statusCount = jss.statusCount + 1
             where jss.applicationStatusId = OLD.applicationStatusId;
        end if;
	     if OLD.jobId <> NEW.jobId
	   then
            update note
               set note.appliesToId = NEW.jobId
             where note.appliesToId = OLD.jobId
               and note.appliestoTable = 'job'
                 ;
      end if;
   end $$

create trigger jobAfterDeleteTrigger
 after delete
    on job
   for each row
 begin
       update applicationStatusSummary
           as jss
          set jss.statusCount = jss.statusCount - 1
        where jss.applicationStatusId = OLD.applicationStatusId;

       delete
         from note
        where note.appliesToTable = 'job'
          and note.appliesToId = OLD.jobId;
   end $$

create trigger companyAfterDeleteTrigger
 after delete
    on company
   for each row
 begin
       delete
         from note
        where appliesToTable = 'company'
          and appliesToId = OLD.companyId;
   end $$

create trigger companyAfterUpdateTrigger
 after update
    on company
   for each row
 begin
	     if OLD.companyId <> NEW.companyId
	   then
            update note
               set note.appliesToId = NEW.companyId
             where note.appliesToId = OLD.companyId
               and note.appliestoTable = 'company'
                 ;
      end if;
   end $$

create trigger contactAfterDeleteTrigger
 after delete
    on contact
   for each row
 begin
       delete
         from note
        where appliesToTable = 'contact'
          and appliesToId = OLD.contactId;
   end $$

create trigger contactAfterUpdateTrigger
 after update
    on contact
   for each row
 begin
	     if OLD.contactId <> NEW.contactId
	   then
            update note
               set note.appliesToId = NEW.contactId
             where note.appliesToId = OLD.contactId
               and note.appliestoTable = 'contact'
                 ;
      end if;
   end $$

create trigger keywordAfterDeleteTrigger
 after delete
    on keyword
   for each row
 begin
       delete
         from note
        where appliesToTable = 'keyword'
          and appliesToId = OLD.keywordId;
   end $$

create trigger keywordAfterUpdateTrigger
 after update
    on keyword
   for each row
 begin
	     if OLD.keywordId <> NEW.keywordId
	   then
            update note
               set note.appliesToId = NEW.keywordId
             where note.appliesToId = OLD.keywordId
               and note.appliestoTable = 'keyword'
                 ;
      end if;
   end $$

create trigger searchAfterDeleteTrigger
 after delete
    on search
   for each row
 begin
       delete
         from note
        where appliesToTable = 'search'
          and appliesToId = OLD.searchId;
   end $$

create trigger searchAfterUpdateTrigger
 after update
    on search
   for each row
 begin
	     if OLD.searchId <> NEW.searchId
	   then
            update note
               set note.appliesToId = NEW.searchId
             where note.appliesToId = OLD.searchId
               and note.appliestoTable = 'search'
                 ;
      end if;
   end $$


delimiter ;

-- -----------------------------------------------------------------------------------
-- Pre-Fill Data
-- -----------------------------------------------------------------------------------
insert applicationStatus
     ( applicationStatusId
     , isActive
     , statusValue
     , sortKey
     )
values ( 1, 1, 'FOUND'        , 10)
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
commit;
