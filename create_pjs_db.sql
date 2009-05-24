-- WARNING - running this script will wipe out any existing PJS data you have.  Please
-- be sure to back up any existing data before running this script.

drop database if exists pjs;
create database pjs;
use pjs;

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
     , urgency               enum('high', 'medium', 'low') not null default 'low'
     , nextActionDue         datetime not null default 0
     , lastStatusChange      datetime not null default 0
     , created               timestamp not null default 0
     , updated               timestamp not null default current_timestamp
                             on update current_timestamp
     , positionTitle         varchar(255) not null default ''
     , applicationStatusId   varchar(255) not null default ''
     , nextAction            varchar(255) not null default ''
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
     , primary key jobKeywordIdx ( jobId, keywordId )
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
