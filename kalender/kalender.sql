# phpMyAdmin SQL Dump
# version 2.5.4
# http://www.phpmyadmin.net
#
# Vært: localhost
# Genereringstidspunkt: 29/12 2003 kl. 19:05:06
# Server version: 4.0.12
# PHP version: 4.3.4RC1
# 
# Database: : `eurole_dk`
# 

# --------------------------------------------------------

#
# Struktur dump for tabellen `kalender`
#

CREATE TABLE `kalender` (
  `id` int(11) NOT NULL auto_increment,
  `dato` date NOT NULL default '0000-00-00',
  `tekst` text,
  `kategori` tinyint(4) NOT NULL default '0',
  `user` text NOT NULL,
  `gentag` text,
  `tidsstempel` timestamp(14) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `id_2` (`id`),
  KEY `id` (`id`)
) TYPE=MyISAM AUTO_INCREMENT=11 ;
