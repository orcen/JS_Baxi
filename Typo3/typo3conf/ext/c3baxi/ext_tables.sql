#
# Table structure for table 'tx_c3baxi_domain_model_haltestelle'
#
CREATE TABLE tx_c3baxi_domain_model_haltestelle
(

    name        varchar(255)     DEFAULT ''     NOT NULL,
    latitude    double(11, 2)    DEFAULT '0.00' NOT NULL,
    longitude   double(11, 2)    DEFAULT '0.00' NOT NULL,
    zone        int(11) unsigned DEFAULT '0',
    number      varchar(16)      DEFAULT NULL,
    wabe        int(16)          DEFAULT NULL,
    calculation INT(11),
    UNIQUE KEY number (number),
);

#
# Table structure for table 'tx_c3baxi_domain_model_zone'
#
CREATE TABLE tx_c3baxi_domain_model_zone
(

    name         varchar(255) DEFAULT ''  NOT NULL,
    haltestellen TEXT         DEFAULT ''  NOT NULL,
    zeiten       varchar(255) DEFAULT ''  NOT NULL,
    linien       int(11)      DEFAULT '0' NOT NULL,

);

#
# Table structure for table 'tx_c3baxi_domain_model_linie'
#
CREATE TABLE tx_c3baxi_domain_model_linie
(
    nr                          int(11)      DEFAULT '0' NOT NULL,
    name                        varchar(255) DEFAULT ''  NOT NULL,
    fahrten                     TEXT         DEFAULT ''  NOT NULL,
    zonen                       int(11)      DEFAULT '0' NOT NULL,
    company                     INT(11)      DEFAULT '0' NOT NULL,
    city_line                   int(1)       DEFAULT 0,
    flatrate_base_price         FLOAT(9, 4)  DEFAULT 0,
    flatrate_unit_price         FLOAT(9, 4)  DEFAULT 0,
    flatrate_special_base_price FLOAT(9, 4)  DEFAULT 0,
    flatrate_special_unit_price FLOAT(9, 4)  DEFAULT 0,
);
CREATE TABLE tx_c3baxi_domain_model_linie_zone_mm
(
    uid_local       int(11) unsigned DEFAULT '0' NOT NULL,
    uid_foreign     int(11) unsigned DEFAULT '0' NOT NULL,
    sorting         int(11) unsigned DEFAULT '0' NOT NULL,
    sorting_foreign int(11) unsigned DEFAULT '0' NOT NULL,

    KEY uid_local (uid_local),
    KEY uid_foreign (uid_foreign)
);
#
# Table structure for table 'tx_c3baxi_domain_model_fahrt'
#
CREATE TABLE tx_c3baxi_domain_model_fahrt
(

    name        varchar(255)     DEFAULT '' NOT NULL,
    linie       int(11) unsigned DEFAULT '0',
    tage        varchar(255)     DEFAULT '' NULL,
    buchbar_bis int(11)          DEFAULT 0,
    zeiten      TEXT             DEFAULT '' NULL,
    rueckfahrt  int(1)           DEFAULT 0,
);


#
# Table structure for table 'tx_c3baxi_domain_model_fahrtzeit'
#
CREATE TABLE tx_c3baxi_domain_model_fahrtzeit
(

    zeit  int(11)          DEFAULT '0' NOT NULL,
    fahrt int(11) unsigned DEFAULT '0',
    zone  int(11) unsigned DEFAULT '0',

);


#
# Table structure for table `tx_c3baxi_domain_model_booking`
#

CREATE TABLE tx_c3baxi_domain_model_booking
(
    adults        tinyint(11)      DEFAULT 1   NOT NULL,
    children      tinyint(11)      DEFAULT 0   NOT NULL,
    fahrt         int(11) unsigned DEFAULT '0',
    date          int(11)          DEFAULT '0' NOT NULL,
    user          int(11)          DEFAULT '0' NOT NULL,
    start_station int(11)          DEFAULT '0' NOT NULL,
    end_station   int(11)          DEFAULT '0' NOT NULL,
    info          text             DEFAULT '',
    confirmed     int(1)           DEFAULT 0   NOT NULL,
    approved      int(1)           DEFAULT 1   NOT NULL,
    reminder_send int(1)           DEFAULT 0   NOT NULL,
    sms           int(11)          DEFAULT 0   NOT NULL,
    export        int(1)           DEFAULT 0   NOT NULL,
    subscription_id INT(11) DEFAULT 0 NOT NULL,
);

CREATE TABLE tx_c3baxi_domain_model_subscription
(
    adults        tinyint(11)      DEFAULT 1   NOT NULL,
    children      tinyint(11)      DEFAULT 0   NOT NULL,
    fahrt         int(11) unsigned DEFAULT '0',
    date          int(11)          DEFAULT '0' NOT NULL,
    days          varchar(64)      DEFAULT ''  NOT NULL,
    user          int(11)          DEFAULT '0' NOT NULL,
    start_station int(11)          DEFAULT '0' NOT NULL,
    end_station   int(11)          DEFAULT '0' NOT NULL,
    info          text             DEFAULT '',
    confirmed     int(1)           DEFAULT 0   NOT NULL,
    approved      int(1)           DEFAULT 1   NOT NULL,
    reminder_send int(1)           DEFAULT 0   NOT NULL,
    exceptions    text             DEFAULT ''  NOT NULL,
);

#
# Table structure for table `tx_c3baxi_domain_model_booking`
#

CREATE TABLE tx_c3baxi_domain_model_company
(
    name           varchar(128) DEFAULT '' NOT NULL,
    info           text         DEFAULT '' NOT NULL,
    car_count      int(11)      DEFAULT 0,
    street         varchar(128) DEFAULT '' NOT NULL,
    city           varchar(128) DEFAULT '' NOT NULL,
    zip            varchar(6)   DEFAULT '' NOT NULL,
    telefon        varchar(32)  DEFAULT '' NOT NULL,
    email          varchar(128) DEFAULT '' NOT NULL,
    contact_person varchar(128) DEFAULT '' NOT NULL,
    routes         varchar(255) DEFAULT '' NOT NULL,
    user           int(11)      DEFAULT 0,
    flatrate_base  FLOAT(9, 4)  DEFAULT 0,
    flatrate_extra FLOAT(9, 4)  DEFAULT 0,
);

#
# fe_users extension
#
CREATE TABLE fe_users
(
    tx_c3baxi_favorites              text        DEFAULT NULL,
    ticket_art                       varchar(32) DEFAULT '' NOT NULL,
    tx_c3baxi_notification_telephone INT(1)      DEFAULT 0,
    tx_c3baxi_notification_email     INT(1)      DEFAULT 0,
);


#
# Table Structure for `tx_c3baxi_domain_model_rating`
#
CREATE TABLE tx_c3baxi_domain_model_rating
(
    object_id int(11)     DEFAULT 0,
    type      varchar(16) DEFAULT '' NOT NULL,
    value     int(11)     DEFAULT 0,
    comment   TEXT        DEFAULT NULL,
);
#
# Table Structure for `tx_c3baxi_domain_model_dateexception`
#
CREATE TABLE tx_c3baxi_domain_model_holiday
(
    type        varchar(16)              NOT NULL DEFAULT '1',
    title       varchar(128) DEFAULT ''  NOT NULL,
    description text         DEFAULT ''  NOT NULL,
    startdate   int(11)      DEFAULT '0' NOT NULL,
    enddate     int(11)      DEFAULT '0' NOT NULL,
);

# Repair relation
# UPDATE tx_c3baxi_domain_model_fahrt SET zeiten =( SELECT GROUP_CONCAT(uid) FROM tx_c3baxi_domain_model_fahrtzeit WHERE fahrt = tx_c3baxi_domain_model_fahrt.uid and deleted = 0 and hidden = 0 )  where linie = 13