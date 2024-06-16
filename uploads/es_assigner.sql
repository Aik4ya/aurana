-- phpMyAdmin SQL Dump
-- version 5.2.1deb1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : ven. 07 juin 2024 à 16:55
-- Version du serveur : 10.11.6-MariaDB-0+deb12u1
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `Aurana_bdd`
--

-- --------------------------------------------------------

--
-- Structure de la table `es_assigner`
--

CREATE TABLE `es_assigner` (
  `ID` int(11) NOT NULL,
  `Utilisateur_ID` int(11) DEFAULT NULL,
  `Tache_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `es_assigner`
--

INSERT INTO `es_assigner` (`ID`, `Utilisateur_ID`, `Tache_ID`) VALUES
(18, 1, 26),
(19, 22, 27),
(20, 22, 28),
(21, 22, 29),
(22, 22, 30);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `es_assigner`
--
ALTER TABLE `es_assigner`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Utilisateur_ID` (`Utilisateur_ID`),
  ADD KEY `Tache_ID` (`Tache_ID`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `es_assigner`
--
ALTER TABLE `es_assigner`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `es_assigner`
--
ALTER TABLE `es_assigner`
  ADD CONSTRAINT `es_assigner_ibfk_1` FOREIGN KEY (`Utilisateur_ID`) REFERENCES `UTILISATEUR` (`Utilisateur_ID`),
  ADD CONSTRAINT `es_assigner_ibfk_2` FOREIGN KEY (`Tache_ID`) REFERENCES `TACHE` (`Tache_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
