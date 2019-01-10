DROP TABLE IF EXISTS bid;
DROP TABLE IF EXISTS ride;
DROP TABLE IF EXISTS car_ownedby;
DROP TABLE IF EXISTS car;
DROP TABLE IF EXISTS person;
DROP TABLE IF EXISTS logRide;
DROP TABLE IF EXISTS logAccount;
DROP TYPE IF EXISTS status;
DROP TYPE IF EXISTS seat;

CREATE TABLE person(
	isadmin BOOLEAN DEFAULT false,
	username VARCHAR(20) UNIQUE NOT NULL,
	password VARCHAR(256) NOT NULL,
	email VARCHAR(100) UNIQUE NOT NULL,
	fullname VARCHAR(256) NOT NULL,
	phone NUMERIC UNIQUE NOT NULL,
	userid SERIAL PRIMARY KEY
);

CREATE TYPE seat
AS ENUM('2-seater', '5-seater', '7-seater');
CREATE TABLE car(
	model VARCHAR(100) NOT NULL,
	colour VARCHAR(50) NOT NULL,
	seats seat NOT NULL,
	license VARCHAR(10) UNIQUE NOT NULL,
	carid SERIAL PRIMARY KEY
);

CREATE TABLE car_ownedby (
	ownerid INTEGER,
	carid INTEGER,
	FOREIGN KEY(ownerid) REFERENCES person(userid) ON DELETE CASCADE,
	FOREIGN KEY(carid) REFERENCES car(carid) ON DELETE CASCADE,
	PRIMARY KEY(ownerid, carid)
);

CREATE TYPE status
AS ENUM('open','close');
CREATE TABLE ride (
	origin VARCHAR(256) NOT NULL,
	dest VARCHAR(256) NOT NULL,
	pickuptime TIMESTAMP NOT NULL,
	minbid MONEY DEFAULT 0.00,
	status status NOT NULL,
	carid INTEGER NOT NULL,
	winnerid INTEGER DEFAULT NULL,
	advertiserid INTEGER NOT NULL,
	CHECK (winnerid <> advertiserid),
	CHECK (status = 'close' AND winnerid IS NOT NULL
		OR status = 'open' AND winnerid IS NULL),
	FOREIGN KEY (winnerid) REFERENCES person(userid) ON DELETE CASCADE,
	FOREIGN KEY (advertiserid) REFERENCES person(userid) ON DELETE CASCADE,
	FOREIGN KEY (carid) REFERENCES car(carid) ON DELETE CASCADE,
	PRIMARY KEY (origin,dest,pickuptime,advertiserid)
);	

CREATE TABLE bid(
	bidamt MONEY DEFAULT 0.05,
	bidtime TIMESTAMP,
	bidderid INTEGER NOT NULL,
	advertiserid INTEGER NOT NULL,
	origin VARCHAR(256) NOT NULL,
	dest VARCHAR(256) NOT NULL,
	pickuptime TIMESTAMP NOT NULL,
	minbid MONEY NOT NULL,
	CHECK (bidamt >= minbid),
	FOREIGN KEY (bidderid) REFERENCES person(userid) ON DELETE CASCADE,
	FOREIGN KEY (advertiserid, origin, dest, pickuptime)
	REFERENCES ride(advertiserid, origin, dest, pickuptime) ON DELETE CASCADE,
	PRIMARY KEY(bidderid, advertiserid, origin, dest, pickuptime)
);

CREATE TABLE logRide (
	origin VARCHAR(256) NOT NULL,
	dest VARCHAR(256) NOT NULL,
	pickuptime TIMESTAMP NOT NULL,
	minbid MONEY DEFAULT 0.00,
	status status NOT NULL,
	carid INTEGER NOT NULL,
	advertiserid INTEGER NOT NULL,
	action VARCHAR(30)
);

CREATE TABLE logAccount (
  	logID SERIAL PRIMARY KEY,
	username VARCHAR(20) NOT NULL,
	password VARCHAR(256) NOT NULL,
	email VARCHAR(100) NOT NULL,
	fullname VARCHAR(256) NOT NULL,
	phone NUMERIC NOT NULL,
	timeofaction TIMESTAMP NOT NULL,
	action VARCHAR(30) NOT NULL
);

CREATE OR REPLACE FUNCTION logAccount()
RETURNS TRIGGER AS $$
DECLARE action VARCHAR(30);
BEGIN
IF TG_OP ='INSERT' THEN
action :='Registration'; 
INSERT INTO logAccount(username,password,email,fullname,phone,timeofaction,action)
VALUES (NEW.username,NEW.password,NEW.email,NEW.fullname,NEW.phone,localtimestamp,action);
ELSIF TG_OP ='UPDATE' THEN
action := 'Update account details';
INSERT INTO logAccount(username,password,email,fullname,phone,timeofaction,action)
VALUES (OLD.username,OLD.password,OLD.email,OLD.fullname,OLD.phone,localtimestamp,action);
ELSIF TG_OP ='DELETE' THEN
action := 'Delete account';
INSERT INTO logAccount(username,password,email,fullname,phone,timeofaction,action)
VALUES (OLD.username,OLD.password,OLD.email,OLD.fullname,OLD.phone,localtimestamp,action);
END IF;
RETURN NULL;
END; $$
LANGUAGE PLPGSQL;

CREATE TRIGGER logAccount
AFTER INSERT OR UPDATE OR DELETE ON person
FOR EACH ROW
EXECUTE PROCEDURE logAccount();

CREATE OR REPLACE FUNCTION hashPassword()
RETURNS TRIGGER AS $$
BEGIN 
NEW.password := md5(New.password);
RETURN NEW;
END; $$
LANGUAGE PLPGSQL;

CREATE TRIGGER hashPassword 
BEFORE INSERT ON person
FOR EACH ROW
EXECUTE PROCEDURE hashPassword();

CREATE OR REPLACE FUNCTION logRide()
RETURNS TRIGGER AS $$
DECLARE action VARCHAR(30);
BEGIN
IF NEW.status = 'close' THEN
action := 'Ride ended';
INSERT INTO logRide(origin,dest,pickuptime,minbid,status,carid,advertiserid,action)
VALUES (OLD.origin,OLD.dest,OLD.pickuptime,OLD.minbid,OLD.status,OLD.carid,OLD.advertiserid,action);
ELSIF TG_OP ='DELETE' THEN
action := 'Deleted ride';
ELSIF TG_op = 'UPDATE' THEN
action := 'Updated Ride';
INSERT INTO logRide(origin,dest,pickuptime,minbid,status,carid,advertiserid,action)
VALUES (OLD.origin,OLD.dest,OLD.pickuptime,OLD.minbid,OLD.status,OLD.carid,OLD.advertiserid,action);
END IF;

RETURN NULL;
END; $$
LANGUAGE PLPGSQL;

CREATE TRIGGER logRide
AFTER UPDATE OR DELETE ON ride
FOR EACH ROW
EXECUTE PROCEDURE logRide();

CREATE OR REPLACE FUNCTION checkWinner()
RETURNS TRIGGER AS $$
DECLARE 
r ride%rowtype;
total integer;
winnerid integer;
pickuptime timestamp;
status status;
BEGIN
winnerid := NEW.winnerid;
pickuptime := NEW.pickuptime;
status := NEW.status;
IF (pickuptime < localtimestamp) THEN
	raise exception 'Pickup time is in the past';
END IF;
select count(*) into total from ride;
IF total > 0 THEN
	FOR r in select * from ride
	LOOP
		IF (winnerid = r.winnerid AND pickuptime = r.pickuptime AND winnerid IS NOT NULL) THEN
			raise exception 'Winner % has already chosen a bid that clashes with this ride', winnerid;
   		END IF;
	END LOOP;
END IF;

RETURN NEW;
END; $$
LANGUAGE PLPGSQL;

CREATE TRIGGER checkWinner
BEFORE UPDATE OR INSERT ON ride
FOR EACH ROW
EXECUTE procedure checkWinner();