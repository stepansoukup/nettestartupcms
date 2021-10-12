-- Adminer 4.7.8 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `category` (`id`, `name`, `slug`, `parent_id`, `deleted`, `order`) VALUES
(1,	'Domácí nahrávání',	'domaci-nahravani',	0,	0,	1),
(2,	'Pluginy',	'pluginy',	0,	0,	2),
(3,	'Virtuální nástroje',	'virtualni-nastroje',	0,	0,	3),
(4,	'IR',	'ir',	0,	0,	4);

DROP TABLE IF EXISTS `menu`;
CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(11) NOT NULL DEFAULT 0,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `menu` (`id`, `title`, `link`, `parent_id`, `order`) VALUES
(9,	'Domácí nahrávání',	'/posts/archive/1/domaci-nahravani',	0,	0),
(10,	'Pluginy',	'/posts/archive/1/pluginy',	0,	0),
(11,	'Virtuální nástroje',	'/posts/archive/1/virtualni-nastroje',	0,	0),
(14,	'IR',	'/posts/archive/1/ir',	0,	0);

DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `pages` (`id`, `title`, `slug`, `content`, `deleted`) VALUES
(1,	'Osobní údaje a cookies',	'osobni-udaje-a-cookies',	'<p>Tento web sám o sobě neuchovává žádné vaše osobní údaje a ani nepotřebuje ukládat cookies.&nbsp;</p><p>Jediný kdo tak činí je Google a jeho služba Analytics. Ta uchovává informace o vás a vašem zařízení stejně tak jako uchovává soubory cookies ve vašem počítači za účelem podrobného sestavení vašeho osobního profilu pro lepší cílení reklam. My z toho máme docela podrobné a složité statistiky zda-li nás někdo vlastně navštěvuje a odkud k nám přišel.&nbsp;</p><p>Používáním webu s těmito podmínkami souhlasíte.</p><p>Děkujeme za pochopení.</p>',	0),
(2,	'Úvodní stránka',	'uvodni-stranka',	'<ul><li>práva redaktorů</li><li>galerie</li><li>bannery</li><li>fb share button</li><li>šablony</li></ul>',	0);

DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image` varchar(200) NOT NULL,
  `category` int(11) NOT NULL,
  `date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user` int(11) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `category` (`category`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`category`) REFERENCES `category` (`id`),
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`user`) REFERENCES `users` (`id`),
  CONSTRAINT `posts_ibfk_3` FOREIGN KEY (`category`) REFERENCES `category` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `posts` (`id`, `title`, `slug`, `content`, `image`, `category`, `date`, `user`, `visible`) VALUES
(1,	'První článek',	'prvni-clanek',	'<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque sapien. In dapibus augue non sapien. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nulla non arcu lacinia neque faucibus fringilla. Nullam sapien sem, ornare ac, nonummy non, lobortis a enim. Nullam dapibus fermentum ipsum. Nullam sit amet magna in magna gravida vehicula. Cras pede libero, dapibus nec, pretium sit amet, tempor quis. Praesent dapibus. Aliquam erat volutpat. Etiam posuere lacus quis dolor. Integer malesuada. Maecenas ipsum velit, consectetuer eu lobortis ut, dictum at dui. Aliquam erat volutpat. Integer malesuada. Praesent id justo in neque elementum ultrices. Mauris tincidunt sem sed arcu. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p><p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque sapien. In dapibus augue non sapien. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nulla non arcu lacinia neque faucibus fringilla. Nullam sapien sem, ornare ac, nonummy non, lobortis a enim. Nullam dapibus fermentum ipsum. Nullam sit amet magna in magna gravida vehicula. Cras pede libero, dapibus nec, pretium sit amet, tempor quis. Praesent dapibus. Aliquam erat volutpat. Etiam posuere lacus quis dolor. Integer malesuada. Maecenas ipsum velit, consectetuer eu lobortis ut, dictum at dui. Aliquam erat volutpat. Integer malesuada. Praesent id justo in neque elementum ultrices. Mauris tincidunt sem sed arcu. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>',	'prvni-clanek.jpeg',	1,	'2021-04-14 07:25:51',	1,	2),
(2,	'Druhý článek',	'druhy-clanek',	'<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque sapien. In dapibus augue non sapien. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Nulla non arcu lacinia neque faucibus fringilla. Nullam sapien sem, ornare ac, nonummy non, lobortis a enim. Nullam dapibus fermentum ipsum. Nullam sit amet magna in magna gravida vehicula. Cras pede libero, dapibus nec, pretium sit amet, tempor quis. Praesent dapibus. Aliquam erat volutpat. Etiam posuere lacus quis dolor. Integer malesuada. Maecenas ipsum velit, consectetuer eu lobortis ut, dictum at dui. Aliquam erat volutpat. Integer malesuada. Praesent id justo in neque elementum ultrices. Mauris tincidunt sem sed arcu. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>',	'druhy-clanek.jpeg',	2,	'2021-04-14 07:25:16',	1,	2),
(3,	'Nevím článek',	'nevim-clanek',	'<p>Ahoj světegregre</p><p>gre</p><p>gre</p><p>gr</p><p><img src=\"https://xdsoft.net/jodit/files/neural_soldano.jpg\" style=\"width: 800px; height: 450px;\"><br></p><p>gr</p><p>gre</p><p>rge</p><p>gre</p><p>rge</p>',	'nevim-clanek.jpeg',	1,	'2021-04-16 15:11:11',	1,	2),
(4,	'Test',	'test',	'<p>testf</p>',	'test.jpeg',	2,	'2021-04-14 10:28:01',	1,	2),
(5,	'Čřánek',	'cranek',	'<p>čus vole vzorovej</p><p><img src=\"https://xdsoft.net/jodit/files/white.jpg\" style=\"width: 300px;\"><br></p>',	'cranek.jpeg',	4,	'2021-04-16 08:54:06',	1,	2);

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1,	'title',	'Nette Startup CMS'),
(2,	'copyright',	'© 2021 <a href=\"https://www.stepansoukup.cz\" target=\"_blank\">Štěpán Soukup</a> | všechna práva vyhrazena'),
(3,	'description',	'Základní redakční systém pro universální využití.'),
(4,	'logo',	'logo.png'),
(5,	'favicon',	'icon.png'),
(6,	'primary',	'ea4e4e'),
(7,	'secondary',	'4f5d73'),
(8,	'menu_bg',	'fff'),
(9,	'content_bg',	'fff'),
(10,	'h_color',	'444'),
(11,	'p_color',	'444'),
(12,	'slider_width',	'600'),
(13,	'subslider_width',	'300'),
(14,	'facebook',	'https://www.facebook.com/stepansoukup.cz'),
(15,	'messenger',	''),
(16,	'instagram',	''),
(17,	'youtube',	''),
(18,	'twitter',	''),
(19,	'linkedin',	''),
(20,	'fb_group',	''),
(21,	'email',	'info@stepansoukup.cz'),
(22,	'scripts',	''),
(23,	'slider_time',	'3'),
(24,	'subslider_time',	'3'),
(25,	'homepage',	'0'),
(26,	'slider',	'0');

DROP TABLE IF EXISTS `slides`;
CREATE TABLE `slides` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `img` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `slides` (`id`, `img`, `url`, `order`) VALUES
(13,	'y9j80kyyxa.jpeg',	'',	1),
(14,	'sadir4u6l7.jpeg',	'',	2);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `register_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
  `role` varchar(100) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `users` (`id`, `username`, `password`, `firstname`, `lastname`, `email`, `register_date`, `role`) VALUES
(1,	'admin',	'$2y$10$GHvIewXosGtaiACKgOTG8u.veJaa0KDh8guSRDd9irl6VHIhIyA3O',	'Firstname',	'Lastname',	'info@example.com',	'2021-10-12 08:15:10',	'admin');

-- 2021-10-12 08:18:02
