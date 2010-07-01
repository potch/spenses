CREATE TABLE user (
 id   INT PRIMARY KEY AUTO_INCREMENT,
 name VARCHAR(32)
);

CREATE TABLE cohort (
 id   INT PRIMARY KEY AUTO_INCREMENT,
 name VARCHAR(32)
);

CREATE TABLE cohortuser (
 cohortid INT,
 userid   INT
);

CREATE TABLE location (
 id      INT PRIMARY KEY AUTO_INCREMENT,
 name    VARCHAR(64),
 addr    VARCHAR(256),
 lat     FLOAT,
 lon     FLOAT
);

CREATE TABLE purchase (
 id          BIGINT PRIMARY KEY AUTO_INCREMENT,
 description VARCHAR(256),
 amount      INT,
 whopaid     INT,
 loc         INT,
 date        TIMESTAMP
);

CREATE TABLE balance (
 idfrom INT,
 idto   INT,
 amount INT
);

CREATE TABLE purchasedetail (
 purchaseid BIGINT,
 paidfor    INT,
 amount     INT
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

INSERT INTO user VALUES ( NULL, "Andrew" );
INSERT INTO user VALUES ( NULL, "Potch"  );
INSERT INTO user VALUES ( NULL, "Nick"   );
INSERT INTO user VALUES ( NULL, "Becky"  );

INSERT INTO tag VALUES ( NULL, "Settle Up" );

INSERT INTO location VALUES ( NULL, "Creamery (Palo Alto)", "566 Emerson St., Palo Alto, CA", 37.443841, -122.161671 );
INSERT INTO location VALUES ( NULL, "Safeway (Secret)", "325 Sharon Park Dr., Menlo Park, CA", 37.423827, -122.198063 );
INSERT INTO location VALUES ( NULL, "Safeway (El Camino, Menlo Park)", "525 El Camino Real, Menlo Park, CA", 37.451626583866435, -122.17835426330566 );
INSERT INTO location VALUES ( NULL, "Andronico's", "500 Stanford Shopping Center, Palo Alto, CA", 37.439122, -122.172561 );
INSERT INTO location VALUES ( NULL, "Mayfield Cafe", "855 El Camino Real, Palo Alto, CA", 37.438253, -122.158699 );
INSERT INTO location VALUES ( NULL, "Mayfield Bakery", "855 El Camino Real, Palo Alto, CA", 37.438253, -122.158699 );
INSERT INTO location VALUES ( NULL, "Izzy's Brooklyn Bagels", "477 South California Ave., Palo Alto, CA", 37.425542, -122.145277 );
INSERT INTO location VALUES ( NULL, "Chipotle (Charleston Ave)", "2400 Charleston Rd., Mountain View, CA", 37.423344, -122.096472 );
INSERT INTO location VALUES ( NULL, "Chipotle (El Camino, Palo Alto)", "2675 El Camino Real, Palo Alto, CA", 37.425934,-122.142992 );
INSERT INTO location VALUES ( NULL, "Coupa Cafe", "538 Ramona St., Palo Alto, CA", 37.446039, -122.161188 );
INSERT INTO location VALUES ( NULL, "Original Pancake House", "420 South San Antonio Road, Los Altos, CA", 37.378888, -122.114067 );
INSERT INTO location VALUES ( NULL, "BevMo", "423 San Antonio Road, Mountain View, CA", 37.404903, -122.109389 );
INSERT INTO location VALUES ( NULL, "Creamery, Peninsula", "900 High Street, Palo Alto, CA", 37.44115, -122.158463 );
INSERT INTO location VALUES ( NULL, "Avanti Pizza", "3536 Alameda De Las Pulgas, Menlo Park, CA", 37.43274993439342, -122.20221519470215 );
INSERT INTO location VALUES ( NULL, "Lulu's On the Alameda", "3539 Alameda De Las Pulgas, Menlo Park, CA", 37.43418118294655, -122.20178604125977 );

INSERT INTO balance VALUES ( 2, 1, 0 );
INSERT INTO balance VALUES ( 3, 1, 0 );
INSERT INTO balance VALUES ( 3, 2, 0 );
INSERT INTO balance VALUES ( 4, 1, 0 );
INSERT INTO balance VALUES ( 4, 2, 0 );
INSERT INTO balance VALUES ( 4, 3, 0 );

INSERT INTO cohort VALUES ( NULL, "Team Sexy" );
INSERT INTO cohort VALUES ( NULL, "E16, Bitches!" );

INSERT INTO cohortuser VALUES ( 1, 1 );
INSERT INTO cohortuser VALUES ( 1, 2 );
INSERT INTO cohortuser VALUES ( 1, 3 );
INSERT INTO cohortuser VALUES ( 1, 4 );

INSERT INTO cohortuser VALUES ( 2, 1 );
INSERT INTO cohortuser VALUES ( 2, 3 );
