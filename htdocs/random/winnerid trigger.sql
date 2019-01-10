CREATE TABLE ridetest (
	origin VARCHAR(256) NOT NULL,
	dest VARCHAR(256) NOT NULL,
	pickuptime TIMESTAMP NOT NULL,
	minbid MONEY DEFAULT 0.00,
	status status NOT NULL,
	carid INTEGER NOT NULL,
	winnerid INTEGER DEFAULT NULL,
	advertiserid INTEGER NOT NULL,
	CHECK (winnerid <> advertiserid),
	FOREIGN KEY (winnerid) REFERENCES person(userid),
	FOREIGN KEY (advertiserid) REFERENCES person(userid),
	FOREIGN KEY (carid) REFERENCES car(carid),
	PRIMARY KEY (origin,dest,pickuptime,advertiserid)
);

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