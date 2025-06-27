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

-- ...existing code... (all your table structures)

-- Testdata voor Allergie tabel
INSERT IGNORE INTO `allergie` (`AllergieID`, `Naam`) VALUES
(1, 'Gluten'),
(2, 'Pinda\'s'),
(3, 'Schaaldieren'),
(4, 'Hazelnoten'),
(5, 'Lactose'),
(6, 'Eieren'),
(7, 'Soja');

-- Testdata voor Persoon tabel (eerst, want Gebruiker heeft PersoonID nodig)
INSERT IGNORE INTO `persoon` (`PersoonID`, `Voornaam`, `Achternaam`, `Geboortedatum`, `Telefoon`, `Email`, `Adres`) VALUES
(1, 'Peter', 'Abraham', '1965-03-15', '0412-111111', 'peter.abraham@voedselbank.nl', 'Hoofdstraat 1, 5371AA Maaskantje'),
(2, 'Marie', 'Jansen', '1972-08-22', '0412-222222', 'marie.jansen@voedselbank.nl', 'Kerkstraat 5, 5371BB Maaskantje'),
(3, 'Jan', 'Pieters', '1980-01-10', '0412-345678', 'jan.pieters@ah.nl', 'Winkelstraat 12, 5371CC Maaskantje'),
(4, 'Maria', 'van Dongen', '1985-06-15', '06-12345678', 'maria.vandongen@email.nl', 'Dorpsstraat 12, 5371AB Maaskantje'),
(5, 'Jan', 'de Vries', '1978-11-03', '06-23456789', 'jan.devries@email.nl', 'Schoolstraat 8, 5371CD Maaskantje'),
(6, 'Petra', 'Hendriks', '1990-04-20', '06-34567890', 'petra.hendriks@email.nl', 'Molenweg 15, 5371EF Maaskantje'),
(7, 'Ahmed', 'Hassan', '1982-09-12', '06-45678901', 'ahmed.hassan@email.nl', 'Nieuwstraat 22, 5371GH Maaskantje');

-- Testdata voor Gebruiker tabel
INSERT IGNORE INTO `gebruiker` (`GebruikerID`, `PersoonID`, `Gebruikersnaam`, `WachtwoordHash`, `IsGeblokkeerd`) VALUES
(1, 1, 'admin', UNHEX('5E884898DA28047151D0E56F8DC6292773603D0D6AABBDD62A11EF721D1542D8'), 0),
(2, 2, 'marie_magazijn', UNHEX('5E884898DA28047151D0E56F8DC6292773603D0D6AABBDD62A11EF721D1542D8'), 0),
(3, 3, 'jan_leverancier', UNHEX('5E884898DA28047151D0E56F8DC6292773603D0D6AABBDD62A11EF721D1542D8'), 0),
(4, 4, 'maria_klant', UNHEX('5E884898DA28047151D0E56F8DC6292773603D0D6AABBDD62A11EF721D1542D8'), 0),
(5, 5, 'jan_klant', UNHEX('5E884898DA28047151D0E56F8DC6292773603D0D6AABBDD62A11EF721D1542D8'), 0);

-- Testdata voor GebruikerRol tabel
INSERT IGNORE INTO `gebruikerrol` (`GebruikerID`, `RolID`) VALUES
(1, 1), -- Peter is Directie
(2, 2), -- Marie is Magazijnmedewerker
(3, 3); -- Jan is Vrijwilliger

-- Testdata voor Leverancier tabel
INSERT IGNORE INTO `leverancier` (`LeverancierID`, `GebruikerID`, `Bedrijfsnaam`, `Adres`, `ContactNaam`, `ContactEmail`, `ContactTelefoon`, `EerstvolgendeLevering`) VALUES
(1, 3, 'Albert Heijn Maaskantje', 'Hoofdstraat 12, 5371AB Maaskantje', 'Jan Pieters', 'jan.pieters@ah.nl', '0412-345678', '2025-06-30 09:00:00'),
(2, 1, 'Jumbo Supermarkten', 'Marktplein 5, 5371CD Maaskantje', 'Marie van der Berg', 'marie.vandenberg@jumbo.com', '0412-567890', '2025-07-02 14:30:00'),
(3, 1, 'Boerderij De Hof', 'Hofweg 25, 5371EF Maaskantje', 'Piet Janssen', 'piet@boerderijdehof.nl', '0412-123456', '2025-07-01 08:00:00'),
(4, 1, 'Bakkerij Van Dijk', 'Kerkstraat 8, 5371GH Maaskantje', 'Kees van Dijk', 'info@bakkerijvandijk.nl', '0412-789012', '2025-06-29 07:00:00'),
(5, 1, 'Groothandel Maaskantje', 'Industrieweg 15, 5371IJ Maaskantje', 'Sandra Verhagen', 's.verhagen@groothandel.nl', '0412-345123', '2025-07-03 10:00:00');

-- Testdata voor Product tabel
INSERT IGNORE INTO `product` (`ProductID`, `LeverancierID`, `AllergieID`, `CategorieID`, `ProductNaam`, `EAN`, `AantalInVoorraad`) VALUES
(1, 1, NULL, 1, 'Aardappelen vastkokend 2kg', '8711200012345', 25),
(2, 1, NULL, 1, 'Bananen 1kg', '8711200023456', 30),
(3, 2, 5, 3, 'Melk halfvol 1L', '8710398054321', 40),
(4, 4, 1, 4, 'Wit brood heel', '8711327065432', 15),
(5, 3, NULL, 1, 'Wortelen 1kg', '8711200076543', 20),
(6, 2, NULL, 6, 'Spaghetti 500g', '8712566087654', 35),
(7, 1, 5, 2, 'Jonge kaas plakken', '8711200098765', 18),
(8, 5, NULL, 5, 'Appelsap 1L', '8710398009876', 28),
(9, 4, 1, 8, 'Koekjes naturel', '8711327010987', 22),
(10, 3, NULL, 1, 'Appels Elstar 1kg', '8711200021098', 45),
(11, 2, NULL, 7, 'Tomatensoep blik', '8712566032109', 50),
(12, 1, 6, 3, 'Eieren 12 stuks', '8711200043210', 12),
(13, 5, NULL, 6, 'Rijst 1kg', '8710398054322', 38),
(14, 4, 1, 4, 'Volkoren brood', '8711327065433', 10),
(15, 3, NULL, 1, 'Uien 2kg', '8711200076544', 33);

-- Testdata voor Klant tabel
INSERT IGNORE INTO `klant` (`KlantID`, `GebruikerID`, `AantalVolwassenen`, `AantalKinderen`, `AantalBabys`, `GeenVarkensvlees`, `Veganistisch`, `Vegetarisch`) VALUES
(1, 4, 2, 1, 0, 0, 0, 1), -- Maria van Dongen - vegetarisch
(2, 5, 1, 2, 1, 1, 0, 0), -- Jan de Vries - geen varkensvlees
(3, 1, 2, 0, 0, 0, 0, 0), -- Petra Hendriks - geen beperkingen
(4, 1, 2, 3, 0, 1, 1, 0), -- Ahmed Hassan - geen varkensvlees + veganistisch
(5, 1, 1, 0, 1, 0, 0, 0); -- Linda van der Berg - geen beperkingen

-- Testdata voor KlantAllergie tabel
INSERT IGNORE INTO `klantallergie` (`KlantID`, `AllergieID`) VALUES
(2, 1), -- Jan de Vries heeft glutenallergie
(2, 5), -- Jan de Vries heeft lactose-intolerantie
(4, 2), -- Ahmed Hassan heeft pinda-allergie
(5, 5); -- Linda heeft lactose-intolerantie

-- Testdata voor Voedselopslag tabel
INSERT IGNORE INTO `voedselopslag` (`ProductID`, `AantalInMagazijn`, `LaatsteAanleverDatum`) VALUES
(1, 25, '2025-06-25'),
(2, 30, '2025-06-26'),
(3, 40, '2025-06-24'),
(4, 15, '2025-06-27'),
(5, 20, '2025-06-25'),
(6, 35, '2025-06-23'),
(7, 18, '2025-06-26'),
(8, 28, '2025-06-24'),
(9, 22, '2025-06-27'),
(10, 45, '2025-06-25'),
(11, 50, '2025-06-23'),
(12, 12, '2025-06-26'),
(13, 38, '2025-06-24'),
(14, 10, '2025-06-27'),
(15, 33, '2025-06-25');

-- Testdata voor Voedselpakket tabel
INSERT IGNORE INTO `voedselpakket` (`VoedselpakketID`, `KlantID`, `DatumSamenstelling`, `DatumUitgifte`) VALUES
(1, 1, '2025-06-26', '2025-06-27'),
(2, 2, '2025-06-26', '2025-06-27'),
(3, 3, '2025-06-26', '2025-06-27'),
(4, 4, '2025-06-26', NULL),
(5, 5, '2025-06-26', NULL);

-- Testdata voor VoedselpakketProduct tabel
INSERT IGNORE INTO `voedselpakketproduct` (`VoedselpakketID`, `ProductID`, `Aantal`) VALUES
(1, 1, 1), -- Maria krijgt aardappelen
(1, 3, 2), -- Maria krijgt melk
(1, 4, 1), -- Maria krijgt brood
(2, 1, 1), -- Jan krijgt aardappelen
(2, 6, 1), -- Jan krijgt spaghetti
(2, 8, 1), -- Jan krijgt appelsap
(3, 2, 2), -- Petra krijgt bananen
(3, 7, 1), -- Petra krijgt kaas
(3, 11, 2), -- Petra krijgt soep
(4, 1, 2), -- Ahmed krijgt aardappelen
(4, 5, 1), -- Ahmed krijgt wortelen
(4, 13, 1), -- Ahmed krijgt rijst
(5, 10, 1), -- Linda krijgt appels
(5, 12, 1); -- Linda krijgt eieren

-- Testdata voor Contact tabel
INSERT IGNORE INTO `contact` (`ContactID`, `GebruikerID`, `Telefoon`, `Email`) VALUES
(1, 1, '0412-111111', 'peter.abraham@voedselbank.nl'),
(2, 2, '0412-222222', 'marie.jansen@voedselbank.nl'),
(3, 3, '0412-345678', 'jan.pieters@ah.nl');

-- Testdata voor Werknemer tabel
INSERT IGNORE INTO `werknemer` (`WerknemerID`, `GebruikerID`) VALUES
(1, 1), -- Peter Abraham
(2, 2); -- Marie Jansen

COMMIT;