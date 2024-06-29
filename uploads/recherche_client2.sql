-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : ven. 31 mai 2024 à 16:19
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `recherche_client2`
--

-- --------------------------------------------------------

--
-- Structure de la table `adresse`
--

CREATE TABLE `adresse` (
  `ID` int(11) NOT NULL,
  `Client_Code` varchar(255) DEFAULT NULL,
  `Departement_code` varchar(255) DEFAULT NULL,
  `Departement` varchar(255) DEFAULT NULL,
  `Adresse` varchar(255) DEFAULT NULL,
  `Code_postal` varchar(255) DEFAULT NULL,
  `Commune` varchar(255) DEFAULT NULL,
  `Pays` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
  `Code` varchar(255) NOT NULL,
  `Nom_complet` varchar(255) DEFAULT NULL,
  `Qualification` varchar(255) DEFAULT NULL,
  `Famille` varchar(255) DEFAULT NULL,
  `Nature` varchar(255) DEFAULT NULL,
  `Titre` varchar(255) DEFAULT NULL,
  `Nom_denomination` varchar(255) DEFAULT NULL,
  `Nom_commercial` varchar(255) DEFAULT NULL,
  `Forme_juridique` varchar(255) DEFAULT NULL,
  `Tel_fax` int(11) DEFAULT NULL,
  `Tel` int(11) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Date_creation` varchar(30) DEFAULT NULL,
  `Tva` varchar(255) DEFAULT NULL,
  `Siret` varchar(255) DEFAULT NULL,
  `Ape_code` varchar(50) DEFAULT NULL,
  `Ape_libelle` varchar(255) DEFAULT NULL,
  `Nafu_code` varchar(50) DEFAULT NULL,
  `Nafu_libelle` varchar(255) DEFAULT NULL,
  `Activite_exercer` varchar(255) DEFAULT NULL,
  `Activite` varchar(255) DEFAULT NULL,
  `Activite_libelle` varchar(255) DEFAULT NULL,
  `Rcs` int(11) DEFAULT NULL,
  `numero` varchar(11) DEFAULT NULL,
  `Chef_de_mission_Code` varchar(5) DEFAULT NULL,
  `Responsable_Code` varchar(5) DEFAULT NULL,
  `Collaborateur_Code` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `collaborateur`
--

CREATE TABLE `collaborateur` (
  `Code` varchar(5) NOT NULL,
  `Nom` varchar(255) DEFAULT NULL,
  `Email` varchar(255) DEFAULT NULL,
  `Tel` varchar(50) DEFAULT NULL,
  `Fonction` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `collaborateur`
--

INSERT INTO `collaborateur` (`Code`, `Nom`, `Email`, `Tel`, `Fonction`) VALUES
('ALZ', 'ZERR Anne laure', 'anne-laure.zerr@dbfaudit.com', NULL, 'Associée'),
('BCH', 'CHOUMERT  Bertrand', 'bertrand.choumert@dbfaudit.com', NULL, 'Associé'),
('MDI', 'DILLENSCHNEIDER Marie', 'marie.dillenschneider@dbfaudit.com', NULL, 'Associé'),
('PNA', 'NADJAHI Pauline', 'Pauline.NADJAHI@dbfaudit.com', NULL, 'Collaboratrice comptable principale'),
('RMI', 'MITRI Raphaël', 'raphael.mitri@dbfaudit.com', NULL, 'Expert-comptable');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `adresse`
--
ALTER TABLE `adresse`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Client_Code` (`Client_Code`);

--
-- Index pour la table `client`
--
ALTER TABLE `client`
  ADD PRIMARY KEY (`Code`),
  ADD KEY `Chef_de_mission_Code` (`Chef_de_mission_Code`),
  ADD KEY `Responsable_Code` (`Responsable_Code`),
  ADD KEY `Collaborateur_Code` (`Collaborateur_Code`);

--
-- Index pour la table `collaborateur`
--
ALTER TABLE `collaborateur`
  ADD PRIMARY KEY (`Code`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `adresse`
--
ALTER TABLE `adresse`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `adresse`
--
ALTER TABLE `adresse`
  ADD CONSTRAINT `adresse_ibfk_1` FOREIGN KEY (`Client_Code`) REFERENCES `client` (`Code`);

--
-- Contraintes pour la table `client`
--
ALTER TABLE `client`
  ADD CONSTRAINT `client_ibfk_1` FOREIGN KEY (`Chef_de_mission_Code`) REFERENCES `collaborateur` (`Code`),
  ADD CONSTRAINT `client_ibfk_2` FOREIGN KEY (`Responsable_Code`) REFERENCES `collaborateur` (`Code`),
  ADD CONSTRAINT `client_ibfk_3` FOREIGN KEY (`Collaborateur_Code`) REFERENCES `collaborateur` (`Code`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
