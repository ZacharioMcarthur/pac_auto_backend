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
-- Structure de la table `categorie_permis`
--

CREATE TABLE `categorie_permis` (
  `id` bigint(20) NOT NULL,
  `libelle` varchar(20) NOT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `categorie_permis`
--

INSERT INTO `categorie_permis` (`id`, `libelle`, `statut`, `created_at`, `updated_at`) VALUES
(1, 'C', 1, '2022-01-28 09:27:44', '2022-01-28 09:27:44'),
(2, 'D', 1, '2022-01-28 09:27:44', '2022-01-28 09:27:44');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `categorie_permis`
--
ALTER TABLE `categorie_permis`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categorie_permis`
--
ALTER TABLE `categorie_permis`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
