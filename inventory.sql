
CREATE TABLE IF NOT EXISTS `data` (
  `id_data` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `player_data` longtext NOT NULL,
  `player_inventory` longtext NOT NULL,
  `player_profile` longtext NOT NULL,
  PRIMARY KEY (`id_data`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=99 ;


CREATE TABLE IF NOT EXISTS `regcodes` (
  `id_regcode` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(7) NOT NULL,
  `sacsid` text NOT NULL,
  `date_add` datetime NOT NULL,
  PRIMARY KEY (`id_regcode`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;



CREATE TABLE IF NOT EXISTS `regions` (
  `id_cell` varchar(5) NOT NULL,
  `region_name` varchar(128) NOT NULL,
  `vert_1_lat` float(10,6) NOT NULL,
  `vert_1_lng` float(10,6) NOT NULL,
  `vert_2_lat` float(10,6) NOT NULL,
  `vert_2_lng` float(10,6) NOT NULL,
  `vert_3_lat` float(10,6) NOT NULL,
  `vert_3_lng` float(10,6) NOT NULL,
  `vert_4_lat` float(10,6) NOT NULL,
  `vert_4_lng` float(10,6) NOT NULL,
  `max_lat` float(10,6) NOT NULL,
  `min_lat` float(10,6) NOT NULL,
  `max_lng` float(10,6) NOT NULL,
  `min_lng` float(10,6) NOT NULL,
  `vertices` polygon NOT NULL,
  PRIMARY KEY (`id_cell`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `region_scores` (
  `id_cell` varchar(4) NOT NULL,
  `score_data` longtext NOT NULL,
  `last_update` int(11) NOT NULL,
  `next_update` int(11) NOT NULL,
  PRIMARY KEY (`id_cell`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE IF NOT EXISTS `region_test` (
  `id_cell` varchar(5) NOT NULL,
  `region_name` varchar(128) NOT NULL,
  `vert_1_lat` float(10,6) NOT NULL,
  `vert_1_lng` float(10,6) NOT NULL,
  `vert_2_lat` float(10,6) NOT NULL,
  `vert_2_lng` float(10,6) NOT NULL,
  `vert_3_lat` float(10,6) NOT NULL,
  `vert_3_lng` float(10,6) NOT NULL,
  `vert_4_lat` float(10,6) NOT NULL,
  `vert_4_lng` float(10,6) NOT NULL,
  `max_lat` float(10,6) NOT NULL,
  `min_lat` float(10,6) NOT NULL,
  `max_lng` float(10,6) NOT NULL,
  `min_lng` float(10,6) NOT NULL,
  `vertices` polygon NOT NULL,
  PRIMARY KEY (`id_cell`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `sessions` (
  `id_session` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `sess_id` varchar(40) NOT NULL,
  `last_login` datetime NOT NULL,
  `token` text NOT NULL,
  PRIMARY KEY (`id_session`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=58 ;


CREATE TABLE IF NOT EXISTS `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `sacsid` text NOT NULL,
  `last_login` datetime NOT NULL,
  `player_profile` longtext NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;
