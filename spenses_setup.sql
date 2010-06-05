CREATE TABLE user (
 id   INTEGER PRIMARY KEY ASC,
 name VARCHAR(32)
);

CREATE TABLE location (
 id      INTEGER PRIMARY KEY ASC,
 name    VARCHAR(64),
 addr    VARCHAR(256),
 lat     FLOAT,
 long    FLOAT
);

CREATE TABLE purchase (
 id      INTEGER PRIMARY KEY ASC,
 desc    VARCHAR(256),
 amount  INTEGER,
 whopaid INTEGER,
 loc     INTEGER,
 date    TIMESTAMP
);

CREATE TABLE balance (
 idfrom INTEGER,
 idto   INTEGER,
 amount INTEGER
);

CREATE TABLE purchasedetail (
 purchaseid INTEGER,
 paidfor    INTEGER,
 amount     INTEGER
);

CREATE TABLE tag (
 id   INTEGER PRIMARY KEY ASC,
 name VARCHAR(64)
);

CREATE TABLE locationtag (
 locationid INTEGER,
 tagid      INTEGER
);

CREATE TABLE purchasetag (
 purchaseid INTEGER,
 tagid      INTEGER
);

INSERT INTO user VALUES ( null, "Andrew" );
INSERT INTO user VALUES ( null, "Potch"  );
INSERT INTO user VALUES ( null, "Nick"   );
INSERT INTO user VALUES ( null, "Becky"  );

INSERT INTO tag VALUES ( null, "Settle Up" );

INSERT INTO location VALUES ( null, "Creamery (Palo Alto)", "566 Emerson St., Palo Alto, CA", 37.443841, -122.161671 );
INSERT INTO location VALUES ( null, "Safeway (Secret)", "325 Sharon Park Dr., Menlo Park, CA", 37.423827, -122.198063 );
INSERT INTO location VALUES ( null, "Safeway (El Camino, Menlo Park)", "525 El Camino Real, Menlo Park, CA", 37.451626583866435, -122.17835426330566 );
INSERT INTO location VALUES ( null, "Andronico's", "500 Stanford Shopping Center, Palo Alto, CA", 37.439122, -122.172561 );
INSERT INTO location VALUES ( null, "Mayfield Cafe", "855 El Camino Real, Palo Alto, CA", 37.438253, -122.158699 );
INSERT INTO location VALUES ( null, "Mayfield Bakery", "855 El Camino Real, Palo Alto, CA", 37.438253, -122.158699 );
INSERT INTO location VALUES ( null, "Izzy's Brooklyn Bagels", "477 South California Ave., Palo Alto, CA", 37.425542, -122.145277 );
INSERT INTO location VALUES ( null, "Chipotle (Charleston Ave)", "2400 Charleston Rd., Mountain View, CA", 37.423344, -122.096472 );
INSERT INTO location VALUES ( null, "Chipotle (El Camino, Palo Alto)", "2675 El Camino Real, Palo Alto, CA", 37.425934,-122.142992 );
INSERT INTO location VALUES ( null, "Coupa Cafe", "538 Ramona St., Palo Alto, CA", 37.446039, -122.161188 );
INSERT INTO location VALUES ( null, "Original Pancake House", "420 South San Antonio Road, Los Altos, CA", 37.378888, -122.114067 );
INSERT INTO location VALUES ( null, "BevMo", "423 San Antonio Road, Mountain View, CA", 37.404903, -122.109389 );
INSERT INTO location VALUES ( null, "Creamery, Peninsula", "900 High Street, Palo Alto, CA", 37.44115, -122.158463 );
INSERT INTO location VALUES ( null, "Avanti Pizza", "3536 Alameda De Las Pulgas, Menlo Park, CA", 37.43274993439342, -122.20221519470215 );
INSERT INTO location VALUES ( null, "Lulu's On the Alameda", "3539 Alameda De Las Pulgas, Menlo Park, CA", 37.43418118294655, -122.20178604125977 );

INSERT INTO balance VALUES ( 2, 1, 0 );
INSERT INTO balance VALUES ( 3, 1, 0 );
INSERT INTO balance VALUES ( 3, 2, 0 );
INSERT INTO balance VALUES ( 4, 1, 0 );
INSERT INTO balance VALUES ( 4, 2, 0 );
INSERT INTO balance VALUES ( 4, 3, 0 );
