<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_create_base extends CI_Migration {

	public function up() {

    $this->db->query('SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0');

		## Create Table ci_sessions
		$this->db->query(<<<'EOT'
CREATE TABLE `ci_sessions` (
  `session_id` varchar(40) collate utf8_bin NOT NULL default '0',
  `ip_address` varchar(16) collate utf8_bin NOT NULL default '0',
  `user_agent` varchar(150) collate utf8_bin NOT NULL,
  `last_activity` int(10) unsigned NOT NULL default '0',
  `user_data` text collate utf8_bin NOT NULL,
  PRIMARY KEY  (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
EOT
);
		
    ## Create Table contributions
		$this->db->query(<<<'EOT'
CREATE TABLE `contributions` (
  `id` int(11) NOT NULL auto_increment,
  `participantId` int(11) NOT NULL,
  `symposiumId` int(11) NOT NULL,
  `title` text collate utf8_unicode_ci,
  `authors` text collate utf8_unicode_ci,
  `institute` text collate utf8_unicode_ci,
  `summary` text collate utf8_unicode_ci,
  `contributionKey` varchar(20) collate utf8_unicode_ci NOT NULL,
  `startTime` datetime default NULL,
  `room` enum('N0','N1','N2','N3','N4','N5','N6','N7','N9') collate utf8_unicode_ci default NULL,
  `type` enum('PLENARY_TALK','INVITED_TALK','ORAL_PRESENTATION','REGULAR_POSTER','ESSENCE_POSTER') collate utf8_unicode_ci NOT NULL,
  `endTime` datetime default NULL,
  `pdfReceived` tinyint(1) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `contributionKey` (`contributionKey`),
  UNIQUE KEY `participantId` (`participantId`),
  KEY `symposiumId` (`symposiumId`),
  CONSTRAINT `contributions_ibfk_2` FOREIGN KEY (`symposiumId`) REFERENCES `symposia` (`id`),
  CONSTRAINT `contributions_ibfk_3` FOREIGN KEY (`participantId`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1091 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
EOT
);

		## Create Table groups
		$this->db->query(<<<'EOT'
CREATE TABLE `groups` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(80) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
EOT
);

		## Create Table lhc_log
		$this->db->query(<<<'EOT'
CREATE TABLE `lhc_slides` (
  `id` int(11) NOT NULL auto_increment,
  `content` text collate utf8_unicode_ci NOT NULL,
  `title` varchar(80) collate utf8_unicode_ci NOT NULL default '',
  `startTime` time NOT NULL,
  `endTime` time NOT NULL,
  `days` varchar(255) collate utf8_unicode_ci NOT NULL default '[]',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
EOT
);

		## Create Table lhc_slides
		$this->db->query(<<<'EOT'
CREATE TABLE `lhc_log` (
  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `message` text collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
EOT
);

		## Create Table login_attempts
		$this->db->query(<<<'EOT'
CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL auto_increment,
  `ip_address` varchar(40) collate utf8_bin NOT NULL,
  `login` varchar(50) collate utf8_bin NOT NULL,
  `time` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
EOT
);

		## Create Table mail
		$this->db->query(<<<'EOT'
CREATE TABLE `mail` (
  `id` int(11) NOT NULL auto_increment,
  `subject` varchar(255) collate utf8_unicode_ci NOT NULL,
  `body` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
EOT
);

		## Create Table mail_participant_map
		$this->db->query(<<<'EOT'
CREATE TABLE `mail_participant_map` (
  `id` int(11) NOT NULL auto_increment,
  `participantId` int(11) NOT NULL,
  `mailId` int(11) NOT NULL,
  `status` enum('QUEUED','IN_PROCESS','SENT','ERROR') NOT NULL default 'QUEUED',
  `last_modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `participantId_2` (`participantId`,`mailId`),
  KEY `participantId` (`participantId`),
  KEY `mailId` (`mailId`),
  CONSTRAINT `mail_participant_map_ibfk_1` FOREIGN KEY (`participantId`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mail_participant_map_ibfk_2` FOREIGN KEY (`participantId`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mail_participant_map_ibfk_3` FOREIGN KEY (`mailId`) REFERENCES `mail` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1918 DEFAULT CHARSET=latin1
EOT
);

		## Create Table participants
		$this->db->query(<<<'EOT'
CREATE TABLE `participants` (
  `id` int(11) NOT NULL auto_increment,
  `firstName` varchar(80) collate utf8_unicode_ci NOT NULL default '',
  `lastName` varchar(80) collate utf8_unicode_ci NOT NULL default '',
  `organization` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `uuid` char(32) collate utf8_unicode_ci NOT NULL,
  `type` enum('PLENARY','INVITED','ORAL','REGULAR_POSTER','ESSENCE_POSTER','COMMITTEE','UNKNOWN') collate utf8_unicode_ci NOT NULL default 'UNKNOWN',
  `isSilverback` tinyint(1) NOT NULL default '0',
  `level` enum('SENIOR','STUDENT','COMMITTEE','UNKNOWN') collate utf8_unicode_ci NOT NULL default 'UNKNOWN',
  `email` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `department` varchar(255) collate utf8_unicode_ci NOT NULL default '',
  `title` varchar(10) collate utf8_unicode_ci NOT NULL default '',
  `silverback` enum('SELECTED','INVITED','NO_ANSWER','ANSWERED') collate utf8_unicode_ci default NULL,
  `hasCancelled` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  UNIQUE KEY `uuid_2` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=11010 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
EOT
);

		## Create Table poster_visitor_map
		$this->db->query(<<<'EOT'
CREATE TABLE `poster_visitor_map` (
  `id` int(11) NOT NULL auto_increment,
  `presenterId` int(11) NOT NULL,
  `visitorId` int(11) NOT NULL,
  `accepted` tinyint(4) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `presenterId` (`presenterId`),
  KEY `visitorId` (`visitorId`),
  CONSTRAINT `seeMeAtMyPosterActivity_ibfk_1` FOREIGN KEY (`presenterId`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `seeMeAtMyPosterActivity_ibfk_2` FOREIGN KEY (`visitorId`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=887 DEFAULT CHARSET=latin1
EOT
);

		## Create Table silverback_answers
		$this->db->query(<<<'EOT'
CREATE TABLE `silverback_answers` (
  `id` int(11) NOT NULL auto_increment,
  `day` enum('ASSIGN_ME','MONDAY','TUESDAY','WEDNESDAY','NOT_IN','ASSIGNED_MONDAY','ASSIGNED_TUESDAY') NOT NULL,
  `timeOfAnswer` datetime NOT NULL,
  `participantId` int(11) NOT NULL,
  `maxStudents` tinyint(4) NOT NULL default '5',
  `restaurant` varchar(80) default NULL,
  PRIMARY KEY  (`id`),
  KEY `participantId` (`participantId`),
  CONSTRAINT `silverback_answers_ibfk_1` FOREIGN KEY (`participantId`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=latin1
EOT
);

		## Create Table silverback_student_map
		$this->db->query(<<<'EOT'
CREATE TABLE `silverback_student_map` (
  `id` int(11) NOT NULL auto_increment,
  `silverbackId` int(11) NOT NULL,
  `studentId` int(11) NOT NULL,
  `timeOfInsert` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `silverbackId` (`silverbackId`,`studentId`),
  KEY `studentId` (`studentId`),
  CONSTRAINT `silverback_student_map_ibfk_1` FOREIGN KEY (`silverbackId`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `silverback_student_map_ibfk_2` FOREIGN KEY (`studentId`) REFERENCES `participants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=152 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
EOT
);

		## Create Table symposia
		$this->db->query(<<<'EOT'
CREATE TABLE `symposia` (
  `id` int(11) NOT NULL,
  `name` varchar(255) collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `id` (`id`),
  UNIQUE KEY `id_2` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
EOT
);

		## Create Table user_autologin
		$this->db->query(<<<'EOT'
CREATE TABLE `user_autologin` (
  `key_id` char(32) collate utf8_bin NOT NULL,
  `user_id` int(11) NOT NULL default '0',
  `user_agent` varchar(150) collate utf8_bin NOT NULL,
  `last_ip` varchar(40) collate utf8_bin NOT NULL,
  `last_login` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`key_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin
EOT
);

		## Create Table user_profiles
		$this->db->query(<<<'EOT'
CREATE TABLE `user_profiles` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `country` varchar(20) collate utf8_bin default NULL,
  `website` varchar(255) collate utf8_bin default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
EOT
);

		## Create Table users
		$this->db->query(<<<'EOT'
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(50) collate utf8_bin NOT NULL,
  `password` varchar(255) collate utf8_bin NOT NULL,
  `email` varchar(100) collate utf8_bin NOT NULL,
  `activated` tinyint(1) NOT NULL default '1',
  `banned` tinyint(1) NOT NULL default '0',
  `ban_reason` varchar(255) collate utf8_bin default NULL,
  `new_password_key` varchar(50) collate utf8_bin default NULL,
  `new_password_requested` datetime default NULL,
  `new_email` varchar(100) collate utf8_bin default NULL,
  `new_email_key` varchar(50) collate utf8_bin default NULL,
  `last_ip` varchar(40) collate utf8_bin NOT NULL,
  `last_login` datetime NOT NULL default '0000-00-00 00:00:00',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_bin
EOT
);

    $this->db->query('SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS');
	 }

	public function down()	{
		### Drop table ci_sessions ##
		$this->dbforge->drop_table("ci_sessions", TRUE);
		### Drop table contributions ##
		$this->dbforge->drop_table("contributions", TRUE);
		### Drop table groups ##
		$this->dbforge->drop_table("groups", TRUE);
		### Drop table lhc_log ##
		$this->dbforge->drop_table("lhc_log", TRUE);
		### Drop table lhc_slides ##
		$this->dbforge->drop_table("lhc_slides", TRUE);
		### Drop table login_attempts ##
		$this->dbforge->drop_table("login_attempts", TRUE);
		### Drop table mail ##
		$this->dbforge->drop_table("mail", TRUE);
		### Drop table mail_participant_map ##
		$this->dbforge->drop_table("mail_participant_map", TRUE);
		### Drop table participants ##
		$this->dbforge->drop_table("participants", TRUE);
		### Drop table poster_visitor_map ##
		$this->dbforge->drop_table("poster_visitor_map", TRUE);
		### Drop table silverback_answers ##
		$this->dbforge->drop_table("silverback_answers", TRUE);
		### Drop table silverback_student_map ##
		$this->dbforge->drop_table("silverback_student_map", TRUE);
		### Drop table symposia ##
		$this->dbforge->drop_table("symposia", TRUE);
		### Drop table user_autologin ##
		$this->dbforge->drop_table("user_autologin", TRUE);
		### Drop table user_profiles ##
		$this->dbforge->drop_table("user_profiles", TRUE);
		### Drop table users ##
		$this->dbforge->drop_table("users", TRUE);

	}
}
