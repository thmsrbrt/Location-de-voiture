-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le : ven. 05 nov. 2021 à 16:08
-- Version du serveur :  5.7.30
-- Version de PHP : 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données : `robeth`
--

-- --------------------------------------------------------

--
-- Structure de la table `client`
--

CREATE TABLE `client` (
                          `id` int(11) NOT NULL,
                          `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                          `prenom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                          `pseudo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
                          `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                          `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                          `adresse` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `client`
--

INSERT INTO `client` (`id`, `nom`, `prenom`, `pseudo`, `password`, `email`, `adresse`) VALUES
                                                                                           (1, 'Thomas', 'Robert', '', '1234567890', 'thomas.robert@email.com', 'Paris1'),
                                                                                           (2, 'Quentin', 'Robert', 'quidhuitre', '1212', 'thomas.robert@email.com', 'Rue du ranelaght Paris'),
                                                                                           (3, 'David', 'Robert', 'dav', '2424', 'thomas.robert@email.com', 'Paris2');

-- --------------------------------------------------------

--
-- Structure de la table `facture`
--

CREATE TABLE `facture` (
                           `id` int(11) NOT NULL,
                           `date_f` date NOT NULL,
                           `valeur` int(11) NOT NULL,
                           `etat_r` tinyint(1) NOT NULL,
                           `id_c` int(11) NOT NULL,
                           `id_v` int(11) NOT NULL,
                           `date_d` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `facture`
--

INSERT INTO `facture` (`id`, `date_f`, `valeur`, `etat_r`, `id_c`, `id_v`, `date_d`) VALUES
                                                                                         (1, '2021-01-01', 4000, 1, 1, 4, '2020-01-01'),
                                                                                         (2, '2021-01-01', 5000, 1, 1, 2, '2020-01-01'),
                                                                                         (3, '2020-09-01', 4000, 0, 3, 1, '2020-01-30'),
                                                                                         (4, '2020-05-01', 4000, 1, 2, 5, '2020-02-15');

-- --------------------------------------------------------

--
-- Structure de la table `vehicule`
--

CREATE TABLE `vehicule` (
                            `id` int(11) NOT NULL,
                            `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `photo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                            `etat` tinyint(1) NOT NULL,
                            `caracteres` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vehicule`
--

INSERT INTO `vehicule` (`id`, `name`, `photo`, `etat`, `caracteres`) VALUES
                                                                         (1, 'Peugeot 508', '/Images/Voitures/508-noir-perla-nera.jpg', 1, 'noir moteur eeeeee'),
                                                                         (2, 'Peugeot 3008', '/Images/Voitures/peugeot-3008-bmw-q3-115.jpg', 1, 'rouge eeeee'),
                                                                         (3, 'Peugeot 5008', '/Images/Voitures/peugeot-5008-2009styp-016b.jpg', 1, 'bleu magnétique'),
                                                                         (4, 'Peugeot 208', '/Images/Voitures/Peugeot_208_GT_Line.jpeg', 1, 'jaune eeeee'),
                                                                         (5, 'DS', '/Images/Voitures/S0-salon-de-geneve-2020-ds-presente-la-ds9-622618.jpg', 1, 'la ds du garage'),
                                                                         (9, 'test qui marche', '/Images/Voitures1240484-20191102231502103-59423426-6185425a4c1fa.png', 1, 'teste et été tété'),
                                                                         (10, 'thomas', '/Images/Voitures/1240484-20191102231502103-59423426-618542a0c98ca.png', 1, 'ooooo ooo o o o o'),
                                                                         (11, 'ttttt', '/Images/Voitures/01-0012e-5LqSFK3TmHf11HLzjPg1eSAZ-6185439a627de.jpg', 0, 'rrrrrc');

-- --------------------------------------------------------

--
-- Structure de la table `vendeur`
--

CREATE TABLE `vendeur` (
                           `id` int(11) NOT NULL,
                           `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
                           `identifiant` int(11) NOT NULL,
                           `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `vendeur`
--

INSERT INTO `vendeur` (`id`, `nom`, `identifiant`, `password`) VALUES
                                                                   (1, 'Eric', 1956, 'eric1234'),
                                                                   (2, 'Isa', 3, 'bambou');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `client`
--
ALTER TABLE `client`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `facture`
--
ALTER TABLE `facture`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `vehicule`
--
ALTER TABLE `vehicule`
    ADD PRIMARY KEY (`id`);

--
-- Index pour la table `vendeur`
--
ALTER TABLE `vendeur`
    ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `client`
--
ALTER TABLE `client`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `facture`
--
ALTER TABLE `facture`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `vehicule`
--
ALTER TABLE `vehicule`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT pour la table `vendeur`
--
ALTER TABLE `vendeur`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
