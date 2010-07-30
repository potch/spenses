
INSERT INTO user SET name="Andrew Pariser",  nick="Andrew", email="pariser@gmail.com",       date_created=NOW(), openid="https://www.google.com/accounts/o8/id", status="ac";
INSERT INTO user SET name="Matt Claypotch",  nick="Potch",  email="thepotch@gmail.com",      date_created=NOW(), openid="https://www.google.com/accounts/o8/id", status="ac";
INSERT INTO user SET name="Nick West",       nick="Nick",   email="nicholas.west@gmail.com", date_created=NOW(), openid="https://www.google.com/accounts/o8/id", status="ac";
INSERT INTO user SET name="Becky Wright",    nick="Becky",  email="b.wright.2010@gmail.com", date_created=NOW(), openid="https://www.google.com/accounts/o8/id", status="ac";
INSERT INTO user SET name="Stefanie Sundby", nick="Stef",   email="stef.sundby@gmail.com",   date_created=NOW(), openid="https://www.google.com/accounts/o8/id", status="ac";

INSERT INTO tag VALUES ( NULL, "Settle Up" );

INSERT INTO location SET name="Creamery (Palo Alto)",            addr="566 Emerson St., Palo Alto, CA",              date_created=NOW(), lat=37.443841, lon=-122.161671 ;
INSERT INTO location SET name="Safeway",                         addr="325 Sharon Park Dr., Menlo Park, CA",         date_created=NOW(), lat=37.423827, lon=-122.198063 ;
INSERT INTO location SET name="Safeway",                         addr="525 El Camino Real, Menlo Park, CA",          date_created=NOW(), lat=37.451627, lon=-122.178354 ;
INSERT INTO location SET name="Andronico's",                     addr="500 Stanford Shopping Center, Palo Alto, CA", date_created=NOW(), lat=37.439122, lon=-122.172561 ;
INSERT INTO location SET name="Mayfield Cafe",                   addr="855 El Camino Real, Palo Alto, CA",           date_created=NOW(), lat=37.438253, lon=-122.158699 ;
INSERT INTO location SET name="Mayfield Bakery",                 addr="855 El Camino Real, Palo Alto, CA",           date_created=NOW(), lat=37.438253, lon=-122.158699 ;
INSERT INTO location SET name="Izzy's Brooklyn Bagels",          addr="477 South California Ave., Palo Alto, CA",    date_created=NOW(), lat=37.425542, lon=-122.145277 ;
INSERT INTO location SET name="Chipotle",                        addr="2400 Charleston Rd., Mountain View, CA",      date_created=NOW(), lat=37.423344, lon=-122.096472 ;
INSERT INTO location SET name="Chipotle",                        addr="2675 El Camino Real, Palo Alto, CA",          date_created=NOW(), lat=37.425934, lon=-122.142992 ;
INSERT INTO location SET name="Coupa Cafe",                      addr="538 Ramona St., Palo Alto, CA",               date_created=NOW(), lat=37.446039, lon=-122.161188 ;
INSERT INTO location SET name="Original Pancake House",          addr="420 South San Antonio Road, Los Altos, CA",   date_created=NOW(), lat=37.378888, lon=-122.114067 ;
INSERT INTO location SET name="BevMo",                           addr="423 San Antonio Road, Mountain View, CA",     date_created=NOW(), lat=37.404903, lon=-122.109389 ;
INSERT INTO location SET name="Creamery, Peninsula",             addr="900 High Street, Palo Alto, CA",              date_created=NOW(), lat=37.441150, lon=-122.158463 ;
INSERT INTO location SET name="Avanti Pizza",                    addr="3536 Alameda De Las Pulgas, Menlo Park, CA",  date_created=NOW(), lat=37.432750, lon=-122.202215 ;
INSERT INTO location SET name="Lulu's On the Alameda",           addr="3539 Alameda De Las Pulgas, Menlo Park, CA",  date_created=NOW(), lat=37.434181, lon=-122.201786 ;

INSERT INTO balance SET userid_from=1, userid_to=2, amount=0.0, cohortid=1, date_updated=NOW(), status='ac' ;
INSERT INTO balance SET userid_from=1, userid_to=3, amount=0.0, cohortid=1, date_updated=NOW(), status='ac' ;
INSERT INTO balance SET userid_from=1, userid_to=4, amount=0.0, cohortid=1, date_updated=NOW(), status='ac' ;
INSERT INTO balance SET userid_from=1, userid_to=5, amount=0.0, cohortid=1, date_updated=NOW(), status='ac' ;
INSERT INTO balance SET userid_from=2, userid_to=3, amount=0.0, cohortid=1, date_updated=NOW(), status='ac' ;
INSERT INTO balance SET userid_from=2, userid_to=4, amount=0.0, cohortid=1, date_updated=NOW(), status='ac' ;
INSERT INTO balance SET userid_from=2, userid_to=5, amount=0.0, cohortid=1, date_updated=NOW(), status='ac' ;
INSERT INTO balance SET userid_from=3, userid_to=4, amount=0.0, cohortid=1, date_updated=NOW(), status='ac' ;
INSERT INTO balance SET userid_from=3, userid_to=5, amount=0.0, cohortid=1, date_updated=NOW(), status='ac' ;
INSERT INTO balance SET userid_from=4, userid_to=5, amount=0.0, cohortid=1, date_updated=NOW(), status='ac' ;

INSERT INTO cohort SET name="Team Sexy",     currency_code="USD", status='ac', date_created=NOW() ;
INSERT INTO cohort SET name="E16, Bitches!", currency_code="USD", status='ac', date_created=NOW() ;

INSERT INTO cohortuser SET cohortid=1, userid=1, inviter=NULL, status='ac' ;
INSERT INTO cohortuser SET cohortid=1, userid=2, inviter=NULL, status='ac' ;
INSERT INTO cohortuser SET cohortid=1, userid=3, inviter=NULL, status='ac' ;
INSERT INTO cohortuser SET cohortid=1, userid=4, inviter=NULL, status='ac' ;
INSERT INTO cohortuser SET cohortid=1, userid=5, inviter=NULL, status='ac' ;

INSERT INTO cohortuser SET cohortid=2, userid=1, inviter=NULL, status='ac' ;
INSERT INTO cohortuser SET cohortid=2, userid=3, inviter=NULL, status='ac' ;

