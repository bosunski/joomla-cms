DROP TABLE IF EXISTS `#__hotel_guest`;
DROP TABLE IF EXISTS `#__hotel_reservation`;
DROP TABLE IF EXISTS `#__hotel_room`;

CREATE TABLE `#__hotel_guest` (
	id INT(11) NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(25) NOT NULL,
    vip BOOLEAN NOT NULL DEFAULT 0,
	published BOOLEAN NOT NULL DEFAULT 0,
	created DATETIME NOT NULL DEFAULT now(),
	PRIMARY KEY (id)
)
	ENGINE =MyISAM
	AUTO_INCREMENT =0
	DEFAULT CHARSET =utf8;

INSERT INTO `#__hotel_guest` (name, vip) VALUES
    ('John Doe', 0), ('Margarit Doe', 0), ('Xavier X', 1), ('Mary Jane', 1);

CREATE TABLE `#__hotel_room` (
    id INT(11) NOT NULL AUTO_INCREMENT,
    floor TINYINT NOT NULL,
    cost FLOAT(5,2) NOT NULL DEFAULT 100.00,
    `empty` BOOLEAN NOT NULL DEFAULT 1,
    published BOOLEAN NOT NULL DEFAULT 0,
    created DATETIME NOT NULL DEFAULT now(),
    PRIMARY KEY (id)
)
    ENGINE =MyISAM
    AUTO_INCREMENT =0
    DEFAULT CHARSET =utf8;

INSERT INTO `#__hotel_room` (floor) VALUES (1), (1), (1), (1), (1);
INSERT INTO `#__hotel_room` (floor, cost) VALUES (2, 200), (2, 200), (2, 200), (2, 200), (2, 200);
INSERT INTO `#__hotel_room` (floor, cost) VALUES (3, 300), (3, 300), (3, 300), (3, 300), (3, 300);

CREATE TABLE `#__hotel_reservation` (
	id INT(11) NOT NULL AUTO_INCREMENT,
	guest_id INT NOT NULL,
	checkin_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
	checkout_date DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
    room_id TINYINT NOT NULL,
    obs VARCHAR(50),
	published BOOLEAN NOT NULL DEFAULT 0,
	created DATETIME NOT NULL DEFAULT now(),
	PRIMARY KEY (id),

    FOREIGN KEY (room_id)
      REFERENCES `#__hotel_room`(id),
	FOREIGN KEY (guest_id)
	  REFERENCES `#__hotel_guest`(id)
)
    ENGINE =MyISAM
    AUTO_INCREMENT =0
    DEFAULT CHARSET =utf8;

INSERT INTO `#__hotel_reservation` (guest_id, checkin_date, checkout_date, room_id, obs) VALUES
    (1, '2018-04-01 00:00:00', '2018-04-10 00:00:00', 6, 'Some observations about this res.'),
    (2, '2018-04-02 00:00:00', '2018-04-07 00:00:00', 2, 'And some other observations about this res.'),
    (3, '2018-04-20 00:00:00', '2018-05-07 00:00:00', 12, 'Some more...'),
    (4, '2018-04-21 00:00:00', '2018-05-01 00:00:00', 13, 'Last observations...');
