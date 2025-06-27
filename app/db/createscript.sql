-- DATABASE EN TABELSTRUCTUUR (MYSQL-VERSIE)

-- Database aanmaken
CREATE DATABASE IF NOT EXISTS VoedselbankDB;
USE VoedselbankDB;

-- Categorie
CREATE TABLE Categorie (
    CategorieID INT AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci UNIQUE NOT NULL
);

INSERT INTO Categorie (Naam) VALUES
('Aardappelen, groente, fruit'),
('Kaas, vleeswaren'),
('Zuivel, plantaardig en eieren'),
('Bakkerij en banket'),
('Frisdrank, sappen, koffie en thee'),
('Pasta, rijst en wereldkeuken'),
('Soepen, sauzen, kruiden en olie'),
('Snoep, koek, chips en chocolade'),
('Baby, verzorging en hygiÃ«ne');

-- Persoon
CREATE TABLE Persoon (
    PersoonID INT AUTO_INCREMENT PRIMARY KEY,
    Voornaam VARCHAR(50) NOT NULL,
    Achternaam VARCHAR(50) NOT NULL,
    Geboortedatum DATE,
    Telefoon VARCHAR(20),
    Email VARCHAR(100),
    Adres VARCHAR(200)
);

-- Gebruiker
CREATE TABLE Gebruiker (
    GebruikerID INT AUTO_INCREMENT PRIMARY KEY,
    PersoonID INT NOT NULL,
    Gebruikersnaam VARCHAR(50) UNIQUE NOT NULL,
    WachtwoordHash VARBINARY(64) NOT NULL,
    IsGeblokkeerd BOOLEAN DEFAULT 0,
    FOREIGN KEY (PersoonID) REFERENCES Persoon(PersoonID)
);

-- Rol
CREATE TABLE Rol (
    RolID INT AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(50) UNIQUE NOT NULL
);

-- GebruikerRol (many-to-many)
CREATE TABLE GebruikerRol (
    GebruikerID INT NOT NULL,
    RolID INT NOT NULL,
    PRIMARY KEY (GebruikerID, RolID),
    FOREIGN KEY (GebruikerID) REFERENCES Gebruiker(GebruikerID),
    FOREIGN KEY (RolID) REFERENCES Rol(RolID)
);

-- Leverancier
CREATE TABLE Leverancier (
    LeverancierID INT AUTO_INCREMENT PRIMARY KEY,
    GebruikerID INT NOT NULL,
    Bedrijfsnaam VARCHAR(100) NOT NULL,
    Adres VARCHAR(200),
    ContactNaam VARCHAR(100),
    ContactEmail VARCHAR(100),
    ContactTelefoon VARCHAR(20),
    EerstvolgendeLevering DATETIME,
    FOREIGN KEY (GebruikerID) REFERENCES Gebruiker(GebruikerID)
);

-- Allergie
CREATE TABLE Allergie (
    AllergieID INT AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(50) UNIQUE NOT NULL
);

-- Product
CREATE TABLE Product (
    ProductID INT AUTO_INCREMENT PRIMARY KEY,
    LeverancierID INT NOT NULL,
    AllergieID INT,
    CategorieID INT NOT NULL,
    ProductNaam VARCHAR(100) UNIQUE NOT NULL,
    EAN CHAR(13) UNIQUE NOT NULL,
    AantalInVoorraad INT NOT NULL CHECK (AantalInVoorraad >= 0),
    FOREIGN KEY (LeverancierID) REFERENCES Leverancier(LeverancierID),
    FOREIGN KEY (AllergieID) REFERENCES Allergie(AllergieID),
    FOREIGN KEY (CategorieID) REFERENCES Categorie(CategorieID)
);

-- ProductLeverancier (optioneel)
CREATE TABLE ProductLeverancier (
    ProductID INT NOT NULL,
    LeverancierID INT NOT NULL,
    PRIMARY KEY (ProductID, LeverancierID),
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID),
    FOREIGN KEY (LeverancierID) REFERENCES Leverancier(LeverancierID)
);

-- Werknemer
CREATE TABLE Werknemer (
    WerknemerID INT AUTO_INCREMENT PRIMARY KEY,
    GebruikerID INT NOT NULL,
    FOREIGN KEY (GebruikerID) REFERENCES Gebruiker(GebruikerID)
);

-- Contact
CREATE TABLE Contact (
    ContactID INT AUTO_INCREMENT PRIMARY KEY,
    GebruikerID INT NOT NULL,
    Telefoon VARCHAR(20),
    Email VARCHAR(100),
    FOREIGN KEY (GebruikerID) REFERENCES Gebruiker(GebruikerID)
);

-- Klant
CREATE TABLE Klant (
    KlantID INT AUTO_INCREMENT PRIMARY KEY,
    GebruikerID INT NOT NULL,
    AantalVolwassenen INT NOT NULL CHECK (AantalVolwassenen >= 0),
    AantalKinderen INT NOT NULL CHECK (AantalKinderen >= 0),
    AantalBabys INT NOT NULL CHECK (AantalBabys >= 0),
    GeenVarkensvlees BOOLEAN DEFAULT 0,
    Veganistisch BOOLEAN DEFAULT 0,
    Vegetarisch BOOLEAN DEFAULT 0,
    FOREIGN KEY (GebruikerID) REFERENCES Gebruiker(GebruikerID)
);

-- Familie
CREATE TABLE Familie (
    KlantID INT NOT NULL,
    FamilielidID INT NOT NULL,
    PRIMARY KEY (KlantID, FamilielidID),
    FOREIGN KEY (KlantID) REFERENCES Klant(KlantID),
    FOREIGN KEY (FamilielidID) REFERENCES Klant(KlantID)
);

-- ProductAllergie
CREATE TABLE ProductAllergie (
    ProductID INT NOT NULL,
    AllergieID INT NOT NULL,
    PRIMARY KEY (ProductID, AllergieID),
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID),
    FOREIGN KEY (AllergieID) REFERENCES Allergie(AllergieID)
);

-- Voedselpakket
CREATE TABLE Voedselpakket (
    VoedselpakketID INT AUTO_INCREMENT PRIMARY KEY,
    KlantID INT NOT NULL,
    DatumSamenstelling DATE NOT NULL,
    DatumUitgifte DATE,
    FOREIGN KEY (KlantID) REFERENCES Klant(KlantID)
);

-- VoedselpakketProduct
CREATE TABLE VoedselpakketProduct (
    VoedselpakketID INT NOT NULL,
    ProductID INT NOT NULL,
    Aantal INT NOT NULL CHECK (Aantal > 0),
    PRIMARY KEY (VoedselpakketID, ProductID),
    FOREIGN KEY (VoedselpakketID) REFERENCES Voedselpakket(VoedselpakketID),
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
);

-- Voedselopslag
CREATE TABLE Voedselopslag (
    ProductID INT PRIMARY KEY,
    AantalInMagazijn INT NOT NULL CHECK (AantalInMagazijn >= 0),
    LaatsteAanleverDatum DATE,
    FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
);

-- KlantAllergie
CREATE TABLE KlantAllergie (
    KlantID INT NOT NULL,
    AllergieID INT NOT NULL,
    PRIMARY KEY (KlantID, AllergieID),
    FOREIGN KEY (KlantID) REFERENCES Klant(KlantID),
    FOREIGN KEY (AllergieID) REFERENCES Allergie(AllergieID)
);

-- Rollen vullen
INSERT INTO Rol (Naam) VALUES ('Directie'), ('Magazijnmedewerker'), ('Vrijwilliger');

-- Indexen
CREATE INDEX IDX_Product_EAN ON Product(EAN);
CREATE INDEX IDX_Voedselpakket_KlantID ON Voedselpakket(KlantID);
CREATE INDEX IDX_Klant_GebruikerID ON Klant(GebruikerID);

-- Stored procedure: VoegProductToe
DELIMITER //
CREATE PROCEDURE VoegProductToe (
    IN p_LeverancierID INT,
    IN p_AllergieID INT,
    IN p_CategorieID INT,
    IN p_ProductNaam VARCHAR(100),
    IN p_EAN CHAR(13),
    IN p_AantalInVoorraad INT
)
BEGIN
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Fout bij toevoegen product';
    END;

    INSERT INTO Product (LeverancierID, AllergieID, CategorieID, ProductNaam, EAN, AantalInVoorraad)
    VALUES (p_LeverancierID, p_AllergieID, p_CategorieID, p_ProductNaam, p_EAN, p_AantalInVoorraad);
END;//
DELIMITER ;

-- Stored procedure: UpdateVoorraadNaPakket
DELIMITER //
CREATE PROCEDURE UpdateVoorraadNaPakket (
    IN p_ProductID INT,
    IN p_AantalGebruikt INT
)
BEGIN
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
END;//
DELIMITER ;

-- Stored procedure: GetProductVoorraadOverzicht
DELIMITER //
CREATE PROCEDURE GetProductVoorraadOverzicht()
BEGIN
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
END;//
DELIMITER ;