DROP TABLE IF EXISTS wcf1_contest_interaction_extra;
CREATE TABLE IF NOT EXISTS wcf1_contest_interaction_extra (
  contestID int(10) unsigned NOT NULL DEFAULT '0',
  participantID int(10) unsigned NOT NULL DEFAULT '0',
  score int(11) NOT NULL DEFAULT '0',
  INDEX contestID (contestID)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

ALTER TABLE wcf1_contest_interaction_ruleset 
  ADD rulesetColumnFactor varchar(64) NOT NULL DEFAULT '' AFTER rulesetColumnTime,
  CHANGE kind kind ENUM( 'user', 'group', 'participant' ) NOT NULL;
