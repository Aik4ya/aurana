-- phpMyAdmin SQL Dump
-- version 5.2.1deb1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le : jeu. 13 juin 2024 à 20:10
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
-- Structure de la table `FICHIER`
--

CREATE TABLE `FICHIER` (
  `Fichier_ID` int(11) NOT NULL,
  `Adresse` varchar(30) NOT NULL,
  `Date_Stock` date DEFAULT NULL,
  `Groupe_ID` int(11) DEFAULT NULL,
  `fichier_type` varchar(50) DEFAULT NULL,
  `fichier_size` int(11) DEFAULT NULL,
  `Utilisateur_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `FICHIER`
--
ALTER TABLE `FICHIER`
  ADD PRIMARY KEY (`Fichier_ID`),
  ADD KEY `Groupe_ID` (`Groupe_ID`),
  ADD KEY `fk_utilisateur_id` (`Utilisateur_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `FICHIER`
--
ALTER TABLE `FICHIER`
  MODIFY `Fichier_ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `FICHIER`
--
ALTER TABLE `FICHIER`
  ADD CONSTRAINT `FICHIER_ibfk_1` FOREIGN KEY (`Groupe_ID`) REFERENCES `GROUPE` (`Groupe_ID`),
  ADD CONSTRAINT `fk_utilisateur_id` FOREIGN KEY (`Utilisateur_id`) REFERENCES `UTILISATEUR` (`Utilisateur_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
