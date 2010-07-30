CREATE TABLE user (
 userid       INT PRIMARY KEY AUTO_INCREMENT,
 name         VARCHAR(32),
 nick         VARCHAR(16),
 openid       VARCHAR(128),
 foursquare   VARCHAR(64) DEFAULT NULL,
 email        VARCHAR(256),
 date_updated TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 date_created TIMESTAMP,
 status       VARCHAR(2)
);

CREATE TABLE cohort (
 cohortid      INT PRIMARY KEY AUTO_INCREMENT,
 name          VARCHAR(32),
 currency_code VARCHAR(3),
 status        VARCHAR(2),
 date_updated  TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
 date_created  TIMESTAMP
);

CREATE TABLE cohortuser (
 cohortid      INT,
 userid        INT,
 inviter       INT,
 status        VARCHAR(2) DEFAULT 'pe' -- 'pe' = pending, 'ac' = active, '--' = deleted
);

CREATE TABLE location (
 locationid   INT PRIMARY KEY AUTO_INCREMENT,
 name         VARCHAR(64),
 addr         VARCHAR(256),
 lat          FLOAT,
 lon          FLOAT,
 date_updated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
 date_created TIMESTAMP
);

CREATE TABLE purchase (
 purchaseid   BIGINT PRIMARY KEY AUTO_INCREMENT,
 description  VARCHAR(256),
 amount       DECIMAL(9,2),
 userid       INT, -- who added the purchase
 userid_payer INT, -- who paid
 locationid   INT,
 date_of      DATETIME,
 date_updated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
 date_created TIMESTAMP,
 is_settle    TINYINT(1) DEFAULT 0
);

CREATE TABLE balance (
 userid_from  INT,
 userid_to    INT,
 amount       DECIMAL(9,2),
 cohortid     INT,
 date_updated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP,
 status       VARCHAR(2)
);

CREATE TABLE iou (
 purchaseid   BIGINT,
 cohortid     INT,
 userid_payer INT,
 userid_payee INT,
 amount       DECIMAL(9,2),
 date_updated TIMESTAMP DEFAULT 0 ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE tag (
 id   BIGINT PRIMARY KEY AUTO_INCREMENT,
 name VARCHAR(64)
);

CREATE TABLE locationtag (
 locationid INT,
 tagid      BIGINT
);

CREATE TABLE purchasetag (
 purchaseid BIGINT,
 tagid      BIGINT
);

