CREATE TABLE `aftale_bruger` (
  `userID` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `name` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `mail` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  PRIMARY KEY (`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `aftale_hoved` (
  `aftaleID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT '0',
  `tekst` varchar(250) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `aktiv` tinyint(1) NOT NULL DEFAULT '0',
  `startdato` date NOT NULL DEFAULT '0000-00-00',
  `slutdato` date NOT NULL DEFAULT '0000-00-00',
  PRIMARY KEY (`aftaleID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `aftale_mulighed` (
  `mulighedID` int(11) NOT NULL AUTO_INCREMENT,
  `aftaleID` int(11) NOT NULL DEFAULT '0',
  `startdato` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `starttid` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `slutdato` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `sluttid` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `note` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`mulighedID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `aftale_svar` (
  `mulighedID` int(11) NOT NULL DEFAULT '0',
  `userID` varchar(32) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `svar` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`mulighedID`,`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `billeder_billed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `galleri` int(11) NOT NULL DEFAULT '0',
  `org_navn` varchar(150) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `billed_navn` varchar(150) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `thumb_navn` varchar(150) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `opretter` int(11) NOT NULL DEFAULT '0',
  `kommentar` text CHARACTER SET latin1 NOT NULL,
  `oprettet` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lukket` char(3) CHARACTER SET latin1 NOT NULL DEFAULT 'nej',
  `slettet` char(3) CHARACTER SET latin1 NOT NULL DEFAULT 'nej',
  `filesize` varchar(15) CHARACTER SET latin1 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=841 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `billeder_count` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `count` int(11) NOT NULL DEFAULT '0',
  `datotid` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `billeder_galleri` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `navn` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `beskrivelse` text CHARACTER SET latin1 NOT NULL,
  `opretter` int(11) NOT NULL DEFAULT '0',
  `oprettet` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `billed` varchar(150) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `lukket` char(3) CHARACTER SET latin1 NOT NULL DEFAULT 'nej',
  `slettet` char(3) CHARACTER SET latin1 NOT NULL DEFAULT 'nej',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `bruger` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `adgangskode` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `navn` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `adresse` varchar(250) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `info` varchar(250) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `redaktoer` varchar(200) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `admin` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `oprettet` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `slettet` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `postnummer` varchar(4) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `bynavn` varchar(45) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `fastnet` varchar(15) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `mobil` varchar(15) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `klanmedlem` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `filer` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filnavn` varchar(150) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `side` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `dato` date NOT NULL DEFAULT '0000-00-00',
  `tekst` varchar(250) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `type` varchar(40) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `sti` varchar(250) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `user` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `forum_indhold` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Forum` tinyint(4) NOT NULL DEFAULT '0',
  `Reply` int(11) NOT NULL DEFAULT '0',
  `Navn` text CHARACTER SET latin1 NOT NULL,
  `Titel` text CHARACTER SET latin1 NOT NULL,
  `Tekst` text CHARACTER SET latin1 NOT NULL,
  `Tid` time NOT NULL DEFAULT '00:00:00',
  `Dato` date NOT NULL DEFAULT '0000-00-00',
  `Email` text CHARACTER SET latin1,
  `IP` tinytext CHARACTER SET latin1,
  PRIMARY KEY (`ID`),
  KEY `Reply` (`Reply`)
) ENGINE=InnoDB AUTO_INCREMENT=206 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `invitation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bruger_id` int(11) NOT NULL DEFAULT '0',
  `kalender_id` int(11) NOT NULL DEFAULT '0',
  `svar` smallint(6) NOT NULL DEFAULT '0',
  `frist` date NOT NULL DEFAULT '0000-00-00',
  `stempel` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `bruger_id` (`bruger_id`,`kalender_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `kalender` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `dato` date NOT NULL DEFAULT '0000-00-00',
  `varighed` double NOT NULL DEFAULT '1',
  `slutdato` date NOT NULL DEFAULT '0000-00-00',
  `titel` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `tekst` text CHARACTER SET latin1,
  `kategori` tinyint(4) NOT NULL DEFAULT '0',
  `user` text CHARACTER SET latin1 NOT NULL,
  `gentag` text CHARACTER SET latin1,
  `invitation` tinyint(1) NOT NULL DEFAULT '0',
  `tidsstempel` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`),
  KEY `dato` (`dato`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` smallint(6) NOT NULL DEFAULT '0',
  `tid` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `event` text CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=984 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `poll_hoved` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL DEFAULT '0',
  `headline` varchar(250) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `content` text CHARACTER SET latin1 NOT NULL,
  `multiple` tinyint(1) NOT NULL DEFAULT '0',
  `finalid` bigint(20) NOT NULL DEFAULT '0',
  `comment` text CHARACTER SET latin1 NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `edited` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `start_date` date NOT NULL DEFAULT '0000-00-00',
  `end_date` date NOT NULL DEFAULT '0000-00-00',
  `deleted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `poll_svar` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `userid` bigint(20) NOT NULL DEFAULT '0',
  `choiceid` bigint(20) NOT NULL DEFAULT '0',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `changed` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `deleted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `poll_valg` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `pollid` bigint(20) NOT NULL DEFAULT '0',
  `headline` varchar(250) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `comment` text CHARACTER SET latin1 NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `side` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `navn` varchar(25) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `tekst` longtext CHARACTER SET latin1 NOT NULL,
  `version` int(11) NOT NULL DEFAULT '0',
  `userchange` int(11) NOT NULL DEFAULT '0',
  `useropret` int(11) NOT NULL DEFAULT '0',
  `userslet` int(11) NOT NULL DEFAULT '0',
  `oprettet` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `changed` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `slettet` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `aktiv` tinyint(4) NOT NULL DEFAULT '0',
  `kommentar` text CHARACTER SET latin1 NOT NULL,
  `menunavn` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `menulink` varchar(10) CHARACTER SET latin1 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=252 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `stats_counter` (
  `counter` text CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `stats_daily` (
  `date` text CHARACTER SET latin1 NOT NULL,
  `time` text CHARACTER SET latin1 NOT NULL,
  `ip` text CHARACTER SET latin1 NOT NULL,
  `ref` text CHARACTER SET latin1 NOT NULL,
  `sessionid` text CHARACTER SET latin1 NOT NULL,
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60468 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;

CREATE TABLE `stats_online` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `timestamp` int(15) NOT NULL DEFAULT '0',
  `ip` varchar(40) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `file` varchar(100) CHARACTER SET latin1 NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `file` (`file`),
  KEY `ip` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=60059 DEFAULT CHARSET=utf8 COLLATE=utf8_danish_ci;
