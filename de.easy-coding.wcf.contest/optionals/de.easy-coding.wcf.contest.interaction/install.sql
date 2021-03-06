ALTER TABLE wcf1_contest ADD enableInteraction TINYINT( 1 ) UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE wcf1_contest ADD interactionLastUpdate INT( 10 ) UNSIGNED NOT NULL DEFAULT 0 AFTER enableInteraction;

DROP TABLE IF EXISTS wcf1_contest_interaction_ruleset;
CREATE TABLE IF NOT EXISTS wcf1_contest_interaction_ruleset (
	rulesetID int(10) unsigned NOT NULL AUTO_INCREMENT,
	kind enum('user','group') NOT NULL,
	rulesetTable varchar(64) NOT NULL DEFAULT '',
	rulesetColumn varchar(64) NOT NULL DEFAULT '',
	rulesetColumnTime varchar(64) NOT NULL DEFAULT '',
	rulesetColumnFactor varchar(64) NOT NULL DEFAULT '',
	PRIMARY KEY (rulesetID)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_contest_interaction;
CREATE TABLE IF NOT EXISTS wcf1_contest_interaction (
	interactionID int(10) unsigned NOT NULL AUTO_INCREMENT,
	rulesetID int(10) unsigned NOT NULL DEFAULT '0',
	contestID int(10) unsigned NOT NULL DEFAULT '0',
	fromTime INT(10) NOT NULL DEFAULT 0,
	untilTime INT(10) NOT NULL DEFAULT 0,
	rulesetFactor int(10) unsigned NOT NULL DEFAULT '0',
	PRIMARY KEY (interactionID),
	INDEX contestID (contestID)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_contest_interaction_extra;
CREATE TABLE IF NOT EXISTS wcf1_contest_interaction_extra (
	contestID int(10) unsigned NOT NULL DEFAULT '0',
	participantID int(10) unsigned NOT NULL DEFAULT '0',
	score int(11) NOT NULL DEFAULT '0',
	INDEX contestID (contestID)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_contest_interaction_data;
CREATE TABLE IF NOT EXISTS wcf1_contest_interaction_data (
	contestID int(10) unsigned NOT NULL DEFAULT '0',
	participantID int(10) unsigned NOT NULL DEFAULT '0',
	score int(10) unsigned NOT NULL DEFAULT '0',
	INDEX contestID (contestID)
) ENGINE=MyISAM	DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_contest_coupon;
CREATE TABLE IF NOT EXISTS wcf1_contest_coupon (
	couponID int(10) unsigned NOT NULL AUTO_INCREMENT,
	contestID int(10) unsigned NOT NULL DEFAULT '0',
	couponCode varchar(64) NOT NULL DEFAULT '',
	fromTime INT(10) unsigned NOT NULL DEFAULT 0,
	untilTime INT(10) unsigned NOT NULL DEFAULT 0,
	score int(11) NOT NULL DEFAULT '0',
	state ENUM('multi', 'single') NOT NULL DEFAULT 'multi',
	PRIMARY KEY (couponID),
	INDEX contestID (contestID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS wcf1_contest_coupon_participant;
CREATE TABLE IF NOT EXISTS wcf1_contest_coupon_participant (
	couponID int(10) unsigned NOT NULL DEFAULT '0',
	participantID int(10) unsigned NOT NULL DEFAULT '0',
	time INT(10) unsigned NOT NULL DEFAULT 0,
	UNIQUE coupon_participant (couponID, participantID),
	INDEX participantID (participantID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
