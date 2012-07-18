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

-- Allow addition of zero default created timestamp so the DB will know
-- to make it the current timestamp (funky hack, but works)
SET @@session.sql_mode = '' ;
SET @@session.autocommit = 1 ;
SET @@session.foreign_key_checks = 0 ;

-- -----------------------------------------------------------------------------------
-- version
-- -----------------------------------------------------------------------------------
CREATE TABLE version
     (
       versionValue          VARCHAR(255) NOT NULL DEFAULT ''
     , updated               TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP
     ) ENGINE='InnoDB' ;
-- This is nearly the only DML in this file but it really needs to be here since
-- it's the version of the data layout.
INSERT version ( versionValue ) VALUES ( '0.0PA2' ) ;

-- -----------------------------------------------------------------------------------
-- applicationStatus
-- -----------------------------------------------------------------------------------
CREATE TABLE applicationStatus
     (
       applicationStatusId   INT UNSIGNED NOT NULL AUTO_INCREMENT
     , statusValue           VARCHAR(50) NOT NULL
     , isActive              BOOLEAN NOT NULL DEFAULT 1
     , sortKey               SMALLINT(3) UNSIGNED NOT NULL DEFAULT 100
     , style                 VARCHAR(4096) NOT NULL DEFAULT ''
     , created               TIMESTAMP NOT NULL DEFAULT 0
     , updated               TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP
     , PRIMARY KEY pk_applicationStatusId ( applicationStatusId )
     ) ENGINE='InnoDB' ;

-- -----------------------------------------------------------------------------------
-- applicationStatusSummary
-- -----------------------------------------------------------------------------------
CREATE TABLE applicationStatusSummary
     (
       applicationStatusId INT UNSIGNED NOT NULL /* No auto_increment: foreign key */
     , statusCount         INT UNSIGNED NOT NULL DEFAULT 0
     , created             TIMESTAMP NOT NULL DEFAULT 0
     , updated             TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                           ON UPDATE CURRENT_TIMESTAMP
     , PRIMARY KEY pk_applicationStatusId ( applicationStatusId )
     , FOREIGN KEY fk_applicationStatusId ( applicationStatusId )
        REFERENCES applicationStatus ( applicationStatusId )
                ON DELETE CASCADE
                ON UPDATE CASCADE
     ) ENGINE='InnoDB' ;

-- -----------------------------------------------------------------------------------
-- company
-- TODO Agency model needs to change. It isn't consistent.
--      Should have an agent table with an agencyCompanyId and an
--      agencyCustomerCompanyId
-- TODO Should allow for companies that use agencies and self-represent/hire.
-- -----------------------------------------------------------------------------------
CREATE TABLE company
     (
       companyId             INT UNSIGNED NOT NULL AUTO_INCREMENT
     , isAnAgency            BOOLEAN NOT NULL DEFAULT 0
     , agencyCompanyId       INT UNSIGNED NULL DEFAULT NULL
                             COMMENT 'When isAnAgency is false, point to agency company ID'
     , companyName           VARCHAR(100) NOT NULL DEFAULT ''
     , companyAddress1       VARCHAR(255) NOT NULL DEFAULT ''
     , companyAddress2       VARCHAR(255) NOT NULL DEFAULT ''
     , companyCity           VARCHAR(60) NOT NULL DEFAULT ''
     , companyState          CHAR(2) NOT NULL DEFAULT 'XX'
     , companyZip            INT(5) UNSIGNED NULL DEFAULT NULL
     , companyPhone          INT UNSIGNED NULL DEFAULT NULL
     , created               TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'
     , updated               TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP
     , PRIMARY KEY pk_companyId ( companyId )
     , FOREIGN KEY fk_agencyCompanyId ( agencyCompanyId )
        REFERENCES company ( companyId )
                ON DELETE CASCADE
                ON UPDATE CASCADE
     
     ) ENGINE='InnoDB' ;

-- -----------------------------------------------------------------------------------
-- contact
-- -----------------------------------------------------------------------------------
CREATE TABLE contact
     (
       contactId             INT UNSIGNED NOT NULL AUTO_INCREMENT
     , contactCompanyId      INT UNSIGNED NOT NULL DEFAULT 0
     , contactName           VARCHAR(255)
     , contactEmail          VARCHAR(255)
     , contactPhone          INT UNSIGNED NOT NULL
     , contactAlternatePhone INT UNSIGNED NULL DEFAULT NULL
     , created               TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'
     , updated               TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP
     , PRIMARY KEY pk_contactId ( contactId )
     , FOREIGN KEY fk_contactCompanyId ( contactCompanyId )
        REFERENCES company ( companyId )
                ON DELETE CASCADE
                ON UPDATE CASCADE
     ) ENGINE='InnoDB' ;

-- -----------------------------------------------------------------------------------
-- job
-- -----------------------------------------------------------------------------------
CREATE TABLE job
     (
       jobId                 INT UNSIGNED NOT NULL AUTO_INCREMENT
     , primaryContactId      INT UNSIGNED NULL DEFAULT NULL
     , companyId             INT UNSIGNED NULL DEFAULT NULL
     , applicationStatusId   INT UNSIGNED NOT NULL
     , lastStatusChange      DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
     , urgency               ENUM( 'high', 'medium', 'low' ) NOT NULL DEFAULT 'low'
     , created               TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'
     , updated               TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP
     , nextActionDue         DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'
     , nextAction            VARCHAR(255) NOT NULL DEFAULT ''
     , positionTitle         VARCHAR(255) NOT NULL DEFAULT ''
     , location              VARCHAR(255) NOT NULL DEFAULT ''
     , url                   VARCHAR(4096) NOT NULL DEFAULT ''
     , PRIMARY KEY pk_jobId ( jobId )
     , FOREIGN KEY fk_primaryContactId ( primaryContactId )
        REFERENCES contact ( contactId )
                ON DELETE CASCADE
                ON UPDATE CASCADE
     , FOREIGN KEY fk_companyId ( companyId )
        REFERENCES company ( companyId )
                ON DELETE CASCADE
                ON UPDATE CASCADE
     , FOREIGN KEY fk_applicationStatusId ( applicationStatusId )
        REFERENCES applicationStatus ( applicationStatusId )
                ON DELETE CASCADE
                ON UPDATE CASCADE
     ) ENGINE='InnoDB' ;

-- -----------------------------------------------------------------------------------
-- keyword
-- -----------------------------------------------------------------------------------
CREATE TABLE keyword
     (
       keywordId             INT UNSIGNED NOT NULL AUTO_INCREMENT
     , keywordValue          VARCHAR(255) NOT NULL
     , sortKey               SMALLINT(3) NOT NULL DEFAULT 0
     , created               TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'
     , updated               TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP
     , PRIMARY KEY pk_keywordId ( keywordId )
     , UNIQUE index valueIdx ( keywordValue )
     ) ENGINE='InnoDB' ;

-- -----------------------------------------------------------------------------------
-- note
-- -----------------------------------------------------------------------------------
CREATE TABLE note
     (
       noteId                INT UNSIGNED NOT NULL AUTO_INCREMENT
     , appliesToTable        ENUM( 'job', 'company', 'contact', 'keyword', 'search' ) NOT NULL
     , appliesToId           INT UNSIGNED NOT NULL
     , created               TIMESTAMP NOT NULL DEFAULT 0
     , updated               TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP
     , note                  TEXT NOT NULL
     , PRIMARY KEY pk_noteId ( noteId )
     , INDEX appliesTo ( appliesToTable, appliesToId, created )
     ) ENGINE='InnoDB' ;

-- -----------------------------------------------------------------------------------
-- search
-- -----------------------------------------------------------------------------------
CREATE TABLE search
     (
       searchId              INT UNSIGNED NOT NULL AUTO_INCREMENT
     , engineName            VARCHAR(255) NOT NULL DEFAULT ''
     , searchName            VARCHAR(255) NOT NULL DEFAULT ''
     , url                   VARCHAR(4096) NOT NULL DEFAULT ''
     , created               TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'
     , updated               TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                             ON UPDATE CURRENT_TIMESTAMP
     , PRIMARY KEY pk_searchId ( searchId )
     ) ENGINE='InnoDB' ;

-- -----------------------------------------------------------------------------------
-- jobKeywordMap (constraints require job and keyword to exist first.
-- -----------------------------------------------------------------------------------
CREATE TABLE jobKeywordMap (
       jobId     INT UNSIGNED NOT NULL
     , keywordId INT UNSIGNED NOT NULL
     , created   TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00'
     , updated   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
                 ON UPDATE CURRENT_TIMESTAMP
     , PRIMARY KEY jobKeywordMapIdx ( jobId, keywordId )
     , FOREIGN KEY fk_jobId ( jobId )
        REFERENCES job ( jobId )
                ON DELETE CASCADE
                ON UPDATE CASCADE
     , FOREIGN KEY fk_keywordId ( keywordId )
        REFERENCES keyword ( keywordId )
                ON DELETE CASCADE
                ON UPDATE CASCADE
     ) ENGINE='InnoDB' ;

-- -----------------------------------------------------------------------------------
-- Triggers
-- -----------------------------------------------------------------------------------
DELIMITER ;;

CREATE TRIGGER applicationStatusAfterInsertTrigger
 AFTER INSERT
    ON applicationStatus
   FOR EACH ROW
 BEGIN
       INSERT applicationStatusSummary
            ( applicationStatusId
            , statusCount
            , created
            , updated
            )
       VALUES
            ( NEW.applicationStatusId
            , 0
            , NULL
            , NULL
            ) ;
   END ;;

CREATE TRIGGER jobAfterInsertTrigger
 AFTER INSERT
    ON job
   FOR EACH ROW
 BEGIN
       UPDATE applicationStatusSummary
           AS jss
          SET jss.statusCount = jss.statusCount + 1
        WHERE jss.applicationStatusId = NEW.applicationStatusId ;
   END ;;

CREATE TRIGGER jobAfterUpdateTrigger
 AFTER UPDATE
    ON job
   FOR EACH ROW
 BEGIN
             IF OLD.applicationStatusId <> NEW.applicationStatusId
           THEN
            UPDATE applicationStatusSummary
                AS jss
               SET jss.statusCount = jss.statusCount + 1
             WHERE jss.applicationStatusId = NEW.applicationStatusId ;
            UPDATE applicationStatusSummary
                AS jss
               SET jss.statusCount = jss.statusCount + 1
             WHERE jss.applicationStatusId = OLD.applicationStatusId ;
        END IF ;
             IF OLD.jobId <> NEW.jobId
           THEN
            UPDATE note
               SET note.appliesToId = NEW.jobId
             WHERE note.appliesToId = OLD.jobId
               AND note.appliestoTable = 'job'
                 ;
      END IF ;
   END ;;

CREATE TRIGGER jobAfterDeleteTrigger
 AFTER DELETE
    ON job
   FOR EACH ROW
 BEGIN
       UPDATE applicationStatusSummary
           AS jss
          SET jss.statusCount = jss.statusCount - 1
        WHERE jss.applicationStatusId = OLD.applicationStatusId ;

       DELETE
         FROM note
        WHERE note.appliesToTable = 'job'
          AND note.appliesToId = OLD.jobId ;
   END ;;

CREATE TRIGGER companyAfterDeleteTrigger
 AFTER DELETE
    ON company
   FOR EACH ROW
 BEGIN
       DELETE
         FROM note
        WHERE appliesToTable = 'company'
          AND appliesToId = OLD.companyId ;
   END ;;

CREATE TRIGGER companyAfterUpdateTrigger
 AFTER UPDATE
    ON company
   FOR EACH ROW
 BEGIN
             IF OLD.companyId <> NEW.companyId
           THEN
            UPDATE note
               SET note.appliesToId = NEW.companyId
             WHERE note.appliesToId = OLD.companyId
               AND note.appliestoTable = 'company'
                 ;
      END IF ;
   END ;;

CREATE TRIGGER contactAfterDeleteTrigger
 AFTER DELETE
    ON contact
   FOR EACH ROW
 BEGIN
       DELETE
         FROM note
        WHERE appliesToTable = 'contact'
          AND appliesToId = OLD.contactId ;
   END ;;

CREATE TRIGGER contactAfterUpdateTrigger
 AFTER UPDATE
    ON contact
   FOR EACH ROW
 BEGIN
             IF OLD.contactId <> NEW.contactId
           THEN
            UPDATE note
               SET note.appliesToId = NEW.contactId
             WHERE note.appliesToId = OLD.contactId
               AND note.appliestoTable = 'contact'
                 ;
      END IF ;
   END ;;

CREATE TRIGGER keywordAfterDeleteTrigger
 AFTER DELETE
    ON keyword
   FOR EACH ROW
 BEGIN
       DELETE
         FROM note
        WHERE appliesToTable = 'keyword'
          AND appliesToId = OLD.keywordId ;
   END ;;

CREATE TRIGGER keywordAfterUpdateTrigger
 AFTER UPDATE
    ON keyword
   FOR EACH ROW
 BEGIN
             IF OLD.keywordId <> NEW.keywordId
           THEN
            UPDATE note
               SET note.appliesToId = NEW.keywordId
             WHERE note.appliesToId = OLD.keywordId
               AND note.appliestoTable = 'keyword'
                 ;
      END IF ;
   END ;;

CREATE TRIGGER searchAfterDeleteTrigger
 AFTER DELETE
    ON search
   FOR EACH ROW
 BEGIN
       DELETE
         FROM note
        WHERE appliesToTable = 'search'
          AND appliesToId = OLD.searchId ;
   END ;;

CREATE TRIGGER searchAfterUpdateTrigger
 AFTER UPDATE
    ON search
   FOR EACH ROW
 BEGIN
             IF OLD.searchId <> NEW.searchId
           THEN
            UPDATE note
               SET note.appliesToId = NEW.searchId
             WHERE note.appliesToId = OLD.searchId
               AND note.appliestoTable = 'search'
                 ;
      END IF ;
   END ;;

DELIMITER ;

-- -----------------------------------------------------------------------------------
-- Pre-Fill Data
-- -----------------------------------------------------------------------------------
INSERT applicationStatus
     ( applicationStatusId
     , isActive
     , statusValue
     , sortKey
     )
VALUES (  1, 1, 'FOUND'        , 10  )
     , (  2, 1, 'CONTACTED'    , 20  )
     , (  3, 1, 'APPLIED'      , 30  )
     , (  4, 1, 'INTERVIEWING' , 40  )
     , (  5, 1, 'FOLLOWUP'     , 50  )
     , (  6, 1, 'CHASING'      , 60  )
     , (  7, 1, 'NETWORKING'   , 70  )
     , (  8, 0, 'UNAVAILABLE'  , 999 )
     , (  9, 0, 'INVALID'      , 999 )
     , ( 10, 0, 'DUPLICATE'    , 999 )
     , ( 11, 0, 'CLOSED'       , 999 )
     ;
UPDATE applicationStatus SET created = updated ;
