-- phpMyAdmin SQL Dump
-- version 3.5.3
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Янв 22 2014 г., 04:01
-- Версия сервера: 5.5.33
-- Версия PHP: 5.3.17

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `rss`
--

-- --------------------------------------------------------

--
-- Структура таблицы `filters`
--

CREATE TABLE IF NOT EXISTS `filters` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rsslist_id` int(10) unsigned NOT NULL,
  `name` varchar(50) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `dirname` varchar(50) NOT NULL,
  `include` text NOT NULL,
  `exclude` text NOT NULL,
  `from` int(10) unsigned NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `lastcheck` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `ru_name` varchar(100) NOT NULL,
  `tag` varchar(50) NOT NULL,
  `img` varchar(200) NOT NULL,
  `include` text NOT NULL,
  `exclude` text NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '1',
  `lastcheck` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `rsslist`
--

CREATE TABLE IF NOT EXISTS `rsslist` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(200) NOT NULL,
  `cookie` varchar(200) NOT NULL,
  `name` varchar(50) NOT NULL,
  `last_update` int(10) unsigned NOT NULL,
  `refresh_time` int(10) unsigned NOT NULL DEFAULT '1800',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `rss_items`
--

CREATE TABLE IF NOT EXISTS `rss_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `v_type` varchar(30) NOT NULL,
  `description` text NOT NULL,
  `link` text NOT NULL,
  `added` int(10) unsigned NOT NULL,
  `item_time` int(10) unsigned NOT NULL,
  `rsslist_id` int(10) unsigned NOT NULL,
  `rss_log_id` int(10) unsigned NOT NULL,
  `groups_id` int(10) NOT NULL,
  `filters_id` int(10) unsigned NOT NULL,
  `file_name` varchar(200) NOT NULL,
  `file` mediumblob NOT NULL,
  `raw` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `rss_log`
--

CREATE TABLE IF NOT EXISTS `rss_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `rsslist_id` int(10) unsigned NOT NULL,
  `time` int(10) unsigned NOT NULL,
  `xml_string` mediumtext NOT NULL,
  `parsed` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
