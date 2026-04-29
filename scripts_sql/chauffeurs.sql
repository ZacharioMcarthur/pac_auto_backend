-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 28 jan. 2022 à 11:47
-- Version du serveur :  10.4.19-MariaDB
-- Version de PHP : 7.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `parcautomobilepac`
--

-- --------------------------------------------------------

--
-- Structure de la table `chauffeurs`
--

CREATE TABLE `chauffeurs` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `matricule` int(11) NOT NULL,
  `annee_permis` int(11) NOT NULL,
  `disponibilite` varchar(60) NOT NULL,
  `adresse` varchar(60) DEFAULT NULL,
  `contact` varchar(22) NOT NULL,
  `statut` tinyint(1) NOT NULL,
  `num_permis` varchar(15) NOT NULL,
  `categorie_permis_id` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `updated_by` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `chauffeurs`
--

INSERT INTO `chauffeurs` (`id`, `user_id`, `matricule`, `annee_permis`, `disponibilite`, `adresse`, `contact`, `statut`, `num_permis`, `categorie_permis_id`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 1, 814, 2001, 'Disponible', 'Akpakpa', '90257475', 1, '2001/C558/DGTT', 1, '2022-01-28 09:23:53', 3, '2022-01-28 09:23:53', NULL),
(2, 2, 750, 2003, 'Course', 'Akassato', '64438320', 1, '2006/D152/DGTT', 2, '2022-01-28 09:23:53', 3, '2022-01-28 09:23:53', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `chauffeurs`
--
ALTER TABLE `chauffeurs`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `chauffeurs`
--
ALTER TABLE `chauffeurs`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
