CREATE DATABASE  IF NOT EXISTS `evidenta_testare` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;
USE `evidenta_testare`;
-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: evidenta_testare
-- ------------------------------------------------------
-- Server version	5.5.5-10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categorie`
--

DROP TABLE IF EXISTS `categorie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorie` (
  `ID_Categorie` int(11) NOT NULL,
  `Denumirea` varchar(25) NOT NULL,
  `Descrierea` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID_Categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorie`
--

LOCK TABLES `categorie` WRITE;
/*!40000 ALTER TABLE `categorie` DISABLE KEYS */;
INSERT INTO `categorie` VALUES (1,'Antibiotic','Medicamente antibacteriene'),(2,'Antiinflamator','Reduce inflamatiile si durerea'),(3,'Analgezic','Reduce durerea'),(4,',Vaccin','Protejeaza impotriva bolilor'),(5,'Antiviral','Combate infectiile virale');
/*!40000 ALTER TABLE `categorie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `consultatie`
--

DROP TABLE IF EXISTS `consultatie`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `consultatie` (
  `ID_Consultatie` int(11) NOT NULL,
  `ID_Doctor` int(11) NOT NULL,
  `ID_Testare` int(11) NOT NULL,
  `DataConsulatie` datetime NOT NULL,
  `Observatii` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ID_Consultatie`),
  KEY `FK_Consultatie_Doctor` (`ID_Doctor`),
  KEY `FK_Consultatie_Testare` (`ID_Testare`),
  CONSTRAINT `FK_Consultatie_Doctor` FOREIGN KEY (`ID_Doctor`) REFERENCES `doctor` (`ID_Doctor`),
  CONSTRAINT `FK_Consultatie_Testare` FOREIGN KEY (`ID_Testare`) REFERENCES `testare_pacient` (`ID_Testare`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `consultatie`
--

LOCK TABLES `consultatie` WRITE;
/*!40000 ALTER TABLE `consultatie` DISABLE KEYS */;
INSERT INTO `consultatie` VALUES (1,1,1,'2022-04-12 00:00:00','Pacient in progres bun'),(2,2,2,'2021-06-18 00:00:00','Studii complete'),(3,3,3,'2023-05-21 00:00:00','Rezultate promitatoare'),(4,4,4,'2021-10-11 00:00:00','Consultatia finala'),(5,5,5,'2022-07-25 00:00:00','Evolutie buna');
/*!40000 ALTER TABLE `consultatie` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctor`
--

DROP TABLE IF EXISTS `doctor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctor` (
  `ID_Doctor` int(11) NOT NULL,
  `NumeDoctor` varchar(30) NOT NULL,
  `PrenumeDoctor` varchar(30) NOT NULL,
  `Specializarea` varchar(25) NOT NULL,
  `Spitalul` varchar(50) NOT NULL,
  `Telefonul` char(10) NOT NULL,
  `Emailul` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_Doctor`),
  UNIQUE KEY `Telefonul` (`Telefonul`),
  UNIQUE KEY `Emailul` (`Emailul`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctor`
--

LOCK TABLES `doctor` WRITE;
/*!40000 ALTER TABLE `doctor` DISABLE KEYS */;
INSERT INTO `doctor` VALUES (1,'Radu','Ion','Cardiologie','Spitalul Judetean','0721123456','ion.radu@spital.ro'),(2,'Stan','Cristina','Infectii','Spitalul Universitar','0722456789','cristina.stan@spital.ro'),(3,'Matei','Andrei','Neurologie','Clinica Sanatatea','0731567890','andrei.matei@clinica.ro'),(4,'Dobre','Simona','Oncologie','Institutul Oncologic','0711456789','simona.dobre@institut.ro'),(5,'Iliescu','Dan','Pediatrie','Spitalul pentru Copii','0723789012','dan.iliescu@spital.ro');
/*!40000 ALTER TABLE `doctor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `medicamente`
--

DROP TABLE IF EXISTS `medicamente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `medicamente` (
  `ID_Medicament` int(11) NOT NULL,
  `Denumirea` varchar(25) NOT NULL,
  `Producatorul` varchar(30) NOT NULL,
  `Descrierea` varchar(50) DEFAULT NULL,
  `DataAprobarii` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `ID_Categorie` int(11) NOT NULL,
  PRIMARY KEY (`ID_Medicament`),
  KEY `FK_Medicamente` (`ID_Categorie`),
  CONSTRAINT `FK_Medicamente` FOREIGN KEY (`ID_Categorie`) REFERENCES `categorie` (`ID_Categorie`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `medicamente`
--

LOCK TABLES `medicamente` WRITE;
/*!40000 ALTER TABLE `medicamente` DISABLE KEYS */;
INSERT INTO `medicamente` VALUES (1,'Amoxicilina','Pfizer','Antibiotic cu spectru larg','2020-05-11 21:00:00',1),(2,'Ibuprofen','Teva','Antiinflamator si analgezic','2019-08-09 21:00:00',2),(3,'Paracetamol','GSK','Analgezic comun','2018-11-14 22:00:00',3),(4,'Vaccin COVID','Moderna','Vaccin impotriva COVID-19','2021-01-19 22:00:00',4),(5,'Oseltamivir','Roche','Antiviral pentru gripa','2017-02-26 22:00:00',5);
/*!40000 ALTER TABLE `medicamente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pacient_medicament`
--

DROP TABLE IF EXISTS `pacient_medicament`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pacient_medicament` (
  `ID_Pacient` int(11) NOT NULL,
  `ID_Medicament` int(11) NOT NULL,
  `DataStart` datetime NOT NULL,
  `DataFinalizare` datetime NOT NULL,
  PRIMARY KEY (`ID_Pacient`,`ID_Medicament`),
  KEY `FK_Pacient_Medicament_Medicament` (`ID_Medicament`),
  CONSTRAINT `FK_Pacient_Medicament_Medicament` FOREIGN KEY (`ID_Medicament`) REFERENCES `medicamente` (`ID_Medicament`),
  CONSTRAINT `FK_Pacient_Medicament_Pacient` FOREIGN KEY (`ID_Pacient`) REFERENCES `pacienti` (`ID_Pacient`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pacient_medicament`
--

LOCK TABLES `pacient_medicament` WRITE;
/*!40000 ALTER TABLE `pacient_medicament` DISABLE KEYS */;
INSERT INTO `pacient_medicament` VALUES (1,1,'2022-02-16 00:00:00','2022-05-16 00:00:00'),(2,2,'2021-05-02 00:00:00','2021-08-02 00:00:00'),(3,3,'2023-03-16 00:00:00','2023-06-16 00:00:00'),(4,4,'2021-03-06 00:00:00','2021-06-06 00:00:00'),(5,5,'2022-06-21 00:00:00','2022-09-21 00:00:00');
/*!40000 ALTER TABLE `pacient_medicament` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pacienti`
--

DROP TABLE IF EXISTS `pacienti`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pacienti` (
  `ID_Pacient` int(11) NOT NULL,
  `Numele` varchar(50) NOT NULL,
  `Prenumele` varchar(50) NOT NULL,
  `DataNasterii` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Sex` char(1) NOT NULL,
  `Adresa` varchar(50) DEFAULT NULL,
  `Telefonul` char(10) NOT NULL,
  `Emailul` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_Pacient`),
  UNIQUE KEY `Telefonul` (`Telefonul`),
  UNIQUE KEY `Emailul` (`Emailul`),
  CONSTRAINT `CONSTRAINT_1` CHECK (`Sex` = 'F' or `Sex` = 'M')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pacienti`
--

LOCK TABLES `pacienti` WRITE;
/*!40000 ALTER TABLE `pacienti` DISABLE KEYS */;
INSERT INTO `pacienti` VALUES (1,'Popescu','Maria','1985-04-11 21:00:00','F','Strada Libertatii 45','0723456789','maria.popescu@gmail.com'),(2,'Ionescu','Mihai','1975-08-22 21:00:00','M','Strada Primaverii 10','0712345678','mihai.ionescu@yahoo.com'),(3,'Georgescu','Ana','1990-02-16 22:00:00','F','Strada Trandafirilor 21','0729876543','ana.georgescu@outlook.com'),(4,'Dumitru','Florin','1988-06-29 21:00:00','M','Strada Pacea 8','0734567890','florin.dumitru@hotmail.com'),(5,'Marinescu','Elena','1992-12-04 22:00:00','F','Strada Sperantei 15','0745123456','elena.marinescu@gmail.com');
/*!40000 ALTER TABLE `pacienti` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `studiu_clinic`
--

DROP TABLE IF EXISTS `studiu_clinic`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studiu_clinic` (
  `ID_Studiu` int(11) NOT NULL,
  `DataInceput` datetime NOT NULL,
  `DataSfarsit` datetime NOT NULL,
  `Scopul` varchar(50) NOT NULL,
  `FazaStudiului` int(11) NOT NULL CHECK (`FazaStudiului` between 1 and 4),
  `ID_Medicament` int(11) NOT NULL,
  PRIMARY KEY (`ID_Studiu`),
  KEY `FK_Studiu_Clinic` (`ID_Medicament`),
  CONSTRAINT `FK_Studiu_Clinic` FOREIGN KEY (`ID_Medicament`) REFERENCES `medicamente` (`ID_Medicament`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `studiu_clinic`
--

LOCK TABLES `studiu_clinic` WRITE;
/*!40000 ALTER TABLE `studiu_clinic` DISABLE KEYS */;
INSERT INTO `studiu_clinic` VALUES (1,'2022-01-15 00:00:00','2022-12-15 00:00:00','Evaluarea eficientei Amoxicilinei',2,1),(2,'2021-06-01 00:00:00','2023-06-01 00:00:00','Studiu Ibuprofen pentru dureri cronice',3,2),(3,'2020-10-20 00:00:00','2022-10-20 00:00:00','Paracetamol ca analgezic pentru copii',1,3),(4,'2021-03-15 00:00:00','2023-03-15 00:00:00','Vaccin COVID faza 3',3,4),(5,'2019-02-01 00:00:00','2022-02-01 00:00:00','Studiu antiviral pentru gripa',4,5);
/*!40000 ALTER TABLE `studiu_clinic` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `studiu_doctor`
--

DROP TABLE IF EXISTS `studiu_doctor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `studiu_doctor` (
  `ID_Studiu` int(11) NOT NULL,
  `ID_Doctor` int(11) NOT NULL,
  `RolDoctor` varchar(100) NOT NULL,
  PRIMARY KEY (`ID_Studiu`,`ID_Doctor`),
  KEY `FK_Studiu_Doctor_Doctor` (`ID_Doctor`),
  CONSTRAINT `FK_Studiu_Doctor_Doctor` FOREIGN KEY (`ID_Doctor`) REFERENCES `doctor` (`ID_Doctor`),
  CONSTRAINT `FK_Studiu_Doctor_Studiu` FOREIGN KEY (`ID_Studiu`) REFERENCES `studiu_clinic` (`ID_Studiu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `studiu_doctor`
--

LOCK TABLES `studiu_doctor` WRITE;
/*!40000 ALTER TABLE `studiu_doctor` DISABLE KEYS */;
INSERT INTO `studiu_doctor` VALUES (1,1,'Cercetator Principal'),(2,2,'Cercetator Secundar'),(3,3,'Coordonator'),(4,4,'Consultant'),(5,5,'Asistent');
/*!40000 ALTER TABLE `studiu_doctor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `testare_pacient`
--

DROP TABLE IF EXISTS `testare_pacient`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `testare_pacient` (
  `ID_Testare` int(11) NOT NULL,
  `ID_Pacient` int(11) NOT NULL,
  `ID_Studiu` int(11) NOT NULL,
  `DataInrolarii` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Statusul` varchar(50) NOT NULL,
  PRIMARY KEY (`ID_Testare`),
  KEY `FK_Testare_Pacient_Pacient` (`ID_Pacient`),
  KEY `FK_Testare_Pacient_Studiu` (`ID_Studiu`),
  CONSTRAINT `FK_Testare_Pacient_Pacient` FOREIGN KEY (`ID_Pacient`) REFERENCES `pacienti` (`ID_Pacient`),
  CONSTRAINT `FK_Testare_Pacient_Studiu` FOREIGN KEY (`ID_Studiu`) REFERENCES `studiu_clinic` (`ID_Studiu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `testare_pacient`
--

LOCK TABLES `testare_pacient` WRITE;
/*!40000 ALTER TABLE `testare_pacient` DISABLE KEYS */;
INSERT INTO `testare_pacient` VALUES (1,1,1,'2022-02-14 22:00:00','Inrolat activ'),(2,2,2,'2021-04-30 21:00:00','Finalizat'),(3,3,3,'2023-03-14 22:00:00','Inrolat activ'),(4,4,4,'2021-03-04 22:00:00','Finalizat'),(5,5,5,'2022-06-19 21:00:00','Inrolat activ');
/*!40000 ALTER TABLE `testare_pacient` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-11-14 19:07:21
