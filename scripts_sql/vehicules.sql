-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 21 jan. 2022 à 19:33
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
-- Structure de la table `vehicules`
--

CREATE TABLE `vehicules` (
  `id` bigint(20) NOT NULL,
  `immatr` varchar(12) NOT NULL,
  `marque` varchar(35) NOT NULL,
  `date_mise_circulation` date NOT NULL,
  `statut` tinyint(1) NOT NULL DEFAULT 1,
  `disponibilite` enum('Panne','Course','Disponible','') NOT NULL DEFAULT 'Disponible',
  `capacite` int(11) NOT NULL,
  `type_vehicule_id` bigint(20) NOT NULL,
  `created_by` bigint(20) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_by` bigint(11) DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Déchargement des données de la table `vehicules`
--

INSERT INTO `vehicules` (`id`, `immatr`, `marque`, `date_mise_circulation`, `statut`, `disponibilite`, `capacite`, `type_vehicule_id`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
(1, 'AT4577RB', 'Toyota', '2022-01-05', 1, 'Disponible', 5, 2, 1, '2022-01-21 10:08:10', 1, '2022-01-21 10:08:10'),
(2, 'AS7457RB', 'Toyota', '2021-01-20', 1, 'Disponible', 5, 3, 1, '2022-01-21 10:36:18', 2, '2021-05-21 02:56:22');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `vehicules`
--
ALTER TABLE `vehicules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `immatr` (`immatr`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `vehicules`
--
ALTER TABLE `vehicules`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
