-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Client :  127.0.0.1
-- Généré le :  Jeu 23 Août 2018 à 17:37
-- Version du serveur :  5.7.14
-- Version de PHP :  7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `filehosting`
--

-- --------------------------------------------------------

--
-- Structure de la table `bill`
--

CREATE TABLE `bill` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_subscription` int(11) NOT NULL,
  `billing_date` date NOT NULL,
  `price` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `credit_card`
--

CREATE TABLE `credit_card` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `card_number` varchar(22) COLLATE utf8_unicode_ci NOT NULL,
  `card_security_code` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `card_month` tinyint(4) NOT NULL,
  `card_year` year(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `file`
--

CREATE TABLE `file` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_folder` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `size` int(11) NOT NULL,
  `target` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `file_share`
--

CREATE TABLE `file_share` (
  `id` int(11) NOT NULL,
  `id_file` int(11) NOT NULL,
  `id_folder` int(11) NOT NULL,
  `id_user_host` int(11) NOT NULL,
  `id_user_guest` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `folder`
--

CREATE TABLE `folder` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_folder` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `target` varchar(255) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `folder_share`
--

CREATE TABLE `folder_share` (
  `id` int(11) NOT NULL,
  `id_folder` int(11) NOT NULL,
  `id_parent_folder` int(11) NOT NULL,
  `id_user_host` int(11) NOT NULL,
  `id_user_guest` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `subscription`
--

CREATE TABLE `subscription` (
  `id` int(11) NOT NULL,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `price` double NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Contenu de la table `subscription`
--

INSERT INTO `subscription` (`id`, `name`, `price`, `description`) VALUES
(1, 'Sans abonnement', 0, 'Vous avez actuellement aucun abonnement actif sur votre compte. Vos accès sont limités. Souscrivez à un abonnement pour profiter de votre espace de stockage.'),
(2, 'Starter', 10, 'Vous avez actuellement l\'abonnement Starter actif sur votre compte. Celui ci est parfait pour un démarrage. Vous pouvez héberger et partager vos fichiers. Pour un espace de stockage plus large vous pouvez souscrire à un abonnement Premium.'),
(3, 'Premium', 20, 'Vous avez actuellement l\'abonnement Premium actif sur votre compte. Celui ci est parfait pour profiter pleinement de l\'hébergement cloud. Vous pouvez héberger et partager vos fichiers et possèdez un espace de stockage large. En tant que membre Premium, si vous souhaitez aggrandir votre espace de stockage vous pouvez acheter de l\'espace supplémentaire.'),
(4, 'Sans Package', 0, 'Vous avez actuellement aucun Package actif sur votre compte. Souscrivez à un Package pour étendre votre espace de stockage.'),
(5, 'Package 1', 7, 'Vous avez actuellement le Package 1 actif sur votre compte. Votre espace de stockage est étendu. Pour un espace de stockage plus large vous pouvez souscrire au Package 2.'),
(6, 'Package 2', 12, 'Vous avez actuellement le Package 2 actif sur votre compte. Celui ci est parfait pour profiter pleinement de l\'hébergement cloud. Votre espace de stockage est large.');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subscription` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `begin_subscription` datetime DEFAULT NULL,
  `end_subscription` datetime DEFAULT NULL,
  `file_size_limit` bigint(20) NOT NULL,
  `current_storage_size` bigint(20) NOT NULL,
  `max_storage_size` bigint(20) NOT NULL,
  `package` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `begin_package` datetime DEFAULT NULL,
  `end_package` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Index pour les tables exportées
--

--
-- Index pour la table `bill`
--
ALTER TABLE `bill`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `credit_card`
--
ALTER TABLE `credit_card`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `file`
--
ALTER TABLE `file`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `file_share`
--
ALTER TABLE `file_share`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `folder`
--
ALTER TABLE `folder`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `folder_share`
--
ALTER TABLE `folder_share`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `subscription`
--
ALTER TABLE `subscription`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables exportées
--

--
-- AUTO_INCREMENT pour la table `bill`
--
ALTER TABLE `bill`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `credit_card`
--
ALTER TABLE `credit_card`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `file`
--
ALTER TABLE `file`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `file_share`
--
ALTER TABLE `file_share`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `folder`
--
ALTER TABLE `folder`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `folder_share`
--
ALTER TABLE `folder_share`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT pour la table `subscription`
--
ALTER TABLE `subscription`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
