-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Gegenereerd op: 27 jun 2025 om 07:46
-- Serverversie: 8.2.0
-- PHP-versie: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `voedselbankdb`
--
CREATE DATABASE IF NOT EXISTS `voedselbankdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `voedselbankdb`;

DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `GetProductVoorraadOverzicht`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetProductVoorraadOverzicht` ()   BEGIN
    SELECT 
        p.ProductID,
        p.ProductNaam,
        p.EAN,
        c.Naam AS Categorie,
        p.AantalInVoorraad,
        l.Bedrijfsnaam AS Leverancier
    FROM Product p
    JOIN Categorie c ON p.CategorieID = c.CategorieID
    JOIN Leverancier l ON p.LeverancierID = l.LeverancierID
    ORDER BY c.Naam, p.ProductNaam;
END$$

DROP PROCEDURE IF EXISTS `UpdateVoorraadNaPakket`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateVoorraadNaPakket` (IN `p_ProductID` INT, IN `p_AantalGebruikt` INT)   BEGIN
    DECLARE v_HuidigeVoorraad INT;

    START TRANSACTION;

    SELECT AantalInVoorraad INTO v_HuidigeVoorraad
    FROM Product WHERE ProductID = p_ProductID;

    IF v_HuidigeVoorraad IS NULL THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Product niet gevonden';
    END IF;

    IF v_HuidigeVoorraad < p_AantalGebruikt THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Onvoldoende voorraad';
    END IF;

    UPDATE Product
    SET AantalInVoorraad = AantalInVoorraad - p_AantalGebruikt
    WHERE ProductID = p_ProductID;

    UPDATE Voedselopslag
    SET AantalInMagazijn = AantalInMagazijn - p_AantalGebruikt
    WHERE ProductID = p_ProductID;

    COMMIT;
END$$

DROP PROCEDURE IF EXISTS `VoegProductToe`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `VoegProductToe` (IN `p_LeverancierID` INT, IN `p_AllergieID` INT, IN `p_CategorieID` INT, IN `p_ProductNaam` VARCHAR(100), IN `p_EAN` CHAR(13), IN `p_AantalInVoorraad` INT)   BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Fout bij toevoegen product';
    END;

    INSERT INTO Product (LeverancierID, AllergieID, CategorieID, ProductNaam, EAN, AantalInVoorraad)
    VALUES (p_LeverancierID, p_AllergieID, p_CategorieID, p_ProductNaam, p_EAN, p_AantalInVoorraad);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `allergie`
--

DROP TABLE IF EXISTS `allergie`;
CREATE TABLE IF NOT EXISTS `allergie` (
  `AllergieID` int NOT NULL AUTO_INCREMENT,
  `Naam` varchar(50) NOT NULL,
  PRIMARY KEY (`AllergieID`),
  UNIQUE KEY `Naam` (`Naam`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `categorie`
--

DROP TABLE IF EXISTS `categorie`;
CREATE TABLE IF NOT EXISTS `categorie` (
  `CategorieID` int NOT NULL AUTO_INCREMENT,
  `Naam` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`CategorieID`),
  UNIQUE KEY `Naam` (`Naam`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `categorie`
--

INSERT INTO `categorie` (`CategorieID`, `Naam`) VALUES
(1, 'Aardappelen, groente, fruit'),
(2, 'Kaas, vleeswaren'),
(3, 'Zuivel, plantaardig en eieren'),
(4, 'Bakkerij en banket'),
(5, 'Frisdrank, sappen, koffie en thee'),
(6, 'Pasta, rijst en wereldkeuken'),
(7, 'Soepen, sauzen, kruiden en olie'),
(8, 'Snoep, koek, chips en chocolade'),
(9, 'Baby, verzorging en hygiëne');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `contact`
--

DROP TABLE IF EXISTS `contact`;
CREATE TABLE IF NOT EXISTS `contact` (
  `ContactID` int NOT NULL AUTO_INCREMENT,
  `GebruikerID` int NOT NULL,
  `Telefoon` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ContactID`),
  KEY `GebruikerID` (`GebruikerID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `familie`
--

DROP TABLE IF EXISTS `familie`;
CREATE TABLE IF NOT EXISTS `familie` (
  `KlantID` int NOT NULL,
  `FamilielidID` int NOT NULL,
  PRIMARY KEY (`KlantID`,`FamilielidID`),
  KEY `FamilielidID` (`FamilielidID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `gebruiker`
--

DROP TABLE IF EXISTS `gebruiker`;
CREATE TABLE IF NOT EXISTS `gebruiker` (
  `GebruikerID` int NOT NULL AUTO_INCREMENT,
  `PersoonID` int NOT NULL,
  `Gebruikersnaam` varchar(50) NOT NULL,
  `WachtwoordHash` varbinary(64) NOT NULL,
  `IsGeblokkeerd` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`GebruikerID`),
  UNIQUE KEY `Gebruikersnaam` (`Gebruikersnaam`),
  KEY `PersoonID` (`PersoonID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `gebruikerrol`
--

DROP TABLE IF EXISTS `gebruikerrol`;
CREATE TABLE IF NOT EXISTS `gebruikerrol` (
  `GebruikerID` int NOT NULL,
  `RolID` int NOT NULL,
  PRIMARY KEY (`GebruikerID`,`RolID`),
  KEY `RolID` (`RolID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `klant`
--

DROP TABLE IF EXISTS `klant`;
CREATE TABLE IF NOT EXISTS `klant` (
  `KlantID` int NOT NULL AUTO_INCREMENT,
  `GebruikerID` int NOT NULL,
  `AantalVolwassenen` int NOT NULL,
  `AantalKinderen` int NOT NULL,
  `AantalBabys` int NOT NULL,
  `GeenVarkensvlees` tinyint(1) DEFAULT '0',
  `Veganistisch` tinyint(1) DEFAULT '0',
  `Vegetarisch` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`KlantID`),
  KEY `IDX_Klant_GebruikerID` (`GebruikerID`)
) ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `klantallergie`
--

DROP TABLE IF EXISTS `klantallergie`;
CREATE TABLE IF NOT EXISTS `klantallergie` (
  `KlantID` int NOT NULL,
  `AllergieID` int NOT NULL,
  PRIMARY KEY (`KlantID`,`AllergieID`),
  KEY `AllergieID` (`AllergieID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `leverancier`
--

DROP TABLE IF EXISTS `leverancier`;
CREATE TABLE IF NOT EXISTS `leverancier` (
  `LeverancierID` int NOT NULL AUTO_INCREMENT,
  `GebruikerID` int NOT NULL,
  `Bedrijfsnaam` varchar(100) NOT NULL,
  `Adres` varchar(200) DEFAULT NULL,
  `ContactNaam` varchar(100) DEFAULT NULL,
  `ContactEmail` varchar(100) DEFAULT NULL,
  `ContactTelefoon` varchar(20) DEFAULT NULL,
  `EerstvolgendeLevering` datetime DEFAULT NULL,
  PRIMARY KEY (`LeverancierID`),
  KEY `GebruikerID` (`GebruikerID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `persoon`
--

DROP TABLE IF EXISTS `persoon`;
CREATE TABLE IF NOT EXISTS `persoon` (
  `PersoonID` int NOT NULL AUTO_INCREMENT,
  `Voornaam` varchar(50) NOT NULL,
  `Achternaam` varchar(50) NOT NULL,
  `Geboortedatum` date DEFAULT NULL,
  `Telefoon` varchar(20) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Adres` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`PersoonID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `ProductID` int NOT NULL AUTO_INCREMENT,
  `LeverancierID` int NOT NULL,
  `AllergieID` int DEFAULT NULL,
  `CategorieID` int NOT NULL,
  `ProductNaam` varchar(100) NOT NULL,
  `EAN` char(13) NOT NULL,
  `AantalInVoorraad` int NOT NULL,
  PRIMARY KEY (`ProductID`),
  UNIQUE KEY `ProductNaam` (`ProductNaam`),
  UNIQUE KEY `EAN` (`EAN`),
  KEY `LeverancierID` (`LeverancierID`),
  KEY `AllergieID` (`AllergieID`),
  KEY `CategorieID` (`CategorieID`),
  KEY `IDX_Product_EAN` (`EAN`)
) ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `productallergie`
--

DROP TABLE IF EXISTS `productallergie`;
CREATE TABLE IF NOT EXISTS `productallergie` (
  `ProductID` int NOT NULL,
  `AllergieID` int NOT NULL,
  PRIMARY KEY (`ProductID`,`AllergieID`),
  KEY `AllergieID` (`AllergieID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `productleverancier`
--

DROP TABLE IF EXISTS `productleverancier`;
CREATE TABLE IF NOT EXISTS `productleverancier` (
  `ProductID` int NOT NULL,
  `LeverancierID` int NOT NULL,
  PRIMARY KEY (`ProductID`,`LeverancierID`),
  KEY `LeverancierID` (`LeverancierID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `rol`
--

DROP TABLE IF EXISTS `rol`;
CREATE TABLE IF NOT EXISTS `rol` (
  `RolID` int NOT NULL AUTO_INCREMENT,
  `Naam` varchar(50) NOT NULL,
  PRIMARY KEY (`RolID`),
  UNIQUE KEY `Naam` (`Naam`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Gegevens worden geëxporteerd voor tabel `rol`
--

INSERT INTO `rol` (`RolID`, `Naam`) VALUES
(1, 'Directie'),
(2, 'Magazijnmedewerker'),
(3, 'Vrijwilliger');

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `voedselopslag`
--

DROP TABLE IF EXISTS `voedselopslag`;
CREATE TABLE IF NOT EXISTS `voedselopslag` (
  `ProductID` int NOT NULL,
  `AantalInMagazijn` int NOT NULL,
  `LaatsteAanleverDatum` date DEFAULT NULL,
  PRIMARY KEY (`ProductID`)
) ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `voedselpakket`
--

DROP TABLE IF EXISTS `voedselpakket`;
CREATE TABLE IF NOT EXISTS `voedselpakket` (
  `VoedselpakketID` int NOT NULL AUTO_INCREMENT,
  `KlantID` int NOT NULL,
  `DatumSamenstelling` date NOT NULL,
  `DatumUitgifte` date DEFAULT NULL,
  PRIMARY KEY (`VoedselpakketID`),
  KEY `IDX_Voedselpakket_KlantID` (`KlantID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `voedselpakketproduct`
--

DROP TABLE IF EXISTS `voedselpakketproduct`;
CREATE TABLE IF NOT EXISTS `voedselpakketproduct` (
  `VoedselpakketID` int NOT NULL,
  `ProductID` int NOT NULL,
  `Aantal` int NOT NULL,
  PRIMARY KEY (`VoedselpakketID`,`ProductID`),
  KEY `ProductID` (`ProductID`)
) ;

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `werknemer`
--

DROP TABLE IF EXISTS `werknemer`;
CREATE TABLE IF NOT EXISTS `werknemer` (
  `WerknemerID` int NOT NULL AUTO_INCREMENT,
  `GebruikerID` int NOT NULL,
  PRIMARY KEY (`WerknemerID`),
  KEY `GebruikerID` (`GebruikerID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
