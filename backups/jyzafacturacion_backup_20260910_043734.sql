-- MySQL dump 10.13  Distrib 8.0.44, for Linux (x86_64)
--
-- Host: localhost    Database: jyzafacturacion
-- ------------------------------------------------------
-- Server version	8.0.44

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `caja_denominaciones`
--

DROP TABLE IF EXISTS `caja_denominaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caja_denominaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caja_id` int NOT NULL,
  `tipo_movimiento` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL DEFAULT '0.00',
  `cantidad` int NOT NULL DEFAULT '0',
  `subtotal` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja_denominaciones`
--

LOCK TABLES `caja_denominaciones` WRITE;
/*!40000 ALTER TABLE `caja_denominaciones` DISABLE KEYS */;
INSERT INTO `caja_denominaciones` VALUES (1,1,'APERTURA','BILLETE',200.00,1,200.00,NULL,NULL);
/*!40000 ALTER TABLE `caja_denominaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caja_egresos`
--

DROP TABLE IF EXISTS `caja_egresos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caja_egresos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caja_id` int NOT NULL,
  `monto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `metodo_pago` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo` enum('GASTO','REEMBOLSO_ANULACION') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'GASTO',
  `descripcion` text COLLATE utf8mb4_general_ci,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja_egresos`
--

LOCK TABLES `caja_egresos` WRITE;
/*!40000 ALTER TABLE `caja_egresos` DISABLE KEYS */;
/*!40000 ALTER TABLE `caja_egresos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caja_ingresos`
--

DROP TABLE IF EXISTS `caja_ingresos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caja_ingresos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caja_id` int NOT NULL,
  `monto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descripcion` text COLLATE utf8mb4_general_ci,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja_ingresos`
--

LOCK TABLES `caja_ingresos` WRITE;
/*!40000 ALTER TABLE `caja_ingresos` DISABLE KEYS */;
/*!40000 ALTER TABLE `caja_ingresos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `caja_movimientos`
--

DROP TABLE IF EXISTS `caja_movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `caja_movimientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `caja_id` int NOT NULL,
  `invoice_id` int NOT NULL,
  `metodo_pago` enum('EFECTIVO','YAPE','TARJETA','TRANSFERENCIA','PLIN','OTROS') COLLATE utf8mb4_general_ci NOT NULL,
  `monto_total` decimal(10,2) NOT NULL,
  `monto_recibido` decimal(10,2) NOT NULL,
  `vuelto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_caja_movimientos_caja` (`caja_id`),
  KEY `fk_caja_movimientos_invoice` (`invoice_id`),
  CONSTRAINT `fk_caja_movimientos_caja` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`),
  CONSTRAINT `fk_caja_movimientos_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `caja_movimientos`
--

LOCK TABLES `caja_movimientos` WRITE;
/*!40000 ALTER TABLE `caja_movimientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `caja_movimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cajas`
--

DROP TABLE IF EXISTS `cajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cajas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `codigo` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int NOT NULL,
  `fecha` date NOT NULL,
  `monto_inicial` decimal(10,2) NOT NULL DEFAULT '0.00',
  `monto_cierre` decimal(10,2) DEFAULT NULL,
  `estado` enum('ABIERTA','CERRADA') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ABIERTA',
  `observacion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cajas`
--

LOCK TABLES `cajas` WRITE;
/*!40000 ALTER TABLE `cajas` DISABLE KEYS */;
INSERT INTO `cajas` VALUES (1,'Caja 1','CAJ001',3,'2026-09-07',200.00,NULL,'ABIERTA','','2026-09-07 23:43:32','2026-09-07 23:43:32');
/*!40000 ALTER TABLE `cajas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias_examenes`
--

DROP TABLE IF EXISTS `categorias_examenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias_examenes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_examenes`
--

LOCK TABLES `categorias_examenes` WRITE;
/*!40000 ALTER TABLE `categorias_examenes` DISABLE KEYS */;
INSERT INTO `categorias_examenes` VALUES (1,'Hematología',1,'2026-09-07 23:49:13','2026-09-07 23:49:13'),(2,'Cultivos',1,'2026-09-08 00:25:36','2026-09-08 00:25:36'),(3,'COMPLETO',1,'2026-09-08 21:28:54','2026-09-08 21:28:54'),(4,'ORINA',1,'2026-09-08 21:31:33','2026-09-08 21:31:33'),(5,'HECES',1,'2026-09-09 18:10:50','2026-09-09 18:10:50'),(6,'BIOPSIA',1,'2026-09-09 18:39:58','2026-09-09 18:39:58'),(7,'PERFILES',1,'2026-09-09 18:41:21','2026-09-09 18:41:21');
/*!40000 ALTER TABLE `categorias_examenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias_productos`
--

DROP TABLE IF EXISTS `categorias_productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias_productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(120) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias_productos`
--

LOCK TABLES `categorias_productos` WRITE;
/*!40000 ALTER TABLE `categorias_productos` DISABLE KEYS */;
INSERT INTO `categorias_productos` VALUES (1,'Servicios ginecológicos','',0,'2026-09-07 23:49:51','2026-09-09 10:32:43'),(2,'Materiales de limpieza','',1,'2026-09-07 23:50:02','2026-09-07 23:50:02'),(3,'Papelería','',1,'2026-09-07 23:50:11','2026-09-07 23:50:11'),(4,'servicios obstétricos','',0,'2026-09-07 23:50:34','2026-09-09 10:32:37'),(5,'Materiales médicos','',1,'2026-09-07 23:50:44','2026-09-07 23:50:44'),(6,'Otras especialidades','',0,'2026-09-07 23:51:26','2026-09-09 10:32:29'),(7,'Medicamentos','',1,'2026-09-07 23:55:54','2026-09-07 23:56:09'),(8,'Adelantos','',0,'2026-09-08 00:35:30','2026-09-09 10:32:19'),(9,'Materiales de escritorio','',1,'2026-09-08 00:43:36','2026-09-08 00:43:36'),(10,'Paquetes ','',0,'2026-09-08 00:49:15','2026-09-09 10:32:11'),(11,'Materiales de psicoprofilaxis','',1,'2026-09-08 00:53:22','2026-09-08 00:53:22'),(12,'Descarte de ITS','',1,'2026-09-08 00:57:47','2026-09-08 00:57:47'),(13,'Ecografias ','',1,'2026-09-08 00:58:58','2026-09-08 00:58:58');
/*!40000 ALTER TABLE `categorias_productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `companies`
--

DROP TABLE IF EXISTS `companies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `companies` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ruc` varchar(11) COLLATE utf8mb4_general_ci NOT NULL,
  `razon_social` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_comercial` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ubigeo` varchar(6) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '150101',
  `departamento` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'LIMA',
  `provincia` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'LIMA',
  `distrito` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'LIMA',
  `urbanizacion` varchar(100) COLLATE utf8mb4_general_ci DEFAULT '-',
  `cod_local` varchar(4) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0000',
  `sol_user` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `sol_pass` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `certificado_sunat` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ambiente` enum('beta','produccion') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'beta',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_companies_ruc` (`ruc`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `companies`
--

LOCK TABLES `companies` WRITE;
/*!40000 ALTER TABLE `companies` DISABLE KEYS */;
INSERT INTO `companies` VALUES (1,'20609186039','CAYCHO CABRERA & ASOCIADOS S.A.C.','CONSULTORIO GINECOLÃ“GICO JYZA','JR. 2 DE MAYO NRO. 1600','100101','HUANUCO','HUANUCO','HUANUCO',NULL,'0000','USERSECU','QPdan98user','certificados/certificadojyza.pem','produccion','2026-09-08 03:52:36','2026-09-08 03:52:36');
/*!40000 ALTER TABLE `companies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `daily_summaries`
--

DROP TABLE IF EXISTS `daily_summaries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `daily_summaries` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `fecha` date NOT NULL,
  `correlativo` int NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `ticket` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` varchar(30) COLLATE utf8mb4_general_ci DEFAULT 'GENERADO',
  `codigo_sunat` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion_sunat` text COLLATE utf8mb4_general_ci,
  `xml_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cdr_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_daily_summaries_company` (`company_id`),
  CONSTRAINT `fk_daily_summaries_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `daily_summaries`
--

LOCK TABLES `daily_summaries` WRITE;
/*!40000 ALTER TABLE `daily_summaries` DISABLE KEYS */;
/*!40000 ALTER TABLE `daily_summaries` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctor_map`
--

DROP TABLE IF EXISTS `doctor_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctor_map` (
  `old_id` int DEFAULT NULL,
  `new_id` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctor_map`
--

LOCK TABLES `doctor_map` WRITE;
/*!40000 ALTER TABLE `doctor_map` DISABLE KEYS */;
/*!40000 ALTER TABLE `doctor_map` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctor_tratamiento_tarifas`
--

DROP TABLE IF EXISTS `doctor_tratamiento_tarifas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctor_tratamiento_tarifas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `doctor_id` int NOT NULL,
  `tratamiento_id` int NOT NULL,
  `monto_fijo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_dtt_doctor_tratamiento` (`doctor_id`,`tratamiento_id`),
  KEY `fk_dtt_tratamiento` (`tratamiento_id`),
  CONSTRAINT `fk_dtt_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctores` (`id`),
  CONSTRAINT `fk_dtt_tratamiento` FOREIGN KEY (`tratamiento_id`) REFERENCES `tratamientos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctor_tratamiento_tarifas`
--

LOCK TABLES `doctor_tratamiento_tarifas` WRITE;
/*!40000 ALTER TABLE `doctor_tratamiento_tarifas` DISABLE KEYS */;
/*!40000 ALTER TABLE `doctor_tratamiento_tarifas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `doctores`
--

DROP TABLE IF EXISTS `doctores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `doctores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `especialidad` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `porcentaje_pago` decimal(5,2) NOT NULL DEFAULT '0.00',
  `modo_pago` enum('PORCENTAJE','FIJO') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'PORCENTAJE',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `doctores`
--

LOCK TABLES `doctores` WRITE;
/*!40000 ALTER TABLE `doctores` DISABLE KEYS */;
/*!40000 ALTER TABLE `doctores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `examenes`
--

DROP TABLE IF EXISTS `examenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `examenes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categoria_examen_id` int NOT NULL,
  `nombre` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `muestra` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `precio_convenio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `laboratorio_id` int DEFAULT NULL,
  `comision_medico` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gasto_materiales` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `desactivado_por_categoria` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_examenes_categoria` (`categoria_examen_id`),
  KEY `laboratorio_id` (`laboratorio_id`),
  CONSTRAINT `examenes_ibfk_laboratorio` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`),
  CONSTRAINT `fk_examenes_categoria` FOREIGN KEY (`categoria_examen_id`) REFERENCES `categorias_examenes` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=345 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `examenes`
--

LOCK TABLES `examenes` WRITE;
/*!40000 ALTER TABLE `examenes` DISABLE KEYS */;
INSERT INTO `examenes` VALUES (1,1,'ANTICOAGULANTE LIPIDICO','',0.00,NULL,3.50,1.00,70.00,1,0,'2026-09-08 00:17:35','2026-09-08 00:17:35'),(2,1,'ANTICUERPOS ANTI B2 GLICOPROTEINAS IGG','',0.00,1,3.50,1.00,70.00,1,0,'2026-09-08 00:19:04','2026-09-08 00:19:04'),(3,1,'ANTICUERPOS ANTI B2 GLICOPROTEINAS IGM','',0.00,1,3.50,1.00,70.00,1,0,'2026-09-08 00:19:40','2026-09-08 00:19:40'),(4,1,'ANTICARDIOLOPINA IGM','',0.00,1,3.50,1.00,70.00,1,0,'2026-09-08 00:20:00','2026-09-08 00:20:00'),(5,1,'ANTICARDIOLOPINA GYG','',0.00,1,3.50,1.00,70.00,1,0,'2026-09-08 00:20:23','2026-09-08 00:20:23'),(6,2,'CULTIVO DE TRICOMONAS','',0.00,NULL,0.00,0.00,140.00,1,0,'2026-09-08 00:26:17','2026-09-08 00:26:17'),(7,1,'PRUEBA RAPIDA DE HEPATITIS - B X 30 UNID','',2.50,1,1.00,1.00,20.00,1,0,'2026-09-08 01:03:06','2026-09-08 01:03:06'),(8,1,'PRUEBA RAPIDA DE HIV/SIFILIS X 25 UND','',4.80,1,1.25,1.00,25.00,1,0,'2026-09-08 01:03:44','2026-09-08 01:03:44'),(9,1,'ANTICOAGULANTE LIPIDICO','sangre',0.00,NULL,0.00,0.00,70.00,1,0,'2026-09-08 21:15:36','2026-09-08 21:15:36'),(10,1,'ANTICUERPOS ANTI B2 GLICOPROTEINAS IGG','sangre',0.00,NULL,0.00,0.00,0.00,1,0,'2026-09-08 21:17:44','2026-09-08 21:17:44'),(11,1,'ANTICUERPOS ANTI B2 GLICOPROTEINAS IGM','sangre',0.00,NULL,0.00,0.00,0.00,1,0,'2026-09-08 21:17:59','2026-09-08 21:17:59'),(12,1,'ANTICARDIOLOPINA IGM','sangre',0.00,NULL,0.00,0.00,70.00,1,0,'2026-09-08 21:18:20','2026-09-08 21:18:20'),(13,1,'ANTICARDIOLOPINA GYG','sangre',0.00,NULL,0.00,0.00,70.00,1,0,'2026-09-08 21:18:37','2026-09-08 21:18:37'),(14,2,'CULTIVO DE TRICOMONAS','sangre',0.00,NULL,0.00,0.00,140.00,1,0,'2026-09-08 21:19:22','2026-09-08 21:19:22'),(15,1,'ADN FRAGMATICO','sangre',0.00,NULL,0.00,0.00,600.00,1,0,'2026-09-08 21:20:09','2026-09-08 21:20:09'),(16,1,'Triodotironina libre (T3)','tubo tapa roja',22.00,1,0.00,0.00,60.00,1,0,'2026-09-08 21:20:36','2026-09-09 16:48:38'),(17,1,'TGO','sangre',0.00,NULL,0.00,0.00,35.00,1,0,'2026-09-08 21:20:56','2026-09-08 21:20:56'),(18,1,'Creatina sérica','Tubo tapa roja ',60.00,NULL,0.00,0.00,120.00,1,0,'2026-09-08 21:21:23','2026-09-09 14:54:01'),(19,1,'ACIDO URICO','sangre',0.00,NULL,0.00,0.00,35.00,1,0,'2026-09-08 21:21:54','2026-09-08 21:22:34'),(20,1,'TES DE HELECHO','sangre',0.00,NULL,0.00,0.00,100.00,1,0,'2026-09-08 21:22:13','2026-09-08 21:22:13'),(21,2,'CULTIVO  DE SECRECION  PROSTATICA','SECRECION ',0.00,NULL,0.00,0.00,150.00,1,0,'2026-09-08 21:23:20','2026-09-08 21:23:20'),(22,2,'CULTIVO DE LACTOBACILUS','SECRECION ',100.00,1,0.00,0.00,200.00,1,0,'2026-09-08 21:23:43','2026-09-09 18:17:45'),(23,1,'HEPATITIS \"B\"HBSAG ANTIGENO AUSTRALIANO','tubo tapa roja',15.00,1,0.00,0.00,45.00,1,0,'2026-09-08 21:24:13','2026-09-09 17:26:42'),(24,2,'CULTIVO DE CANDIASIS','SECRECION ',35.00,1,0.00,0.00,120.00,1,0,'2026-09-08 21:24:41','2026-09-09 18:17:37'),(25,1,'ACIDO BILIAR','tubo tapa roja',60.00,NULL,0.00,0.00,120.00,1,0,'2026-09-08 21:26:11','2026-09-09 09:12:17'),(26,1,'PROLACTINA','tubo tapa roja',35.00,1,0.00,0.00,70.00,1,0,'2026-09-08 21:26:31','2026-09-09 16:43:36'),(27,1,'PERFIL FEMENINO ( FSH, LH, PROLAC, PROGEST, ESTRADIOL)','sangre',0.00,NULL,0.00,0.00,280.00,1,0,'2026-09-08 21:27:32','2026-09-08 21:27:32'),(28,1,'PRE OPERATORIO( G,U,C,HG,GS,TC Y TS , O/C, RPR, VIH, HVB)','sangre',0.00,NULL,0.00,0.00,150.00,1,0,'2026-09-08 21:27:47','2026-09-08 21:27:47'),(29,3,'PRE OBSTETRICO( HG,G,U,CREA,O/C,RPR,HIV)','SANGRE',0.00,NULL,0.00,0.00,120.00,1,0,'2026-09-08 21:28:04','2026-09-08 21:29:14'),(30,3,'CHEQUEO GENERAL ( HG, O/C, HECES SIM, TGO, TGP, U , CREA, A. URICO, RPR)','',0.00,NULL,0.00,0.00,110.00,1,0,'2026-09-08 21:29:34','2026-09-08 21:29:34'),(31,1,'PERFIL COAGULACION','SANGRE',0.00,NULL,0.00,0.00,150.00,1,0,'2026-09-08 21:29:52','2026-09-08 21:29:52'),(32,1,'PERFIL TIROIDEO ( TSH, T3L, T4L)','SANGRE',0.00,NULL,0.00,0.00,150.00,1,0,'2026-09-08 21:30:43','2026-09-08 21:30:43'),(33,1,'PERFIL REUMATOIDEO ( A. URICO, URICO, CREA, UREA, O/C)','SANGRE',0.00,NULL,0.00,0.00,65.00,1,0,'2026-09-08 21:31:03','2026-09-08 21:31:03'),(34,1,'PERFIL HEPATICO ','sangre',0.00,NULL,0.00,0.00,110.00,1,0,'2026-09-08 21:32:24','2026-09-08 21:32:24'),(35,1,'PERFIL LIPIDICO ( COLESTEROL TOTAL, HDL,LDL,VLDL,TRIGLICERIDOS,LIPIDOS TOTALES','SANGRE',0.00,NULL,0.00,0.00,70.00,1,0,'2026-09-08 21:32:39','2026-09-08 21:32:39'),(36,4,'UROCULTIVO CON REMOVEDOR ANTIBIOTICO','Frasco con recolección de orina',35.00,NULL,0.00,0.00,150.00,1,0,'2026-09-08 21:33:03','2026-09-09 18:25:57'),(37,4,'Urocultivo +ATB','Frasco con recolección de orina',18.00,1,0.00,0.00,60.00,1,0,'2026-09-08 21:33:21','2026-09-09 18:25:29'),(38,1,'HEMOCULTIVO','SANGRE',0.00,NULL,0.00,0.00,70.00,1,0,'2026-09-08 21:33:45','2026-09-08 21:33:45'),(39,2,'EXAMEN DE HONGOS Y ACAROS (KOH)','RASPADO DE PIEL',0.00,NULL,0.00,0.00,20.00,1,0,'2026-09-08 21:35:02','2026-09-08 21:35:02'),(40,2,'EXAMEN CITOQUIMICO ( citológico y bioquímico)','',0.00,NULL,0.00,0.00,90.00,1,0,'2026-09-08 21:35:41','2026-09-08 21:35:41'),(41,2,'COPROCULTIVO','',30.00,NULL,0.00,0.00,40.00,0,0,'2026-09-08 21:35:57','2026-09-09 18:29:23'),(42,2,'BK DIRECTO','LIQUIDO CORPORAL',0.00,NULL,0.00,0.00,15.00,1,0,'2026-09-08 21:36:37','2026-09-08 21:36:37'),(43,1,'THEVENON','Frasco con recolección de heces',8.00,1,0.00,0.00,30.00,1,0,'2026-09-08 21:37:19','2026-09-09 18:13:20'),(44,2,'TEST DE GRAHAN','MUESTRA ANAL',0.00,NULL,0.00,0.00,15.00,1,0,'2026-09-08 21:38:15','2026-09-08 21:38:15'),(45,1,'REACCION INFLAMATORIA','',0.00,NULL,0.00,0.00,20.00,1,0,'2026-09-08 21:38:32','2026-09-08 21:38:32'),(46,2,'PARASITOSIS X CONCENTRACION ( FAUST)','SECRECION',0.00,NULL,0.00,0.00,0.00,1,0,'2026-09-08 21:39:39','2026-09-08 21:39:39'),(47,2,'PARASITOSIS SIMPLE ( 1 MUESTRA )','SECRECION ',0.00,NULL,0.00,0.00,8.00,1,0,'2026-09-08 21:40:05','2026-09-08 21:40:05'),(48,2,'PARASITOSIS SERIADO ( 3 MUESTRAS)','SECRECCIO',0.00,NULL,0.00,0.00,25.00,1,0,'2026-09-08 21:40:27','2026-09-08 21:40:27'),(49,5,'COPROFUNCIONAL','SECRECION HESES',0.00,NULL,0.00,0.00,20.00,1,0,'2026-09-08 21:41:10','2026-09-09 18:18:05'),(50,1,'HCG- SUB UNIDAD BETA CUALITATIVO','tubo tapa roja',10.00,1,0.00,0.00,25.00,1,0,'2026-09-08 21:41:28','2026-09-09 18:06:57'),(51,1,'HCG - SANGRE ( CUALITATIVA)','SANGRE',0.00,NULL,0.00,0.00,25.00,1,0,'2026-09-08 21:41:42','2026-09-08 21:41:42'),(52,4,'HCG- ORINA ( CUALITATIVA)','ORINA',0.00,NULL,0.00,0.00,20.00,1,0,'2026-09-08 21:42:03','2026-09-08 21:42:03'),(53,1,'TEST DE HEMOLISIS','Tubo tapa morada',15.00,NULL,0.00,0.00,35.00,1,0,'2026-09-08 21:42:21','2026-09-09 09:00:38'),(54,4,'PROTEINURIA CUANTITATIVA ( ORINA 24 HORAS )','',0.00,NULL,0.00,0.00,35.00,1,0,'2026-09-08 21:42:41','2026-09-08 21:42:41'),(55,4,'PROTEINURIA CUALITATIVA','',0.00,NULL,0.00,0.00,10.00,1,0,'2026-09-08 21:43:16','2026-09-08 21:43:16'),(56,4,'MICROALMINURA','',0.00,NULL,0.00,0.00,50.00,1,0,'2026-09-08 21:43:30','2026-09-08 21:43:30'),(57,4,'MICROALBINURA','',0.00,NULL,0.00,0.00,50.00,1,0,'2026-09-08 21:43:47','2026-09-08 21:43:47'),(58,4,'EXAMEN DE ORINA','',0.00,NULL,0.00,0.00,15.00,1,0,'2026-09-08 21:44:00','2026-09-08 21:44:00'),(59,4,'UREA','',0.00,NULL,0.00,0.00,35.00,1,0,'2026-09-08 21:44:14','2026-09-08 21:44:14'),(60,1,'TRIGLICERIDOS','',0.00,NULL,0.00,0.00,35.00,1,0,'2026-09-08 21:44:30','2026-09-08 21:44:30'),(61,1,'TRANSAMINASAS TGP','tubo tapa roja',7.00,1,0.00,0.00,35.00,1,0,'2026-09-08 21:44:44','2026-09-09 15:32:16'),(62,1,'TRANSAMINASAS TGO','tubo tapa roja',7.00,1,0.00,0.00,35.00,1,0,'2026-09-08 21:44:56','2026-09-09 15:32:23'),(63,1,'PROTEINAS T Y F','',0.00,NULL,0.00,0.00,15.00,1,0,'2026-09-08 21:45:18','2026-09-08 21:45:18'),(64,1,'MAGNESIO','tubo tapa roja',40.00,1,0.00,0.00,80.00,1,0,'2026-09-08 21:45:32','2026-09-09 15:46:28'),(65,1,'LIPASA SERICA','tubo tapa roja',20.00,1,0.00,0.00,45.00,1,0,'2026-09-08 21:45:56','2026-09-09 15:37:29'),(66,1,'HEMOGLOBINA GLICOSILADA','Tubo tapa morada',20.00,1,0.00,0.00,100.00,1,0,'2026-09-08 21:46:09','2026-09-09 15:21:17'),(67,1,'Tolerancia a la glucosa x 5 determinación','tubo tapa roja',30.00,1,0.00,0.00,65.00,1,0,'2026-09-08 21:46:43','2026-09-09 15:22:14'),(68,1,'GLUCOSA','tubo tapa roja',5.00,NULL,0.00,0.00,15.00,1,0,'2026-09-08 21:46:56','2026-09-09 14:57:00'),(69,1,'GASES ARTERIALES (AGA)','Jeringa heparinizada/heparina de Na',70.00,1,0.00,0.00,120.00,1,0,'2026-09-08 21:47:13','2026-09-09 15:54:18'),(70,1,'GASES ARTERIALES (AGA) GAMMMA GLUTAMIL TRANSPEPTIDASA ( GGTP)','Jeringa heparinizada/heparina de Na',70.00,1,0.00,0.00,140.00,1,0,'2026-09-08 21:47:25','2026-09-09 15:54:09'),(71,1,'FOSFORO','tubo tapa roja',10.00,1,0.00,0.00,80.00,1,0,'2026-09-08 21:47:36','2026-09-09 15:45:50'),(72,1,'FOSFATA ALCALINA','',0.00,NULL,0.00,0.00,25.00,1,0,'2026-09-08 21:47:48','2026-09-08 21:47:48'),(73,1,'ELECTROLITOS SERICOS','tubo tapa roja',30.00,1,0.00,0.00,65.00,1,0,'2026-09-08 21:48:01','2026-09-09 15:42:02'),(74,1,'DESHIDROGENASA LACTICA( DHL)','',0.00,NULL,0.00,0.00,35.00,1,0,'2026-09-08 21:48:20','2026-09-08 21:48:20'),(75,1,'DEPURACION DE CREATININA','tubo tapa roja/ galonera con orina',20.00,1,0.00,0.00,45.00,1,0,'2026-09-08 21:48:31','2026-09-09 15:20:40'),(76,1,'CPK TOTAL','tubo tapa roja',25.00,1,0.00,0.00,50.00,1,0,'2026-09-08 21:48:43','2026-09-09 15:41:04'),(77,1,'CPK MB','tubo tapa roja',25.00,NULL,0.00,0.00,50.00,1,0,'2026-09-08 21:48:58','2026-09-09 15:40:39'),(78,1,'COLESTEROL TOTAL','tubo tapa roja',6.00,NULL,0.00,0.00,20.00,1,0,'2026-09-08 21:49:11','2026-09-09 14:58:02'),(79,1,'COLESTEROL LDL','tubo tapa roja',6.00,1,0.00,0.00,20.00,1,0,'2026-09-08 21:49:54','2026-09-09 14:59:29'),(80,1,'COLESTEROL HDL','tubo tapa roja',6.00,1,0.00,0.00,20.00,1,0,'2026-09-08 21:50:05','2026-09-09 14:58:44'),(81,1,'CALCIO SERICO','',0.00,NULL,0.00,0.00,25.00,0,0,'2026-09-08 21:50:17','2026-09-09 15:39:17'),(82,1,'BILIRRUBINA T Y F','',0.00,NULL,0.00,0.00,15.00,1,0,'2026-09-08 21:50:29','2026-09-08 21:50:29'),(83,1,'AMILASA SERICA','',0.00,NULL,0.00,0.00,35.00,0,0,'2026-09-08 21:50:40','2026-09-09 15:35:07'),(84,1,'ADENOSINA DEAMINASA ( ADA)','',0.00,NULL,0.00,0.00,50.00,1,0,'2026-09-08 21:50:52','2026-09-08 21:50:52'),(85,1,'PSA TOTAL','',0.00,NULL,0.00,0.00,65.00,1,0,'2026-09-08 21:51:11','2026-09-08 21:51:11'),(86,1,'PSA LIBRE','',0.00,NULL,0.00,0.00,65.00,1,0,'2026-09-08 21:51:27','2026-09-08 21:51:27'),(87,1,'CA 19- 9 ( PANCREAS)','tubo tapa roja',35.00,1,0.00,0.00,65.00,1,0,'2026-09-08 21:51:49','2026-09-09 18:00:18'),(88,1,'CA 125 ( OVARIO)','tubo tapa roja',35.00,1,0.00,0.00,65.00,1,0,'2026-09-08 21:52:02','2026-09-09 17:59:23'),(89,1,'ANTIGENO CARCINO EMBRIONARIO ( CEA)','tubo tapa amarilla',18.90,2,0.00,0.00,65.00,1,0,'2026-09-08 21:52:13','2026-09-09 17:58:52'),(90,1,'ALFA FETO PROTEINAS( AFP)','tubo tapa amarilla',26.00,2,0.00,0.00,65.00,1,0,'2026-09-08 21:52:27','2026-09-09 17:58:15'),(91,1,'TSH','',0.00,NULL,0.00,0.00,50.00,0,0,'2026-09-08 21:52:37','2026-09-09 16:47:44'),(92,1,'TESTOSTERONA TOTAL','tubo tapa roja',35.00,1,0.00,0.00,70.00,1,0,'2026-09-08 21:52:48','2026-09-09 16:50:36'),(93,1,'T4 LIBRE','tubo tapa roja',22.00,1,0.00,0.00,60.00,1,0,'2026-09-08 21:53:17','2026-09-09 16:49:13'),(94,1,'T3 LIBRE','',0.00,NULL,0.00,0.00,50.00,1,0,'2026-09-08 21:53:32','2026-09-08 21:53:32'),(95,1,'T3 ( TRIIDOTRIRONINA)','',0.00,NULL,0.00,0.00,50.00,1,0,'2026-09-08 21:53:43','2026-09-08 21:53:43'),(96,1,'PROALACTINA','',0.00,NULL,0.00,0.00,60.00,1,0,'2026-09-08 21:53:58','2026-09-08 21:53:58'),(97,1,'PROGESTERONA','tubo tapa roja',28.00,1,0.00,0.00,70.00,1,0,'2026-09-08 21:54:09','2026-09-09 16:44:01'),(98,1,'INSULINA','',0.00,NULL,0.00,0.00,60.00,1,0,'2026-09-08 21:54:25','2026-09-08 21:54:25'),(99,1,'HORMONA LUTENIZANTE(LH)','tubo tapa roja',22.00,1,0.00,0.00,60.00,1,0,'2026-09-08 21:55:09','2026-09-09 16:42:34'),(100,1,'HORMONA FOLICULO ESTIMULANTE ( FSH)','tubo tapa roja',22.00,1,0.00,0.00,70.00,1,0,'2026-09-08 21:55:22','2026-09-09 16:42:04'),(101,1,'CORTISOL  (AM)','',0.00,NULL,0.00,0.00,60.00,1,0,'2026-09-08 21:55:33','2026-09-08 21:55:33'),(102,1,'ESTRADIOL','tubo tapa roja',22.00,1,0.00,0.00,70.00,1,0,'2026-09-08 21:55:45','2026-09-09 16:35:27'),(103,4,'MARIHUANA - THC ( CUALITATIVO)','Frasco con recolección de orina ',30.00,NULL,0.00,0.00,100.00,1,0,'2026-09-08 21:56:00','2026-09-09 18:36:56'),(104,1,'COCAINA -COC( CUALITATIVO)','',0.00,NULL,0.00,0.00,45.00,1,0,'2026-09-08 21:56:16','2026-09-08 21:56:16'),(105,1,'TROPONINA 1','tubo tapa roja',70.00,1,0.00,0.00,140.00,1,0,'2026-09-08 21:56:43','2026-09-09 15:55:41'),(106,1,'SIFILIS ( PRUEBA RAPIDA)','',0.00,NULL,0.00,0.00,40.00,1,0,'2026-09-08 21:56:56','2026-09-08 21:56:56'),(107,1,'RPR( SIFILIS ) CUANTITATIVO','',0.00,NULL,0.00,0.00,70.00,1,0,'2026-09-08 21:57:10','2026-09-08 21:57:10'),(108,1,'RPR ( SIFILIS) CUALITATIVO','',0.00,NULL,0.00,0.00,15.00,1,0,'2026-09-08 21:57:22','2026-09-08 21:57:22'),(109,1,'PROTEINA ¨C¨REACTIVA ( CUANTITATIVA)','tubo tapa roja',20.00,1,0.00,0.00,45.00,1,0,'2026-09-08 21:57:34','2026-09-09 17:10:23'),(110,1,'PROTEINA ¨C¨ REACTIVA (PCR) CUALITATIVA','tubo tapa roja',7.00,1,0.00,0.00,20.00,1,0,'2026-09-08 21:57:45','2026-09-09 17:09:29'),(111,1,'MIOGLOBINA','tubo tapa roja',90.00,1,0.00,0.00,180.00,1,0,'2026-09-08 21:58:00','2026-09-09 15:56:14'),(112,1,'INMUNOGLOBINA E (IgE)','tubo tapa roja',28.00,1,0.00,0.00,60.00,1,0,'2026-09-08 21:58:21','2026-09-09 17:36:56'),(113,1,'HIV 1/2 ( PRUEBA RAPIDA)','',0.00,NULL,0.00,0.00,40.00,1,0,'2026-09-08 21:58:36','2026-09-08 21:58:36'),(114,1,'HEPATITIS C, HCV ( PRUEBA RAPIDA)','tubo tapa roja',22.00,1,0.00,0.00,60.00,1,0,'2026-09-08 21:58:59','2026-09-09 17:29:20'),(115,1,'HEPATITIS B, HBsAG ( PRUEBA RAPIDA)','tubo tapa roja',28.00,1,0.00,0.00,50.00,1,0,'2026-09-08 21:59:12','2026-09-09 17:27:49'),(116,1,'HEPATITIS A, HAV ( PRUEBA RAPIDA )','tubo tapa roja',22.00,1,0.00,0.00,60.00,1,0,'2026-09-08 21:59:24','2026-09-09 17:28:27'),(117,1,'HELICOBACTER PYLORI IgM','tubo tapa roja',30.00,1,0.00,0.00,60.00,1,0,'2026-09-08 21:59:39','2026-09-09 17:30:33'),(118,1,'HELICOBACTER PYLORI IgG','tubo tapa roja',30.00,1,0.00,0.00,60.00,1,0,'2026-09-08 21:59:53','2026-09-09 17:30:59'),(119,1,'FACTOR REUMATOIDE CUALITATIVO','tubo tapa roja',8.00,1,0.00,0.00,20.00,1,0,'2026-09-08 22:00:05','2026-09-09 17:12:30'),(120,1,'COOMBS INDIRECTO','tubo tapa roja',50.00,NULL,0.00,0.00,80.00,1,0,'2026-09-08 22:00:35','2026-09-09 08:59:59'),(121,1,'COOMBS DIRECTO','Tubo tapa morada',50.00,NULL,0.00,0.00,80.00,1,0,'2026-09-08 22:00:47','2026-09-09 08:59:28'),(122,1,'ANTI ESTRETOLISINA ( ASO)','tubo tapa roja',8.00,NULL,0.00,0.00,120.00,1,0,'2026-09-08 22:00:58','2026-09-09 17:16:29'),(123,1,'AGLUTINACIONES','',0.00,NULL,0.00,0.00,15.00,1,0,'2026-09-08 22:01:15','2026-09-08 22:01:15'),(124,1,'ANTI CCP IGG (PEPTIDO CICLICO CITRULINADO)','tubo tapa roja',185.00,1,0.00,0.00,350.00,1,0,'2026-09-08 22:01:27','2026-09-09 17:14:17'),(125,1,'V.S.G.','',0.00,NULL,0.00,0.00,10.00,1,0,'2026-09-08 22:01:37','2026-09-08 22:01:37'),(126,1,'TROMBOPLASTINA (TPTA)','',0.00,NULL,0.00,0.00,35.00,1,0,'2026-09-08 22:01:53','2026-09-08 22:01:53'),(127,1,'TIEMPO TROMBINA','Tubo tapa celeste ',25.00,NULL,0.00,0.00,60.00,1,0,'2026-09-08 22:02:05','2026-09-09 09:05:35'),(128,1,'PROTOMBINA INR (TP)','',0.00,NULL,0.00,0.00,35.00,1,0,'2026-09-08 22:02:21','2026-09-08 22:02:21'),(129,1,'COAGULACION Y SANGRIA','SANGRE CAPILAR ',6.00,NULL,0.00,0.00,20.00,1,0,'2026-09-08 22:02:34','2026-09-09 09:06:31'),(130,1,'RECUENTO DE RETICULOCITOS','Tubo tapa morada',12.00,NULL,0.00,0.00,40.00,1,0,'2026-09-08 22:02:48','2026-09-09 08:53:02'),(131,1,'RECUENTO DE PLAQUETAS','Tubo tapa morada',7.00,NULL,0.00,0.00,10.00,1,0,'2026-09-08 22:03:00','2026-09-09 08:44:11'),(132,1,'HEMOGLOBINA','Tubo tapa morada',4.00,NULL,0.00,0.00,20.00,1,0,'2026-09-08 22:03:13','2026-09-09 08:42:45'),(133,1,'HEMATOCRITO','Tubo tapa morada',6.00,NULL,0.00,0.00,20.00,1,0,'2026-09-08 22:03:23','2026-09-09 08:50:58'),(134,1,'GRUPO Y FACTOR SANGUINEO','Tubo tapa morada',4.00,NULL,0.00,0.00,20.00,1,0,'2026-09-08 22:03:36','2026-09-09 08:55:06'),(135,1,'FIBRINOGENO','',0.00,NULL,0.00,0.00,40.00,1,0,'2026-09-08 22:03:46','2026-09-08 22:03:46'),(136,1,'FERRITINA','tubo tapa roja',35.00,1,0.00,0.00,70.00,1,0,'2026-09-08 22:03:58','2026-09-09 16:01:05'),(137,1,'FENOMENO L.E.','',0.00,NULL,0.00,0.00,40.00,1,0,'2026-09-08 22:04:11','2026-09-08 22:04:11'),(138,1,'DIMERO D.','Tubo tapa celeste ',60.00,NULL,0.00,0.00,120.00,1,0,'2026-09-08 22:04:24','2026-09-09 09:10:35'),(139,1,'CONSTANTES CORPUSCULARES','Tubo tapa morada',8.00,NULL,0.00,0.00,35.00,1,0,'2026-09-08 22:04:45','2026-09-09 08:55:57'),(140,1,'HEMOGRAMA COMPLETO','Tubo tapa morada ',7.00,NULL,0.00,0.00,35.00,1,0,'2026-09-09 08:41:59','2026-09-09 08:41:59'),(141,1,'RENCUENTRO DE HEMATIES ','Tubo tapa morada ',7.00,NULL,0.00,0.00,35.00,1,0,'2026-09-09 08:50:12','2026-09-09 08:50:12'),(142,1,'Recuento de leucocitos','Tubo tapa morada',7.00,NULL,0.00,0.00,35.00,1,0,'2026-09-09 08:53:46','2026-09-09 08:53:46'),(143,1,'Células L.E','Tubo tapa morada',12.00,NULL,0.00,0.00,40.00,1,0,'2026-09-09 08:56:38','2026-09-09 08:56:38'),(144,1,'Lamina periférica','Tubo tapa morada ',10.00,NULL,0.00,0.00,40.00,1,0,'2026-09-09 08:57:39','2026-09-09 08:57:39'),(145,1,'Velocidad de Sedimentación Globular (VSG)','Tubo tapa morada ',8.00,NULL,0.00,0.00,20.00,1,0,'2026-09-09 08:58:29','2026-09-09 08:58:29'),(146,1,'Eosinofilos en secreción (esputo, faringea)','Tubo con NaCl y laminá porta objeto',20.00,NULL,0.00,0.00,50.00,1,0,'2026-09-09 09:01:55','2026-09-09 09:01:55'),(147,1,'Eosinofilos en secreción nasal','tubo con NaCl y lamina porta objeto',20.00,NULL,0.00,0.00,50.00,1,0,'2026-09-09 09:03:00','2026-09-09 09:03:00'),(148,1,'Tiempo protrombina + INR','Tubo tapa celeste ',20.00,NULL,0.00,0.00,50.00,1,0,'2026-09-09 09:03:35','2026-09-09 09:03:35'),(149,1,'Tiempo parcial tromboplastina activada','Tubo tapa celeste',25.00,NULL,0.00,0.00,60.00,1,0,'2026-09-09 09:04:11','2026-09-09 09:04:11'),(150,1,'Fibrinógeno','Tubo tapa celeste ',25.00,NULL,0.00,0.00,60.00,1,0,'2026-09-09 09:07:22','2026-09-09 09:07:22'),(151,1,'Fragilidad capilar','Lazo torniquete',15.00,NULL,0.00,0.00,45.00,1,0,'2026-09-09 09:08:09','2026-09-09 09:08:09'),(152,1,'Fragilidad globular','Tubo tapa morada',25.00,NULL,0.00,0.00,60.00,1,0,'2026-09-09 09:08:58','2026-09-09 09:08:58'),(153,1,'Retracción de coagulo','Tubo tapa roja ',15.00,NULL,0.00,0.00,45.00,1,0,'2026-09-09 09:09:52','2026-09-09 09:09:52'),(154,1,'Ácido láctico (lactato)','Tubo tapa negro/morada',55.00,NULL,0.00,0.00,110.00,1,0,'2026-09-09 09:11:35','2026-09-09 09:11:35'),(155,1,'Alcohol etílico','Tubo tapa roja',55.00,NULL,0.00,0.00,110.00,1,0,'2026-09-09 09:13:20','2026-09-09 09:13:20'),(156,1,'Alcohol etílico en orina','Frasco con recolección de orina',50.00,NULL,0.00,0.00,100.00,1,0,'2026-09-09 09:14:03','2026-09-09 09:14:03'),(157,4,'D-xilosa ','Frasco con recolección de orina',80.00,NULL,0.00,0.00,100.00,1,0,'2026-09-09 09:14:58','2026-09-09 09:14:58'),(158,4,'Alcohol etílico en orina','Frasco con recolección de orina',50.00,NULL,0.00,0.00,100.00,1,0,'2026-09-09 09:16:01','2026-09-09 09:16:01'),(159,1,'Aldolasa sérica','Tubo tapa roja ',70.00,1,0.00,0.00,140.00,1,0,'2026-09-09 14:47:17','2026-09-09 14:47:17'),(160,1,'ADA (Adenosindiaminasa)','Tubo con líquidos biológicos',100.00,NULL,0.00,0.00,200.00,1,0,'2026-09-09 14:54:58','2026-09-09 14:54:58'),(161,1,'Colesterol VLDL ','tubo tapa roja',6.00,1,0.00,0.00,20.00,1,0,'2026-09-09 15:00:10','2026-09-09 15:00:10'),(162,1,'Lípidos totales','tubo tapa roja',15.00,1,0.00,0.00,45.00,1,0,'2026-09-09 15:14:54','2026-09-09 15:14:54'),(163,1,'Triglicéridos','tubo tapa roja',7.00,1,0.00,0.00,35.00,1,0,'2026-09-09 15:15:24','2026-09-09 15:15:45'),(164,1,'Urea','tubo tapa roja',6.00,NULL,0.00,0.00,35.00,1,0,'2026-09-09 15:17:28','2026-09-09 15:17:28'),(165,1,'Creatinina','tubo tapa roja',6.00,NULL,0.00,0.00,35.00,1,0,'2026-09-09 15:18:10','2026-09-09 15:18:10'),(166,1,'Ácido úrico','tubo tapa roja',7.00,NULL,0.00,0.00,35.00,1,0,'2026-09-09 15:18:58','2026-09-09 15:18:58'),(167,1,'BUM ureico','tubo tapa roja',15.00,1,0.00,0.00,45.00,1,0,'2026-09-09 15:19:45','2026-09-09 15:19:45'),(168,1,'Tolerancia a la glucosa x 3 determinación','tubo tapa roja',15.00,1,0.00,0.00,45.00,1,0,'2026-09-09 15:22:58','2026-09-09 15:22:58'),(169,1,'Glucosa basaly post pandrial ','tubo tapa roja',12.00,1,0.00,0.00,25.00,1,0,'2026-09-09 15:23:49','2026-09-09 15:23:49'),(170,1,'Fosfatasa acida total','tubo tapa roja',25.00,1,0.00,0.00,65.00,1,0,'2026-09-09 15:24:43','2026-09-09 15:24:43'),(171,1,'Fosfatasa acida prostática','tubo tapa roja',30.00,1,0.00,0.00,60.00,1,0,'2026-09-09 15:25:31','2026-09-09 15:25:31'),(172,1,'Deshidrogenasa láctica (DHL)','Tubo tapa roja ',35.00,1,0.00,0.00,70.00,1,0,'2026-09-09 15:27:02','2026-09-09 15:27:02'),(173,1,'Bilirrubina total y fraccionada','tubo tapa roja',7.00,1,0.00,0.00,35.00,1,0,'2026-09-09 15:27:55','2026-09-09 15:27:55'),(174,1,'Proteínas total y fraccionada','tubo tapa roja',7.00,1,0.00,0.00,35.00,1,0,'2026-09-09 15:28:41','2026-09-09 15:28:41'),(175,1,'Fosfatasa alcalina','tubo tapa roja',7.00,1,0.00,0.00,35.00,1,0,'2026-09-09 15:33:03','2026-09-09 15:33:03'),(176,1,'Gamma glutamil transpeptidasa','tubo tapa roja',15.00,1,0.00,0.00,45.00,1,0,'2026-09-09 15:34:02','2026-09-09 15:34:02'),(177,1,'Amilasa sérica','tubo tapa roja',15.00,1,0.00,0.00,45.00,1,0,'2026-09-09 15:34:41','2026-09-09 15:34:41'),(178,1,'Calcio sérico','tubo tapa roja',12.00,1,0.00,0.00,40.00,1,0,'2026-09-09 15:38:23','2026-09-09 15:38:23'),(179,1,'Calcio iónico','tubo tapa roja',15.00,1,0.00,0.00,45.00,1,0,'2026-09-09 15:39:06','2026-09-09 15:39:06'),(180,1,'Sodio (Na) ','tubo tapa roja',20.00,1,0.00,0.00,30.00,1,0,'2026-09-09 15:43:34','2026-09-09 15:43:34'),(181,1,'Cloro (C)','tubo tapa roja',10.00,1,0.00,0.00,30.00,1,0,'2026-09-09 15:44:17','2026-09-09 15:44:17'),(182,1,'Potasio (K)','tubo tapa roja',10.00,1,0.00,0.00,30.00,1,0,'2026-09-09 15:45:08','2026-09-09 15:45:08'),(183,1,'Vitamina D 25 Hidroxi (Vitamina D3) ','tubo tapa dorada dos frascos',17.70,2,0.00,0.00,320.00,1,0,'2026-09-09 15:53:22','2026-09-09 16:01:21'),(184,1,'Troponina T','Tubo tapa roja ',70.00,1,0.00,0.00,140.00,1,0,'2026-09-09 15:55:07','2026-09-09 15:55:07'),(185,1,'Procalcitonina','tubo tapa roja',180.00,1,0.00,0.00,250.00,1,0,'2026-09-09 15:56:55','2026-09-09 15:56:55'),(186,1,'Pro BNP(Péptido natrureico)','tubo tapa roja',360.00,1,0.00,0.00,450.00,1,0,'2026-09-09 15:58:15','2026-09-09 15:58:15'),(187,1,'Ácido fólico ','tubo tapa roja',35.00,1,0.00,0.00,70.00,1,0,'2026-09-09 15:59:07','2026-09-09 15:59:07'),(188,1,'Vitamina B12 ','tubo tapa roja',35.00,1,0.00,0.00,70.00,1,0,'2026-09-09 15:59:46','2026-09-09 15:59:46'),(189,1,'Hierro sérico','tubo tapa roja',35.00,1,0.00,0.00,70.00,1,0,'2026-09-09 16:00:22','2026-09-09 16:00:22'),(190,1,'Saturación de transferrina ','tubo tapa roja',35.00,1,0.00,0.00,70.00,1,0,'2026-09-09 16:02:04','2026-09-09 16:02:04'),(191,1,'Transferrina ','tubo tapa roja',35.00,1,0.00,0.00,0.00,1,0,'2026-09-09 16:02:32','2026-09-09 16:02:32'),(192,1,'TEST DE TOLERANCIA A LA GLUCOSA','TAPA ROJO/ PREVIA PREPARACION DE PX',18.00,1,0.00,0.00,100.00,1,0,'2026-09-09 16:04:25','2026-09-09 16:04:25'),(193,1,'Sars Cov2 (IgG-IgM)- Anticuerpo c/п','tubo tapa roja',40.00,1,0.00,0.00,80.00,1,0,'2026-09-09 16:11:58','2026-09-09 16:11:58'),(194,1,'Sars Cov2-Antígeno','Hisopo para (hisopado oro faringea)',30.00,1,0.00,0.00,60.00,1,0,'2026-09-09 16:13:38','2026-09-09 16:13:38'),(195,1,'Herpes lIgM','tubo tapa roja',50.00,1,0.00,0.00,120.00,1,0,'2026-09-09 16:14:48','2026-09-09 16:14:48'),(196,1,'Herpes lIgG ','tubo tapa roja',50.00,1,0.00,0.00,120.00,1,0,'2026-09-09 16:15:44','2026-09-09 16:15:44'),(197,1,'Herpes ll IgM','tubo tapa roja',50.00,1,0.00,0.00,120.00,1,0,'2026-09-09 16:16:29','2026-09-09 16:16:29'),(198,1,'Herpes ll IgG ','tubo tapa roja',50.00,1,0.00,0.00,100.00,1,0,'2026-09-09 16:17:23','2026-09-09 16:17:23'),(199,1,'Herpeszoster IgM ','tubo tapa roja',70.00,1,0.00,0.00,120.00,1,0,'2026-09-09 16:18:00','2026-09-09 16:18:00'),(200,1,'Herpeszoster IgG','tubo tapa roja',70.00,1,0.00,0.00,120.00,1,0,'2026-09-09 16:18:33','2026-09-09 16:18:33'),(201,1,'Citomegalovirus IgM','tubo tapa roja',50.00,1,0.00,0.00,120.00,1,0,'2026-09-09 16:19:33','2026-09-09 16:19:33'),(202,1,'Citomegalovirus IgG ','tubo tapa roja',50.00,1,0.00,0.00,100.00,1,0,'2026-09-09 16:20:03','2026-09-09 16:20:03'),(203,1,'Rubeola IgM','tubo tapa roja',50.00,1,0.00,0.00,100.00,1,0,'2026-09-09 16:21:07','2026-09-09 16:21:07'),(204,1,'Rubeola IgG','tubo tapa roja',50.00,1,0.00,0.00,100.00,1,0,'2026-09-09 16:21:43','2026-09-09 16:21:43'),(205,1,'Toxoplasma IgM','tubo tapa roja',50.00,1,0.00,0.00,100.00,1,0,'2026-09-09 16:22:20','2026-09-09 16:22:20'),(206,1,'Toxoplasma IgG','tubo tapa roja',50.00,1,0.00,0.00,100.00,1,0,'2026-09-09 16:22:46','2026-09-09 16:22:46'),(207,1,'Clamydea trachomatis IgM','TAPA DORADA',65.00,1,0.00,0.00,100.00,1,0,'2026-09-09 16:23:23','2026-09-09 16:23:23'),(208,1,'Clamydea trachomatis IgG ','TAPA DORADA',65.00,1,0.00,0.00,100.00,1,0,'2026-09-09 16:24:13','2026-09-09 16:25:21'),(209,1,'Despistaje alérgico (36 alérgenos)','tubo tapa roja',330.00,1,0.00,0.00,450.00,1,0,'2026-09-09 16:24:57','2026-09-09 16:24:57'),(210,1,'Despistaje alérgico (295 alérgenos)','tubo tapa roja',1200.00,1,0.00,0.00,1500.00,1,0,'2026-09-09 16:28:34','2026-09-09 16:28:34'),(211,1,'Anti Cardiolipina IgA ','tubo tapa roja',60.00,1,0.00,0.00,110.00,1,0,'2026-09-09 16:29:25','2026-09-09 16:29:25'),(212,1,'Anti Cardiolipina IgM','tubo tapa roja',60.00,1,0.00,0.00,110.00,1,0,'2026-09-09 16:29:57','2026-09-09 16:29:57'),(213,1,'Anti Cardiolipina IgG','tubo tapa roja',60.00,1,0.00,0.00,120.00,1,0,'2026-09-09 16:30:35','2026-09-09 16:30:35'),(214,1,'Anti DNA - DS Nativo o Doble cadena ','tubo tapa roja',70.00,1,0.00,0.00,120.00,1,0,'2026-09-09 16:31:17','2026-09-09 16:31:17'),(215,1,'nti DNA - SS Auto Aco cadena simple','tubo tapa roja',60.00,1,0.00,0.00,120.00,1,0,'2026-09-09 16:31:42','2026-09-09 16:31:42'),(216,1,'Anti Mitocondriales (AMA)','tubo tapa roja',60.00,1,0.00,0.00,110.00,1,0,'2026-09-09 16:32:12','2026-09-09 16:32:12'),(217,1,'Anti Musculo Liso (ASMA)','tubo tapa roja',70.00,1,0.00,0.00,120.00,1,0,'2026-09-09 16:32:49','2026-09-09 16:32:49'),(218,1,'Anticuerpo antinucleares (ANA) ','tubo tapa roja',60.00,1,0.00,0.00,110.00,1,0,'2026-09-09 16:33:42','2026-09-09 16:33:42'),(219,1,'Anticoagulante lupico','tubo tapa roja',60.00,1,0.00,0.00,110.00,1,0,'2026-09-09 16:34:16','2026-09-09 16:34:16'),(220,1,'Galactomano','tubo tapa roja',270.00,1,0.00,0.00,350.00,1,0,'2026-09-09 16:34:49','2026-09-09 16:34:49'),(221,1,'Estriol libre','tubo tapa roja',40.00,1,0.00,0.00,70.00,1,0,'2026-09-09 16:36:08','2026-09-09 16:36:08'),(222,1,'Cortisol (am)','tubo tapa roja',40.00,1,0.00,0.00,70.00,1,0,'2026-09-09 16:37:41','2026-09-09 16:37:41'),(223,1,'Cortisol (pm) ','tubo tapa roja',40.00,1,0.00,0.00,70.00,1,0,'2026-09-09 16:39:33','2026-09-09 16:39:33'),(224,1,'Insulina basal','tubo tapa roja',28.00,1,0.00,0.00,80.00,1,0,'2026-09-09 16:45:12','2026-09-09 16:45:12'),(225,1,'Triodotironina (T3)','tubo tapa roja',22.00,1,0.00,0.00,60.00,1,0,'2026-09-09 16:45:46','2026-09-09 16:45:46'),(226,1,'Tiroxina (T4)','tubo tapa roja',22.00,1,0.00,0.00,60.00,1,0,'2026-09-09 16:46:40','2026-09-09 16:46:40'),(227,1,'Tirotropina (TSH ultrasensible)','tubo tapa roja',22.00,1,0.00,0.00,60.00,1,0,'2026-09-09 16:47:28','2026-09-09 16:47:28'),(228,1,'Tiroglobulina','tubo tapa roja',50.00,1,0.00,0.00,100.00,1,0,'2026-09-09 16:50:01','2026-09-09 16:50:01'),(229,1,'Testosterona libre','tubo tapa roja',40.00,1,0.00,0.00,70.00,1,0,'2026-09-09 16:51:12','2026-09-09 16:51:12'),(230,4,'Acido úrico orina simple','Frasco con recolección de orina',7.00,1,0.00,0.00,30.00,1,0,'2026-09-09 16:54:11','2026-09-09 16:54:11'),(231,4,'Ácido úrico orina 24 horas','Galonera con recolección de orina',9.00,1,0.00,0.00,35.00,1,0,'2026-09-09 16:55:07','2026-09-09 16:55:07'),(232,4,'Albumina orina simple','Frasco con recolección de orina ',10.00,1,0.00,0.00,30.00,1,0,'2026-09-09 16:55:48','2026-09-09 16:55:48'),(233,4,'Albumina orina 24 horas','Galonera con recolección de orina',18.00,1,0.00,0.00,35.00,1,0,'2026-09-09 16:56:35','2026-09-09 16:56:35'),(234,4,'Calcio en orina simple','Frasco con recolección de orina',12.00,1,0.00,0.00,35.00,1,0,'2026-09-09 16:57:15','2026-09-09 16:57:15'),(235,4,'Calcio en orina 24 horas','Galonera con recolección de orina',20.00,1,0.00,0.00,40.00,1,0,'2026-09-09 16:59:15','2026-09-09 16:59:15'),(236,4,'Creatinina orina simple','Frasco con recolección de orina',10.00,1,0.00,0.00,30.00,1,0,'2026-09-09 16:59:58','2026-09-09 16:59:58'),(237,4,'Proteinuria simple','Frasco con recolección de orina',15.00,1,0.00,0.00,30.00,1,0,'2026-09-09 17:01:12','2026-09-09 17:01:12'),(238,4,'Proteinuria simple','Frasco con recolección de orina',15.00,1,0.00,0.00,30.00,1,0,'2026-09-09 17:01:12','2026-09-09 17:01:12'),(239,4,'Proteinuria 24horas','Galonera con recolección de orina',25.00,1,0.00,0.00,60.00,1,0,'2026-09-09 17:02:05','2026-09-09 17:02:05'),(240,1,'Aglutinaciones lamina ','tubo tapa roja',8.00,1,0.00,0.00,20.00,1,0,'2026-09-09 17:04:23','2026-09-09 17:04:23'),(241,1,'Aglutinaciones tubo (salmonelosis)','tubo tapa roja',15.00,1,0.00,0.00,45.00,1,0,'2026-09-09 17:05:17','2026-09-09 17:05:17'),(242,1,'Aglutinaciones tubo (brucelosis)','tubo tapa roja',15.00,1,0.00,0.00,45.00,1,0,'2026-09-09 17:05:51','2026-09-09 17:05:51'),(243,1,'Aglutinaciones 2-Mercaptoetanol','tubo tapa roja',30.00,1,0.00,0.00,60.00,1,0,'2026-09-09 17:06:32','2026-09-09 17:06:32'),(244,1,'Aglutinacionesfenómeno de zona','tubo tapa roja',30.00,1,0.00,0.00,60.00,1,0,'2026-09-09 17:07:18','2026-09-09 17:07:18'),(245,1,'Rosa de bengala','tubo tapa roja',25.00,1,0.00,0.00,50.00,1,0,'2026-09-09 17:08:12','2026-09-09 17:08:12'),(246,1,'Factor Reumatoide cuantitativo','tubo tapa roja',25.00,1,0.00,0.00,50.00,1,0,'2026-09-09 17:13:17','2026-09-09 17:13:17'),(247,1,'Complemento C3','tubo tapa roja',60.00,1,0.00,0.00,120.00,1,0,'2026-09-09 17:14:54','2026-09-09 17:14:54'),(248,1,'Complemento C4','tubo tapa roja',60.00,1,0.00,0.00,120.00,1,0,'2026-09-09 17:15:18','2026-09-09 17:15:18'),(249,1,'RPR/VDRL','tubo tapa roja',12.00,1,0.00,0.00,50.00,1,0,'2026-09-09 17:17:13','2026-09-09 17:17:13'),(250,1,'Prueba de sífilis por inmunocromatografia ','tubo tapa roja',0.00,1,0.00,0.00,50.00,1,0,'2026-09-09 17:23:41','2026-09-09 17:23:41'),(251,1,'RPR diluciones','tubo tapa roja',25.00,1,0.00,0.00,50.00,1,0,'2026-09-09 17:24:12','2026-09-09 17:24:12'),(252,1,'FTA Absorbido','tubo tapa roja',55.00,1,0.00,0.00,100.00,1,0,'2026-09-09 17:24:39','2026-09-09 17:24:39'),(253,1,'VIH Prueba rápida ','tubo tapa roja',12.00,1,0.00,0.00,45.00,1,0,'2026-09-09 17:25:06','2026-09-09 17:25:06'),(254,1,'VIH 1-2(Ac-Ag 3°/4° Generación)','tubo tapa roja',70.00,1,0.00,0.00,140.00,1,0,'2026-09-09 17:25:36','2026-09-09 17:25:36'),(255,1,'HTLVI-II Anticuerpos','tubo tapa roja',70.00,1,0.00,0.00,140.00,1,0,'2026-09-09 17:26:02','2026-09-09 17:26:02'),(256,1,'Helycobacterpylori directo','tubo tapa roja',18.00,1,0.00,0.00,35.00,1,0,'2026-09-09 17:31:42','2026-09-09 17:31:42'),(257,1,'Inmunoglobulina M (IgM)','tubo tapa roja',40.00,1,0.00,0.00,80.00,1,0,'2026-09-09 17:37:56','2026-09-09 17:37:56'),(258,1,'Inmunoglobulina G (lgG)','tubo tapa roja',40.00,1,0.00,0.00,80.00,1,0,'2026-09-09 17:38:22','2026-09-09 17:38:22'),(259,1,'Inmunoglobulina D (IgD)','tubo tapa roja',40.00,1,0.00,0.00,80.00,1,0,'2026-09-09 17:38:50','2026-09-09 17:38:50'),(260,1,'Inmunoglobulina A (IgA)','tubo tapa roja',40.00,1,0.00,0.00,80.00,1,0,'2026-09-09 17:39:14','2026-09-09 17:39:14'),(261,1,'Dengue (Antígeno -Anticuerpo)','tubo tapa roja',50.00,1,0.00,0.00,100.00,1,0,'2026-09-09 17:40:00','2026-09-09 17:40:00'),(262,1,'Dengue virus anticuerpos IgM','tubo tapa roja',70.00,1,0.00,0.00,140.00,1,0,'2026-09-09 17:40:50','2026-09-09 17:40:50'),(263,1,'Dengue Virus Anticuerpos IgG','tubo tapa roja',70.00,1,0.00,0.00,140.00,1,0,'2026-09-09 17:41:16','2026-09-09 17:41:16'),(264,1,'PERFIL ROMA','tapa amarilla / 2 tubs',309.20,2,0.00,0.00,450.00,1,0,'2026-09-09 17:47:19','2026-09-09 17:47:51'),(265,1,'Paratohormona Intacta','tubo tapa roja',60.00,1,0.00,0.00,120.00,1,0,'2026-09-09 17:49:33','2026-09-09 17:49:33'),(266,1,'Beta HCG Libre','tubo tapa roja',180.00,1,0.00,0.00,250.00,1,0,'2026-09-09 17:50:16','2026-09-09 17:50:16'),(267,1,'PAPP-A','tubo tapa roja',150.00,1,0.00,0.00,250.00,1,0,'2026-09-09 17:50:53','2026-09-09 17:50:53'),(268,1,'Dehidroepiandosterona (DHEA-SO4) ','tubo tapa roja/ amarilla',70.00,1,0.00,0.00,140.00,1,0,'2026-09-09 17:51:59','2026-09-09 17:51:59'),(269,1,'ACTH','tubo tapa roja',90.00,1,0.00,0.00,180.00,1,0,'2026-09-09 17:52:41','2026-09-09 17:52:41'),(270,1,'Hormona Antimulleriana (AMS/MIS)','tubo tapa amarilla',141.60,2,0.00,0.00,280.00,1,0,'2026-09-09 17:53:47','2026-09-09 17:53:47'),(271,1,'Anti Tiroglobulina (Anti-ATG)','tubo tapa roja',55.00,1,0.00,0.00,100.00,1,0,'2026-09-09 17:54:45','2026-09-09 17:54:45'),(272,1,'Anti Tiroperoxidasa (ATPO - Microsomal)','tubo tapa roja',45.00,1,0.00,0.00,100.00,1,0,'2026-09-09 17:55:13','2026-09-09 17:55:13'),(273,1,'Anticuerpos Antitiroideos ','tubo tapa roja',95.00,1,0.00,0.00,180.00,1,0,'2026-09-09 17:56:00','2026-09-09 17:56:00'),(274,1,'Dihidrostestorena (DHT)','tubo tapa roja',135.00,1,0.00,0.00,200.00,1,0,'2026-09-09 17:56:22','2026-09-09 17:56:22'),(275,1,'Hormona de Crecimiento','tubo tapa roja',70.00,1,0.00,0.00,140.00,1,0,'2026-09-09 17:56:58','2026-09-09 17:56:58'),(276,1,'Ca 15-3 mama ','tubo tapa roja',55.00,1,0.00,0.00,110.00,1,0,'2026-09-09 18:01:13','2026-09-09 18:02:22'),(277,1,'Ca 549 mama','tubo tapa roja',180.00,1,0.00,0.00,250.00,1,0,'2026-09-09 18:01:54','2026-09-09 18:01:54'),(278,1,'Ca 27-29 mama ','tubo tapa roja',320.00,1,0.00,0.00,450.00,1,0,'2026-09-09 18:03:18','2026-09-09 18:03:18'),(279,1,'Ca 72-4 estomago ','tubo tapa roja',60.00,1,0.00,0.00,120.00,1,0,'2026-09-09 18:03:53','2026-09-09 18:03:53'),(280,1,'Cyfra 21-1(CK19) ','tubo tapa roja',180.00,1,0.00,0.00,250.00,1,0,'2026-09-09 18:04:30','2026-09-09 18:04:30'),(281,1,'Calcitonina ','tubo tapa roja',70.00,1,0.00,0.00,140.00,1,0,'2026-09-09 18:05:04','2026-09-09 18:05:04'),(282,1,'PSA total ','tubo tapa roja',30.00,1,0.00,0.00,65.00,1,0,'2026-09-09 18:05:20','2026-09-09 18:05:20'),(283,1,'PSA libre','tubo tapa roja',35.00,1,0.00,0.00,65.00,1,0,'2026-09-09 18:05:42','2026-09-09 18:05:42'),(284,1,'HCG-sub unidad beta cuantitativo ','tubo tapa roja',22.00,1,0.00,0.00,65.00,1,0,'2026-09-09 18:07:15','2026-09-09 18:07:15'),(285,1,'HCG-sub unidad beta cuantitativo ','tubo tapa roja',22.00,1,0.00,0.00,65.00,1,0,'2026-09-09 18:07:15','2026-09-09 18:07:15'),(286,4,'Examen deorina completo','Frasco con recolección de orina',5.00,1,0.00,0.00,15.00,1,0,'2026-09-09 18:08:27','2026-09-09 18:08:27'),(287,4,'Sedimento urinario','Frasco con recolección de orina',5.00,1,0.00,0.00,20.00,1,0,'2026-09-09 18:08:44','2026-09-09 18:08:44'),(288,4,'Test de hemolisis','Frasco con recolección de orina',10.00,1,0.00,0.00,30.00,1,0,'2026-09-09 18:09:06','2026-09-09 18:09:06'),(289,4,'Calculo urinario (litiasis)','Frasco con recolección del calculo',10.00,1,0.00,0.00,30.00,1,0,'2026-09-09 18:09:38','2026-09-09 18:09:38'),(290,4,'Prueba de ADDIS','Galonera con recolección de orina',50.00,1,0.00,0.00,100.00,1,0,'2026-09-09 18:10:30','2026-09-09 18:10:30'),(291,5,'Examen de heces simple','Frasco con recolección de heces',5.00,1,0.00,0.00,30.00,1,0,'2026-09-09 18:11:42','2026-09-09 18:11:42'),(292,5,'Examen de heces seriado 3 muestras','Frasco con recolección de heces ',15.00,1,0.00,0.00,30.00,1,0,'2026-09-09 18:12:04','2026-09-09 18:12:04'),(293,5,'Reacción inflamatoria','Frasco con recolección de heces 8.',8.00,1,0.00,0.00,30.00,1,0,'2026-09-09 18:12:29','2026-09-09 18:12:29'),(294,5,'Test benedit','Frasco con recolección de heces ',8.00,1,0.00,0.00,30.00,1,0,'2026-09-09 18:14:01','2026-09-09 18:14:01'),(295,5,'Test sudan ','Frasco con recolección de heces',8.00,1,0.00,0.00,30.00,1,0,'2026-09-09 18:14:29','2026-09-09 18:14:29'),(296,5,'test Graham ','Lamina con cinta adhesiva',15.00,1,0.00,0.00,45.00,1,0,'2026-09-09 18:15:19','2026-09-09 18:15:19'),(297,5,'Parasitosis x concentración (Faust)','Frasco con recolección de heces',10.00,1,0.00,0.00,40.00,1,0,'2026-09-09 18:15:45','2026-09-09 18:15:45'),(298,5,'Parasitosis x sedimentación (Faust)','Frasco con recolección de heces ',10.00,1,0.00,0.00,40.00,1,0,'2026-09-09 18:16:10','2026-09-09 18:16:10'),(299,2,'CULTIVO DE SEMEN','FRASCO RECOLECTOR',30.00,1,0.00,0.00,150.00,1,0,'2026-09-09 18:19:08','2026-09-09 18:19:08'),(300,2,'CULTIVO QUISTE BARTOLINO ','SECRECION ',30.00,1,0.00,0.00,140.00,1,0,'2026-09-09 18:22:07','2026-09-09 18:23:35'),(301,2,'Examen directo - Gram secreción vaginal','SECRECCIO',6.00,1,0.00,0.00,120.00,1,0,'2026-09-09 18:22:30','2026-09-09 18:22:30'),(302,2,'Examen directo - Gramsecreción uretral','SECRECION',8.00,1,0.00,0.00,100.00,1,0,'2026-09-09 18:22:58','2026-09-09 18:22:58'),(303,2,'Examen directo - Gram secreción faríngea','SECRECION ',10.00,1,0.00,0.00,100.00,1,0,'2026-09-09 18:23:27','2026-09-09 18:23:27'),(304,2,'Examen directo de secreciones','Tubo con NaCl y lamina porta objeto',10.00,1,0.00,0.00,140.00,1,0,'2026-09-09 18:24:07','2026-09-09 18:24:07'),(305,2,'Cultivos de secreciones + ATВ','Tubo con NaCl e hisopo ',30.00,1,0.00,0.00,150.00,1,0,'2026-09-09 18:24:36','2026-09-09 18:24:36'),(306,5,'Coprocultivo + ATB','Frasco con recolección de heces',30.00,1,0.00,0.00,60.00,1,0,'2026-09-09 18:26:29','2026-09-09 18:26:29'),(307,2,'Hemocultivo','Medio de cultivo con sangre',45.00,1,0.00,0.00,90.00,1,0,'2026-09-09 18:27:05','2026-09-09 18:27:05'),(308,2,'Investigación de Hongos/ácaros','Bisturí/hisopo/placa raspado hisopado',10.00,1,0.00,0.00,50.00,1,0,'2026-09-09 18:27:33','2026-09-09 18:27:33'),(309,2,'Bacilos copia','Placa Petri con muestra (Espécimen)',35.00,1,0.00,0.00,140.00,1,0,'2026-09-09 18:28:04','2026-09-09 18:28:04'),(310,2,'Investigación leishmania','Lamina con raspado de piel',15.00,1,0.00,0.00,70.00,1,0,'2026-09-09 18:28:44','2026-09-09 18:28:44'),(311,2,'Investigación malaria','Tubo/lamina con gota gruesa y frotis',15.00,1,0.00,0.00,70.00,1,0,'2026-09-09 18:29:55','2026-09-09 18:29:55'),(312,2,'Investigación bartonella','Lamina con frotis sanguíneo',15.00,1,0.00,0.00,70.00,1,0,'2026-09-09 18:30:18','2026-09-09 18:30:18'),(313,2,'Espermatograma','Frasco con Líquido seminal ',0.00,2,0.00,0.00,200.00,1,0,'2026-09-09 18:32:31','2026-09-09 18:32:31'),(314,2,'Test de helecho','Frasco con líquido amniótico',0.00,NULL,0.00,0.00,0.00,1,0,'2026-09-09 18:34:07','2026-09-09 18:34:07'),(315,2,'Frasco con líquido amniótico','',0.00,4,0.00,0.00,100.00,1,0,'2026-09-09 18:35:11','2026-09-09 18:35:11'),(316,4,'Dosaje de cocaína (COC)','Frasco con recolección de orina',30.00,1,0.00,0.00,100.00,1,0,'2026-09-09 18:37:11','2026-09-09 18:37:36'),(317,4,'Dosaje de éxtasis (MDMA)','Frasco con recolección de orina',30.00,1,0.00,0.00,110.00,1,0,'2026-09-09 18:38:02','2026-09-09 18:38:02'),(318,4,'Dosaje debenzodiacepinas','Frasco con recolección de orina',110.00,1,0.00,0.00,200.00,1,0,'2026-09-09 18:38:34','2026-09-09 18:38:34'),(319,4,'Dosaje de anfetaminas','Frasco con recolección de orina',110.00,1,0.00,0.00,200.00,1,0,'2026-09-09 18:39:07','2026-09-09 18:39:07'),(320,1,'Acetaminophen -paracetamol','tubo tapa roja',100.00,1,0.00,0.00,200.00,1,0,'2026-09-09 18:39:30','2026-09-09 18:39:30'),(321,7,'PERFIL LIPIDICO','Colesterol total - Colesterol HDL Colesterol LDL- Colesterol VLDL Triglicéridos',20.00,1,0.00,0.00,70.00,1,0,'2026-09-09 18:42:37','2026-09-09 18:42:37'),(322,7,'PERFIL HEPATICO','TGO-TGP - Fosfatasa alcalina Gamma glutamil transferasa Bilirrubina total y fraccionada Proteínas total y fraccionada(FRASCO ROJO)',45.00,1,0.00,0.00,110.00,1,0,'2026-09-09 18:43:20','2026-09-09 18:43:20'),(323,7,'PERFIL RENAL ','Urea - Creatinina Ácido úrico(TUBO ROJO)',20.00,1,0.00,0.00,60.00,1,0,'2026-09-09 18:43:52','2026-09-09 18:43:52'),(324,7,'PERFIL FISIOLOGICO','TAPA ROJA / MORADA',124.00,1,0.00,0.00,165.00,1,0,'2026-09-09 18:44:49','2026-09-09 18:45:52'),(325,7,'PERFIL REUMATICO','Hemograma completo Ácido úrico PCR - Factor reumatoideo',37.00,NULL,0.00,0.00,90.00,1,0,'2026-09-09 18:45:36','2026-09-09 18:45:36'),(326,7,'PERFIL COAGULACION','tubo tapa roja/ MORADA',58.00,1,0.00,0.00,100.00,1,0,'2026-09-09 18:46:19','2026-09-09 18:46:19'),(327,7,'PERFIL GESTANTE 1 ','tubo tapa roja/ MORADO',60.00,1,0.00,0.00,180.00,1,0,'2026-09-09 18:46:54','2026-09-09 18:46:54'),(328,7,'PERFIL GESTANTE 2','ROJA/ MORADA',50.00,1,0.00,0.00,150.00,1,0,'2026-09-09 18:47:24','2026-09-09 18:47:24'),(329,7,'PERFIL GESTANTE 3','ROPJA/ MORADA',15.00,1,0.00,0.00,60.00,1,0,'2026-09-09 18:47:47','2026-09-09 18:47:47'),(330,7,'PERFIL NEONATAL','Hemograma completo Grupo sanguíneo Factor Rh Glucosa VIH-Hepatitis B-RPR PCR cuantitativo',85.00,1,0.00,0.00,150.00,1,0,'2026-09-09 18:48:24','2026-09-09 18:48:24'),(331,7,'PERFIL PRE OPERATORIО','ROJA/ MORADA',50.00,1,0.00,0.00,250.00,1,0,'2026-09-09 18:49:02','2026-09-09 18:49:02'),(332,7,'PERFIL 21','',0.00,NULL,0.00,0.00,0.00,1,0,'2026-09-09 18:50:03','2026-09-09 18:50:03'),(333,7,'PERFIL TIROIDEO ','',66.00,1,0.00,0.00,150.00,1,0,'2026-09-09 18:50:50','2026-09-09 18:50:50'),(334,7,'PERFIL CARDIACO','',288.00,NULL,0.00,0.00,450.00,1,0,'2026-09-09 18:51:13','2026-09-09 18:51:13'),(335,7,'PERFIL TORCH','tapa amarilla / 2 tubs',650.00,2,0.00,0.00,700.00,1,0,'2026-09-09 18:52:10','2026-09-09 18:52:10'),(336,7,'PERFIL HORMONAL FEMENINO','ROJA/ MORADA',110.00,NULL,0.00,0.00,280.00,1,0,'2026-09-09 18:52:50','2026-09-09 18:52:50'),(337,7,'PERFIL HORMONAL MASCULINO','',115.00,1,0.00,0.00,230.00,1,0,'2026-09-09 18:53:46','2026-09-09 18:53:46'),(338,7,'PERFIL FERRICO','',140.00,1,0.00,0.00,230.00,1,0,'2026-09-09 18:54:10','2026-09-09 18:54:10'),(339,7,'PERFIL CHEQUEО','',64.00,NULL,0.00,0.00,120.00,1,0,'2026-09-09 18:54:37','2026-09-09 18:54:37'),(340,7,'PERFIL SINDROME OVARIO POLIQUISTICO1','tubo tapa roja',95.00,1,0.00,0.00,360.00,1,0,'2026-09-09 18:55:02','2026-09-09 18:55:02'),(341,7,'PERFIL SINDROME OVARIO POLIQUISTICO 2','tubo tapa roja',95.00,1,0.00,0.00,220.00,1,0,'2026-09-09 18:55:29','2026-09-09 18:55:29'),(342,7,'PERFIL DOWN','',350.00,NULL,0.00,0.00,800.00,1,0,'2026-09-09 18:55:55','2026-09-09 18:55:55'),(343,7,'PERFIL AMEU','',40.00,NULL,0.00,0.00,0.00,1,0,'2026-09-09 18:56:10','2026-09-09 18:56:10'),(344,7,'PERFIL PRE CLAMPSIA','',0.00,NULL,0.00,0.00,0.00,1,0,'2026-09-09 18:56:27','2026-09-09 18:56:27');
/*!40000 ALTER TABLE `examenes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historias_clinicas`
--

DROP TABLE IF EXISTS `historias_clinicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historias_clinicas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `paciente_id` int DEFAULT NULL,
  `dni` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `edad` int DEFAULT NULL,
  `sexo` varchar(2) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_orden` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `como_entero` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `obs_administrativas` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ocupacion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parentesco` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `apoderado` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `recomendado` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `dni` (`dni`),
  KEY `fk_historia_paciente` (`paciente_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `historias_clinicas_ibfk_1` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `historias_clinicas_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historias_clinicas`
--

LOCK TABLES `historias_clinicas` WRITE;
/*!40000 ALTER TABLE `historias_clinicas` DISABLE KEYS */;
/*!40000 ALTER TABLE `historias_clinicas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingresos_mercaderia`
--

DROP TABLE IF EXISTS `ingresos_mercaderia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingresos_mercaderia` (
  `id` int NOT NULL AUTO_INCREMENT,
  `proveedor_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `almacen` varchar(100) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Almacen Principal',
  `tipo_doc` enum('FACTURA','BOLETA','GUIA','OTRO') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'FACTURA',
  `serie` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `numero` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `moneda` enum('SOLES','DOLARES') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'SOLES',
  `fecha_ingreso` datetime NOT NULL,
  `fecha_factura` date DEFAULT NULL,
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `igv` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `observacion` text COLLATE utf8mb4_general_ci,
  `estado` enum('REGISTRADO','ANULADO') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'REGISTRADO',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ingmerc_proveedor` (`proveedor_id`),
  KEY `fk_ingmerc_usuario` (`usuario_id`),
  CONSTRAINT `fk_ingmerc_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`),
  CONSTRAINT `fk_ingmerc_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingresos_mercaderia`
--

LOCK TABLES `ingresos_mercaderia` WRITE;
/*!40000 ALTER TABLE `ingresos_mercaderia` DISABLE KEYS */;
/*!40000 ALTER TABLE `ingresos_mercaderia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ingresos_mercaderia_detalle`
--

DROP TABLE IF EXISTS `ingresos_mercaderia_detalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ingresos_mercaderia_detalle` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ingreso_mercaderia_id` int NOT NULL,
  `producto_id` int NOT NULL,
  `cantidad` decimal(12,2) NOT NULL,
  `precio_unitario` decimal(12,4) NOT NULL DEFAULT '0.0000',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `igv` decimal(12,2) NOT NULL DEFAULT '0.00',
  `total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `lote` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_vencimiento` date DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_ingmercdet_ingreso` (`ingreso_mercaderia_id`),
  KEY `fk_ingmercdet_producto` (`producto_id`),
  CONSTRAINT `fk_ingmercdet_ingreso` FOREIGN KEY (`ingreso_mercaderia_id`) REFERENCES `ingresos_mercaderia` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ingmercdet_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ingresos_mercaderia_detalle`
--

LOCK TABLES `ingresos_mercaderia_detalle` WRITE;
/*!40000 ALTER TABLE `ingresos_mercaderia_detalle` DISABLE KEYS */;
/*!40000 ALTER TABLE `ingresos_mercaderia_detalle` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_cuotas`
--

DROP TABLE IF EXISTS `invoice_cuotas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_cuotas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `numero_cuota` tinyint unsigned NOT NULL,
  `monto` decimal(12,2) NOT NULL,
  `fecha_vencimiento` date NOT NULL,
  `estado` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'PENDIENTE',
  `metodo_pago` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `caja_id` int DEFAULT NULL,
  `pagado_en` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_invoice_id` (`invoice_id`),
  CONSTRAINT `fk_invoice_cuotas_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_cuotas`
--

LOCK TABLES `invoice_cuotas` WRITE;
/*!40000 ALTER TABLE `invoice_cuotas` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_cuotas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_distribuciones`
--

DROP TABLE IF EXISTS `invoice_distribuciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_distribuciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `tipo` enum('LABORATORIO','MATERIALES') COLLATE utf8mb4_general_ci NOT NULL,
  `laboratorio_id` int DEFAULT NULL,
  `monto` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descripcion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_invoice_distribuciones_invoice` (`invoice_id`),
  KEY `fk_invoice_distribuciones_laboratorio` (`laboratorio_id`),
  CONSTRAINT `fk_invoice_distribuciones_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  CONSTRAINT `fk_invoice_distribuciones_laboratorio` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_distribuciones`
--

LOCK TABLES `invoice_distribuciones` WRITE;
/*!40000 ALTER TABLE `invoice_distribuciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_distribuciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoice_items`
--

DROP TABLE IF EXISTS `invoice_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoice_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `invoice_id` int NOT NULL,
  `tipo_item` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tratamiento_id` int DEFAULT NULL,
  `producto_id` int DEFAULT NULL,
  `examen_id` int DEFAULT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `nombre_sunat` varchar(250) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cantidad` decimal(10,2) NOT NULL DEFAULT '1.00',
  `valor_unitario` decimal(10,6) NOT NULL DEFAULT '0.000000',
  `precio_unitario` decimal(10,2) NOT NULL DEFAULT '0.00',
  `descuento` decimal(10,4) NOT NULL DEFAULT '0.0000',
  `descuento_tipo` varchar(20) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'porcentaje',
  `base_igv` decimal(10,2) NOT NULL DEFAULT '0.00',
  `igv` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `codigo_producto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'TRAT',
  `unidad` varchar(10) COLLATE utf8mb4_general_ci DEFAULT 'NIU',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_invoice_items_invoice` (`invoice_id`),
  KEY `fk_invoice_items_tratamiento` (`tratamiento_id`),
  KEY `fk_invoice_items_examen` (`examen_id`),
  CONSTRAINT `fk_invoice_items_examen` FOREIGN KEY (`examen_id`) REFERENCES `examenes` (`id`),
  CONSTRAINT `fk_invoice_items_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_invoice_items_tratamiento` FOREIGN KEY (`tratamiento_id`) REFERENCES `tratamientos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoice_items`
--

LOCK TABLES `invoice_items` WRITE;
/*!40000 ALTER TABLE `invoice_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoice_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `invoices`
--

DROP TABLE IF EXISTS `invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `company_id` int NOT NULL,
  `paciente_id` int DEFAULT NULL,
  `historia_clinica_id` int DEFAULT NULL,
  `doctor_id` int DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `caja_id` int DEFAULT NULL,
  `tipo_doc` varchar(2) COLLATE utf8mb4_general_ci NOT NULL COMMENT '01=factura,03=boleta',
  `serie` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `correlativo` int DEFAULT NULL,
  `cliente_tipo_doc` varchar(2) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT '1=DNI,6=RUC',
  `cliente_numero` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cliente_nombre` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cliente_direccion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cliente_email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `subtotal` decimal(10,2) DEFAULT '0.00',
  `igv` decimal(10,2) DEFAULT '0.00',
  `total` decimal(10,2) DEFAULT '0.00',
  `estado` varchar(30) COLLATE utf8mb4_general_ci DEFAULT 'BORRADOR',
  `forma_pago` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'CONTADO',
  `codigo_sunat` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion_sunat` text COLLATE utf8mb4_general_ci,
  `xml_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `cdr_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `hash_xml` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `daily_summary_id` int DEFAULT NULL,
  `enviado_sunat_at` datetime DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `cliente_facturacion_id` int DEFAULT NULL,
  `pdf_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_invoice_numero` (`company_id`,`serie`,`correlativo`),
  KEY `fk_invoices_company` (`company_id`),
  KEY `fk_invoices_paciente` (`paciente_id`),
  KEY `fk_invoices_historia` (`historia_clinica_id`),
  KEY `fk_invoices_daily_summary` (`daily_summary_id`),
  KEY `fk_invoices_doctor` (`doctor_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_invoices_caja_id` (`caja_id`),
  CONSTRAINT `fk_invoices_caja_id` FOREIGN KEY (`caja_id`) REFERENCES `cajas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_invoices_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`),
  CONSTRAINT `fk_invoices_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_invoices_historia` FOREIGN KEY (`historia_clinica_id`) REFERENCES `historias_clinicas` (`id`),
  CONSTRAINT `fk_invoices_paciente` FOREIGN KEY (`paciente_id`) REFERENCES `pacientes` (`id`),
  CONSTRAINT `fk_invoices_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `invoices`
--

LOCK TABLES `invoices` WRITE;
/*!40000 ALTER TABLE `invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `laboratorios`
--

DROP TABLE IF EXISTS `laboratorios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `laboratorios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `laboratorios`
--

LOCK TABLES `laboratorios` WRITE;
/*!40000 ALTER TABLE `laboratorios` DISABLE KEYS */;
INSERT INTO `laboratorios` VALUES (1,'Luigui',1,'2026-09-08 00:17:47','2026-09-08 00:17:47'),(2,'Suiza lab',1,'2026-09-08 00:17:54','2026-09-08 00:17:54'),(3,'Sisgenyca',1,'2026-09-08 00:18:03','2026-09-08 00:18:03'),(4,'Diagnóstic',1,'2026-09-08 00:18:12','2026-09-08 00:18:12'),(5,'FARMINDUSTRIA',1,'2026-09-09 00:19:49','2026-09-09 00:19:49');
/*!40000 ALTER TABLE `laboratorios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pacientes`
--

DROP TABLE IF EXISTS `pacientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pacientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `apellido` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono_celular` varchar(22) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estado` varchar(1) COLLATE utf8mb4_general_ci DEFAULT 'A',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pacientes`
--

LOCK TABLES `pacientes` WRITE;
/*!40000 ALTER TABLE `pacientes` DISABLE KEYS */;
/*!40000 ALTER TABLE `pacientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos_doctores_historial`
--

DROP TABLE IF EXISTS `pagos_doctores_historial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos_doctores_historial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `doctor_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date NOT NULL,
  `monto_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_comprobantes` int NOT NULL DEFAULT '0',
  `observaciones` text COLLATE utf8mb4_general_ci,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_pagos_doctores_historial_doctor` (`doctor_id`),
  KEY `fk_pagos_doctores_historial_user` (`user_id`),
  CONSTRAINT `fk_pagos_doctores_historial_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctores` (`id`),
  CONSTRAINT `fk_pagos_doctores_historial_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos_doctores_historial`
--

LOCK TABLES `pagos_doctores_historial` WRITE;
/*!40000 ALTER TABLE `pagos_doctores_historial` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagos_doctores_historial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos_doctores_historial_movimientos`
--

DROP TABLE IF EXISTS `pagos_doctores_historial_movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos_doctores_historial_movimientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pago_historial_id` int NOT NULL,
  `caja_movimiento_id` int NOT NULL,
  `invoice_id` int NOT NULL,
  `metodo_pago` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `base_doctor` decimal(10,2) NOT NULL DEFAULT '0.00',
  `monto_pagado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `conceptos` text COLLATE utf8mb4_general_ci,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pdhm_caja_movimiento` (`caja_movimiento_id`),
  KEY `fk_pdhm_pago_historial` (`pago_historial_id`),
  KEY `fk_pdhm_invoice` (`invoice_id`),
  CONSTRAINT `fk_pdhm_caja_movimiento` FOREIGN KEY (`caja_movimiento_id`) REFERENCES `caja_movimientos` (`id`),
  CONSTRAINT `fk_pdhm_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  CONSTRAINT `fk_pdhm_pago_historial` FOREIGN KEY (`pago_historial_id`) REFERENCES `pagos_doctores_historial` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos_doctores_historial_movimientos`
--

LOCK TABLES `pagos_doctores_historial_movimientos` WRITE;
/*!40000 ALTER TABLE `pagos_doctores_historial_movimientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagos_doctores_historial_movimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos_laboratorios_historial`
--

DROP TABLE IF EXISTS `pagos_laboratorios_historial`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos_laboratorios_historial` (
  `id` int NOT NULL AUTO_INCREMENT,
  `laboratorio_id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `fecha_desde` date NOT NULL,
  `fecha_hasta` date NOT NULL,
  `monto_total` decimal(10,2) NOT NULL DEFAULT '0.00',
  `total_comprobantes` int NOT NULL DEFAULT '0',
  `observaciones` text COLLATE utf8mb4_general_ci,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_plh_laboratorio` (`laboratorio_id`),
  KEY `fk_plh_user` (`user_id`),
  CONSTRAINT `fk_plh_laboratorio` FOREIGN KEY (`laboratorio_id`) REFERENCES `laboratorios` (`id`),
  CONSTRAINT `fk_plh_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos_laboratorios_historial`
--

LOCK TABLES `pagos_laboratorios_historial` WRITE;
/*!40000 ALTER TABLE `pagos_laboratorios_historial` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagos_laboratorios_historial` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pagos_laboratorios_historial_distribuciones`
--

DROP TABLE IF EXISTS `pagos_laboratorios_historial_distribuciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pagos_laboratorios_historial_distribuciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `pago_historial_id` int NOT NULL,
  `invoice_distribucion_id` int NOT NULL,
  `invoice_id` int NOT NULL,
  `monto_pagado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `conceptos` text COLLATE utf8mb4_general_ci,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_plhd_invoice_distribucion` (`invoice_distribucion_id`),
  KEY `fk_plhd_pago_historial` (`pago_historial_id`),
  KEY `fk_plhd_invoice` (`invoice_id`),
  CONSTRAINT `fk_plhd_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  CONSTRAINT `fk_plhd_invoice_distribucion` FOREIGN KEY (`invoice_distribucion_id`) REFERENCES `invoice_distribuciones` (`id`),
  CONSTRAINT `fk_plhd_pago_historial` FOREIGN KEY (`pago_historial_id`) REFERENCES `pagos_laboratorios_historial` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pagos_laboratorios_historial_distribuciones`
--

LOCK TABLES `pagos_laboratorios_historial_distribuciones` WRITE;
/*!40000 ALTER TABLE `pagos_laboratorios_historial_distribuciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `pagos_laboratorios_historial_distribuciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permisos`
--

DROP TABLE IF EXISTS `permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permisos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `controller` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_permiso` (`controller`,`action`)
) ENGINE=InnoDB AUTO_INCREMENT=322 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permisos`
--

LOCK TABLES `permisos` WRITE;
/*!40000 ALTER TABLE `permisos` DISABLE KEYS */;
INSERT INTO `permisos` VALUES (5,'Campañas','add','Agregar campaña','2026-01-25 18:44:18','2026-01-25 18:48:09'),(6,'Campañas','edit','Editar campaña','2026-01-25 18:44:18','2026-01-25 18:48:09'),(7,'Campañas','index','Listar campañas','2026-01-25 18:44:18','2026-01-25 18:48:09'),(8,'Campañas','view','Ver campaña','2026-01-25 18:44:18','2026-01-25 18:48:09'),(9,'Categorias','add','Agregar categoría','2026-01-25 18:36:52','2026-01-25 18:48:09'),(10,'Categorias','edit','Editar categoría','2026-01-25 18:36:52','2026-01-25 18:48:09'),(11,'Categorias','index','Listar categorías','2026-01-25 18:36:52','2026-01-25 18:48:09'),(12,'Categorias','view','Ver categoría','2026-01-25 18:36:52','2026-01-25 18:48:09'),(13,'Citas','add','Agregar cita','2026-01-25 18:36:52','2026-01-25 18:36:52'),(14,'Citas','edit','Editar cita','2026-01-25 18:36:52','2026-01-25 18:36:52'),(15,'Citas','index','Listar citas','2026-01-25 18:36:52','2026-01-25 18:36:52'),(16,'Citas','reportecitas','Ver reporte de citas','2026-01-25 18:36:52','2026-01-25 18:36:52'),(17,'Citas','view','Ver cita','2026-01-25 18:36:52','2026-01-25 18:36:52'),(18,'Consultas','add','Agregar consulta','2026-01-25 18:36:52','2026-01-25 18:36:52'),(19,'Consultas','edit','Editar consulta','2026-01-25 18:36:52','2026-01-25 18:36:52'),(20,'Consultas','index','Listar consultas','2026-01-25 18:36:52','2026-01-25 18:36:52'),(21,'Consultas','view','Ver detalle de consulta','2026-01-25 18:36:52','2026-01-25 18:36:52'),(26,'Departamentos','add','Agregar departamento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(27,'Departamentos','edit','Editar departamento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(28,'Departamentos','index','Listar departamentos','2026-01-25 18:36:52','2026-01-25 18:36:52'),(29,'Departamentos','view','Ver departamento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(30,'Doctores','add','Agregar doctor','2026-01-25 18:36:52','2026-01-25 18:36:52'),(31,'Doctores','edit','Editar doctor','2026-01-25 18:36:52','2026-01-25 18:36:52'),(32,'Doctores','index','Listar doctores','2026-01-25 18:36:52','2026-01-25 18:36:52'),(33,'Doctores','view','Ver doctor','2026-01-25 18:36:52','2026-01-25 18:36:52'),(34,'Documentos','add','Agregar documento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(35,'Documentos','edit','Editar documento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(36,'Documentos','index','Listar documentos','2026-01-25 18:36:52','2026-01-25 18:36:52'),(37,'Documentos','view','Ver documento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(42,'ExamenesFisicos','add','Agregar examen físico','2026-01-25 18:36:52','2026-01-25 18:48:09'),(43,'ExamenesFisicos','edit','Editar examen físico','2026-01-25 18:36:52','2026-01-25 18:48:09'),(44,'ExamenesFisicos','index','Listar exámenes físicos','2026-01-25 18:36:52','2026-01-25 18:48:09'),(45,'ExamenesFisicos','view','Ver examen físico','2026-01-25 18:36:52','2026-01-25 18:48:09'),(46,'HistoriasClinicas','add','Agregar historia clínica','2026-01-25 18:36:52','2026-01-25 18:48:09'),(47,'HistoriasClinicas','edit','Editar historia clínica','2026-01-25 18:36:52','2026-01-25 18:48:09'),(48,'HistoriasClinicas','index','Listar historias clínicas','2026-01-25 18:36:52','2026-01-25 18:48:09'),(49,'HistoriasClinicas','view','Ver historia clínica','2026-01-25 18:36:52','2026-01-25 18:48:09'),(50,'HorariosBloqueos','add','Agregar bloqueo de horarios','2026-01-25 18:36:52','2026-01-25 18:36:52'),(51,'HorariosBloqueos','edit','Editar bloqueo de horarios','2026-01-25 18:36:52','2026-01-25 18:36:52'),(52,'HorariosBloqueos','index','Listar bloqueos de horarios','2026-01-25 18:36:52','2026-01-25 18:36:52'),(53,'HorariosBloqueos','view','Ver bloqueo de horarios','2026-01-25 18:36:52','2026-01-25 18:36:52'),(54,'HorariosDoctores','add','Agregar horario doctor','2026-01-25 18:36:52','2026-01-25 18:36:52'),(55,'HorariosDoctores','edit','Editar horario doctor','2026-01-25 18:36:52','2026-01-25 18:36:52'),(56,'HorariosDoctores','editAll','Editar todos los horarios del doctor','2026-01-25 18:36:52','2026-01-25 18:36:52'),(57,'HorariosDoctores','index','Listar horarios doctores','2026-01-25 18:36:52','2026-01-25 18:36:52'),(58,'HorariosDoctores','view','Ver horario doctor','2026-01-25 18:36:52','2026-01-25 18:36:52'),(59,'Medicamentos','add','Agregar medicamento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(60,'Medicamentos','edit','Editar medicamento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(61,'Medicamentos','index','Listar medicamentos','2026-01-25 18:36:52','2026-01-25 18:36:52'),(62,'Medicamentos','view','Ver medicamento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(67,'Pacientes','add','Agregar paciente','2026-01-25 18:36:52','2026-01-25 18:36:52'),(68,'Pacientes','edit','Editar paciente','2026-01-25 18:36:52','2026-01-25 18:36:52'),(69,'Pacientes','index','Listar pacientes','2026-01-25 18:36:52','2026-01-25 18:36:52'),(70,'Pacientes','view','Ver detalle de paciente','2026-01-25 18:36:52','2026-01-25 18:36:52'),(71,'Permisos','add','Agregar permiso','2026-01-25 18:36:52','2026-01-25 18:36:52'),(72,'Permisos','edit','Editar permiso','2026-01-25 18:36:52','2026-01-25 18:36:52'),(73,'Permisos','index','Listar permisos','2026-01-25 18:36:52','2026-01-25 18:36:52'),(74,'Permisos','view','Ver permiso','2026-01-25 18:36:52','2026-01-25 18:36:52'),(75,'Procedimientos','add','Agregar procedimiento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(76,'Procedimientos','edit','Editar procedimiento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(77,'Procedimientos','index','Listar procedimientos','2026-01-25 18:36:52','2026-01-25 18:36:52'),(78,'Procedimientos','view','Ver procedimiento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(79,'Productos','add','Agregar producto','2026-01-25 18:36:52','2026-01-25 18:36:52'),(80,'Productos','edit','Editar producto','2026-01-25 18:36:52','2026-01-25 18:36:52'),(81,'Productos','index','Listar productos','2026-01-25 18:36:52','2026-01-25 18:36:52'),(82,'Productos','view','Ver producto','2026-01-25 18:36:52','2026-01-25 18:36:52'),(87,'Recetas','add','Agregar receta','2026-01-25 18:36:52','2026-01-25 18:36:52'),(88,'Recetas','edit','Editar receta','2026-01-25 18:36:52','2026-01-25 18:36:52'),(89,'Recetas','index','Listar recetas','2026-01-25 18:36:52','2026-01-25 18:36:52'),(90,'Recetas','view','Ver receta','2026-01-25 18:36:52','2026-01-25 18:36:52'),(91,'Roles','add','Agregar rol','2026-01-25 18:36:52','2026-01-25 18:36:52'),(92,'Roles','createWithDefaults','Crear rol con permisos por defecto','2026-01-25 18:36:52','2026-01-25 18:36:52'),(93,'Roles','edit','Editar rol','2026-01-25 18:36:52','2026-01-25 18:36:52'),(94,'Roles','index','Listar roles','2026-01-25 18:36:52','2026-01-25 18:36:52'),(95,'Roles','view','Ver rol','2026-01-25 18:36:52','2026-01-25 18:36:52'),(100,'Tratamientos','add','Agregar tratamiento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(101,'Tratamientos','edit','Editar tratamiento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(102,'Tratamientos','index','Listar tratamientos','2026-01-25 18:36:52','2026-01-25 18:36:52'),(103,'Tratamientos','view','Ver tratamiento','2026-01-25 18:36:52','2026-01-25 18:36:52'),(104,'Users','add','Agregar usuario','2026-01-25 18:36:52','2026-01-25 18:36:52'),(105,'Users','edit','Editar usuario','2026-01-25 18:36:52','2026-01-25 18:36:52'),(106,'Users','index','Listar usuarios','2026-01-25 18:36:52','2026-01-25 18:36:52'),(107,'Users','permisos','Gestionar permisos de usuario','2026-01-25 18:36:52','2026-01-25 18:36:52'),(108,'Users','view','Ver usuario','2026-01-25 18:36:52','2026-01-25 18:36:52'),(109,'ViasAdministracion','add','Agregar vía de administración','2026-01-25 18:36:52','2026-01-25 18:48:09'),(110,'ViasAdministracion','edit','Editar vía de administración','2026-01-25 18:36:52','2026-01-25 18:48:09'),(111,'ViasAdministracion','index','Listar vías de administración','2026-01-25 18:36:52','2026-01-25 18:48:09'),(112,'ViasAdministracion','view','Ver vía de administración','2026-01-25 18:36:52','2026-01-25 18:48:09'),(113,'VistaConsultasProcedimientos','index','Listar consultas procedimientos','2026-01-25 18:36:52','2026-01-25 18:36:52'),(114,'VistaConsultasProcedimientos','view','Ver consultas procedimientos','2026-01-25 18:36:52','2026-01-25 18:36:52'),(115,'VistaRecetasDepartamentos','index','Listar recetas por departamentos','2026-01-25 18:36:52','2026-01-25 18:36:52'),(116,'VistaRecetasDepartamentos','view','Ver recetas por departamentos','2026-01-25 18:36:52','2026-01-25 18:36:52'),(117,'VistaReporteConsultasDoctores','index','Listar reporte de consultas por doctores','2026-01-25 18:36:52','2026-01-25 18:36:52'),(118,'VistaReporteConsultasDoctores','view','Ver reporte de consultas por doctores','2026-01-25 18:36:52','2026-01-25 18:36:52'),(119,'VistaReportePacientes','index','Listar reporte de pacientes','2026-01-25 18:36:52','2026-01-25 18:36:52'),(120,'VistaReportePacientes','view','Ver reporte de pacientes','2026-01-25 18:36:52','2026-01-25 18:36:52'),(123,'Users','toggleStatus','Cambiar estado de usuario (Activo/Inactivo)','2026-01-25 19:10:13','2026-01-25 19:10:13'),(124,'Citas','citaDiaria','Ver citas del dia','2026-01-25 20:09:28','2026-01-31 12:12:59'),(125,'RecetasMedicamentos','index','Listar recetas medicamentos','2026-01-25 20:09:33','2026-01-25 20:09:33'),(126,'RecetasMedicamentos','add','Agregar receta medicamento','2026-01-25 20:09:33','2026-01-25 20:09:33'),(127,'RecetasMedicamentos','edit','Editar receta medicamento','2026-01-25 20:09:33','2026-01-25 20:09:33'),(128,'RecetasMedicamentos','view','Ver receta medicamento','2026-01-25 20:09:33','2026-01-25 20:09:33'),(129,'FormasFarmaceuticas','index','Listar formas farmaceuticas','2026-01-25 20:09:38','2026-01-25 20:09:38'),(130,'FormasFarmaceuticas','add','Agregar forma farmaceutica','2026-01-25 20:09:38','2026-01-25 20:09:38'),(131,'FormasFarmaceuticas','edit','Editar forma farmaceutica','2026-01-25 20:09:38','2026-01-25 20:09:38'),(132,'FormasFarmaceuticas','view','Ver forma farmaceutica','2026-01-25 20:09:38','2026-01-25 20:09:38'),(177,'Citas','marcarHoraLlegada','Marcar hora de llegada en cita','2026-01-31 12:00:26','2026-01-31 12:00:26'),(178,'Citas','restablecerCita','Restablecer estado de cita','2026-01-31 12:00:26','2026-01-31 12:00:26'),(179,'Citas','changeStatus','Cambiar estado de cita','2026-01-31 12:00:26','2026-01-31 12:00:26'),(180,'Presupuestos','add','Agregar presupuesto','2026-01-31 12:00:26','2026-01-31 12:12:24'),(181,'Presupuestos','edit','Editar presupuesto','2026-01-31 12:00:26','2026-01-31 12:12:27'),(182,'Presupuestos','view','Ver presupuesto','2026-01-31 12:00:26','2026-01-31 12:12:29'),(183,'Presupuestos','index','Listar presupuestos','2026-01-31 12:00:26','2026-01-31 12:12:31'),(193,'Cajas','add','Agregar caja','2026-02-23 02:09:05','2026-02-23 02:09:05'),(194,'Cajas','index','Listar cajas','2026-02-23 02:09:05','2026-02-23 02:09:05'),(195,'Cajas','view','Ver caja','2026-02-23 02:09:05','2026-02-23 02:09:05'),(196,'Cajas','abrirCaja','Abrir caja','2026-02-23 02:09:05','2026-02-23 02:09:05'),(197,'Cajas','cerrarCaja','Cerrar caja','2026-02-23 02:09:05','2026-02-23 02:09:05'),(198,'Cajas','reportePdf','Descargar reporte PDF de caja','2026-02-23 02:09:05','2026-02-23 02:09:05'),(204,'Pacientes','delete','Eliminar un paciente','2026-03-05 11:15:26','2026-03-05 11:15:26'),(205,'Recetas','exportPdf','Exportar receta a PDF','2026-04-14 13:14:48','2026-04-14 13:14:48'),(218,'Invoices','enviarResumenDiario','Enviar resumen diario a SUNAT','2026-05-01 00:24:12','2026-05-01 00:24:12'),(219,'CategoriasProductos','index','Ver listado de categorías de productos','2026-05-06 01:42:17','2026-05-06 01:42:17'),(220,'CategoriasProductos','add','Crear nueva categoría de producto','2026-05-06 01:42:17','2026-05-06 01:42:17'),(221,'CategoriasProductos','edit','Editar categoría de producto','2026-05-06 01:42:17','2026-05-06 01:42:17'),(222,'CategoriasProductos','delete','Eliminar categoría de producto','2026-05-06 01:42:17','2026-05-06 01:42:17'),(228,'Invoices','add','Crear nueva factura','2026-05-06 01:53:04','2026-05-06 01:53:04'),(229,'Invoices','view','Ver detalle de factura','2026-05-06 01:53:04','2026-05-06 01:53:04'),(230,'Invoices','index','Ver listado de facturas','2026-05-06 01:53:04','2026-05-06 01:53:04'),(231,'Invoices','emitir','Emitir factura','2026-05-06 01:53:04','2026-05-06 01:53:04'),(232,'Invoices','pdf','Generar PDF de factura','2026-05-06 01:53:04','2026-05-06 01:53:04'),(233,'Invoices','consultarResumen','Consultar resumen de facturas','2026-05-06 01:53:04','2026-05-06 01:53:04'),(234,'Recordatorios','add','Agregar recordatorio','2026-05-29 00:00:00','2026-05-29 00:00:00'),(235,'Recordatorios','edit','Editar recordatorio','2026-05-29 00:00:00','2026-05-29 00:00:00'),(236,'Recordatorios','index','Listar recordatorios','2026-05-29 00:00:00','2026-05-29 00:00:00'),(237,'Recordatorios','view','Ver recordatorio','2026-05-29 00:00:00','2026-05-29 00:00:00'),(238,'Recordatorios','delete','Eliminar recordatorio','2026-05-29 00:00:00','2026-05-29 00:00:00'),(239,'Recordatorios','getByPaciente','Obtener recordatorios por paciente','2026-05-29 00:00:00','2026-05-29 00:00:00'),(240,'Ingresos','index','Listar ingresos','2026-06-09 03:02:29','2026-06-09 03:02:29'),(241,'Ingresos','view','Ver detalle de ingreso','2026-06-09 03:02:29','2026-06-09 03:02:29'),(242,'Ingresos','add','Agregar ingreso','2026-06-09 03:02:29','2026-06-09 03:02:29'),(243,'Egresos','index','Listar egresos','2026-06-09 03:02:29','2026-06-09 03:02:29'),(244,'Egresos','view','Ver detalle de egreso','2026-06-09 03:02:29','2026-06-09 03:02:29'),(245,'Egresos','add','Agregar egreso','2026-06-09 03:02:29','2026-06-09 03:02:29'),(246,'PaquetesPagos','add','Agregar paquete de pago','2026-05-31 00:00:00','2026-05-31 00:00:00'),(247,'PaquetesPagos','edit','Editar paquete de pago','2026-05-31 00:00:00','2026-05-31 00:00:00'),(248,'PaquetesPagos','index','Listar paquetes de pago','2026-05-31 00:00:00','2026-05-31 00:00:00'),(249,'PaquetesPagos','view','Ver paquete de pago','2026-05-31 00:00:00','2026-05-31 00:00:00'),(250,'PaquetesPagos','delete','Eliminar paquete de pago','2026-05-31 00:00:00','2026-05-31 00:00:00'),(251,'PaquetesPagos','agregarCuotas','Agregar cuotas a paquete','2026-05-31 00:00:00','2026-05-31 00:00:00'),(252,'PaquetesPagos','registrarPago','Registrar pago de cuota','2026-05-31 00:00:00','2026-05-31 00:00:00'),(253,'RecordatorioControles','add','add','2026-05-23 11:44:46','2026-05-23 11:44:46'),(254,'RecordatorioControles','edit','edit','2026-05-23 11:44:46','2026-05-23 11:44:46'),(255,'RecordatorioControles','index','index','2026-05-23 11:45:04','2026-05-23 11:45:04'),(256,'RecordatorioControles','view','view','2026-05-23 11:45:04','2026-05-23 11:45:04'),(257,'RecordatorioControles','delete','delete','2026-05-23 12:26:56','2026-05-23 12:26:56'),(258,'RecordatorioControles','reportes','reportes','2026-05-23 12:31:37','2026-05-23 12:31:37'),(259,'Reportes','index','Ver reportes de comprobantes','2026-06-15 02:05:41','2026-06-15 02:05:41'),(260,'Laboratorios','index','Listar laboratorios','2026-07-28 21:28:43','2026-07-28 21:28:43'),(261,'Laboratorios','view','Ver detalle de laboratorio','2026-07-28 21:28:43','2026-07-28 21:28:43'),(262,'Laboratorios','add','Agregar laboratorio','2026-07-28 21:28:43','2026-07-28 21:28:43'),(263,'Laboratorios','edit','Editar laboratorio','2026-07-28 21:28:43','2026-07-28 21:28:43'),(264,'Laboratorios','delete','Eliminar laboratorio','2026-07-28 21:28:43','2026-07-28 21:28:43'),(265,'PagosDoctores','index','Ver reporte de pagos a doctores','2026-07-28 22:37:46','2026-07-28 22:37:46'),(266,'PagosDoctores','exportPdf','Exportar reporte de pagos a doctores (PDF)','2026-07-28 22:37:46','2026-07-28 22:37:46'),(267,'PagosDoctores','exportarExcel','Exportar reporte de pagos a doctores (Excel)','2026-07-28 22:37:46','2026-07-28 22:37:46'),(269,'PagosDoctores','historial','Ver historial de pagos a doctores','2026-07-28 22:37:46','2026-07-28 22:37:46'),(270,'PagosDoctores','historialDetalle','Ver detalle de un pago a doctor','2026-07-28 22:37:46','2026-07-28 22:37:46'),(271,'PagosDoctores','registrarPago','Ver pantalla de registro de pago a doctor','2026-07-28 23:24:15','2026-07-28 23:24:15'),(272,'PagosDoctores','guardarPago','Guardar pago a doctor','2026-07-28 23:24:15','2026-07-28 23:24:15'),(275,'Examenes','index','Listar ex├ímenes','2026-07-30 23:13:14','2026-07-30 23:13:14'),(276,'Examenes','view','Ver detalle de examen','2026-07-30 23:13:14','2026-07-30 23:13:14'),(277,'Examenes','add','Agregar examen','2026-07-30 23:13:14','2026-07-30 23:13:14'),(278,'Examenes','edit','Editar examen','2026-07-30 23:13:14','2026-07-30 23:13:14'),(279,'Examenes','delete','Eliminar examen','2026-07-30 23:13:14','2026-07-30 23:13:14'),(280,'CategoriasExamenes','index','Listar categor├¡as de ex├ímenes','2026-07-30 23:13:14','2026-07-30 23:13:14'),(281,'CategoriasExamenes','view','Ver detalle de categor├¡a de examen','2026-07-30 23:13:14','2026-07-30 23:13:14'),(282,'CategoriasExamenes','add','Agregar categor├¡a de examen','2026-07-30 23:13:14','2026-07-30 23:13:14'),(283,'CategoriasExamenes','edit','Editar categor├¡a de examen','2026-07-30 23:13:14','2026-07-30 23:13:14'),(284,'CategoriasExamenes','delete','Eliminar categor├¡a de examen','2026-07-30 23:13:14','2026-07-30 23:13:14'),(285,'Tratamientos','delete','Desactivar tratamiento','2026-07-30 23:45:21','2026-07-30 23:45:21'),(286,'Tratamientos','reactivar','Reactivar tratamiento','2026-07-30 23:45:21','2026-07-30 23:45:21'),(287,'Examenes','reactivar','Reactivar examen','2026-07-30 23:49:28','2026-07-30 23:49:28'),(288,'CategoriasExamenes','reactivar','Reactivar categor├¡a de examen','2026-07-30 23:53:39','2026-07-30 23:53:39'),(289,'PagosLaboratorios','index','Ver reporte de pagos a laboratorios','2026-08-08 11:37:26','2026-08-08 11:37:26'),(290,'PagosLaboratorios','registrarPago','Ver pantalla de registro de pago a laboratorio','2026-08-08 11:37:26','2026-08-08 11:37:26'),(291,'PagosLaboratorios','guardarPago','Guardar pago a laboratorio','2026-08-08 11:37:26','2026-08-08 11:37:26'),(292,'PagosLaboratorios','historial','Ver historial de pagos a laboratorios','2026-08-08 11:37:26','2026-08-08 11:37:26'),(293,'PagosLaboratorios','historialDetalle','Ver detalle de un pago a laboratorio','2026-08-08 11:37:26','2026-08-08 11:37:26'),(294,'Proveedores','index','Listar proveedores','2026-08-08 12:23:29','2026-08-08 12:23:29'),(295,'Proveedores','view','Ver detalle de proveedor','2026-08-08 12:23:29','2026-08-08 12:23:29'),(296,'Proveedores','add','Agregar proveedor','2026-08-08 12:23:29','2026-08-08 12:23:29'),(297,'Proveedores','edit','Editar proveedor','2026-08-08 12:23:29','2026-08-08 12:23:29'),(298,'Proveedores','delete','Eliminar proveedor','2026-08-08 12:23:29','2026-08-08 12:23:29'),(299,'PanelInventario','index','Ver panel de inventario','2026-08-08 12:48:04','2026-08-08 12:48:04'),(300,'PanelInventario','exportarExcel','Exportar reporte de inventario (Excel)','2026-08-08 12:48:04','2026-08-08 12:48:04'),(301,'PanelInventario','exportarPdf','Exportar reporte de inventario (PDF)','2026-08-08 12:48:04','2026-08-08 12:48:04'),(302,'PanelInventario','reporteGanancias','Ver reporte de ganancia por ventas de productos','2026-08-08 13:02:36','2026-08-08 13:02:36'),(303,'PanelInventario','exportarGananciasExcel','Exportar reporte de ganancia de productos (Excel)','2026-08-08 13:02:36','2026-08-08 13:02:36'),(304,'PanelInventario','exportarGananciasPdf','Exportar reporte de ganancia de productos (PDF)','2026-08-08 13:02:36','2026-08-08 13:02:36'),(305,'PagosDoctores','pdfPago','Descargar comprobante PDF de un pago a doctor','2026-08-18 23:22:49','2026-08-18 23:22:49'),(306,'PagosLaboratorios','pdfPago','Descargar comprobante PDF de un pago a laboratorio','2026-08-18 23:28:07','2026-08-18 23:28:07'),(307,'Finanzas','index','Ver panel consolidado de finanzas (ingresos, egresos, pagos)','2026-08-18 23:45:16','2026-08-18 23:45:16'),(308,'CategoriasProductos','view','Ver detalle de categorÃ­a de producto','2026-09-04 03:18:42','2026-09-04 03:18:42'),(309,'ProductoMovimientos','add','Registrar ingreso/egreso de stock','2026-09-04 03:18:55','2026-09-04 03:18:55'),(310,'ProductoMovimientos','historial','Ver historial de movimientos de stock','2026-09-04 03:18:55','2026-09-04 03:18:55'),(311,'Productos','delete','Desactivar producto','2026-09-09 03:05:11','2026-09-09 03:05:11'),(312,'CategoriasProductos','reactivar','Reactivar categorÃ­a de producto','2026-09-09 03:05:21','2026-09-09 03:05:21'),(313,'Productos','reactivar','Reactivar producto','2026-09-09 15:16:20','2026-09-09 15:16:20'),(314,'IngresosMercaderia','index','Listar ingresos de mercaderÃ­a','2026-09-10 04:28:34','2026-09-10 04:28:34'),(315,'IngresosMercaderia','view','Ver detalle de ingreso de mercaderÃ­a','2026-09-10 04:28:34','2026-09-10 04:28:34'),(316,'IngresosMercaderia','add','Registrar ingreso de mercaderÃ­a','2026-09-10 04:28:34','2026-09-10 04:28:34'),(317,'IngresosMercaderia','anular','Anular ingreso de mercaderÃ­a','2026-09-10 04:28:34','2026-09-10 04:28:34'),(321,'IngresosMercaderia','vencimientos','Reporte de vencimientos de mercaderÃ­a','2026-09-10 04:28:42','2026-09-10 04:28:42');
/*!40000 ALTER TABLE `permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `phinxlog`
--

DROP TABLE IF EXISTS `phinxlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `phinxlog` (
  `version` bigint NOT NULL,
  `migration_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_time` timestamp NULL DEFAULT NULL,
  `end_time` timestamp NULL DEFAULT NULL,
  `breakpoint` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `phinxlog`
--

LOCK TABLES `phinxlog` WRITE;
/*!40000 ALTER TABLE `phinxlog` DISABLE KEYS */;
/*!40000 ALTER TABLE `phinxlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuestos`
--

DROP TABLE IF EXISTS `presupuestos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuestos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `historia_id` int DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `notas` text COLLATE utf8mb4_general_ci,
  `tipo_de_cambio` decimal(10,2) DEFAULT NULL,
  `nombre_apellido` text COLLATE utf8mb4_general_ci,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paciente_id` (`historia_id`),
  CONSTRAINT `presupuestos_ibfk_1` FOREIGN KEY (`historia_id`) REFERENCES `historias_clinicas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuestos`
--

LOCK TABLES `presupuestos` WRITE;
/*!40000 ALTER TABLE `presupuestos` DISABLE KEYS */;
/*!40000 ALTER TABLE `presupuestos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuestos_invoices`
--

DROP TABLE IF EXISTS `presupuestos_invoices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuestos_invoices` (
  `id` int NOT NULL AUTO_INCREMENT,
  `presupuesto_id` int NOT NULL,
  `invoice_id` int NOT NULL,
  `monto_facturado` decimal(10,2) NOT NULL DEFAULT '0.00',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_presupuestos_invoices_presupuesto` (`presupuesto_id`),
  KEY `fk_presupuestos_invoices_invoice` (`invoice_id`),
  CONSTRAINT `fk_presupuestos_invoices_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`),
  CONSTRAINT `fk_presupuestos_invoices_presupuesto` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuestos_invoices`
--

LOCK TABLES `presupuestos_invoices` WRITE;
/*!40000 ALTER TABLE `presupuestos_invoices` DISABLE KEYS */;
/*!40000 ALTER TABLE `presupuestos_invoices` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuestos_tratamientos`
--

DROP TABLE IF EXISTS `presupuestos_tratamientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuestos_tratamientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `presupuesto_id` int NOT NULL,
  `tratamiento_id` int DEFAULT NULL,
  `tipo_item` varchar(20) NOT NULL DEFAULT 'tratamiento',
  `producto_id` int DEFAULT NULL,
  `examen_id` int DEFAULT NULL,
  `precio_unitario` decimal(10,2) NOT NULL,
  `cantidad` int NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `modified` datetime NOT NULL,
  `created` datetime NOT NULL,
  `observaciones` text,
  `descuento` int DEFAULT NULL,
  `descuento_tipo` varchar(20) NOT NULL DEFAULT 'porcentaje',
  PRIMARY KEY (`id`),
  KEY `presupuesto_id` (`presupuesto_id`),
  KEY `tratamiento_id` (`tratamiento_id`),
  KEY `fk_presupuestos_tratamientos_producto` (`producto_id`),
  KEY `fk_presupuestos_tratamientos_examen` (`examen_id`),
  CONSTRAINT `fk_presupuestos_tratamientos_examen` FOREIGN KEY (`examen_id`) REFERENCES `examenes` (`id`),
  CONSTRAINT `fk_presupuestos_tratamientos_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `presupuestos_tratamientos_ibfk_1` FOREIGN KEY (`presupuesto_id`) REFERENCES `presupuestos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `presupuestos_tratamientos_ibfk_2` FOREIGN KEY (`tratamiento_id`) REFERENCES `tratamientos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuestos_tratamientos`
--

LOCK TABLES `presupuestos_tratamientos` WRITE;
/*!40000 ALTER TABLE `presupuestos_tratamientos` DISABLE KEYS */;
/*!40000 ALTER TABLE `presupuestos_tratamientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `producto_movimientos`
--

DROP TABLE IF EXISTS `producto_movimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `producto_movimientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `producto_id` int NOT NULL,
  `usuario_id` int DEFAULT NULL,
  `ingreso_mercaderia_id` int DEFAULT NULL,
  `tipo` enum('ingreso','egreso') COLLATE utf8mb4_general_ci NOT NULL,
  `cantidad` decimal(10,2) NOT NULL,
  `stock_anterior` decimal(10,2) NOT NULL,
  `stock_nuevo` decimal(10,2) NOT NULL,
  `motivo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_producto_movimientos_producto` (`producto_id`),
  KEY `fk_producto_movimientos_usuario` (`usuario_id`),
  KEY `fk_prodmov_ingreso` (`ingreso_mercaderia_id`),
  CONSTRAINT `fk_prodmov_ingreso` FOREIGN KEY (`ingreso_mercaderia_id`) REFERENCES `ingresos_mercaderia` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_producto_movimientos_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`),
  CONSTRAINT `fk_producto_movimientos_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `producto_movimientos`
--

LOCK TABLES `producto_movimientos` WRITE;
/*!40000 ALTER TABLE `producto_movimientos` DISABLE KEYS */;
INSERT INTO `producto_movimientos` VALUES (1,102,2,NULL,'ingreso',100.00,200.00,300.00,'OOOO','2026-09-09 00:18:41','2026-09-09 00:18:41');
/*!40000 ALTER TABLE `producto_movimientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `categoria_producto_id` int NOT NULL,
  `proveedor_id` int DEFAULT NULL,
  `nombre` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `codigo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `codigo_sunat` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `unidad` varchar(10) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'NIU',
  `precio` decimal(10,2) NOT NULL DEFAULT '0.00',
  `precio_compra` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock` decimal(10,2) NOT NULL DEFAULT '0.00',
  `stock_minimo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `desactivado_por_categoria` tinyint(1) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_productos_categoria` (`categoria_producto_id`),
  KEY `proveedor_id` (`proveedor_id`),
  CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_producto_id`) REFERENCES `categorias_productos` (`id`),
  CONSTRAINT `fk_productos_proveedor` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedores` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=729 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (1,1,6,'TOMOGRAFIA ABDOMINOPELVICA CON CONTRASTE','','000001','','NIU',870.00,0.00,100.00,10.00,0,1,'2026-09-07 23:54:55','2026-09-07 23:54:55'),(2,7,NULL,'CLORURO  DE SODIO  0.9% X 12 FCO. BRAUN','','000002','','NIU',30.00,6.00,0.00,0.00,1,0,'2026-09-08 00:04:56','2026-09-08 00:04:56'),(3,5,NULL,'NYLON AZUL 3/0 HR 30  X 24 UNI','','000003','','NIU',8.00,3.33,0.00,0.00,1,0,'2026-09-08 00:05:54','2026-09-08 00:05:54'),(4,5,NULL,'GASA 7.5 X 7.5 X 20 SOBRES','','000004','','NIU',12.00,8.80,0.00,0.00,1,0,'2026-09-08 00:06:58','2026-09-08 00:06:58'),(5,7,NULL,'CLORURO DE SODIO 0.9% FCO.250 ML X 40 FCO.','','000005','','NIU',20.00,3.50,40.00,20.00,1,0,'2026-09-08 00:07:41','2026-09-09 08:57:48'),(6,2,NULL,'DETERGENTE DOFFI 1 KG','','000006','','NIU',9.00,6.50,0.00,0.00,1,0,'2026-09-08 00:08:36','2026-09-08 00:08:36'),(7,2,NULL,'LIMPIA VIDRIO 650 ML','','000007','','NIU',0.00,10.00,0.00,0.00,1,0,'2026-09-08 00:09:25','2026-09-08 00:09:25'),(8,2,NULL,'LAVAVAGILLA 500 ML','','000008','','NIU',0.00,0.00,0.00,0.00,1,0,'2026-09-08 00:09:45','2026-09-08 00:09:45'),(9,7,NULL,'DEXTROSA  33.3% X 20 ML AMP.','','000009','','NIU',9.00,1.50,0.00,0.00,1,0,'2026-09-08 00:10:33','2026-09-08 00:10:33'),(10,5,NULL,'VALBULA VAGINAL','','000010','','NIU',400.00,300.00,0.00,0.00,1,0,'2026-09-08 00:11:21','2026-09-08 00:11:21'),(11,5,NULL,'SUTURA V-LOC 0 COLOR-VERDE','','000011','','NIU',0.00,0.00,0.00,0.00,1,0,'2026-09-08 00:11:56','2026-09-08 00:12:57'),(12,5,NULL,'POLIPROPILENO AZUL 0 CT-1','','000012','','NIU',0.00,0.00,0.00,0.00,1,0,'2026-09-08 00:12:20','2026-09-08 00:12:20'),(13,5,NULL,'KIT DE TENSIOMETRO MANUALANEROIDE+ESTESTOCOPICO YUWELL','','000013','','NIU',0.00,59.97,0.00,0.00,1,0,'2026-09-08 00:13:56','2026-09-08 00:13:56'),(14,5,NULL,'TENSIOMETRO DIGITAL RIESTER  6 V DC. O 4 PILAS AA','','000014','','NIU',0.00,363.50,0.00,0.00,1,0,'2026-09-08 00:14:37','2026-09-08 00:14:37'),(15,5,NULL,'MALLA  BIO MESH 1.5 X 30 CM.(POLYPROPYLENO','','000015','','NIU',0.00,35.00,0.00,0.00,1,0,'2026-09-08 00:15:14','2026-09-08 00:15:14'),(16,7,NULL,'FUNGIMAX 150 MG. X 2 CAP.','','000016','','NIU',15.00,2.20,981.00,100.00,1,0,'2026-09-08 00:21:31','2026-09-09 09:06:44'),(17,7,NULL,'ITRACONAZOL 100 MG X 100 CAP. (PORTUGAL)','','000017','','NIU',3.00,1.15,100.00,100.00,1,0,'2026-09-08 00:22:05','2026-09-09 08:01:54'),(18,7,NULL,'DICLOFENACO 75 MG AMP.','','000018','','NIU',5.00,0.00,0.00,0.00,1,0,'2026-09-08 00:22:37','2026-09-08 00:22:37'),(19,7,NULL,'FLUZOL X 1 CAPSULA (FLUCONAZOL 150 MG. )','','000019','','NIU',15.00,2.80,0.00,0.00,1,0,'2026-09-08 00:23:11','2026-09-08 00:23:11'),(20,7,NULL,'FLAVOSEX 200MG X 30 CAP (FLAVOXATO)','','000020','','NIU',3.00,1.29,300.00,50.00,1,0,'2026-09-08 00:24:03','2026-09-08 22:12:06'),(21,5,NULL,'CATGUT CROMICO 2/0 MR35','','000021','','NIU',0.00,0.00,0.00,0.00,1,0,'2026-09-08 00:24:32','2026-09-08 00:24:32'),(22,5,NULL,'CATGUT CROMICO 2  HR 40 X 24','','000022','','NIU',0.00,0.00,0.00,0.00,1,0,'2026-09-08 00:25:05','2026-09-08 00:25:05'),(23,1,NULL,'OZONOTERAPIA DE MAMA X SECION','','000023','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 00:27:11','2026-09-08 00:27:11'),(24,7,NULL,'DEBLAX X 2 CAP.VITAMINA D3','','000024','','NIU',125.00,90.00,0.00,0.00,1,0,'2026-09-08 00:28:16','2026-09-08 00:28:16'),(25,6,NULL,'EKG EXAMEN','','000025','','NIU',50.00,0.00,0.00,0.00,0,1,'2026-09-08 00:28:41','2026-09-08 00:28:41'),(26,7,NULL,'NAYARA GEL HIDRATE. VAGINAL 30G.(DIMEXA)','','000026','','NIU',40.00,31.00,12.00,5.00,1,0,'2026-09-08 00:29:52','2026-09-09 00:15:07'),(27,8,NULL,'ADELANTO DE HIFU','','000027','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 00:36:05','2026-09-08 00:36:17'),(28,7,NULL,'CEFUROXIMA 500 MG. X 50 TB.(IQUIFARMA)','','000028','','NIU',3.00,1.65,100.00,50.00,1,0,'2026-09-08 00:36:44','2026-09-08 21:46:01'),(29,8,NULL,'ADELANTO DE RETIRO DE DIU','','000029','','NIU',50.00,0.00,0.00,0.00,0,1,'2026-09-08 00:37:12','2026-09-08 00:37:12'),(30,7,NULL,'AQUACIDE','','000030','','NIU',15.00,0.00,0.00,0.00,1,0,'2026-09-08 00:37:45','2026-09-08 00:37:45'),(31,1,NULL,'BIOPSIA DE MAMA','','000031','','NIU',650.00,95.00,0.00,0.00,0,1,'2026-09-08 00:38:17','2026-09-08 00:38:17'),(32,8,NULL,'ADELANTO DE BIOPSIA DE ENDOMETRIO','','000032','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 00:38:47','2026-09-08 00:38:47'),(33,7,NULL,'NEOLIFE 200MG+200MCG X 30 SOBRES','','000033','','NIU',80.00,0.00,16.00,5.00,1,0,'2026-09-08 00:39:26','2026-09-09 08:55:38'),(34,8,NULL,'ADELANTO DE COLPORRAFIA ANT Y POST','','000034','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 00:40:24','2026-09-08 00:40:24'),(35,7,NULL,'FLUCONGOL 150 MG X 2 CAP./FLUCONAZOL)','','000035','','NIU',15.00,1.90,0.00,0.00,1,0,'2026-09-08 00:41:03','2026-09-08 00:41:03'),(36,7,NULL,'ENITRAX 100 MG. X 14 CAP. (ITRACONAZOL) c/ cap.','','000036','','NIU',12.00,7.00,0.00,0.00,1,0,'2026-09-08 00:41:34','2026-09-08 00:41:34'),(37,2,NULL,'AMBIENTADOR SAPOLIO 360 ML ARRU.BEBE','','000037','','NIU',9.00,7.50,0.00,0.00,1,0,'2026-09-08 00:42:13','2026-09-08 00:42:13'),(38,2,NULL,'LEJIA SAPOLIO 1 GALON','','000038','','NIU',16.00,14.00,0.00,0.00,1,0,'2026-09-08 00:42:44','2026-09-08 00:42:44'),(39,9,NULL,'GRAMPAS 26/6 X500','','000039','','NIU',0.00,0.00,0.00,0.00,1,0,'2026-09-08 00:44:07','2026-09-08 00:44:07'),(40,7,NULL,'CREMA ACLARADORA INTIMA 30G.','','000040','','NIU',80.00,55.00,0.00,0.00,1,0,'2026-09-08 00:44:40','2026-09-08 00:44:40'),(41,8,NULL,'ADELANTO DE CONTROL PRENATAL','','000041','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 00:45:08','2026-09-08 00:45:08'),(42,7,NULL,'ACEITE OZONIZADO 50 ML','','000042','','NIU',130.00,35.00,0.00,0.00,1,0,'2026-09-08 00:45:46','2026-09-08 00:45:46'),(43,1,NULL,'BIOPSIA DE VULVA','','000043','','NIU',400.00,0.00,0.00,0.00,0,1,'2026-09-08 00:46:12','2026-09-08 00:46:12'),(44,7,NULL,'BLISSEL 50 MICROGRAMOS GEL VAGINAL','','000044','','NIU',60.00,41.40,0.00,0.00,1,0,'2026-09-08 00:46:46','2026-09-08 00:46:46'),(45,1,NULL,'EXTRACCION DE CONDILOMA CON LASER CO2 + PLASMA','','000045','','NIU',400.00,0.00,0.00,0.00,0,1,'2026-09-08 00:47:21','2026-09-08 00:47:47'),(46,7,NULL,'CLORURO DE SODIO 0.9%   FCO. 500 ML X 30','','000046','','NIU',25.00,4.00,240.00,60.00,1,0,'2026-09-08 00:48:55','2026-09-09 09:04:53'),(47,10,NULL,'PAQUETE BASICO CUOTA IV','','000047','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 00:49:46','2026-09-08 00:49:46'),(48,10,NULL,'PAQUETE BASICO CUOTA III','','000048','','NIU',300.00,0.00,0.00,0.00,0,1,'2026-09-08 00:50:16','2026-09-08 00:50:16'),(49,10,NULL,'PAQUETE BASICO CUOTA II','','000049','','NIU',600.00,0.00,0.00,0.00,0,1,'2026-09-08 00:50:41','2026-09-08 00:50:41'),(50,10,NULL,'PAQUETE BASICO CUOTA I','','000050','','NIU',700.00,0.00,0.00,0.00,0,1,'2026-09-08 00:51:09','2026-09-08 00:51:09'),(51,7,NULL,'ULTRA GUT FCO.DE 30 CAP.','','000051','','NIU',250.00,180.10,69.00,48.00,1,0,'2026-09-08 00:51:43','2026-09-08 21:42:37'),(52,7,NULL,'TERBISIL 1% CREMA 15G.','','000052','','NIU',55.00,34.00,0.00,0.00,1,0,'2026-09-08 00:52:18','2026-09-08 00:52:18'),(53,7,NULL,'BUTOZOL 2% CREMA VAGINAL X 15G.+APLICADOR','','000053','','NIU',55.00,35.20,45.00,10.00,1,0,'2026-09-08 00:52:48','2026-09-09 08:45:54'),(54,11,NULL,'PELOTA DE YOGA P/TERAPIA 65CM.(OBSTETRA)','','000054','','NIU',50.00,40.00,0.00,0.00,1,0,'2026-09-08 00:53:55','2026-09-08 00:53:55'),(55,1,NULL,'LAVADO CON OZONO Y CLORURO + INSUFLASION + PLASMA','','000055','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 00:54:26','2026-09-08 00:54:26'),(56,1,NULL,'DRENAJE DE ABCESO EN LABIO MAYOR','','000056','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 00:54:58','2026-09-08 00:54:58'),(57,8,NULL,'ADELANTO DE MAPEO ENDOMETRIOSIS','','000057','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 00:55:23','2026-09-08 00:55:23'),(58,1,NULL,'MAPEO DE ENDOMETRIOSIS','','000058','','NIU',250.00,0.00,0.00,0.00,0,1,'2026-09-08 00:55:49','2026-09-08 00:55:49'),(59,1,NULL,'VAPORIZACION LASER CO2 DE CERVIX','','000059','','NIU',800.00,0.00,0.00,0.00,0,1,'2026-09-08 00:56:16','2026-09-08 00:56:16'),(60,1,NULL,'LASER CO2+RPR','','000060','','NIU',900.00,0.00,0.00,0.00,0,1,'2026-09-08 00:56:41','2026-09-08 00:56:41'),(61,8,NULL,'ADELANTO DE COLPOSCOPIA','','000061','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 00:57:04','2026-09-08 00:57:04'),(62,12,NULL,'DESCARTE DE ITS','','000062','','NIU',30.00,0.00,0.00,0.00,1,0,'2026-09-08 00:58:19','2026-09-08 00:58:19'),(63,13,NULL,'ECOGRAFIA DOPPLER DE MAMA','','000063','','NIU',100.00,0.00,0.00,0.00,1,0,'2026-09-08 00:59:16','2026-09-08 00:59:30'),(64,8,NULL,'ADELANTO DE CRIOTERPIA','','000064','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 01:00:12','2026-09-08 01:00:12'),(65,8,NULL,'ADELANTO DE LABORATORIO','','000065','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 01:00:57','2026-09-08 01:00:57'),(66,8,NULL,'ADELANTO DE PAP','','000066','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 01:01:19','2026-09-08 01:01:19'),(67,1,NULL,'EXTRACCION DE QUISTE DE BARTOLINO EN SALA','','000067','','NIU',3000.00,0.00,0.00,0.00,0,1,'2026-09-08 01:05:11','2026-09-08 01:05:11'),(68,1,NULL,'DEBRIDACION DE ABCESO EN SALA','','000068','','NIU',2500.00,0.00,0.00,0.00,0,1,'2026-09-08 01:12:49','2026-09-08 01:12:49'),(69,1,NULL,'DEBRIDACION DE ABCESO LOCAL','','000069','','NIU',600.00,0.00,0.00,0.00,0,1,'2026-09-08 09:15:32','2026-09-08 09:15:32'),(70,1,NULL,'MARZUPIALIZACION EN SALA','','000070','','NIU',2500.00,0.00,0.00,0.00,0,1,'2026-09-08 09:18:46','2026-09-08 09:18:46'),(71,1,NULL,'MARZUPIALIZACION LOCAL','','000071','','NIU',600.00,0.00,0.00,0.00,0,1,'2026-09-08 09:20:26','2026-09-08 09:20:26'),(72,1,NULL,'PAPANICOLAU EN BASE LIQUIDA','','000072','','NIU',150.00,45.00,0.00,0.00,0,1,'2026-09-08 09:24:07','2026-09-08 09:26:30'),(73,3,NULL,'INFUSION TE / ANIZ / MANZANILLA X CAJA','','000073','','NIU',3.00,2.00,0.00,0.00,1,0,'2026-09-08 09:53:16','2026-09-08 09:53:16'),(74,3,NULL,'MOÑOS P/CABELLO','','000074','','NIU',4.00,3.00,0.00,0.00,1,0,'2026-09-08 09:54:22','2026-09-08 09:54:22'),(75,3,NULL,'SILICONA  FCO. 250 ML','','000076','','NIU',6.50,4.80,0.00,0.00,1,0,'2026-09-08 09:56:35','2026-09-08 09:56:35'),(76,3,NULL,'PORTAPAPEL MICA A4 X 10','','000077','','NIU',0.80,0.50,0.00,0.00,1,0,'2026-09-08 09:57:42','2026-09-08 09:57:42'),(77,5,NULL,'ALQUILER DE HISTEROSCOPIO','','000075','','NIU',1200.00,0.00,0.00,0.00,1,0,'2026-09-08 09:58:44','2026-09-08 09:59:08'),(78,9,NULL,'GRAPAS 26/6 X 5000 CAJA','','000078','','NIU',3.30,3.00,0.00,0.00,1,0,'2026-09-08 10:01:11','2026-09-08 10:01:11'),(79,9,NULL,'FOLDER C FASTER','','000079','','NIU',7.00,5.80,0.00,0.00,1,0,'2026-09-08 10:02:35','2026-09-08 10:02:35'),(80,9,NULL,'CLIPS DE METAL CAJA','','000080','','NIU',2.00,1.40,0.00,0.00,1,0,'2026-09-08 10:03:38','2026-09-08 10:04:14'),(81,3,NULL,'PAPEL DE REGALO PLIEGE','','000081','','NIU',0.80,0.42,0.00,0.00,1,0,'2026-09-08 10:06:08','2026-09-08 10:06:08'),(82,3,NULL,'PAPEL CREPE C. ROSAD-VERDE','','000082','','NIU',1.50,1.00,0.00,0.00,1,0,'2026-09-08 10:07:12','2026-09-08 10:07:12'),(83,3,NULL,'CAJITAS PAR CHOCOLATES-GESTANTES','','000083','','NIU',5.00,3.00,0.00,0.00,1,0,'2026-09-08 10:08:17','2026-09-08 10:08:17'),(84,2,NULL,'LIMPIA TODO 3 EN UNO-ANTIB,PERFUM,LIMPIA  GALON 4L.','','000084','','NIU',19.50,15.00,0.00,0.00,1,0,'2026-09-08 10:09:15','2026-09-08 10:09:15'),(85,1,NULL,'RETIRO DE POLIPO','','000085','','NIU',1200.00,0.00,0.00,0.00,0,1,'2026-09-08 10:10:21','2026-09-08 10:10:21'),(86,8,NULL,'ADELANTO DE EXTRAXION DE CONDILOMA','','000086','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 10:11:00','2026-09-08 10:11:00'),(87,7,NULL,'ACIDO FOLICO 0.5 mg x 100 tb.','','000087','','NIU',8.00,4.00,2.00,0.00,1,0,'2026-09-08 10:12:51','2026-09-08 23:49:16'),(88,7,NULL,'GENTAMICINA  160 MG X 30 AMP.(LIPD)','','000088','','NIU',6.00,0.50,150.00,50.00,1,0,'2026-09-08 10:13:57','2026-09-09 08:52:44'),(89,7,NULL,'CLINDAMICINA 600 MG. X 25 AMP (DANY )','','000089','','NIU',6.00,2.38,0.00,0.00,0,0,'2026-09-08 10:15:01','2026-09-09 00:35:16'),(90,7,NULL,'FLAVOXATO 200 MG X 30 TB.','','000090','','NIU',3.00,0.63,300.00,50.00,1,0,'2026-09-08 10:16:43','2026-09-08 22:12:56'),(91,1,NULL,'PLASMA RICO EN PLAQUETAS + OZONO','','000091','','NIU',350.00,0.00,0.00,0.00,0,1,'2026-09-08 10:18:00','2026-09-08 10:18:00'),(92,3,NULL,'ROLLO 5 X 10  PLASTICO','','000092','','NIU',13.00,10.00,0.00,0.00,1,0,'2026-09-08 10:19:10','2026-09-08 10:19:10'),(93,1,NULL,'MIOMECTOMIA ABIERTA','','000093','','NIU',7000.00,0.00,0.00,0.00,0,1,'2026-09-08 10:20:04','2026-09-08 10:20:04'),(94,1,NULL,'POLIPECTOMIA (CERVIX) + BIOPSIA','','000094','','NIU',600.00,0.00,0.00,0.00,0,1,'2026-09-08 10:20:40','2026-09-08 10:20:40'),(95,9,NULL,'CINTA DE EMBALAJE DE 200 YARDA GRANDE','','000095','','NIU',8.00,3.20,0.00,0.00,1,0,'2026-09-08 10:21:31','2026-09-08 10:21:31'),(96,2,NULL,'AROMATIZANTE DE AMBIENTE -GLADE SPTAY','','000096','','NIU',20.00,10.00,0.00,0.00,1,0,'2026-09-08 10:22:10','2026-09-08 10:22:10'),(97,6,1,'ADN FRAGMATICO','','000097','','NIU',600.00,0.00,0.00,0.00,0,1,'2026-09-08 10:23:27','2026-09-08 10:23:27'),(98,7,NULL,'TUBO DE VIDRIO x 100 und COLOR    LILA','','000098','','NIU',37.00,27.00,18.00,3.00,1,0,'2026-09-08 10:24:35','2026-09-09 08:39:56'),(99,7,NULL,'AGUA ESTERIL P/INJECCION LITRO','','000099','','NIU',10.00,7.00,30.00,12.00,1,0,'2026-09-08 10:25:22','2026-09-09 08:58:54'),(100,7,NULL,'AGUA OXIGENADA 10 VOL  X 1 LITRO','','000100','','NIU',8.00,4.00,4.00,2.00,1,0,'2026-09-08 10:26:03','2026-09-09 08:34:34'),(101,7,NULL,'TUBO EN Y','','000101','','NIU',48.00,38.00,0.00,0.00,1,0,'2026-09-08 10:26:44','2026-09-08 10:26:44'),(102,7,NULL,'ABOCAT # 16 X 100 UNID','','000102','','NIU',6.00,0.75,300.00,200.00,1,0,'2026-09-08 10:28:04','2026-09-09 00:18:41'),(103,3,NULL,'CARTULINA OPALINA X 25','','000103','','NIU',10.00,8.80,0.00,0.00,1,0,'2026-09-08 10:29:13','2026-09-08 10:29:13'),(104,2,NULL,'BOLSA DE BASURA  20 X 30 COLOR NEGRO','','000104','','NIU',10.00,7.00,0.00,0.00,1,0,'2026-09-08 10:30:20','2026-09-08 10:30:20'),(105,2,NULL,'BOLSA DE BASURA 20 X 30  COLOR ROJA/AMARILLO','','000105','','NIU',8.00,13.00,0.00,0.00,1,0,'2026-09-08 10:31:01','2026-09-08 10:31:01'),(106,3,NULL,'CUCHARITAS N° 5','','000106','','NIU',3.50,2.50,0.00,0.00,1,0,'2026-09-08 10:31:43','2026-09-08 10:31:43'),(107,9,NULL,'MASKIN ESCOLAR 24X18 YD','','000107','','NIU',3.00,4.00,0.00,0.00,1,0,'2026-09-08 10:33:29','2026-09-08 10:33:29'),(108,5,NULL,'TUBO DESTRUCTOR DE DE OZONO','','000108','','NIU',1000.00,900.00,0.00,0.00,1,0,'2026-09-08 10:34:13','2026-09-08 10:34:13'),(109,3,NULL,'BIDON DE AGUA','','000109','','NIU',14.00,10.00,0.00,0.00,1,0,'2026-09-08 10:35:34','2026-09-08 10:35:34'),(110,8,NULL,'ADELANTO DE LASER CO2','','000110','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 10:36:18','2026-09-08 10:36:18'),(111,8,NULL,'ADELANTO DE OZONOTERAPIA','','000111','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 10:36:52','2026-09-08 10:36:52'),(112,7,NULL,'ALBISEC ONE GRANULADO 2 G.','','000112','','NIU',60.00,45.10,70.00,20.00,1,0,'2026-09-08 10:38:33','2026-09-08 23:44:00'),(113,7,NULL,'PIETRA ED 2 MG. (DIENOGEST) X 28 TB.','','000113','','NIU',120.00,94.08,0.00,0.00,1,0,'2026-09-08 10:39:11','2026-09-08 10:39:11'),(114,2,NULL,'LEJIA  4 LITRO','','000114','','NIU',10.00,8.50,0.00,0.00,1,0,'2026-09-08 10:40:08','2026-09-08 10:40:08'),(115,3,NULL,'PORTA PAPEL A4','','000115','','NIU',1.00,0.50,0.00,0.00,1,0,'2026-09-08 10:40:56','2026-09-08 10:40:56'),(116,7,NULL,'PROBIOTICO X 30 OVULO (CRISPATO)','','000116','','NIU',300.00,60.00,0.00,0.00,1,0,'2026-09-08 10:41:47','2026-09-08 10:42:09'),(117,7,NULL,'TERBINAFINA 250 MG X 100 TB.','','000117','','NIU',3.00,0.34,100.00,50.00,1,0,'2026-09-08 10:43:02','2026-09-09 00:47:42'),(118,7,NULL,'METRONIDAZOL (METREXOL) X 100 ML AMP.','','000118','','NIU',12.00,3.00,12.00,5.00,1,0,'2026-09-08 10:43:58','2026-09-09 00:44:18'),(119,7,NULL,'ITRACONZOL 100 MG. X 100 CAP.','','000119','','NIU',3.00,1.15,0.00,0.00,0,0,'2026-09-08 10:44:33','2026-09-09 08:00:46'),(120,7,NULL,'SULFADIAZINA DE 400 GR. BOTE','','000120','','NIU',80.00,68.00,0.00,0.00,1,0,'2026-09-08 10:46:00','2026-09-08 10:46:00'),(121,7,NULL,'CATGU CROMICO 2/0 HR 35 X 24 UND.','','000121','','NIU',8.00,3.50,0.00,0.00,1,0,'2026-09-08 10:47:17','2026-09-08 10:47:17'),(122,7,NULL,'SULFADIAZINA 1% 50 GR.( SULFANIL)','','','000122','NIU',19.00,12.50,0.00,0.00,1,0,'2026-09-08 10:48:21','2026-09-08 10:48:21'),(123,3,NULL,'BOLSA C/ASA 12 X 16 X 100 BLANCO','','000123','','NIU',4.00,3.50,0.00,0.00,1,0,'2026-09-08 10:49:09','2026-09-08 10:49:09'),(124,7,NULL,'LIDOCAINA JALEA X 30 GR.','','000124','','NIU',18.00,9.00,0.00,0.00,1,0,'2026-09-08 10:50:07','2026-09-08 10:50:07'),(125,7,NULL,'SONDA FOLEY LATEX N° 20','','000125','','NIU',7.00,4.50,3.00,0.00,1,0,'2026-09-08 10:50:48','2026-09-09 09:14:09'),(126,7,NULL,'IODOPOVIDONA 10% X LT.','','000126','','NIU',40.00,30.00,2.00,1.00,1,0,'2026-09-08 10:51:31','2026-09-09 08:09:19'),(127,7,NULL,'IODOPOVIDONA 8.5 % X LT','','000127','','NIU',30.00,0.00,2.00,1.00,1,0,'2026-09-08 10:52:05','2026-09-09 08:10:02'),(128,7,NULL,'CIDEX OPA GALON (desinfectante)','','000128','','NIU',600.00,400.00,1.00,0.00,1,0,'2026-09-08 10:52:57','2026-09-09 08:36:32'),(129,2,NULL,'MANDIL CHAQUETA Y PANTALON DESCARTABLE','','000129','','NIU',7.00,4.00,0.00,0.00,1,0,'2026-09-08 10:53:42','2026-09-08 10:53:42'),(130,7,NULL,'TUBO DE VIDRIO TAPA CELESTE L. X 100','','000130','','NIU',36.00,29.00,13.00,3.00,1,0,'2026-09-08 10:54:19','2026-09-09 08:42:57'),(131,7,NULL,'ALCOHOL ISOPROPILICO X LT.','','000131','','NIU',40.00,35.00,10.00,4.00,1,0,'2026-09-08 10:55:22','2026-09-09 08:08:31'),(132,7,NULL,'MANDIL ESTERIL  QUIRURGICO T- M','','000132','','NIU',10.00,7.00,4.00,2.00,1,0,'2026-09-08 10:56:39','2026-09-09 09:19:49'),(133,7,NULL,'OVULO KENFIR 30','','000133','','NIU',200.00,110.00,0.00,0.00,1,0,'2026-09-08 10:57:34','2026-09-08 10:57:34'),(134,7,NULL,'ACEITE OZONIZADO  20 ML.','','000134','','NIU',100.00,35.00,0.00,0.00,1,0,'2026-09-08 10:58:13','2026-09-08 10:58:13'),(135,8,NULL,'ADELANTO PARA OZONO TERAPIA','','000135','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 10:59:12','2026-09-08 10:59:12'),(136,1,NULL,'SECION DE SILLA ELECTROMAGNETICA','','000136','','NIU',60.00,0.00,0.00,0.00,0,1,'2026-09-08 11:00:15','2026-09-08 11:00:15'),(137,1,NULL,'OZONO TERAPIA (LAVADO CON OZONO MAS CLORURO + INSUFLACION CON OZONO + PLASMA RICO EN PLAQUETAS CON OZONO)','','000137','','NIU',250.00,0.00,0.00,0.00,0,1,'2026-09-08 11:01:00','2026-09-08 11:01:00'),(138,7,NULL,'OVULO PLUS X 30 UND.','','000138','','NIU',200.00,110.00,0.00,0.00,1,0,'2026-09-08 11:01:59','2026-09-08 11:01:59'),(139,7,NULL,'BRUNELLE 2 MG+0.03 MG. X 21 COMPRIMIDO','','000139','','NIU',60.00,26.60,0.00,0.00,1,0,'2026-09-08 11:02:51','2026-09-08 11:02:51'),(140,7,NULL,'METFORMINA 850 MG X 100 TB.','','000140','','NIU',0.80,0.08,800.00,200.00,1,0,'2026-09-08 11:03:41','2026-09-09 00:57:11'),(141,7,NULL,'BLADOXATO 200 MG. X 20 TB. (FLAVOXATO  )','','000141','','NIU',3.00,1.35,100.00,50.00,1,0,'2026-09-08 11:04:25','2026-09-08 22:10:17'),(142,7,NULL,'BICERTO 150 MG. X 10 TB.','','000142','','NIU',6.00,3.00,470.00,100.00,1,0,'2026-09-08 11:05:23','2026-09-08 21:52:40'),(143,7,NULL,'BICERTO 150 MG. X 10 TB.','','000142','','NIU',6.00,3.00,0.00,0.00,1,0,'2026-09-08 11:05:24','2026-09-08 11:05:24'),(144,6,4,'PCR ITS TEST DE COBA ( VARON ) o° TEST DE COBA (VARON)','','000143','','NIU',450.00,220.00,0.00,0.00,0,1,'2026-09-08 11:10:08','2026-09-08 11:10:08'),(145,7,NULL,'MENOPUR 75 UI X 5 AMP.','','000144','','NIU',200.00,100.72,0.00,0.00,1,0,'2026-09-08 11:13:07','2026-09-08 11:13:07'),(146,7,NULL,'CHIP DE TESTOSTERONA 125 MG','','00014','','NIU',0.00,0.00,0.00,0.00,1,0,'2026-09-08 11:13:47','2026-09-08 11:13:47'),(147,7,NULL,'CHIP DE TESTOSTERONA 125 MG','','000145','','NIU',600.00,238.00,0.00,0.00,1,0,'2026-09-08 11:14:23','2026-09-08 11:14:23'),(148,7,NULL,'CHIP DE METFORMINA','','000146','','NIU',600.00,0.00,0.00,0.00,1,0,'2026-09-08 11:14:57','2026-09-08 11:14:57'),(149,8,NULL,'ADELANTO DE HISTEROSCOPIA','','','','NIU',1000.00,0.00,0.00,0.00,0,1,'2026-09-08 11:15:29','2026-09-08 11:15:29'),(150,7,NULL,'EXTENSION DESECHABLE ESTERIL ANDER MED','','000148','','NIU',20.00,0.00,0.00,0.00,1,0,'2026-09-08 11:16:08','2026-09-08 11:16:08'),(151,1,NULL,'HILOS TENSORES','','000149','','NIU',2500.00,0.00,0.00,0.00,0,1,'2026-09-08 11:17:13','2026-09-08 11:17:13'),(152,7,NULL,'CLERILAX 250 MG X 10 TB.','','000150','','NIU',5.00,2.30,400.00,100.00,1,0,'2026-09-08 11:17:50','2026-09-08 22:04:30'),(153,7,NULL,'DEQUAZOL -R  X 8 OVULOS','','000151','','NIU',10.00,6.75,0.00,0.00,1,0,'2026-09-08 11:18:37','2026-09-08 11:18:37'),(154,1,NULL,'ATENUACION DE CICATRIZ','','000152','','NIU',300.00,0.00,0.00,0.00,0,1,'2026-09-08 11:19:07','2026-09-08 11:19:36'),(155,1,NULL,'LABIOPLASTIA','','000153','','NIU',3500.00,0.00,0.00,0.00,0,1,'2026-09-08 11:20:14','2026-09-08 11:20:14'),(156,1,NULL,'BIOPSIA DE CONDILOMA','','000154','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 11:21:03','2026-09-08 11:21:03'),(157,1,NULL,'EXTRACION DE CONDILOMA','','000155','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 11:21:38','2026-09-08 11:21:38'),(158,1,NULL,'BLANQUEAMIENTOS  VULVAR','','000156','','NIU',350.00,0.00,0.00,0.00,0,1,'2026-09-08 12:06:59','2026-09-08 12:06:59'),(159,1,NULL,'VAPORIZACION','','000157','','NIU',600.00,0.00,0.00,0.00,0,1,'2026-09-08 12:07:37','2026-09-08 12:07:37'),(160,1,NULL,'PLASMA RICO EN PLAQUETAS','','000158','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 12:08:26','2026-09-08 12:08:26'),(161,1,NULL,'HIFU','','000159','','NIU',250.00,0.00,0.00,0.00,0,1,'2026-09-08 12:09:03','2026-09-08 12:09:03'),(162,1,NULL,'LASER CO2','','000160','','NIU',250.00,0.00,0.00,0.00,0,1,'2026-09-08 12:09:47','2026-09-08 12:09:47'),(163,7,NULL,'CYPLERSAMEX 20UI/ML AMP.','','000161','','NIU',200.00,0.00,0.00,0.00,1,0,'2026-09-08 12:11:12','2026-09-08 12:11:12'),(164,7,NULL,'HIDROCORTISONA 100 MG.POLVO SOL/INYECTABLE','','000162','','NIU',15.00,0.00,0.00,0.00,1,0,'2026-09-08 12:12:22','2026-09-08 12:12:22'),(165,7,NULL,'HIDROCORTISONA 250 MG.POLVO PARA SOL/INYECTABLE','','000163','','NIU',20.00,6.00,8.00,4.00,1,0,'2026-09-08 12:13:26','2026-09-09 00:57:47'),(166,7,NULL,'EUTIROX 25 MICROGRAMOS X 50 TB.','','000164','','NIU',0.00,0.64,3.00,0.00,1,0,'2026-09-08 12:14:09','2026-09-08 12:14:09'),(167,7,NULL,'JABON BABAY DOVE 75 GR.','','000165','','NIU',2.60,0.00,5.00,0.00,1,0,'2026-09-08 12:15:35','2026-09-08 12:15:35'),(168,1,NULL,'VAPORIZACION LASER DE VERRUGAS EN LABIOS MENORES + VAPORIZACION DE CERVIX','','000166','','NIU',800.00,0.00,0.00,0.00,0,1,'2026-09-08 12:16:32','2026-09-08 12:16:32'),(169,8,NULL,'ADELANTO VAPORIZACION LASER DE VERRUGA EN LABIOS MENORES+VAPORIZACIO DE CERVIX','','000167','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 12:18:09','2026-09-08 12:18:09'),(170,7,NULL,'DOXICICLINA 100 MG. X 100 CAP.','','000168','','NIU',0.16,0.00,700.00,100.00,1,0,'2026-09-08 12:19:04','2026-09-09 00:55:57'),(171,1,NULL,'CONSULTA DE FERTILIDAD','','000169','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 12:19:45','2026-09-08 12:19:45'),(172,7,NULL,'SONDA FOLEY # 8','','000170','','NIU',8.00,0.50,0.00,0.00,1,0,'2026-09-08 12:21:02','2026-09-08 12:21:02'),(173,7,NULL,'GASA ESTERIL 5 X 5','','000171','','NIU',20.00,18.00,4.00,2.00,1,0,'2026-09-08 12:21:31','2026-09-09 09:55:44'),(174,7,NULL,'BAJA LENGUA X 500','','000172','','NIU',20.00,18.00,15.00,4.00,1,0,'2026-09-08 12:22:05','2026-09-09 09:28:26'),(175,7,NULL,'PIZETA X 250 ML','','000173','','NIU',8.00,8.00,0.00,0.00,1,0,'2026-09-08 12:22:47','2026-09-08 12:22:47'),(176,7,NULL,'TEGADERM  10 X 12  X 50 UNID.','','000174','','NIU',5.00,3.60,0.00,0.00,1,0,'2026-09-08 12:23:28','2026-09-08 12:23:28'),(177,7,NULL,'AGUJA 30 G X 1/2 X 100','','000175','','NIU',1.00,25.00,1.00,0.00,1,0,'2026-09-08 12:24:22','2026-09-09 09:22:49'),(178,6,1,'PERFIL ROMA ','','000176','','NIU',450.00,0.00,0.00,0.00,0,1,'2026-09-08 12:25:16','2026-09-08 12:25:16'),(179,1,NULL,'HISTEROSCOPIA','','000177','','NIU',4500.00,0.00,0.00,0.00,0,1,'2026-09-08 12:26:02','2026-09-08 12:26:02'),(180,1,NULL,'EXTRACCION DE FORINCULO','','000178','','NIU',350.00,0.00,0.00,0.00,0,1,'2026-09-08 12:28:00','2026-09-08 12:28:00'),(181,7,NULL,'CAMPO DESCARTABLE PQTE. X 10 /CON DISEÑO','','000179','','NIU',4.00,3.00,0.00,0.00,1,0,'2026-09-08 12:30:29','2026-09-08 12:30:29'),(182,7,NULL,'PULVERIZADOR COMPLETO','','000180','','NIU',8.00,0.00,0.00,0.00,1,0,'2026-09-08 12:31:01','2026-09-08 12:31:01'),(183,7,NULL,'APLICADOR DE PULVERIZADOR','','000181','','NIU',3.00,3.00,0.00,0.00,1,0,'2026-09-08 12:31:38','2026-09-08 12:32:21'),(184,2,NULL,'TOALLA BLANCA DE 26 X 48 CM','','000182','','NIU',8.00,6.00,0.00,0.00,1,0,'2026-09-08 12:33:16','2026-09-08 12:33:16'),(185,2,NULL,'DISPENSADOR DE JABON LIQUIDO X 50 ML','','000183','','NIU',25.00,0.00,0.00,0.00,1,0,'2026-09-08 12:33:51','2026-09-08 12:33:51'),(186,9,NULL,'PERFORADOR K-40 HASTA 50 HOJAS','','000184','','NIU',184.00,0.00,0.00,0.00,1,0,'2026-09-08 12:34:36','2026-09-08 12:34:36'),(187,9,NULL,'TABLERO GRANDE ARTESCO','','000185','','NIU',10.00,0.00,0.00,0.00,1,0,'2026-09-08 12:35:14','2026-09-08 12:35:14'),(188,7,NULL,'INFEXIL PLUS X 30 COMPRIMIDO','','000186','','NIU',2.00,60.00,0.00,0.00,1,0,'2026-09-08 12:35:55','2026-09-08 12:35:55'),(189,7,NULL,'PROBIOTICO X 14 ( CRISPATO ) OVULO','','000187','','NIU',140.00,35.00,0.00,0.00,1,0,'2026-09-08 12:36:34','2026-09-08 12:36:34'),(190,7,NULL,'PROBIOTICOS X 7  (CRISPATO ) OVULO','','000188','','NIU',70.00,25.00,0.00,0.00,1,0,'2026-09-08 12:37:14','2026-09-08 12:37:14'),(191,6,1,'PERFIL TORCH  (TOX0PLASMA-RUBEOLA-HERPES 1-HERPES 2-CITOMEGALOVIRUS) EN IGG,IGM','','000189','','NIU',650.00,0.00,0.00,0.00,0,1,'2026-09-08 12:55:44','2026-09-08 12:55:44'),(192,9,NULL,'PERFORADOR ARTESCO MEDIANO - 208','','000190','','NIU',40.00,30.50,0.00,0.00,1,0,'2026-09-08 12:57:27','2026-09-08 12:57:27'),(193,2,NULL,'AMBIENTADOR ELECTRICO SAPOLIO 40 ML','','000191','','NIU',40.00,7.50,0.00,0.00,1,0,'2026-09-08 12:58:03','2026-09-08 12:58:03'),(194,2,NULL,'DESATORADOR DE BAÑO CHUPON','','000192','','NIU',6.00,4.00,0.00,0.00,1,0,'2026-09-08 12:58:46','2026-09-08 12:58:46'),(195,2,NULL,'RECOJEDOR','','000193','','NIU',3.00,0.00,5.00,0.00,1,0,'2026-09-08 12:59:38','2026-09-08 12:59:38'),(196,2,NULL,'GUANTE DE JEBE P/LAVAR 1 PAR','','000194','','NIU',8.00,5.50,0.00,0.00,1,0,'2026-09-08 13:00:24','2026-09-08 13:00:24'),(197,2,NULL,'TOALLA HIGIENICA LADY FREE X 30 UNID','','000195','','NIU',10.00,3.50,0.00,0.00,1,0,'2026-09-08 13:01:07','2026-09-08 13:01:07'),(198,6,NULL,'MATERIALES DE PROCEDIMIENTO','','000196','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 13:03:28','2026-09-08 13:03:28'),(199,7,NULL,'VITAMINA D TOTAL 25','','000197','','NIU',280.00,0.00,0.00,0.00,1,0,'2026-09-08 13:04:06','2026-09-08 13:04:06'),(200,6,6,'HISTEROSALPINOGRAFIA','','000198','','NIU',550.00,0.00,0.00,0.00,0,1,'2026-09-08 13:04:53','2026-09-08 13:04:53'),(201,6,1,'CLAMIDIA IGM','','000199','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 13:08:30','2026-09-08 13:08:30'),(202,8,NULL,'ADELANTO DE CAUTERIZACION','','000200','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 13:09:13','2026-09-08 13:09:13'),(203,3,NULL,'CAJA TERMOPOL CHICA ','','000200','','NIU',12.00,9.00,0.00,0.00,1,0,'2026-09-08 13:10:06','2026-09-08 13:10:06'),(204,5,NULL,'RECETARIO DEL MEDICO','','000202','','NIU',1.00,0.00,0.00,0.00,1,0,'2026-09-08 13:10:40','2026-09-08 13:10:40'),(205,3,NULL,'SOBRES PARA RESULTADOS DE LABORATORIO','','000203','','NIU',1.00,0.00,0.00,0.00,1,0,'2026-09-08 13:11:10','2026-09-08 13:11:10'),(206,7,NULL,'MOLIERI 20 X 24 TB.','','000204','','NIU',50.00,28.60,15.00,8.00,1,0,'2026-09-08 13:11:45','2026-09-09 00:04:21'),(207,4,NULL,'COLOCAR AMP. INTRAMUSCULAR','','000205','','NIU',5.00,0.00,0.00,0.00,0,1,'2026-09-08 13:12:25','2026-09-08 13:12:25'),(208,4,NULL,'COLOCACION DE MEDICAMENTO A LA VENA','','000206','','NIU',20.00,0.00,0.00,0.00,0,1,'2026-09-08 13:12:57','2026-09-08 13:12:57'),(209,7,NULL,'ERGOMETRINA AMPOLLA','','000207','','NIU',8.00,0.00,0.00,0.00,1,0,'2026-09-08 13:13:47','2026-09-08 13:13:47'),(210,7,NULL,'ESPALADRAPO 3M1 X 12 CHIQUITO','','000208','','NIU',8.00,4.59,24.00,12.00,1,0,'2026-09-08 13:14:10','2026-09-08 23:52:18'),(211,7,NULL,'LEVOCETIRIZINA 5 MG X 100 TB.','','000209','','NIU',3.00,0.30,300.00,100.00,1,0,'2026-09-08 13:15:32','2026-09-09 08:51:34'),(212,9,NULL,'LAPIZ CON BORRADOR ARTESCO','','000210','','NIU',2.00,1.50,0.00,0.00,1,0,'2026-09-08 13:16:12','2026-09-08 13:16:12'),(213,9,NULL,'CLIPS METALICO X 100 UNIDAD.','','000211','','NIU',3.00,2.50,0.00,0.00,1,0,'2026-09-08 13:16:50','2026-09-08 13:16:50'),(214,8,NULL,'ADELANTO DE ECO DE MAMA','','000212','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 13:17:27','2026-09-08 13:17:27'),(215,7,NULL,'FLUCOSTAT 200 MG X 1 TB.','','000213','','NIU',15.00,7.36,144.00,50.00,1,0,'2026-09-08 13:18:37','2026-09-09 07:55:21'),(216,3,NULL,'PAPEL LUSTRE COLOR VARIOS','','000214','','NIU',0.40,0.33,0.00,0.00,1,0,'2026-09-08 13:19:25','2026-09-08 13:19:25'),(217,3,NULL,'ARCHIVADOR LAY PLASTICO OF','','000215','','NIU',5.00,4.70,0.00,0.00,1,0,'2026-09-08 13:20:04','2026-09-08 13:20:04'),(218,3,NULL,'ENGRAMPADOR VIKINGO 25','','000216','','NIU',6.00,5.80,0.00,0.00,1,0,'2026-09-08 13:20:30','2026-09-08 13:20:30'),(219,7,NULL,'NAT D  VITAMINA D 1000 IU X 60','','000217','','NIU',40.00,36.00,10.00,5.00,1,0,'2026-09-08 13:21:15','2026-09-08 23:38:10'),(220,6,2,'MICROALBUMINERAL 24 HS','','000218','','NIU',65.00,0.00,0.00,0.00,0,1,'2026-09-08 13:52:40','2026-09-08 13:52:40'),(221,6,2,'TSH ULTRASENSIBLE','','000219','','NIU',60.00,0.00,0.00,0.00,0,1,'2026-09-08 13:53:32','2026-09-08 13:53:32'),(222,6,2,'TGO','','000220','','NIU',35.00,0.00,0.00,0.00,0,1,'2026-09-08 13:54:49','2026-09-08 13:54:49'),(223,6,2,'CREATINA','','000221','','NIU',35.00,0.00,0.00,0.00,0,1,'2026-09-08 13:55:42','2026-09-08 13:55:42'),(224,6,2,'ACIDO URICO','','000222','','NIU',35.00,0.00,0.00,0.00,0,1,'2026-09-08 13:56:29','2026-09-08 13:56:29'),(225,6,2,'TES DE HELECHO','','000223','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 13:56:59','2026-09-08 13:56:59'),(226,6,NULL,'CONSULTA ENDOCRINO','','000224','','NIU',120.00,0.00,0.00,0.00,0,1,'2026-09-08 13:57:40','2026-09-08 13:57:40'),(227,7,NULL,'ACIDO POLIGLICOLICO  1 MR 40 X 24 UND','','000225','','NIU',6.00,5.84,72.00,24.00,1,0,'2026-09-08 13:58:41','2026-09-09 09:45:46'),(228,7,NULL,'ACIDO POLIGLICOLICO 3/0 DS 35 X 24 UND.','','000226','','NIU',12.00,5.83,120.00,24.00,1,0,'2026-09-08 13:59:27','2026-09-09 09:44:04'),(229,7,NULL,'LINO 1 QUIRURGUICO MULTIEMPAQUE  0 X 24 UND','','000227','','NIU',4.50,3.50,96.00,24.00,1,0,'2026-09-08 14:00:06','2026-09-09 09:31:02'),(230,7,NULL,'NYLON AZUL 3/0 DS 25 X 24 UND.','','000228','','NIU',4.00,3.33,0.00,0.00,1,0,'2026-09-08 14:00:38','2026-09-08 14:00:38'),(231,3,NULL,'PAPEL PARA MONITOR M -ONOME ROLLO PLANO','','000229','','NIU',15.00,0.00,16.00,0.00,1,0,'2026-09-08 14:01:06','2026-09-08 14:01:37'),(232,7,NULL,'MANDIL DECARTABLE T-M','','000230','','NIU',3.00,2.00,0.00,0.00,1,0,'2026-09-08 14:02:25','2026-09-08 14:02:25'),(233,7,NULL,'SONDA NELATON N° 14','','000231','','NIU',6.00,1.80,0.00,0.00,1,0,'2026-09-08 14:02:59','2026-09-08 14:02:59'),(234,7,NULL,'GORRO DESCART.ENFERMERA C/BLANCO X 100 IND.','','000232','','NIU',11.00,10.00,0.00,0.00,1,0,'2026-09-08 14:03:35','2026-09-08 14:03:35'),(235,2,NULL,'DETERGENTE SUNNY DE 5 KG.','','000233','','NIU',40.00,0.00,0.00,0.00,1,0,'2026-09-08 14:04:25','2026-09-08 14:04:25'),(236,9,NULL,'PLASTILINA  X 12 VIKINGO','','000234','','NIU',3.90,3.50,0.00,0.00,1,0,'2026-09-08 14:05:06','2026-09-08 14:05:06'),(237,9,NULL,'CINTA DE EMBALAJE 100','','000235','','NIU',5.00,4.30,0.00,0.00,1,0,'2026-09-08 14:05:45','2026-09-08 14:05:45'),(238,7,NULL,'PULVERIZADOR COMPLETO','','000236','','NIU',6.50,6.50,0.00,0.00,1,0,'2026-09-08 14:06:23','2026-09-08 14:06:23'),(239,4,NULL,'CURACION DE HERIDA','','000237','','NIU',20.00,0.00,0.00,0.00,0,1,'2026-09-08 14:06:59','2026-09-08 14:06:59'),(240,6,6,'HISTEROSONOGRAFIA','','000238','','NIU',250.00,0.00,0.00,0.00,0,1,'2026-09-08 14:07:39','2026-09-08 14:07:39'),(241,1,NULL,'DEBRIDACION','','000239','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 14:08:52','2026-09-08 14:08:52'),(242,1,NULL,'EXTRACCION DE CONDILOMA MAS BIOPSIA','','000240','','NIU',400.00,0.00,0.00,0.00,0,1,'2026-09-08 14:09:34','2026-09-08 14:09:34'),(243,7,NULL,'ENAT VITAMIN E TUBO 50 GR.','','000241','','NIU',30.00,13.73,0.00,0.00,1,0,'2026-09-08 14:10:22','2026-09-08 14:10:22'),(244,7,NULL,'NAT C 1000 X 30 TB.','','000242','','0',40.00,20.60,0.00,0.00,1,0,'2026-09-08 14:11:42','2026-09-08 14:11:42'),(245,1,NULL,'CONTROL ECOGRAFICO','','000243','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 14:12:19','2026-09-08 14:12:19'),(246,6,NULL,'SOLICITUD DE PATOLOOGIA','','000244','','NIU',50.00,0.00,0.00,0.00,0,1,'2026-09-08 14:12:53','2026-09-08 14:12:53'),(247,6,2,'CULTIVO  DE SECRECION  PROSTATICA','','000245','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 14:13:34','2026-09-08 14:13:34'),(248,6,NULL,'TENSIOMETRO MONITOR ELECTRONICO DE PRECION ARTERIAL YUWELL','','000246','','NIU',230.00,225.00,0.00,0.00,0,1,'2026-09-08 14:15:05','2026-09-08 14:15:05'),(249,7,NULL,'NIFEDIPINO 10 MG','','000247','000247','NIU',10.00,0.00,0.00,0.00,1,0,'2026-09-08 14:33:09','2026-09-08 16:17:02'),(250,3,NULL,'MAQUILLAJE COLOR AL AGUA','','000248','','NIU',20.00,0.00,0.00,0.00,1,0,'2026-09-08 14:33:41','2026-09-08 14:33:41'),(251,3,NULL,'CAJITAS PARA USB','','000249','','NIU',8.00,1.20,0.00,0.00,1,0,'2026-09-08 14:34:09','2026-09-08 14:34:09'),(252,9,NULL,'DELINEADOR PLOMO / BLANCO','','000250','','NIU',15.00,10.00,0.00,0.00,1,0,'2026-09-08 14:34:46','2026-09-08 14:34:46'),(253,9,NULL,'VINIFAN OFICIO','','000251','','NIU',10.00,0.60,0.00,0.00,1,0,'2026-09-08 14:35:27','2026-09-08 14:35:27'),(254,9,NULL,'CRAYOLA LAY JUMBO SOFIA PUPP X 8','','000252','','NIU',20.00,15.00,0.00,0.00,1,0,'2026-09-08 14:36:16','2026-09-08 14:36:16'),(255,3,NULL,'CHOCOLATE DE 320 G.  NESTLE (MULTIPACK) PAQUETE X 40 BOMBO SURTID','','000253','','NIU',30.00,25.00,0.00,0.00,1,0,'2026-09-08 14:36:53','2026-09-08 14:36:53'),(256,3,NULL,'LABIAL LIQUIDO IMPORTADO','','000254','','NIU',6.00,3.00,0.00,0.00,1,0,'2026-09-08 14:37:24','2026-09-08 14:37:24'),(257,5,NULL,'LAPIZ DE ELECTOCAUTERIO','','000255','','NIU',20.00,5.50,0.00,0.00,1,0,'2026-09-08 14:38:00','2026-09-08 14:38:00'),(258,6,2,'CULTIVO DE LACTOBACILUS','','000256','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 14:38:37','2026-09-08 14:38:37'),(259,6,2,'HEPATITIS \"B\"HBSAG ANTIGENO AUSTRALIANO','','000257','','NIU',45.00,0.00,0.00,0.00,0,1,'2026-09-08 14:39:16','2026-09-08 14:39:16'),(260,6,2,'CULTIVO DE CANDIASIS','','000258','','NIU',120.00,0.00,0.00,0.00,0,1,'2026-09-08 14:39:45','2026-09-08 14:39:45'),(261,3,NULL,'PAPEL DE REGALO PARA BEBE UNISET','','000259','','NIU',2.00,8.00,0.00,0.00,1,0,'2026-09-08 14:40:30','2026-09-08 14:40:30'),(262,3,NULL,'PORTA RETRATO DE 29.5 CM. X 21 CM.','','000260','','NIU',12.00,0.00,0.00,0.00,1,0,'2026-09-08 14:40:53','2026-09-08 14:40:53'),(263,3,NULL,'PORTA RETRATO DE 8\" X 10 P/ GESTANTES','','000261','','NIU',20.00,16.00,0.00,0.00,1,0,'2026-09-08 14:41:33','2026-09-08 14:41:33'),(264,8,NULL,'ADELANTO DEL 20% DE HISTEROCTOMIA RADICAL+ SALPINGUECTOMIA+OFORECTOMIA','','000262','','NIU',2400.00,0.00,0.00,0.00,0,1,'2026-09-08 14:42:34','2026-09-08 14:42:34'),(265,1,NULL,'HISTERECTOMIA RADICAL+SALPINGUECTOMIA+OFORECTOMIA','','000263','','NIU',120000.00,0.00,0.00,0.00,0,1,'2026-09-08 14:43:24','2026-09-08 14:43:24'),(266,6,2,'ACIDO BILIAR','','000264','','NIU',120.00,0.00,0.00,0.00,0,1,'2026-09-08 14:44:00','2026-09-08 14:44:00'),(267,7,NULL,'MIRENA (LEVONORGSTREL 20 UG )','','000265','','NIU',900.00,503.80,0.00,0.00,1,0,'2026-09-08 14:44:33','2026-09-08 14:44:33'),(268,6,2,'PROLACTINA','','000266','','NIU',70.00,0.00,0.00,0.00,0,1,'2026-09-08 14:45:00','2026-09-08 14:45:00'),(269,7,NULL,'LET 2.5 MG X 30 TB (LETROZOL)  ( letrovitae )','','000267','','NIU',10.00,3.62,0.00,0.00,1,0,'2026-09-08 14:45:53','2026-09-08 14:45:53'),(270,7,NULL,'MERIOFERT 75UI AMP.','','000268','','NIU',140.00,82.00,18.00,8.00,1,0,'2026-09-08 14:46:28','2026-09-08 23:45:04'),(271,7,NULL,'CHORIOMON 5000UI AMP.','','000269','','NIU',220.00,133.80,6.00,3.00,1,0,'2026-09-08 14:47:02','2026-09-09 00:38:03'),(272,7,NULL,'CICLOSTERORA FUERTE+JERINGA X 1 AMP.','','000270','','NIU',70.00,37.00,0.00,0.00,1,0,'2026-09-08 14:47:45','2026-09-08 14:47:45'),(273,6,2,'CULTIVO DE SECRECION DE ABSESO DE BARTOLINA','','000271','','NIU',120.00,0.00,0.00,0.00,0,1,'2026-09-08 14:48:21','2026-09-08 14:49:00'),(274,7,NULL,'KETOPAN 100 MG X 25 AMP. IV.(KETOPROFNO)','','000272','','NIU',8.00,2.40,0.00,0.00,1,0,'2026-09-08 14:49:26','2026-09-08 14:49:26'),(275,7,NULL,'GINOTHYL X 6 OVULOS','','000273','','NIU',10.00,3.88,288.00,60.00,1,0,'2026-09-08 14:49:53','2026-09-08 21:58:41'),(276,7,NULL,'YASMIN RECUBIERTA  CAJA X 21 TB.','','000274','','NIU',50.00,35.50,0.00,0.00,1,0,'2026-09-08 14:50:24','2026-09-08 14:50:24'),(277,7,NULL,'VAGISTEN 0.5 MG X 10 OVULOS','','000275','','NIU',10.00,4.90,0.00,0.00,1,0,'2026-09-08 14:50:53','2026-09-08 14:50:53'),(278,7,NULL,'VAGISTEN CREMA VAGINAL 15 GR.','','000276','','NIU',50.00,33.30,0.00,0.00,1,0,'2026-09-08 14:51:20','2026-09-08 14:51:20'),(279,7,NULL,'ESTUDIO DE PATOLOGUIA','','000277','','NIU',200.00,0.00,0.00,0.00,1,0,'2026-09-08 14:52:12','2026-09-08 14:52:12'),(280,1,NULL,'ECOGRAFIA RESERVA OVARICA','','000278','','NIU',120.00,0.00,0.00,0.00,0,1,'2026-09-08 14:53:01','2026-09-08 14:53:01'),(281,6,2,'AGA+ELECTROLITOS','','000297','','NIU',140.00,0.00,0.00,0.00,0,1,'2026-09-08 14:53:46','2026-09-08 14:53:46'),(282,7,NULL,'ACIDO POLIGLICOLICO 1 HR 35 X 24 SOBRES','','000280','','NIU',8.00,0.00,0.00,0.00,1,0,'2026-09-08 14:54:33','2026-09-08 14:54:33'),(283,7,NULL,'ACIDO POLIGLICOLICO 3/0 DS 30 X 24 SOBRES','','000281','','NIU',8.00,0.00,0.00,0.00,1,0,'2026-09-08 14:55:04','2026-09-08 14:55:04'),(284,7,NULL,'HISTEROCTOMIA VAGINAL+COLPORRAFIA','','000282','','NIU',6500.00,0.00,0.00,0.00,1,0,'2026-09-08 14:55:35','2026-09-08 14:55:35'),(285,8,NULL,'ADELANTO 20% HISTEROCTOMIA VAGINAL+COLPORRAFIA','','000283','','NIU',1300.00,0.00,0.00,0.00,0,1,'2026-09-08 14:56:07','2026-09-08 14:56:07'),(286,8,NULL,'ADELANTO MAPEO PARA ENDOMETRIOSIS','','000284','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 14:56:35','2026-09-08 14:56:35'),(287,1,NULL,'MAPEO PARA ENDOMETRIOSIS','','000285','','NIU',120.00,0.00,0.00,0.00,0,1,'2026-09-08 14:57:04','2026-09-08 14:57:04'),(288,7,NULL,'GOFEN 400 MG X 60 CAP.BLANDA (IBUPROFENO )','','000286','','NIU',3.00,0.60,180.00,60.00,1,0,'2026-09-08 14:57:41','2026-09-09 08:05:13'),(289,7,NULL,'Eddad (vitamina E )  X 30 CAPSULA','','000287','','NIU',50.00,30.00,18.00,5.00,1,0,'2026-09-08 14:58:08','2026-09-08 23:40:29'),(290,8,NULL,'ADELANTO DE BIOXIA DE CERVIX','','000288','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 14:58:38','2026-09-08 14:58:38'),(291,8,NULL,'ADELANTO DE BIOXIA DE CONDILOMA','','000289','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 14:59:09','2026-09-08 14:59:09'),(292,8,NULL,'ADELANTO DE TEST DE COBA','','000290','','NIU',50.00,0.00,0.00,0.00,0,1,'2026-09-08 14:59:40','2026-09-08 14:59:40'),(293,6,4,'CITOLOGUIA LIQUIDA','','000291','','NIU',20.00,0.00,0.00,0.00,0,1,'2026-09-08 15:00:57','2026-09-08 15:00:57'),(294,7,NULL,'T DE PLATA ANDALAN  (SILVERFLEX CU  380 ) COBRE Y PLATA','','000292','','NIU',650.00,150.00,0.00,0.00,1,0,'2026-09-08 15:01:30','2026-09-08 15:01:30'),(295,7,NULL,'FEMME 200 ML x 48 und. (PROBIOWLLNESS )','','000293','','NIU',0.00,50.83,144.00,48.00,1,0,'2026-09-08 15:02:18','2026-09-09 09:00:06'),(296,7,NULL,'TUBO DE VIDRIO x 100 und.TAPA AMARILLO X 100 UNID','','000294','','NIU',50.00,40.00,7.00,2.00,1,0,'2026-09-08 15:02:51','2026-09-09 08:41:12'),(297,7,NULL,'TUBO DE VIDRIO TAPA ROJA X  100 UNID','','000295','','NIU',35.00,27.00,16.00,3.00,1,0,'2026-09-08 15:03:19','2026-09-09 08:42:03'),(298,3,NULL,'MUÑERAS DE CABELLO','','000296','','NIU',1.00,0.00,0.00,0.00,1,0,'2026-09-08 15:03:55','2026-09-08 15:03:55'),(299,9,NULL,'PILA AA X 2 UNID.','','000297','','NIU',2.50,0.00,0.00,0.00,1,0,'2026-09-08 15:04:27','2026-09-08 15:04:27'),(300,9,NULL,'PILA AAA X 2 UNID','','000298','','NIU',2.00,0.00,0.00,0.00,1,0,'2026-09-08 15:04:54','2026-09-08 15:04:54'),(301,2,NULL,'ESCOBASA','','000298','','NIU',14.00,10.50,0.00,0.00,1,0,'2026-09-08 15:05:30','2026-09-08 15:05:30'),(302,2,NULL,'TRAPEADOR DE TELA','','000300','','NIU',5.00,5.00,0.00,0.00,1,0,'2026-09-08 15:06:11','2026-09-08 15:06:11'),(303,2,NULL,'PAPEL SECADOR DE MANO 550 MT ODET','','000301','','NIU',13.00,13.00,0.00,0.00,1,0,'2026-09-08 15:06:40','2026-09-08 15:06:40'),(304,2,NULL,'LIMPIADOR  MULTIUSO EN ESPUMA 750 ML SPRAY','','000302','','NIU',15.00,0.00,0.00,0.00,1,0,'2026-09-08 15:07:06','2026-09-08 15:07:06'),(305,2,NULL,'LIMPIATODO  DKASA GALON 4 LT.','','000303','','NIU',18.00,15.00,0.00,0.00,1,0,'2026-09-08 15:07:49','2026-09-08 15:07:49'),(306,9,NULL,'RESALTADOR AMARILLO','','000304','','NIU',1.80,1.40,0.00,0.00,1,0,'2026-09-08 15:08:20','2026-09-08 15:08:20'),(307,9,NULL,'CORRECTOR 9 ML ARTESCO','','000305','','NIU',1.50,1.20,0.00,0.00,1,0,'2026-09-08 15:08:59','2026-09-08 15:08:59'),(308,9,NULL,'LAPICERO ROJO Y AZUL','','000306','','NIU',0.60,0.00,0.60,0.00,1,0,'2026-09-08 15:09:29','2026-09-08 15:09:29'),(309,3,NULL,'CARAMELOS SURTIDOS FIESTA X 1 KG.','','000307','','NIU',18.00,13.50,0.00,0.00,1,0,'2026-09-08 15:10:13','2026-09-08 15:10:13'),(310,7,NULL,'CLORELASE PLUS 15 GR. UNG.','','000308','','NIU',35.00,24.00,0.00,0.00,1,0,'2026-09-08 15:10:47','2026-09-08 15:10:47'),(311,7,NULL,'BIOZIN GALON 3.785 ML','','000309','','NIU',80.00,0.00,2.00,1.00,1,0,'2026-09-08 15:11:18','2026-09-09 08:12:12'),(312,7,NULL,'LUGOL GINECOLOGICO LITRO','','000310','','NIU',50.00,130.00,7.00,2.00,1,0,'2026-09-08 15:11:46','2026-09-09 08:10:56'),(313,7,NULL,'ACIDO ACETICO LITRO','','000311','','NIU',30.00,45.00,8.00,2.00,1,0,'2026-09-08 15:12:22','2026-09-09 08:11:29'),(314,7,NULL,'MASCARILLA DESCARTABLE X 50 UNID','','000312','','NIU',6.00,3.50,6.00,3.00,1,0,'2026-09-08 15:16:41','2026-09-09 09:59:53'),(315,7,NULL,'AGUJA # 18 X 1 X 100','','000313','','NIU',0.50,0.00,1.00,0.00,1,0,'2026-09-08 15:17:37','2026-09-09 07:59:26'),(316,2,NULL,'AMBIENTADOR ORION  SPRAY 400 ML','','000314','','NIU',9.00,6.50,0.00,0.00,1,0,'2026-09-08 15:18:11','2026-09-08 15:18:11'),(317,2,NULL,'JABON LIQUIDO GALON 3.78 ML','','000315','','NIU',18.00,9.90,0.00,0.00,1,0,'2026-09-08 15:18:38','2026-09-08 15:18:38'),(318,3,NULL,'CONTOMETRO TERMICO ROLLO  55 GRS.','','000316','','NIU',7.00,4.50,0.00,0.00,1,0,'2026-09-08 15:19:26','2026-09-08 15:19:26'),(319,2,NULL,'PAPEL INDICADOR PH 0.14 X 100 UND.','','000317','','NIU',18.00,15.00,0.00,0.00,1,0,'2026-09-08 15:20:01','2026-09-08 15:20:01'),(320,7,NULL,'GASA ESTERIL 10 X 10 CM X 50 UNID','','000318','','NIU',2.00,0.60,200.00,200.00,1,0,'2026-09-08 15:20:33','2026-09-09 08:17:33'),(321,7,NULL,'COMPRESA DE GASA ESTERIL 48 X 48 CM','','000319','','NIU',13.00,8.00,117.00,20.00,1,0,'2026-09-08 15:21:10','2026-09-09 08:21:55'),(322,7,NULL,'MASCARA DE OXIGNO C /BOLSA R. ADULTO','','000320','','NIU',8.00,4.50,7.00,3.00,1,0,'2026-09-08 15:21:33','2026-09-09 08:20:42'),(323,7,NULL,'NYLON AZUL 3/0 DS 30 X 24 HILOS','','000321','','NIU',6.00,3.40,0.00,0.00,1,0,'2026-09-08 15:22:07','2026-09-08 15:22:07'),(324,7,NULL,'LINO QUIRURGICO  O MULTI.1 10 X 75 750CM X 24','','000322','','NIU',6.00,3.55,24.00,0.00,1,0,'2026-09-08 15:22:34','2026-09-09 09:30:13'),(325,7,NULL,'ACIDO POLIGLICOLICO 1 HR 40 X  24 HILOS','','000323','','NIU',8.00,5.62,120.00,24.00,1,0,'2026-09-08 15:23:50','2026-09-09 09:44:48'),(326,7,NULL,'SONDA FOLEY N° 14 DE 2 VIAS','','000324','','NIU',6.00,2.50,10.00,10.00,1,0,'2026-09-08 15:24:41','2026-09-09 09:17:41'),(327,7,NULL,'GEL ULTRASONIDO X GALON','','000325','','NIU',40.00,45.00,12.00,3.00,1,0,'2026-09-08 15:25:15','2026-09-09 09:27:36'),(328,7,NULL,'VENDA ELASTICA 3 X 5 YARDA','','000326','','NIU',3.50,0.00,0.00,0.00,1,0,'2026-09-08 15:25:43','2026-09-08 15:25:43'),(329,7,NULL,'VENDA ELASTICA 4 X 5 YARDA','','000327','','NIU',3.50,0.00,0.00,0.00,1,0,'2026-09-08 15:26:13','2026-09-08 15:26:13'),(330,7,NULL,'VENDA ELASTICA  6 X 5 YARDAS','','000328','','NIU',3.50,1.80,40.00,20.00,1,0,'2026-09-08 15:26:56','2026-09-09 08:19:28'),(331,8,NULL,'ADELANTO DE ECO TRASVAGINAL.','','000329','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 15:27:24','2026-09-08 15:27:24'),(332,1,NULL,'COLPORRAFIA ANTRO+POSTRIOR','','000330','','NIU',5000.00,0.00,0.00,0.00,0,1,'2026-09-08 15:27:51','2026-09-08 15:27:51'),(333,8,NULL,'ADELANTO 20%  COLPORRAFIA','','000331','','NIU',1000.00,0.00,0.00,0.00,0,1,'2026-09-08 15:28:25','2026-09-08 15:28:25'),(334,9,NULL,'BOLSA DE PAPEL KRAFT # 4','','000332','','NIU',0.13,0.14,0.00,0.00,1,0,'2026-09-08 15:29:00','2026-09-08 15:29:00'),(335,7,NULL,'CATGUT CROMICO 1 HR 40 CIRCULO REDONDO X 24','','000333','','NIU',8.00,3.25,168.00,24.00,1,0,'2026-09-08 15:29:30','2026-09-09 09:34:00'),(336,7,NULL,'CATGUT CROMICO 2/0 HR 40 CIRCULO REDONDO X 24','','000334','','NIU',8.00,3.42,168.00,24.00,1,0,'2026-09-08 15:30:05','2026-09-09 09:32:18'),(337,8,NULL,'ADELANTO 20% HISTERECTOMIA ABDOMINAL TOTAL+SALPING.+FORECTOMIA','','000335','','NIU',1800.00,0.00,0.00,0.00,0,1,'2026-09-08 15:30:41','2026-09-08 15:30:41'),(338,1,NULL,'HISTERECTOMIA ABDOMINAL TOTAL+SALPINGESTOMIA +FORECTOMIA','','000336','','NIU',9000.00,0.00,0.00,0.00,0,1,'2026-09-08 15:31:16','2026-09-08 15:31:16'),(339,6,NULL,'CONSULTA PSICOLOGIA','','000337','','NIU',80.00,0.00,0.00,0.00,0,1,'2026-09-08 15:31:46','2026-09-08 15:31:46'),(340,5,NULL,'INFORME MEDICO','','000338','','NIU',100.00,0.00,0.00,0.00,1,0,'2026-09-08 15:32:34','2026-09-08 15:32:34'),(341,7,NULL,'GINNA CREMA X 40 GR','','000339','','NIU',75.00,45.93,64.00,20.00,1,0,'2026-09-08 15:33:07','2026-09-09 00:34:06'),(342,8,NULL,'ADELANTO DEL 20% DE LA CIRUGUIA QUISTECTOMIA TOTAL','','000340','','NIU',1800.00,0.00,0.00,0.00,0,1,'2026-09-08 15:33:35','2026-09-08 15:33:35'),(343,8,NULL,'GARANTIA','','000341','','NIU',1600.00,0.00,0.00,0.00,0,1,'2026-09-08 15:34:05','2026-09-08 15:34:05'),(344,1,NULL,'CIRUGUIA DE QUISTECTOMIA TOTAL','','000342','','NIU',9000.00,0.00,0.00,0.00,0,1,'2026-09-08 15:34:36','2026-09-08 15:34:36'),(345,1,NULL,'CAUTERIZACION','','000343','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 15:35:04','2026-09-08 15:35:04'),(346,7,NULL,'LIDOCAINA 2%  X 20 ML. AMPOLLA','','000344','','NIU',5.00,3.44,34.00,10.00,1,0,'2026-09-08 15:35:37','2026-09-09 08:45:05'),(347,6,3,'BIOXIA DE CONDILOMA','','000345','','NIU',350.00,0.00,0.00,0.00,0,1,'2026-09-08 15:36:12','2026-09-08 15:36:12'),(348,1,NULL,'HISTERECTOMIA LAPAROSCOPICA','','000346','','NIU',8500.00,0.00,0.00,0.00,0,1,'2026-09-08 15:36:50','2026-09-08 15:36:50'),(349,8,NULL,'ADLANTO DE QUISTECTOMIA LAPAROSCOPICA 20 %','','000347','','NIU',1200.00,0.00,0.00,0.00,0,1,'2026-09-08 15:37:22','2026-09-08 15:37:22'),(350,1,NULL,'QUISTECTOMIA LAPAROSCOPICA','','000348','','NIU',6000.00,0.00,0.00,0.00,0,1,'2026-09-08 15:37:52','2026-09-08 15:37:52'),(351,3,NULL,'VASOS DESCARTABLE  X 50 N \" 6.5 ONZA','','000349','','NIU',2.50,1.90,0.00,0.00,1,0,'2026-09-08 15:38:31','2026-09-08 15:38:31'),(352,1,NULL,'EPISIORRAFIA','','000350','','NIU',50.00,0.00,0.00,0.00,0,1,'2026-09-08 15:39:04','2026-09-08 15:39:04'),(353,7,NULL,'AGUA ESTERIL P/INYECTABLE','','000351','','NIU',3.00,0.00,0.00,0.00,1,0,'2026-09-08 15:40:40','2026-09-08 15:40:40'),(354,7,NULL,'aguja  # 21 x 1 1/2 x 100','','000352','','NIU',15.00,6.00,12.00,5.00,1,0,'2026-09-08 15:41:07','2026-09-09 09:20:21'),(355,7,NULL,'AGUJA # 23 x 1 x 100','','000353','','NIU',15.00,0.00,0.00,0.00,1,0,'2026-09-08 15:41:32','2026-09-08 15:41:32'),(356,7,NULL,'agujas  # 25 x 5/8 x 100','','000354','','NIU',15.00,5.00,6.00,6.00,1,0,'2026-09-08 15:42:03','2026-09-09 07:58:20'),(357,7,NULL,'ESPALADRAPOS 3M  3 X 4 GRANDE','','000355','','NIU',18.00,14.50,15.00,4.00,1,0,'2026-09-08 15:42:36','2026-09-08 23:53:36'),(358,7,NULL,'APLICADOR CON PUNTA DE ALGODON x 100 (hisopo)','','000356','','NIU',10.00,10.00,24.00,10.00,1,0,'2026-09-08 15:43:08','2026-09-09 09:11:20'),(359,6,NULL,'CULTIVO BACTERIANO+OBSERVACION DE ESTRUCTURA MICITICA','','000357','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 15:44:11','2026-09-08 15:44:11'),(360,6,NULL,'SECRECION DE GLANDE','','000358','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 15:44:43','2026-09-08 15:44:43'),(361,3,NULL,'PAPEL GRAF PLIEGO','','000359','','NIU',1.00,0.40,0.00,0.00,1,0,'2026-09-08 15:45:18','2026-09-08 15:45:18'),(362,7,NULL,'GASA 7.5 X 7.5  X 50 UNIDAD','','000359','','NIU',22.00,24.00,10.00,5.00,1,0,'2026-09-08 15:45:54','2026-09-09 09:18:21'),(363,7,NULL,'ARCODEX 120 MG X 7 TB.','','000361','','NIU',5.00,1.93,42.00,20.00,1,0,'2026-09-08 15:46:27','2026-09-09 08:43:58'),(364,7,NULL,'ACICLOVIR 200 MG X 100 TB.','','000362','','NIU',2.00,0.12,0.00,0.00,1,0,'2026-09-08 15:47:06','2026-09-08 15:47:06'),(365,7,NULL,'LACTULOSA 3.33 MG X 100 ML.','','000363','','NIU',15.00,7.00,21.00,5.00,1,0,'2026-09-08 15:47:36','2026-09-09 00:40:28'),(366,7,NULL,'AZITROMICINA 500 MG X 100 TB.','','000364','','NIU',2.00,0.70,200.00,100.00,1,0,'2026-09-08 15:48:20','2026-09-09 00:53:30'),(367,7,NULL,'jeringa de 1 ml','','000365','','NIU',2.00,0.12,0.00,0.00,1,0,'2026-09-08 15:48:55','2026-09-08 15:48:55'),(368,6,1,'ANTI ATG-ANTI TIROGLOBULINA','','000366','','NIU',115.00,0.00,0.00,0.00,0,1,'2026-09-08 15:53:23','2026-09-08 15:53:23'),(369,6,1,'ANTIFOSFOLIPIDOS (L10)','','000367','','NIU',410.00,0.00,0.00,0.00,0,1,'2026-09-08 15:55:14','2026-09-08 15:55:14'),(370,6,1,'ANTI DNA - DS NATIVO ODOBLE CADENA','','000368','','NIU',135.00,0.00,0.00,0.00,0,1,'2026-09-08 15:56:18','2026-09-08 15:56:18'),(371,6,1,'SM (SMITH) AUTO ANTICUERPOS','','000369','','NIU',140.00,0.00,0.00,0.00,0,1,'2026-09-08 15:57:03','2026-09-08 15:57:03'),(372,6,NULL,'ANTI TIROPEROXIDASA','','000370','','NIU',105.00,0.00,0.00,0.00,0,1,'2026-09-08 15:57:25','2026-09-08 15:57:25'),(373,6,NULL,'ANTICUERPOS ANTITUCLIARES (ANA)','','000371','','NIU',130.00,0.00,0.00,0.00,0,1,'2026-09-08 15:58:01','2026-09-08 15:58:01'),(374,7,NULL,'GUANTES QUIRURGICO T-8 X 50 UNID','','000372','','NIU',2.00,0.70,100.00,50.00,1,0,'2026-09-08 15:59:00','2026-09-09 09:49:02'),(375,6,6,'ECOGRAFIA DE PARTES BLANDAS','','000373','','NIU',90.00,0.00,0.00,0.00,0,1,'2026-09-08 15:59:45','2026-09-08 15:59:45'),(376,6,NULL,'CONSULTA DE CARDIOLOGIA','','000374','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 16:00:13','2026-09-08 16:00:13'),(377,3,NULL,'CUADERNO DE 100 HOJAS','','000375','','NIU',3.00,0.00,0.00,0.00,1,0,'2026-09-08 16:00:52','2026-09-08 16:00:52'),(378,3,NULL,'PAPEL TOALLA X 12 PAQUETE','','000376','','NIU',3.00,2.33,0.00,0.00,1,0,'2026-09-08 16:01:37','2026-09-08 16:01:37'),(379,3,NULL,'TINTA CYAN (TURQUESA) IMPRA. CANON','','000377','','NIU',35.00,0.00,0.00,0.00,1,0,'2026-09-08 16:02:03','2026-09-08 16:02:03'),(380,3,NULL,'TINTA MAGENTA IMPRA. CANON','','000378','','NIU',35.00,0.00,0.00,0.00,1,0,'2026-09-08 16:02:33','2026-09-08 16:02:33'),(381,3,NULL,'TINTA AMARILLO IMPRA. CANON','','000379','','NIU',35.00,0.00,0.00,0.00,1,0,'2026-09-08 16:02:59','2026-09-08 16:02:59'),(382,3,NULL,'TINTA NEGRO IMPRA.CANON','','000380','','NIU',35.00,0.00,0.00,0.00,1,0,'2026-09-08 16:03:29','2026-09-08 16:03:29'),(383,3,NULL,'TINTA AMARILLO /YLOW) IMPRA.EPSON','','000381','','NIU',35.00,0.00,0.00,0.00,1,0,'2026-09-08 16:03:56','2026-09-08 16:03:56'),(384,3,NULL,'TINTA MAGENTA IMPRA.EPSON','','000382','','NIU',35.00,37.00,0.00,0.00,1,0,'2026-09-08 16:05:06','2026-09-08 16:05:06'),(385,3,NULL,'TINTA NEGRO IMPRA.EPSON','','000383','','NIU',35.00,39.00,0.00,0.00,1,0,'2026-09-08 16:05:54','2026-09-08 16:05:54'),(386,3,NULL,'PERFUMADOR DE AMBINT SPRAY','','000384','','NIU',4.00,8.00,0.00,0.00,1,0,'2026-09-08 16:06:25','2026-09-08 16:06:25'),(387,3,NULL,'PAPEL HIGIENICO X 6','','000385','','NIU',2.30,1.60,0.00,0.00,1,0,'2026-09-08 16:07:09','2026-09-08 16:07:09'),(388,3,NULL,'PAPEL TOALLA INTERFOLIADO SUPREMO 200  DOBLE HOJA','','000386','','NIU',10.00,8.50,0.00,0.00,1,0,'2026-09-08 16:07:39','2026-09-08 16:07:39'),(389,6,2,'CULTIVO DE SECRECION','','000387','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 16:08:20','2026-09-08 16:08:20'),(390,7,NULL,'BENCILPENICILINA BENZATINA 2\'400.000 UI. POLVO SUSPENC','','000388','','NIU',30.00,0.00,0.00,0.00,1,0,'2026-09-08 16:08:58','2026-09-08 16:08:58'),(391,1,NULL,'EXTRACCION DE POLIPO VAGINAL','','000389','','NIU',400.00,0.00,0.00,0.00,0,1,'2026-09-08 16:09:31','2026-09-08 16:09:31'),(392,8,NULL,'ADELANTO DE DIU','','000390','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 16:10:05','2026-09-08 16:10:05'),(393,7,NULL,'DUPIVACAINA AMP.','','000391','','NIU',15.00,7.50,0.00,0.00,1,0,'2026-09-08 16:10:43','2026-09-08 16:10:43'),(394,1,NULL,'EXTRAXION DE POLIPO CERVICAL','','000392','','NIU',550.00,0.00,0.00,0.00,0,1,'2026-09-08 16:11:17','2026-09-08 16:11:17'),(395,1,NULL,'LIDOCAINA GEL 2% X 30 GR.(LIDONOSTRUM)','','000393','','NIU',25.00,0.00,0.00,0.00,0,1,'2026-09-08 16:11:46','2026-09-08 16:11:46'),(396,7,NULL,'ASPIRINA 100 MG X 100 TB.','','000394','','NIU',0.90,0.62,800.00,300.00,1,0,'2026-09-08 16:12:59','2026-09-08 22:02:12'),(397,3,NULL,'PAPEL FOTOGRAFICO','','000395','','NIU',0.90,0.62,0.00,0.00,1,0,'2026-09-08 16:13:50','2026-09-08 16:13:50'),(398,3,NULL,'PAPEL BON BRIO 4AX 75 G. X 500 HOJAS','','000396','','NIU',25.00,11.50,0.00,0.00,1,0,'2026-09-08 16:14:32','2026-09-08 16:14:32'),(399,7,NULL,'PRESERVATIVO X 144 UNID.','','000397','','NIU',45.00,35.00,4.00,3.00,1,0,'2026-09-08 16:15:08','2026-09-09 09:56:50'),(400,7,NULL,'PRESERVATIVO X 144 UNID.','','000397','','NIU',45.00,35.00,0.00,0.00,0,0,'2026-09-08 16:18:11','2026-09-09 09:56:01'),(401,7,NULL,'ALGODON HIDROF 1000 GR','','000398','','NIU',30.00,32.00,4.00,2.00,1,0,'2026-09-08 16:18:44','2026-09-09 09:23:21'),(402,7,NULL,'TERMOMETRO DIGITAL INFRAROJO','','000399','','NIU',40.00,0.00,0.00,0.00,1,0,'2026-09-08 16:19:15','2026-09-08 16:19:15'),(403,3,NULL,'PAPEL EKG MONITOR','','000400','','NIU',15.00,12.00,0.00,0.00,1,0,'2026-09-08 16:19:43','2026-09-08 16:19:43'),(404,7,NULL,'ALCOHOL 70% LITRO','','000401','','NIU',8.00,7.50,106.00,10.00,1,0,'2026-09-08 16:20:15','2026-09-09 08:07:39'),(405,7,NULL,'CAJA DE CONTENEDOR PUNZO CORTANTE 7 LT','','000402','','NIU',8.00,5.50,9.00,5.00,1,0,'2026-09-08 16:20:45','2026-09-09 09:13:26'),(406,7,NULL,'GUANTES QUIRURGICO T- 6','','000403','','NIU',2.00,0.00,0.00,0.00,1,0,'2026-09-08 16:21:07','2026-09-08 16:21:07'),(407,7,NULL,'GUANTES QUIRURGICO T.6 1/2','','000404','','NIU',2.00,0.76,350.00,50.00,1,0,'2026-09-08 16:21:35','2026-09-09 09:52:27'),(408,7,NULL,'GUANTES QUIRURGICO X 50 PARES  T-7','','000405','','NIU',2.00,0.75,250.00,50.00,1,0,'2026-09-08 16:22:00','2026-09-09 09:51:14'),(409,7,NULL,'GUANTES QUIRURGICO T-7.5 X 50 PARES','','000406','','NIU',2.00,0.76,800.00,50.00,1,0,'2026-09-08 16:22:22','2026-09-09 09:54:34'),(410,7,NULL,'FRASCO  VERDE P/ANALIS 100 ML x 250 caja','','000407','','NIU',0.50,0.40,750.00,250.00,1,0,'2026-09-08 16:22:59','2026-09-09 09:08:05'),(411,7,NULL,'ESPATULA CITOCEPILLO X 100 UNID','','000408','','NIU',35.00,35.00,0.00,0.00,1,0,'2026-09-08 16:23:23','2026-09-08 16:23:23'),(412,7,NULL,'LAMINAS PORTA OBJETO X 50 UNID','','000409','','NIU',5.00,3.00,48.00,10.00,1,0,'2026-09-08 16:23:48','2026-09-09 08:18:51'),(413,7,NULL,'ESPECULO  x 100 und. T - S  ','','000410','','NIU',1.20,1.10,146.00,100.00,1,0,'2026-09-08 16:24:17','2026-09-09 09:12:38'),(414,7,NULL,'ESPECULO T-L','','000411','','NIU',1.20,0.00,80.00,50.00,1,0,'2026-09-08 16:24:50','2026-09-09 09:26:18'),(415,7,NULL,'ESPECULO  X 100 UND. T-M','','000412','','NIU',6.00,1.20,1300.00,200.00,1,0,'2026-09-08 16:25:16','2026-09-09 09:24:19'),(416,8,NULL,'ADELANTO CESAREA','','000413','','NIU',1100.00,0.00,0.00,0.00,0,1,'2026-09-08 16:25:42','2026-09-08 16:25:42'),(417,7,NULL,'MYCOFENTIN 1000 MG. X 1 CAP. VAG.','','000414','','NIU',40.00,30.60,80.00,20.00,1,0,'2026-09-08 16:26:18','2026-09-08 23:46:31'),(418,7,NULL,'OXACILINA DE 1 gr. X 10 AM.','','000415','','NIU',10.00,2.30,150.00,30.00,1,0,'2026-09-08 16:26:44','2026-09-09 07:56:48'),(419,7,NULL,'VOLUZOL 2 % 15 GR. C.V CON APLICADOR','','000416','','NIU',70.00,37.00,20.00,10.00,1,0,'2026-09-08 16:27:08','2026-09-08 22:03:16'),(420,7,NULL,'GUANTES SIMPLE T-M X CAJA DE 100 UNID','','000417','','NIU',12.00,11.00,100.00,20.00,1,0,'2026-09-08 16:27:46','2026-09-09 09:25:40'),(421,1,NULL,'HISTERECTOMIA','','000418','','NIU',7000.00,0.00,0.00,0.00,0,1,'2026-09-08 16:28:11','2026-09-08 16:28:11'),(422,6,4,'CULTIVO DE SEMEN','','000419','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 16:28:48','2026-09-08 16:28:48'),(423,7,NULL,'PREDNISONA 5 MG. X 100 TB.','','000420','','NIU',0.50,0.05,300.00,100.00,1,0,'2026-09-08 16:29:23','2026-09-09 00:54:24'),(424,8,NULL,'ADELANTO DE CONO LEEP','','000421','','NIU',1000.00,0.00,0.00,0.00,0,1,'2026-09-08 16:29:50','2026-09-08 16:29:50'),(425,7,NULL,'BISTURI  # 21','','000422','','NIU',5.00,0.00,0.00,0.00,1,0,'2026-09-08 16:30:17','2026-09-08 16:30:17'),(426,7,NULL,'EPINEFRINA 1 MG  AMP.','','000423','','NIU',8.00,3.50,0.00,0.00,1,0,'2026-09-08 16:30:51','2026-09-08 16:30:51'),(427,7,NULL,'AMPICILINA 1 GR. AMP.','','000424','','NIU',8.00,0.00,0.00,0.00,1,0,'2026-09-08 16:31:16','2026-09-08 16:31:16'),(428,1,NULL,'PAQUETE PLATINUM CUOTA 4','','000425','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 16:32:10','2026-09-08 16:32:10'),(429,1,NULL,'PAQUETE PLATINUM CUOTA 3','','000426','','NIU',600.00,0.00,0.00,0.00,0,1,'2026-09-08 16:33:20','2026-09-08 16:33:20'),(430,1,NULL,'PAQUETE PLATINUM CUOTA 2','','000427','','NIU',800.00,0.00,0.00,0.00,0,1,'2026-09-08 16:33:59','2026-09-08 16:33:59'),(431,1,NULL,'PAQUETE PLATINUM CUOTA 1','','000428','','NIU',900.00,0.00,0.00,0.00,0,1,'2026-09-08 16:34:26','2026-09-08 16:34:26'),(432,1,NULL,'PAQUETE PLATINUM','','000429','','NIU',2500.00,0.00,0.00,0.00,0,1,'2026-09-08 16:35:04','2026-09-08 16:35:04'),(433,1,NULL,'PAQUETE ORO CUOTA 4','','000430','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 16:35:42','2026-09-08 16:35:42'),(434,1,NULL,'PAQUETE ORO CUOTA 3','','000431','','NIU',500.00,0.00,0.00,0.00,0,1,'2026-09-08 16:36:15','2026-09-08 16:36:15'),(435,1,NULL,'PAQUETE ORO CUOTA 2','','000432','','NIU',1100.00,0.00,0.00,0.00,0,1,'2026-09-08 16:36:41','2026-09-08 16:36:41'),(436,1,NULL,'PAQUETE ORO CUOTA 1','','000433','','NIU',1300.00,0.00,0.00,0.00,0,1,'2026-09-08 16:37:17','2026-09-08 16:37:17'),(437,1,NULL,'PAQUETE BRONCE CUOTA 4','','000434','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 16:37:48','2026-09-08 16:37:48'),(438,1,NULL,'PAQUETE BRONCE CUOTA 3','','000435','','NIU',400.00,0.00,0.00,0.00,0,1,'2026-09-08 16:38:18','2026-09-08 16:38:18'),(439,1,NULL,'PAQUETE BRONCE CUOTA 2','','000436','','NIU',800.00,0.00,0.00,0.00,0,1,'2026-09-08 16:38:38','2026-09-08 16:38:38'),(440,1,NULL,'PAQUETE BRONCE CUOTA  1','','000437','','NIU',900.00,0.00,0.00,0.00,0,1,'2026-09-08 16:39:02','2026-09-08 16:39:02'),(441,6,2,'CORTISOL (PM)','','000438','','NIU',70.00,0.00,0.00,0.00,0,1,'2026-09-08 16:39:53','2026-09-08 16:39:53'),(442,6,1,'CLAMYDIA IGM.','','000439','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 16:40:42','2026-09-08 16:40:42'),(443,7,NULL,'XUMADOL-EFETAMOL1 GR. (PARACETAMOL)  X 20 SOBRES','','000440','','NIU',5.00,1.17,0.00,0.00,1,0,'2026-09-08 16:41:21','2026-09-08 16:41:21'),(444,8,NULL,'ADELANTO ECO./GENETICA','','000441','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 16:41:58','2026-09-08 16:41:58'),(445,8,NULL,'ADELANTO ECO./DOPPLE','','000442','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 16:42:12','2026-09-08 16:42:48'),(446,8,NULL,'ADELANTO ECO./OBSTETRICO','','000443','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 16:43:11','2026-09-08 16:43:11'),(447,8,NULL,'ADELANTO ECO/MORFOLOGICA','','000444','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 16:43:41','2026-09-08 16:43:41'),(448,7,NULL,'NATALBEN PRECONCEPTIVO X 30 CAP.','','000445','','NIU',60.00,0.00,0.00,0.00,1,0,'2026-09-08 16:44:16','2026-09-08 16:44:16'),(449,7,NULL,'FLAVIA NOCTA FAST X  30 COMP.','','000446','','NIU',160.00,120.53,0.00,0.00,1,0,'2026-09-08 16:44:50','2026-09-08 16:44:50'),(450,7,NULL,'CADELIUS 600 MG./1000 UI V-D3 X 30 COMP.','','000447','','NIU',80.00,60.64,0.00,0.00,1,0,'2026-09-08 16:45:29','2026-09-08 16:45:29'),(451,4,NULL,'APLICACION DE MEDICAMENTO X VIA  IV.','','000448','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 16:46:03','2026-09-08 16:46:03'),(452,10,NULL,'PAQUETE BRONCE','','000449','','NIU',2300.00,0.00,0.00,0.00,0,1,'2026-09-08 16:46:32','2026-09-08 21:13:20'),(453,8,NULL,'ADELANTO CONTROL MUJER','','000450','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 16:47:01','2026-09-08 16:47:01'),(454,8,NULL,'ADELANTO PSICOLOGIA','','000451','','NIU',50.00,0.00,0.00,0.00,0,1,'2026-09-08 16:47:26','2026-09-08 16:47:26'),(455,8,NULL,'ADELANTO CHEQUEO COMPLETO','','000452','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 16:47:51','2026-09-08 16:47:51'),(456,8,NULL,'ADELANTO DE CONSULTA','','000453','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 16:48:13','2026-09-08 16:48:13'),(457,7,NULL,'DIVINA 28 X 28 TB.','','000454','','NIU',15.00,3.00,5.00,3.00,1,0,'2026-09-08 16:48:46','2026-09-09 00:52:44'),(458,7,NULL,'CONTROL PRE NATAL','','000455','','NIU',100.00,42.30,0.00,0.00,1,0,'2026-09-08 16:49:14','2026-09-08 16:49:14'),(459,7,NULL,'DOLOMOL 1 G. X 100 TB.','','000456','','NIU',3.00,0.21,300.00,100.00,1,0,'2026-09-08 16:49:47','2026-09-09 00:50:31'),(460,7,NULL,'BOLSA RECOLECTORA DE ORINA P/ ADULTO','','000457','','NIU',6.00,1.80,30.00,10.00,1,0,'2026-09-08 16:50:16','2026-09-09 08:25:35'),(461,7,NULL,'COMPRESA DE GASA 15 X 50 X 4 UND.','','000458','','NIU',10.00,0.00,8.00,5.00,1,0,'2026-09-08 16:50:47','2026-09-09 08:24:43'),(462,7,NULL,'SONDA DE ASPIRACION SUCCION NELATON # 14','','000459','','NIU',7.00,1.80,0.00,0.00,1,0,'2026-09-08 16:51:14','2026-09-08 16:51:14'),(463,7,NULL,'SONDA NASOGASTRICA','','000460','','NIU',7.00,0.00,0.00,0.00,1,0,'2026-09-08 16:51:42','2026-09-08 16:51:42'),(464,7,NULL,'SONDA FOLEY # 16','','000461','','NIU',8.00,0.00,0.00,0.00,1,0,'2026-09-08 16:52:09','2026-09-08 16:52:09'),(465,7,NULL,'TRAMADOL 100 MG X 10 UNID','','000462','','NIU',12.00,0.00,110.00,40.00,1,0,'2026-09-08 16:52:36','2026-09-09 08:52:11'),(466,7,NULL,'CLOTRIMAZOL VAGINAL 500 MG. X 50 TB.','','000463','','NIU',3.00,0.38,250.00,50.00,1,0,'2026-09-08 16:53:10','2026-09-09 00:55:17'),(467,6,2,'CREATININA','','000464','','NIU',35.00,0.00,0.00,0.00,0,1,'2026-09-08 16:53:38','2026-09-08 16:53:38'),(468,7,NULL,'CEFAZOLINA  1 G X 10 AMP.','','000465','','NIU',8.00,2.20,250.00,100.00,1,0,'2026-09-08 16:54:02','2026-09-09 00:17:14'),(469,7,NULL,'ABOCAT # 22 G X 1 X50','','000466','','NIU',6.00,0.70,0.00,0.00,1,0,'2026-09-08 16:55:04','2026-09-08 16:55:04'),(470,7,NULL,'ABOCAT # 20 G X 11/4 X 50','','000467','','NIU',6.00,1.60,0.00,0.00,1,0,'2026-09-08 16:55:30','2026-09-08 16:55:30'),(471,7,NULL,'ABOCAT # 18 G X 11/4 X50','','000468','','NIU',6.00,0.70,0.00,0.00,1,0,'2026-09-08 16:56:02','2026-09-08 16:56:02'),(472,7,NULL,'LLAVE DE TRIPLE VIA CON EXTENCION X 50CM. X 50 UND.','','000469','','NIU',6.00,1.10,200.00,50.00,1,0,'2026-09-08 16:56:29','2026-09-09 09:57:35'),(473,6,1,'PERFIL SINDROME DOWN','','000470','','NIU',800.00,0.00,0.00,0.00,0,1,'2026-09-08 16:57:46','2026-09-08 16:57:46'),(474,6,3,'BIOPSIA DE POLIPO','','000471','','NIU',400.00,0.00,0.00,0.00,0,1,'2026-09-08 16:58:13','2026-09-08 16:58:13'),(475,7,NULL,'oxitocina 10 ui/ml  ampolla','','000472','','NIU',8.00,4.50,0.00,0.00,1,0,'2026-09-08 16:59:13','2026-09-08 16:59:13'),(476,3,NULL,'USB','','000473','','NIU',40.00,15.00,0.00,0.00,1,0,'2026-09-08 16:59:49','2026-09-08 16:59:49'),(477,7,NULL,'TRAMADOL 50 MG X 10 AMP','','000474','','NIU',12.00,0.00,0.00,0.00,1,0,'2026-09-08 17:00:25','2026-09-08 17:00:25'),(478,7,NULL,'METAMIZOL 1G. X 50 AMP.','','000475','','NIU',8.00,0.78,950.00,100.00,1,0,'2026-09-08 17:00:56','2026-09-09 00:32:51'),(479,6,2,'HEMOGRAMA COMPLETO','','000476','','NIU',35.00,0.00,0.00,0.00,0,1,'2026-09-08 17:01:32','2026-09-08 17:01:32'),(480,6,2,'PERFIL DE SINDROME DE OVARIO POLIQUISTICO','','000477','','NIU',360.00,0.00,0.00,0.00,0,1,'2026-09-08 17:02:01','2026-09-08 17:02:01'),(481,6,NULL,'PAQUETE PREVENITS','','000478','','NIU',380.00,0.00,0.00,0.00,0,1,'2026-09-08 17:02:33','2026-09-08 17:02:33'),(482,7,NULL,'MODULCASS (IMIQUIMOD 5%) CREME 10 G','','000479','','NIU',280.00,131.60,0.00,0.00,1,0,'2026-09-08 17:03:21','2026-09-08 17:03:21'),(483,4,NULL,'VIH (prueba rapida)','','000480','','NIU',45.00,0.00,0.00,0.00,0,1,'2026-09-08 17:04:03','2026-09-08 17:04:03'),(484,6,2,'CULTIVO DE BARTHLINO','','000481','','NIU',120.00,0.00,0.00,0.00,0,1,'2026-09-08 17:04:32','2026-09-08 17:04:32'),(485,6,2,'BACILIO GRAN NEGATIVO','','000482','','NIU',140.00,0.00,0.00,0.00,0,1,'2026-09-08 17:05:01','2026-09-08 17:05:01'),(486,6,NULL,'BHCG CUANTITATIVA','','000483','','NIU',65.00,0.00,0.00,0.00,0,1,'2026-09-08 17:05:24','2026-09-08 17:05:24'),(487,6,2,'PCR CUANTITATIVA','','000484','','NIU',45.00,0.00,0.00,0.00,0,1,'2026-09-08 17:05:54','2026-09-08 17:05:54'),(488,6,2,'BHCG CUALITATIVA','','000485','','NIU',25.00,0.00,0.00,0.00,0,1,'2026-09-08 17:06:24','2026-09-08 17:06:24'),(489,6,1,'CLAMIDIA IGG','','000486','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 17:07:04','2026-09-08 17:07:04'),(490,6,2,'GONORREA','','000487','','NIU',140.00,0.00,0.00,0.00,0,1,'2026-09-08 17:07:46','2026-09-08 17:07:46'),(491,6,NULL,'NUTRICION','','000488','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 17:08:15','2026-09-08 17:08:15'),(492,6,NULL,'PSICOLOGIA','','000489','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 17:08:43','2026-09-08 17:08:43'),(493,4,NULL,'PSICOPROFILAXIS 1 SESION','','000490','','NIU',90.00,0.00,0.00,0.00,0,1,'2026-09-08 17:09:19','2026-09-08 17:09:19'),(494,1,NULL,'RETIRO DE DIU','','000491','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 17:09:46','2026-09-08 17:09:46'),(495,1,NULL,'COLOCACION DE DIU','','000492','','NIU',500.00,0.00,0.00,0.00,0,1,'2026-09-08 17:10:08','2026-09-08 17:10:08'),(496,4,NULL,'RETIRO DE IMPLANTE','','000493','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 17:10:42','2026-09-08 17:10:42'),(497,4,NULL,'IMPLANTE NEXPLANON ANTICONSEPTIVO Y COLOCACION','','000494','','NIU',400.00,192.60,0.00,0.00,0,1,'2026-09-08 17:11:20','2026-09-08 17:11:20'),(498,1,NULL,'ECOGRAFIA MORFOLOGICA','','000495','','NIU',260.00,0.00,0.00,0.00,0,1,'2026-09-08 17:11:51','2026-09-08 17:11:51'),(499,1,NULL,'ECOGRAFIA  5D','','000496','','NIU',240.00,0.00,0.00,0.00,0,1,'2026-09-08 17:12:23','2026-09-08 17:12:23'),(500,1,NULL,'ECOGRAFIA GENETICA','','000497','','NIU',260.00,0.00,0.00,0.00,0,1,'2026-09-08 17:12:46','2026-09-08 17:12:46'),(501,1,NULL,'PELVIMETRIA','','000498','','NIU',90.00,0.00,0.00,0.00,0,1,'2026-09-08 17:13:02','2026-09-08 17:13:43'),(502,10,NULL,'PAQUETE ORO','','000499','','NIU',3100.00,0.00,0.00,0.00,0,1,'2026-09-08 17:14:16','2026-09-08 21:12:13'),(503,6,NULL,'PERFIL DE QUISTE DE OVARIO','','000500','','NIU',500.00,0.00,0.00,0.00,0,1,'2026-09-08 17:14:47','2026-09-08 17:14:47'),(504,6,3,'BIOXIA DE CERVIX','','000501','','NIU',400.00,0.00,0.00,0.00,0,1,'2026-09-08 17:16:22','2026-09-08 17:16:22'),(505,6,NULL,'CONSULTA','','000502','','NIU',90.00,0.00,0.00,0.00,0,1,'2026-09-08 17:16:51','2026-09-08 17:16:51'),(506,7,NULL,'METRODIL 500 MG X 10 OVULOS','','000503','','NIU',6.00,1.75,500.00,50.00,1,0,'2026-09-08 17:17:28','2026-09-09 00:45:32'),(507,7,NULL,'VITAMINA D 2000 UI X 30 TB.(SOMA)','','000504','','NIU',30.00,15.00,15.00,0.00,1,0,'2026-09-08 17:18:04','2026-09-08 23:36:04'),(508,7,NULL,'VINAGRE DE MANZANA  caja x 12 de 1L.','','000505','','NIU',40.00,12.50,76.00,24.00,1,0,'2026-09-08 17:18:44','2026-09-09 09:05:54'),(509,7,NULL,'URODIXIL EXTRA FORTE X 180 CAP.','','000506','','NIU',5.00,0.00,0.00,0.00,1,0,'2026-09-08 17:19:18','2026-09-08 17:19:18'),(510,7,NULL,'MYLANTA DOS 240 ML.','','000507','','NIU',35.00,27.00,1.00,1.00,1,0,'2026-09-08 17:19:45','2026-09-09 00:59:01'),(511,7,NULL,'MENSILLE  1 AMP. DE MES','','000508','','NIU',25.00,0.00,0.00,0.00,1,0,'2026-09-08 17:20:13','2026-09-08 17:21:03'),(512,7,NULL,'METOCLOPRAMIDA 10 MG X 100 TB.','','000509','','NIU',3.00,0.00,0.00,0.00,1,0,'2026-09-08 17:21:23','2026-09-08 17:21:23'),(513,7,NULL,'MAGAL -D 200 ML','','000510','','NIU',40.00,0.00,0.00,0.00,1,0,'2026-09-08 17:23:09','2026-09-08 17:23:09'),(514,7,NULL,'HEPAVIT B  COMPLEX X 100 CAP.','','000511','','NIU',5.00,0.00,0.00,0.00,1,0,'2026-09-08 17:23:33','2026-09-08 17:23:33'),(515,7,NULL,'GASTRORAL 200 ML. (IGFA)','','000512','','NIU',30.00,0.00,0.00,0.00,1,0,'2026-09-08 17:24:01','2026-09-08 17:24:01'),(516,7,NULL,'DERMAGEN CREME 20 G','','000513','','NIU',20.00,5.70,27.00,5.00,1,0,'2026-09-08 17:24:45','2026-09-09 08:04:02'),(517,7,NULL,'PROCAPS X 50 CAP.(VITAMINA E)','','000514','','NIU',50.00,14.50,10.00,5.00,1,0,'2026-09-08 17:25:13','2026-09-08 22:07:18'),(518,7,NULL,'VIRUSUPRIL 5 G','','000515','','NIU',350.00,0.00,0.00,0.00,1,0,'2026-09-08 17:25:39','2026-09-08 17:25:39'),(519,7,NULL,'UROFURIN 100MG X 120 TB','','000516','','NIU',3.00,1.48,0.00,0.00,1,0,'2026-09-08 17:26:08','2026-09-08 17:26:08'),(520,7,NULL,'URODIFER 100 MG. X 100 TB.(FENAZOPD)','','000517','','NIU',3.00,0.30,0.00,0.00,1,0,'2026-09-08 17:26:54','2026-09-08 17:26:54'),(521,7,NULL,'SULFADIAZINA DE PLATA 1% 30 G CREMA','','000518','','NIU',25.00,7.50,3.00,0.00,1,0,'2026-09-08 17:27:19','2026-09-08 21:59:57'),(522,7,NULL,'SEROTOCAF 500 MG.X 100 CAP.(CEFALEXINA)','','000519','','NIU',3.00,0.38,1800.00,300.00,1,0,'2026-09-08 17:27:46','2026-09-09 08:53:21'),(523,7,NULL,'PHARMA COLAG PLUS  ACT. 600 G TARRO','','000520','','NIU',120.00,58.00,0.00,0.00,1,0,'2026-09-08 17:28:15','2026-09-08 17:28:15'),(524,7,NULL,'OVUSITOL D X 30 SOBRES','','000521','','NIU',120.00,72.50,0.00,0.00,1,0,'2026-09-08 17:28:44','2026-09-08 17:28:44'),(525,7,NULL,'OMEGANATUR 1000MG X30 CAP.','','000522','','NIU',50.00,19.50,24.00,5.00,1,0,'2026-09-08 17:29:12','2026-09-09 07:57:31'),(526,7,NULL,'N-BUTILBROMURO DE HIOSINA X 25 AMP.','','000523','','NIU',8.00,1.85,0.00,0.00,1,0,'2026-09-08 17:29:40','2026-09-08 17:29:40'),(527,7,NULL,'NATALBEN SUPRA D X 30 CAP.','','000524','','NIU',75.00,42.90,0.00,0.00,1,0,'2026-09-08 17:30:15','2026-09-08 17:30:15'),(528,7,NULL,'MISULONA 200 MG X 10 TB.','','000525','','NIU',5.00,1.65,100.00,50.00,1,0,'2026-09-08 17:30:44','2026-09-08 23:42:01'),(529,7,NULL,'METROFEN X 10 OVULOS','','000526','','NIU',10.00,1.84,1060.00,100.00,1,0,'2026-09-08 17:31:45','2026-09-09 08:50:57'),(530,7,NULL,'METOCLONYL X 10 AMP.(METOCLOPRAMIDA)','','000527','','NIU',8.00,0.85,150.00,50.00,1,0,'2026-09-08 17:32:23','2026-09-08 23:47:48'),(531,7,NULL,'LIDOCAINA 5% UNGUENTO','','000528','','NIU',20.00,0.00,0.00,0.00,1,0,'2026-09-08 17:33:08','2026-09-08 17:33:08'),(532,7,NULL,'jeringa de 3 ml x 100UND','','000529','','NIU',3.00,0.12,500.00,200.00,1,0,'2026-09-08 17:33:38','2026-09-09 08:16:49'),(533,7,NULL,'GINACABA 2% (CLOT.)CREMA VAGINAL 20G.','','000530','','NIU',50.00,16.50,0.00,0.00,1,0,'2026-09-08 17:34:11','2026-09-08 17:34:11'),(534,7,NULL,'GESTAGRAMIN 10/10MG. X 30 TBL.','','000531','','NIU',2.50,1.94,0.00,0.00,1,0,'2026-09-08 17:34:46','2026-09-08 17:34:46'),(535,7,NULL,'CLINDESS X 7 OVULOS','','000532','','NIU',15.00,6.23,350.00,60.00,1,0,'2026-09-08 17:35:31','2026-09-08 22:05:33'),(536,7,NULL,'FUNZAL 150 MG X 2 CAP /FLUCONAZOL)','','000533','','NIU',40.00,29.69,40.00,10.00,1,0,'2026-09-08 17:36:15','2026-09-09 08:47:09'),(537,7,NULL,'FISIOFER 40 MG X 10  AMP. BEBIBLE','','000534','','NIU',10.00,5.99,0.00,0.00,1,0,'2026-09-08 17:36:43','2026-09-08 17:36:43'),(538,7,NULL,'DOLOL 500 MG X 100 TB (PARACETAMOL)','','000535','','NIU',1.50,0.00,0.00,0.00,1,0,'2026-09-08 17:37:19','2026-09-08 17:37:19'),(539,7,NULL,'DEXAMETAXONA 4MG X  100 AMP','','000536','','NIU',8.00,0.00,300.00,100.00,1,0,'2026-09-08 17:37:41','2026-09-09 08:44:30'),(540,7,NULL,'DEQUAZOL ORAL X 20 TB.','','000537','','NIU',3.00,1.07,1600.00,200.00,1,0,'2026-09-08 17:38:22','2026-09-09 08:48:38'),(541,7,NULL,'DEQUAZOL R X 60 OVULOS','','000538','','NIU',6.00,6.50,0.00,0.00,1,0,'2026-09-08 17:39:11','2026-09-08 17:39:11'),(542,7,NULL,'CLOMIN 50 MG X 10 TB.','','000539','','NIU',150.00,70.30,0.00,0.00,1,0,'2026-09-08 17:39:40','2026-09-08 17:39:40'),(543,7,NULL,'CITRATO DE MAGNECIO + ZIG  350 G. FRUTO-ROJO','','000540','','NIU',90.00,38.00,28.00,8.00,1,0,'2026-09-08 17:40:04','2026-09-09 00:07:22'),(544,7,NULL,'CITROCALCIO X 30 TBL.','','000541','','NIU',60.00,15.00,30.00,5.00,1,0,'2026-09-08 17:40:31','2026-09-09 08:02:50'),(545,7,NULL,'ATORVASTATINA  20 mg. X 100 TBL.','','000542','','NIU',0.50,0.01,300.00,200.00,1,0,'2026-09-08 17:40:58','2026-09-08 21:34:10'),(546,7,NULL,'ANDROMAS X 30 SOBRES','','000543','','NIU',180.00,148.70,0.00,0.00,1,0,'2026-09-08 17:41:46','2026-09-08 17:41:46'),(547,7,NULL,'AMIKACINA 500 MG X 10 AMP. ( MEDIFARMA )','','000544','','NIU',8.00,1.60,150.00,50.00,1,0,'2026-09-08 17:42:12','2026-09-08 22:01:22'),(548,7,NULL,'ALBISEC X 12 CAP.','','000545','','NIU',8.00,0.00,0.00,0.00,1,0,'2026-09-08 17:42:45','2026-09-08 17:42:45'),(549,7,NULL,'ALERFREE - C X 100 CAP.','','000546','','NIU',3.00,0.00,0.00,0.00,1,0,'2026-09-08 17:43:06','2026-09-08 17:43:06'),(550,7,NULL,'OVULO PLUS X 14','','000547','','NIU',140.00,64.40,0.00,0.00,1,0,'2026-09-08 17:43:35','2026-09-08 17:43:35'),(551,7,NULL,'OVULO +KENFIR X 14','','000548','','NIU',140.00,64.40,0.00,0.00,1,0,'2026-09-08 17:44:15','2026-09-08 17:44:15'),(552,7,NULL,'OVULO+KENFIR X 7','','000549','','NIU',70.00,36.20,0.00,0.00,1,0,'2026-09-08 17:44:48','2026-09-08 17:44:48'),(553,7,NULL,'OVULO PLUS X 7','','000550','','NIU',70.00,36.20,0.00,0.00,1,0,'2026-09-08 17:45:15','2026-09-08 17:45:15'),(554,7,NULL,'OVULO HIALURONICO X 7','','000551','','NIU',70.00,36.16,0.00,0.00,1,0,'2026-09-08 17:46:02','2026-09-08 17:46:02'),(555,7,NULL,'GEL INTIMO ACIDO BORICO 30G.','','000552','','NIU',70.00,41.80,0.00,0.00,1,0,'2026-09-08 17:47:21','2026-09-08 17:47:21'),(556,7,NULL,'COLAGENO HODROLIZADO X 60 CAP.','','000553','','NIU',120.00,65.00,0.00,0.00,1,0,'2026-09-08 17:48:11','2026-09-08 17:48:11'),(557,7,NULL,'CLIMUJER X 60 CAPSULA','','000554','','NIU',120.00,47.50,0.00,0.00,1,0,'2026-09-08 17:48:36','2026-09-08 17:48:36'),(558,7,NULL,'GLUCOSAMINA CONDROITINA','','000555','','NIU',120.00,0.00,0.00,0.00,1,0,'2026-09-08 17:49:06','2026-09-08 17:49:06'),(559,7,NULL,'CONO LEEP','','000556','','NIU',2500.00,0.00,0.00,0.00,1,0,'2026-09-08 17:49:29','2026-09-08 17:49:29'),(560,7,NULL,'ACEITE DE COCO','','000557','','NIU',20.00,12.00,34.00,10.00,1,0,'2026-09-08 17:49:53','2026-09-09 09:47:43'),(561,7,NULL,'ACEITE DE OREGANO','','000558','','NIU',0.00,23.72,42.00,10.00,1,0,'2026-09-08 17:50:15','2026-09-09 09:47:14'),(562,6,4,'PCR ITS TEST COBA (MUJER) o° TEST DE COBAS  (MUJER )','','000559','','NIU',450.00,220.00,0.00,0.00,0,1,'2026-09-08 17:50:15','2026-09-08 17:51:42'),(563,7,NULL,'ACEITE DE OREGANO','','000558','','NIU',40.00,23.72,0.00,0.00,0,0,'2026-09-08 17:50:16','2026-09-09 10:00:45'),(564,7,NULL,'VITACOSE PLUS 1 AMP.','','000560','','NIU',50.00,12.60,9.00,5.00,1,0,'2026-09-08 17:52:21','2026-09-08 21:53:23'),(565,7,NULL,'ACICLOVIR 5% CREMA','','000561','','NIU',8.00,2.60,6.00,5.00,1,0,'2026-09-08 17:53:06','2026-09-08 22:06:40'),(566,7,NULL,'ACICLOVIR  800 mg x 10 TAB.','','000562','','NIU',3.00,0.90,100.00,0.00,1,0,'2026-09-08 17:53:47','2026-09-08 23:34:33'),(567,7,NULL,'CEFTRIAXONA 1 GRAMO X 10 AMP.','','000563','','NIU',6.00,1.10,590.00,100.00,1,0,'2026-09-08 17:54:14','2026-09-09 00:13:41'),(568,7,NULL,'EQUIPO DE VENOCLISIS caja de 500 und.','','000564','','NIU',6.00,0.72,1250.00,300.00,1,0,'2026-09-08 17:54:41','2026-09-09 09:17:04'),(569,7,NULL,'LLAVE DE TRIPLE VIA','','000565','','NIU',6.00,0.75,50.00,0.00,1,0,'2026-09-08 17:55:12','2026-09-09 09:59:06'),(570,7,NULL,'HIERRO  100 MG X 5 AMP.(HIERROMIN-HEALTHFE.)','','000566','','NIU',15.00,4.80,25.00,10.00,1,0,'2026-09-08 17:55:46','2026-09-09 08:00:12'),(571,6,2,'UROCULTIVO + ATB','','000567','','NIU',60.00,0.00,0.00,0.00,0,1,'2026-09-08 17:56:27','2026-09-08 17:56:27'),(572,6,2,'HERPES II IgG','','000568','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 17:57:00','2026-09-08 17:57:00'),(573,6,1,'HERPES II IgM','','000569','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 17:57:32','2026-09-08 17:57:32'),(574,6,NULL,'HERPES I IgG','','000570','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 17:58:06','2026-09-08 17:58:06'),(575,6,NULL,'RPR/ VDRL','','000571','','NIU',25.00,0.00,0.00,0.00,0,1,'2026-09-08 17:58:39','2026-09-08 17:58:39'),(576,6,2,'PCR CUALITATIVO','','000572','','NIU',20.00,0.00,0.00,0.00,0,1,'2026-09-08 17:59:09','2026-09-08 17:59:09'),(577,6,2,'TESTOSTERONA LIBRE','','000573','','NIU',80.00,0.00,0.00,0.00,0,1,'2026-09-08 17:59:48','2026-09-08 17:59:48'),(578,6,2,'TESTOSTERONA TOTAL','','000574','','NIU',80.00,0.00,0.00,0.00,0,1,'2026-09-08 18:00:25','2026-09-08 18:00:25'),(579,6,2,'TIROXINA LIBRE T4','','000575','','NIU',60.00,0.00,0.00,0.00,0,1,'2026-09-08 18:00:53','2026-09-08 18:00:53'),(580,6,2,'TRIODOTIRONINA LIBRE T3','','000576','','NIU',60.00,0.00,0.00,0.00,0,1,'2026-09-08 18:01:18','2026-09-08 18:01:18'),(581,6,NULL,'TIROTROPINA TSH ULTRASENSIBLE','','000577','','NIU',60.00,0.00,0.00,0.00,0,1,'2026-09-08 18:01:47','2026-09-08 18:01:47'),(582,6,NULL,'TIROXINA T4','','000578','','NIU',60.00,0.00,0.00,0.00,0,1,'2026-09-08 18:02:34','2026-09-08 18:02:34'),(583,6,2,'INSULINA BASAL','','000579','','NIU',80.00,0.00,0.00,0.00,0,1,'2026-09-08 18:02:55','2026-09-08 18:03:16'),(584,6,2,'LIPIDOS TOTALES','','000580','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 18:03:41','2026-09-08 18:03:41'),(585,6,2,'COLESTEROL VLDL','','000581','','NIU',15.00,0.00,0.00,0.00,0,1,'2026-09-08 18:04:08','2026-09-08 18:04:08'),(586,6,NULL,'TEST COMMBS INDIRECTO','','000582','','NIU',80.00,0.00,0.00,0.00,0,1,'2026-09-08 18:04:35','2026-09-08 18:04:35'),(587,6,2,'TEST COOMBS DIRECTO','','000583','','NIU',80.00,0.00,0.00,0.00,0,1,'2026-09-08 18:05:05','2026-09-08 18:05:05'),(588,6,NULL,'PERFIL REUMATICO','','000584','','NIU',120.00,0.00,0.00,0.00,0,1,'2026-09-08 18:05:32','2026-09-08 18:05:32'),(589,6,NULL,'PERFIL FISIOLOGICO','','000585','','NIU',160.00,0.00,0.00,0.00,0,1,'2026-09-08 18:05:59','2026-09-08 18:05:59'),(590,6,NULL,'PERFIL RENAL','','000586','','NIU',43.00,0.00,0.00,0.00,0,1,'2026-09-08 18:06:25','2026-09-08 18:06:25'),(591,6,NULL,'PERFIL FERRICO','','000587','','NIU',230.00,0.00,0.00,0.00,0,1,'2026-09-08 18:06:58','2026-09-08 18:06:58'),(592,6,NULL,'PERFIL HORMONAL MASCULINO','','000588','','NIU',230.00,0.00,0.00,0.00,0,1,'2026-09-08 18:07:19','2026-09-08 18:07:19'),(593,6,NULL,'PERFIL HORMONAL FEMENINO','','000589','','NIU',280.00,0.00,0.00,0.00,0,1,'2026-09-08 18:07:41','2026-09-08 18:07:41'),(594,6,NULL,'PERFIL CARDIACO','','000590','','NIU',400.00,0.00,0.00,0.00,0,1,'2026-09-08 18:08:19','2026-09-08 18:08:19'),(595,6,NULL,'PERFIL TIREOIDEO','','000591','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 18:08:39','2026-09-08 18:08:39'),(596,6,NULL,'PERFIL 21','','000592','','NIU',244.00,0.00,0.00,0.00,0,1,'2026-09-08 18:09:15','2026-09-08 18:09:47'),(597,6,NULL,'PERFIL PREOPERATORIO','','000593','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 18:10:52','2026-09-08 18:10:52'),(598,6,NULL,'PERFIL NEONATAL','','000594','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 18:11:38','2026-09-08 18:11:38'),(599,6,NULL,'PERFIL GESTANTE 3 TRIMESTRE','','000595','','NIU',60.00,0.00,0.00,0.00,0,1,'2026-09-08 18:12:15','2026-09-08 18:12:15'),(600,6,NULL,'PERFIL GESTANTE 2 TRIMESTRE','','000596','','NIU',60.00,0.00,0.00,0.00,0,1,'2026-09-08 18:12:44','2026-09-08 18:12:44'),(601,6,NULL,'PERFIL GESTANTE','','000597','','NIU',85.00,0.00,0.00,0.00,0,1,'2026-09-08 18:13:12','2026-09-08 18:13:12'),(602,6,2,'TEST DE HELECHO','','000598','','NIU',25.00,0.00,0.00,0.00,0,1,'2026-09-08 18:13:41','2026-09-08 18:13:41'),(603,6,NULL,'PAPP-A','','000599','','NIU',285.00,0.00,0.00,0.00,0,1,'2026-09-08 18:14:15','2026-09-08 18:14:15'),(604,6,2,'BETA HCG LIBRE','','000600','','NIU',265.00,0.00,0.00,0.00,0,1,'2026-09-08 18:14:41','2026-09-08 18:14:41'),(605,6,1,'HORMONA ANTIMULERIANA','','000601','','NIU',280.00,0.00,0.00,0.00,0,1,'2026-09-08 18:17:17','2026-09-08 18:17:17'),(606,6,NULL,'T DE COBRE   ANDALAN ( CLASICO  ) COBRE','','000602','','NIU',400.00,40.00,0.00,0.00,0,1,'2026-09-08 18:17:46','2026-09-08 18:17:46'),(607,6,NULL,'GARDASIL 9  (VACUNA  C .V P HUMANO ) X PROMOCIO 10% DESCUENTO','','000603','','NIU',670.00,471.50,0.00,0.00,0,1,'2026-09-08 18:18:19','2026-09-08 18:18:19'),(608,4,NULL,'ADELANTO DE IMPLANTE ANTICONCEPTIVO Y/O RETIRPO','','000604','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 18:18:40','2026-09-08 18:19:11'),(609,1,NULL,'CONO LEEP FRIO','','000605','','NIU',3000.00,0.00,0.00,0.00,0,1,'2026-09-08 18:19:43','2026-09-08 18:19:43'),(610,1,NULL,'CERCLAJE','','000606','','NIU',3500.00,0.00,0.00,0.00,0,1,'2026-09-08 18:20:15','2026-09-08 18:20:15'),(611,1,NULL,'CURACION','','000607','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 18:20:38','2026-09-08 18:21:07'),(612,4,NULL,'INTRAMUSCULAR','','000608','','NIU',5.00,0.00,0.00,0.00,0,1,'2026-09-08 18:21:45','2026-09-08 18:21:45'),(613,1,NULL,'LAPARATOMÍA EXPLORATORIA EMB. ECTOPICO + LAPAROSCOPICA','','000609','','NIU',6000.00,0.00,0.00,0.00,0,1,'2026-09-08 18:22:16','2026-09-08 18:22:16'),(614,1,NULL,'LAPARATOMIA EXPLORATORIA EN EMB. ECTÓPICO (C. ABIERTA)','','000610','','NIU',5000.00,0.00,0.00,0.00,0,1,'2026-09-08 18:22:42','2026-09-08 18:22:42'),(615,1,NULL,'CESAREA + LIGADURA DE TROMPAS','','000611','','NIU',5500.00,0.00,0.00,0.00,0,1,'2026-09-08 18:23:02','2026-09-08 18:23:02'),(616,1,NULL,'LIGADURA DE TROMPAS Y LAPAROSCOPICA','','000612','','NIU',5500.00,0.00,0.00,0.00,0,1,'2026-09-08 18:23:24','2026-09-08 18:23:24'),(617,1,NULL,'LIGADURA DE TROMPAS CIRUGIA ABIERTA','','000613','','NIU',4500.00,0.00,0.00,0.00,0,1,'2026-09-08 18:23:46','2026-09-08 18:23:46'),(618,1,NULL,'COLPORRAFIA','','000614','','NIU',4500.00,0.00,0.00,0.00,0,1,'2026-09-08 18:24:16','2026-09-08 18:24:43'),(619,1,NULL,'HISTERECTOMIA VAGINAL- ABDOMINAL','','000615','','NIU',6500.00,0.00,0.00,0.00,0,1,'2026-09-08 18:25:26','2026-09-08 18:25:26'),(620,1,NULL,'LAPAROSCOPICA','','000616','','NIU',5000.00,0.00,0.00,0.00,0,1,'2026-09-08 18:25:55','2026-09-08 18:25:55'),(621,1,NULL,'QUISTECTOMÍA CIRUGÍA ABIERTA','','000617','','NIU',4000.00,0.00,0.00,0.00,0,1,'2026-09-08 18:26:21','2026-09-08 18:26:21'),(622,1,NULL,'CESAREA','','000618','','NIU',5500.00,0.00,0.00,0.00,0,1,'2026-09-08 18:26:54','2026-09-08 18:26:54'),(623,1,NULL,'PARTO VAGINAL SIN DOLOR','','000619','','NIU',4300.00,0.00,0.00,0.00,0,1,'2026-09-08 18:27:19','2026-09-08 18:27:19'),(624,1,NULL,'PARTO VAGINAL CON DOLOR','','000620','','NIU',3800.00,0.00,0.00,0.00,0,1,'2026-09-08 18:27:55','2026-09-08 18:27:55'),(625,6,3,'PAPANICOLAU','','000621','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 18:28:35','2026-09-08 18:28:35'),(626,6,NULL,'MONITOREO FETAL','','000622','','NIU',80.00,0.00,0.00,0.00,0,1,'2026-09-08 18:29:09','2026-09-08 18:29:09'),(627,1,NULL,'LEGRADO UTERINO','','000623','','NIU',2500.00,0.00,0.00,0.00,0,1,'2026-09-08 18:30:00','2026-09-08 18:30:00'),(628,1,NULL,'EXTRACION DE FIBROADENOMA DE MAMA','','000624','','NIU',2000.00,0.00,0.00,0.00,0,1,'2026-09-08 18:30:29','2026-09-08 18:30:29'),(629,1,NULL,'EXTRACION DE CONDILOMA MENOS DE 3','','000625','','NIU',400.00,0.00,0.00,0.00,0,1,'2026-09-08 18:30:51','2026-09-08 18:30:51'),(630,1,NULL,'EXTRACION DE QUISTE DE BARTOLINO / MARSULIZACION','','000626','','NIU',500.00,0.00,0.00,0.00,0,1,'2026-09-08 18:31:13','2026-09-08 18:31:13'),(631,1,NULL,'AMEU','','000627','','NIU',2000.00,0.00,0.00,0.00,0,1,'2026-09-08 18:31:39','2026-09-08 23:36:11'),(632,6,NULL,'BIOPSIA DE ENDOMETRIO ','','000628','','NIU',500.00,0.00,0.00,0.00,0,1,'2026-09-08 18:35:49','2026-09-08 18:35:49'),(633,1,NULL,'COLPOSCOPIA ','','','000629','NIU',160.00,0.00,0.00,0.00,0,1,'2026-09-08 18:36:53','2026-09-08 18:36:53'),(634,7,NULL,'CLORURO DE SODIO AL 9% X 100 ML X 120 UND.','','000630','','NIU',15.00,1.80,1720.00,400.00,1,0,'2026-09-08 18:37:52','2026-09-09 09:03:30'),(635,7,NULL,'CLORURO DE SODIO AL 9% X 1000 ML X 12 FCO.','','000631','','NIU',30.00,4.50,432.00,60.00,1,0,'2026-09-08 18:38:21','2026-09-09 09:01:39'),(636,7,NULL,'ABOCAT # 24 G. X 1000 UNIDAD /NIPRO) V 05/30','','000632','','NIU',6.00,0.70,600.00,200.00,1,0,'2026-09-08 18:38:48','2026-09-09 08:06:22'),(637,7,NULL,'ALITA NUNERO','','000634','','NIU',3.00,0.00,0.00,0.00,1,0,'2026-09-08 18:39:14','2026-09-08 18:39:14'),(638,7,NULL,'JERINGA 20 ml x 50 un.','','000635','','NIU',3.00,0.30,900.00,200.00,1,0,'2026-09-08 18:40:01','2026-09-09 08:15:34'),(639,7,NULL,'JERINGA 5 ml x 100 und','','000636','','NIU',3.00,0.12,1000.00,200.00,1,0,'2026-09-08 18:40:29','2026-09-09 00:05:56'),(640,7,NULL,'VOLUTROL 100 ml caja x 100','','000637','','NIU',3.00,0.12,510.00,100.00,1,0,'2026-09-08 18:40:55','2026-09-09 09:15:31'),(641,7,NULL,'jeringa 10 ml x 100 unid.','','000638','','NIU',3.00,0.17,800.00,200.00,1,0,'2026-09-08 18:41:37','2026-09-09 08:14:15'),(642,6,2,'PUNTAJE DE NUGGLE','','000639','','NIU',120.00,0.00,0.00,0.00,0,1,'2026-09-08 18:42:20','2026-09-08 18:42:20'),(643,1,NULL,'CRIOTERAPIA POR CADA SESION','','000640','','NIU',500.00,0.00,0.00,0.00,0,1,'2026-09-08 18:42:44','2026-09-08 18:43:10'),(644,1,NULL,'ECPGRAFIA PELVICA ','','000641','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 18:43:45','2026-09-08 18:43:45'),(645,1,NULL,'ECOGRAFIA  DOPPLER','','000642','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 18:44:17','2026-09-08 18:44:17'),(646,1,NULL,'ECOGRAFIA DE MAMA','','000643','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 18:44:53','2026-09-08 18:44:53'),(647,1,NULL,'ECOGRAFIA OBSTETRICA','','000644','','NIU',90.00,0.00,0.00,0.00,0,1,'2026-09-08 18:45:34','2026-09-08 18:45:34'),(648,1,NULL,'ECOGRAFIA TRANSVAGINAL','','000645','','NIU',90.00,0.00,0.00,0.00,0,1,'2026-09-08 18:46:16','2026-09-08 18:46:16'),(649,10,NULL,'CHEQUEO COMPLETO ','','000646','','NIU',230.00,0.00,0.00,0.00,0,1,'2026-09-08 18:46:47','2026-09-08 18:46:47'),(650,10,NULL,'PAQUETE OBSTETRICO','','000647','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 18:49:35','2026-09-08 18:49:35'),(651,10,NULL,'CONTROL MUJER','','000648','','NIU',180.00,0.00,0.00,0.00,0,1,'2026-09-08 18:50:05','2026-09-08 18:50:05'),(652,10,NULL,'CHEQUEO INTEGRAL','','000649','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 18:50:25','2026-09-08 18:50:25'),(653,7,NULL,'OMEPRAZOL 50 MG X 10 AMP.  C/U','','000650','','NIU',8.00,1.50,90.00,50.00,1,0,'2026-09-08 18:54:07','2026-09-09 00:16:10'),(654,7,NULL,'SOLUNA NF 1 ML AMP C/U','','000652','','NIU',30.00,13.30,35.00,10.00,1,0,'2026-09-08 18:54:40','2026-09-09 08:49:40'),(655,7,NULL,'SOLUTRES 1ML AMP C/U','','000653','','NIU',30.00,30.00,8.00,5.00,1,0,'2026-09-08 18:55:13','2026-09-09 08:50:08'),(656,7,NULL,'KETEROLACO 60 MG X 25 AMP','','000654','','NIU',6.00,0.00,0.00,0.00,1,0,'2026-09-08 18:55:13','2026-09-08 18:56:27'),(657,7,NULL,'KETEROLACO 30MG AMP C/U','','000655','','NIU',6.00,0.00,0.00,0.00,1,0,'2026-09-08 18:56:59','2026-09-08 18:56:59'),(658,7,NULL,'CLORFENAMINA 10MG AMP C/U','','000656','','NIU',3.00,0.00,0.00,0.00,1,0,'2026-09-08 18:57:30','2026-09-08 18:57:30'),(659,7,NULL,'BENCILPENICILINA BENZATINICA  1200 00 UL X 10 AMP','','000657','','NIU',15.00,1.50,60.00,20.00,1,0,'2026-09-08 18:58:21','2026-09-09 00:46:49'),(660,7,NULL,'LEVOCTRIM FORTE 750 MG C/U X 7 TB.','','000658','','NIU',5.00,1.21,105.00,35.00,1,0,'2026-09-08 18:58:51','2026-09-09 00:52:04'),(661,7,NULL,'DUPBASTON X CAJA','','000659','','NIU',150.00,93.00,0.00,0.00,1,0,'2026-09-08 19:02:18','2026-09-08 19:02:59'),(662,7,NULL,'GEL INTIMO HIALURONICO','','000660','','NIU',70.00,41.80,0.00,0.00,1,0,'2026-09-08 19:03:43','2026-09-08 19:03:43'),(663,7,NULL,'JABON INTIMO HIALURONICO','','000661','','NIU',50.00,30.50,0.00,0.00,1,0,'2026-09-08 19:04:30','2026-09-08 19:04:30'),(664,6,1,'ESPERMATOGRAMA','','000662','','NIU',200.00,0.00,0.00,0.00,0,1,'2026-09-08 19:05:24','2026-09-08 19:05:24'),(665,6,NULL,'ANTIMULERINA','','000663','','NIU',450.00,0.00,0.00,0.00,0,1,'2026-09-08 19:06:15','2026-09-08 19:06:15'),(666,1,NULL,'EXTRACCION DE CONDILOMA MAYOR A 3','','000664','','NIU',600.00,0.00,0.00,0.00,0,1,'2026-09-08 19:06:50','2026-09-08 19:06:50'),(667,7,NULL,'CYTOTEC TABLETAS','','000665','','NIU',15.00,0.00,0.00,0.00,1,0,'2026-09-08 19:07:21','2026-09-08 19:07:21'),(668,7,NULL,'QUETAXAL TAB.','','000666','','NIU',2.00,0.00,0.00,0.00,1,0,'2026-09-08 19:07:49','2026-09-08 19:07:49'),(669,6,2,'PERFIL FEMENINO ( FSH, LH, PROLAC, PROGEST, ESTRADIOL)','','000667','','NIU',280.00,0.00,0.00,0.00,0,1,'2026-09-08 19:08:18','2026-09-08 19:08:18'),(670,6,NULL,'PRE OPERATORIO( G,U,C,HG,GS,TC Y TS , O/C, RPR, VIH, HVB)','','000668','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 19:08:48','2026-09-08 19:08:48'),(671,6,2,'PRE OBSTETRICO( HG,G,U,CREA,O/C,RPR,HIV)','','000669','','NIU',120.00,0.00,0.00,0.00,0,1,'2026-09-08 19:09:20','2026-09-08 19:09:20'),(672,6,NULL,'CHEQUEO GENERAL ( HG, O/C, HECES SIM, TGO, TGP, U , CREA, A. URICO, RPR)','','000670','','NIU',110.00,0.00,0.00,0.00,0,1,'2026-09-08 19:10:04','2026-09-08 19:10:04'),(673,6,2,'PERFIL COAGULACION','','000671','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 19:10:48','2026-09-08 19:10:48'),(674,6,NULL,'PERFIL TIROIDEO ( TSH, T3L, T4L)','','000672','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 19:11:22','2026-09-08 19:11:22'),(675,6,NULL,'PERFIL REUMATOIDEO ( A. URICO, URICO, CREA, UREA, O/C)','','000673','','NIU',65.00,0.00,0.00,0.00,0,1,'2026-09-08 19:11:47','2026-09-08 19:11:47'),(676,6,NULL,'PERFIL HEPATICO','','000674','','NIU',110.00,0.00,0.00,0.00,0,1,'2026-09-08 19:12:08','2026-09-08 19:12:08'),(677,6,NULL,'PERFIL LIPIDICO ( COLESTEROL TOTAL, HDL,LDL,VLDL,TRIGLICERIDOS,LIPIDOS TOTALES','','000675','','NIU',70.00,0.00,0.00,0.00,0,1,'2026-09-08 19:12:30','2026-09-08 19:12:30'),(678,6,2,'UROCULTIVO CON REMOVEDOR ANTIBIOTICO','','000676','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 19:12:59','2026-09-08 19:12:59'),(679,6,NULL,'BLOCK CELL','','000677','','NIU',120.00,0.00,0.00,0.00,0,1,'2026-09-08 19:15:14','2026-09-08 19:15:14'),(680,6,NULL,'PIEZA QUIRURGICA GRANDE','','000678','','NIU',180.00,0.00,0.00,0.00,0,1,'2026-09-08 19:15:55','2026-09-08 19:15:55'),(681,6,NULL,'PIEZA QUIRURGICA GRANDE','','000679','','NIU',180.00,0.00,0.00,0.00,0,1,'2026-09-08 19:16:27','2026-09-08 19:16:27'),(682,6,NULL,'PIEZA QUIRURGICA MEDIANA','','000680','','NIU',150.00,0.00,0.00,0.00,0,1,'2026-09-08 19:16:54','2026-09-08 19:16:54'),(683,7,NULL,'PIEZA QUIRURGICA PEQUEÑA','','000681','','NIU',120.00,0.00,0.00,0.00,1,0,'2026-09-08 19:17:57','2026-09-08 19:17:57'),(684,7,NULL,'BAAF ( tiroides, ganglios, partes blanda, mama.)','','000682','','NIU',100.00,0.00,0.00,0.00,1,0,'2026-09-08 19:18:34','2026-09-08 19:18:34'),(685,6,2,'HEMOCULTIVO','','000683','','NIU',70.00,0.00,0.00,0.00,0,1,'2026-09-08 19:19:52','2026-09-08 19:20:20'),(686,6,NULL,'FROTIS DE PIEL ( directo y gram )','','000684','','NIU',20.00,0.00,0.00,0.00,0,1,'2026-09-08 19:20:47','2026-09-08 19:20:47'),(687,6,NULL,'EXAMEN DE HONGOS Y ACAROS (KOH)','','000685','','NIU',20.00,0.00,0.00,0.00,0,1,'2026-09-08 19:21:14','2026-09-08 19:21:14'),(688,6,NULL,'EXAMEN CITOQUIMICO ( citológico y bioquímico)','','000686','','NIU',90.00,0.00,0.00,0.00,0,1,'2026-09-08 19:21:41','2026-09-08 19:21:41'),(689,6,NULL,'COPROCULTIVO','','000687','','NIU',40.00,0.00,0.00,0.00,0,1,'2026-09-08 19:22:10','2026-09-08 19:22:10'),(690,6,NULL,'BK DIRECTO','','000688','','NIU',15.00,0.00,0.00,0.00,0,1,'2026-09-08 19:23:09','2026-09-08 19:23:09'),(691,6,NULL,'THEVENON','','000689','','NIU',15.00,0.00,0.00,0.00,0,1,'2026-09-08 19:23:41','2026-09-08 19:23:41'),(692,6,NULL,'TEST DE GRAHAN','','000690','','NIU',15.00,0.00,0.00,0.00,0,1,'2026-09-08 19:24:09','2026-09-08 19:24:09'),(693,6,NULL,'REACCION INFLAMATORIA','','000691','','NIU',20.00,0.00,0.00,0.00,0,1,'2026-09-08 19:24:35','2026-09-08 19:24:35'),(694,6,2,'PARASITOSIS SIMPLE ( 1 MUESTRA )','','000693','','NIU',8.00,0.00,0.00,0.00,0,1,'2026-09-08 19:25:24','2026-09-08 19:25:24'),(695,6,NULL,'PARASITOSIS SERIADO ( 3 MUESTRAS)','','000694','','NIU',25.00,0.00,0.00,0.00,0,1,'2026-09-08 19:25:50','2026-09-08 19:25:50'),(696,6,NULL,'COPROFUNCIONAL','','000695','','NIU',20.00,0.00,0.00,0.00,0,1,'2026-09-08 19:26:32','2026-09-08 19:26:32'),(697,6,NULL,'HCG- SUB UNIDAD BETA ( CUANTITATIVO)','','000696','','NIU',65.00,0.00,0.00,0.00,0,1,'2026-09-08 19:27:07','2026-09-08 19:27:07'),(698,6,NULL,'HCG - SANGRE ( CUALITATIVA)','','000697','','NIU',25.00,0.00,0.00,0.00,0,1,'2026-09-08 19:27:32','2026-09-08 19:27:32'),(699,6,NULL,'HCG- ORINA ( CUALITATIVA)','','000698','','NIU',28.00,0.00,0.00,0.00,0,1,'2026-09-08 19:27:56','2026-09-08 19:27:56'),(700,6,NULL,'TEST DE HEMOLISIS','','000699','','NIU',20.00,0.00,0.00,0.00,0,1,'2026-09-08 19:28:17','2026-09-08 19:28:17'),(701,6,2,'PROTEINURIA CUANTITATIVA ( ORINA 24 HORAS )','','000700','','NIU',35.00,0.00,0.00,0.00,0,1,'2026-09-08 19:28:50','2026-09-08 19:28:50'),(702,6,NULL,'PROTEINURIA CUALITATIVA','','000701','','NIU',10.00,0.00,0.00,0.00,0,1,'2026-09-08 19:29:18','2026-09-08 19:29:18'),(703,6,NULL,'MICROALMINURA','','000702','','NIU',50.00,0.00,0.00,0.00,0,1,'2026-09-08 19:29:57','2026-09-08 19:29:57'),(704,6,NULL,'MICROALBIMINURA','','000703','','NIU',50.00,0.00,0.00,0.00,0,1,'2026-09-08 19:30:37','2026-09-08 19:30:37'),(705,6,NULL,'EXAMEN DE ORINA','','000704','','NIU',15.00,0.00,0.00,0.00,0,1,'2026-09-08 19:31:04','2026-09-08 19:31:04'),(706,6,NULL,'UREA','','000705','','NIU',35.00,0.00,0.00,0.00,0,1,'2026-09-08 19:31:43','2026-09-08 19:31:43'),(707,6,2,'TRIGLICERIDOS','','000706','','NIU',35.00,0.00,0.00,0.00,0,1,'2026-09-08 19:32:15','2026-09-08 19:32:15'),(708,6,NULL,'TRANSAMINASAS TGP','','000707','','NIU',15.00,0.00,0.00,0.00,0,1,'2026-09-08 19:32:36','2026-09-08 19:32:36'),(709,6,NULL,'TRANSAMINASAS TGO','','000708','','NIU',15.00,0.00,0.00,0.00,0,1,'2026-09-08 19:32:53','2026-09-08 19:32:53'),(710,6,NULL,'PROTEINAS T Y F','','000709','','NIU',15.00,0.00,0.00,0.00,0,1,'2026-09-08 19:33:26','2026-09-08 19:33:26'),(711,6,NULL,'MAGNESIO','','000710','','NIU',30.00,0.00,0.00,0.00,0,1,'2026-09-08 19:33:49','2026-09-08 19:33:49'),(712,6,NULL,'LIPASA SERICA','','000711','','NIU',45.00,0.00,0.00,0.00,0,1,'2026-09-08 19:34:32','2026-09-08 19:34:32'),(713,6,NULL,'HEMOGLOBINA GLICOSILADA','','000712','','NIU',100.00,0.00,0.00,0.00,0,1,'2026-09-08 19:34:56','2026-09-08 19:34:56'),(714,6,NULL,'GLUCOSA TOLERANCIA','','000713','','NIU',45.00,0.00,0.00,0.00,0,1,'2026-09-08 19:35:24','2026-09-08 19:35:24'),(715,7,NULL,'CIPROFLOXACINO  500 MG  X 100 TB.','','000715','','NIU',3.00,0.00,0.00,0.00,1,0,'2026-09-08 19:48:22','2026-09-08 19:48:22'),(716,7,NULL,'DIMENHIDRINATO 50 MG X 25 AMP.','','000716','','NIU',8.00,1.62,125.00,50.00,1,0,'2026-09-08 19:49:00','2026-09-08 21:48:12'),(717,7,NULL,'MESIGYNA  1 AMP.','','000717','','NIU',30.00,16.80,15.00,5.00,1,0,'2026-09-08 19:49:44','2026-09-08 23:50:14'),(718,7,NULL,'SOLOUNA 5  1ML X AMP.','','000718','','NIU',30.00,13.00,35.00,10.00,1,0,'2026-09-08 19:50:27','2026-09-09 08:49:10'),(719,7,NULL,'CLINDAMICINA 600 MG X 25 AMP.','','000719','','NIU',6.00,2.56,550.00,100.00,1,0,'2026-09-08 19:50:59','2026-09-09 00:37:17'),(720,7,NULL,'ACIDO TRANEXAMICO 100 MG (DIPHAXAMICO)  AMPOLLA','','000720','','NIU',55.00,5.10,0.00,0.00,1,0,'2026-09-08 19:51:33','2026-09-08 19:51:33'),(721,7,NULL,'DIXI-35  X 21 COMPRIMIDOS','','000721','','NIU',50.00,31.00,5.00,3.00,1,0,'2026-09-08 19:51:57','2026-09-09 08:46:31'),(722,7,NULL,'JABON INTIMO OZONO FCO','','000722','','NIU',50.00,0.00,0.00,0.00,1,0,'2026-09-08 19:52:23','2026-09-08 19:52:23'),(723,7,NULL,'JABON INTIMO MASCULINO FCO','','000723','','NIU',50.00,22.60,0.00,0.00,1,0,'2026-09-08 19:53:02','2026-09-08 19:53:02'),(724,7,NULL,'JABON INTIMO FEMENINO CON KEFIR FCO','','000724','','NIU',50.00,22.60,0.00,0.00,1,0,'2026-09-08 19:54:12','2026-09-08 19:54:12'),(725,7,NULL,'LIDONOSTRUM GEL 2% X 30 G.','','000725','','NIU',25.00,0.00,5.00,3.00,1,0,'2026-09-08 21:56:30','2026-09-09 08:28:12'),(726,7,NULL,'LAPIZ DE ELECTROCAUTERIO','','000726','','NIU',20.00,5.50,6.00,5.00,1,0,'2026-09-09 08:33:52','2026-09-09 08:33:52'),(727,5,NULL,'CATGUT CROMICO 2 HR 40 X 24','','000727','','NIU',6.50,0.00,24.00,0.00,1,0,'2026-09-09 09:40:02','2026-09-09 09:40:02'),(728,5,NULL,'CATGUT CROMICO 2/0 MR 35 X 24 UND.','','728','','NIU',0.00,0.00,96.00,24.00,1,0,'2026-09-09 09:42:20','2026-09-09 09:42:20');
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedores` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `ruc` varchar(11) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `whatsapp` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedores`
--

LOCK TABLES `proveedores` WRITE;
/*!40000 ALTER TABLE `proveedores` DISABLE KEYS */;
INSERT INTO `proveedores` VALUES (1,'Suiza lab',NULL,NULL,'','',1,'2026-09-07 23:52:19','2026-09-07 23:52:19'),(2,'Luigui',NULL,NULL,'','',1,'2026-09-07 23:52:25','2026-09-07 23:52:25'),(3,'Diagnóstic',NULL,NULL,'','',1,'2026-09-07 23:52:32','2026-09-07 23:52:32'),(4,'Sisgenyca',NULL,NULL,'','',1,'2026-09-07 23:52:42','2026-09-07 23:52:42'),(5,'Santa fe',NULL,NULL,'','',1,'2026-09-07 23:52:48','2026-09-07 23:52:48'),(6,'Campos',NULL,NULL,'','',1,'2026-09-07 23:52:55','2026-09-07 23:52:55');
/*!40000 ALTER TABLE `proveedores` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `descripcion` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Admin','Administrador del sistema con todos los permisos','2026-01-25 08:34:45','2026-01-25 19:33:58'),(2,'Doctor','Medico con acceso a pacientes, consultas y citas','2026-01-25 08:43:40','2026-01-25 08:57:57'),(3,'Recepcion','Recepcion','2026-02-24 11:55:53','2026-02-24 11:55:53');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles_permisos`
--

DROP TABLE IF EXISTS `roles_permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles_permisos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `rol_id` int NOT NULL,
  `permiso_id` int NOT NULL,
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_rol_permiso` (`rol_id`,`permiso_id`),
  KEY `fk_rp_permiso` (`permiso_id`),
  CONSTRAINT `roles_permisos_ibfk_1` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `roles_permisos_ibfk_2` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=466 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles_permisos`
--

LOCK TABLES `roles_permisos` WRITE;
/*!40000 ALTER TABLE `roles_permisos` DISABLE KEYS */;
INSERT INTO `roles_permisos` VALUES (5,1,5,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(6,1,6,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(7,1,7,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(8,1,8,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(9,1,9,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(10,1,10,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(11,1,11,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(12,1,12,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(13,1,13,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(14,1,14,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(15,1,15,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(16,1,16,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(17,1,17,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(18,1,18,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(19,1,19,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(20,1,20,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(21,1,21,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(26,1,26,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(27,1,27,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(28,1,28,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(29,1,29,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(30,1,30,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(31,1,31,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(32,1,32,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(33,1,33,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(34,1,34,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(35,1,35,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(36,1,36,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(37,1,37,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(42,1,42,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(43,1,43,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(44,1,44,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(45,1,45,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(46,1,46,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(47,1,47,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(48,1,48,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(49,1,49,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(50,1,50,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(51,1,51,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(52,1,52,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(53,1,53,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(54,1,54,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(55,1,55,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(56,1,56,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(57,1,57,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(58,1,58,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(59,1,59,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(60,1,60,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(61,1,61,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(62,1,62,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(67,1,67,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(68,1,68,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(69,1,69,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(70,1,70,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(71,1,71,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(72,1,72,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(73,1,73,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(74,1,74,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(75,1,75,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(76,1,76,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(77,1,77,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(78,1,78,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(79,1,79,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(80,1,80,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(81,1,81,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(82,1,82,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(87,1,87,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(88,1,88,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(89,1,89,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(90,1,90,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(91,1,91,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(92,1,92,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(93,1,93,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(94,1,94,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(95,1,95,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(100,1,100,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(101,1,101,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(102,1,102,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(103,1,103,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(104,1,104,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(105,1,105,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(106,1,106,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(107,1,107,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(108,1,108,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(109,1,109,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(110,1,110,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(111,1,111,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(112,1,112,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(113,1,113,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(114,1,114,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(115,1,115,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(116,1,116,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(117,1,117,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(118,1,118,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(119,1,119,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(120,1,120,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(123,1,123,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(124,1,124,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(125,1,125,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(126,1,126,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(127,1,127,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(128,1,128,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(129,1,129,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(130,1,130,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(131,1,131,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(132,1,132,'2026-01-25 20:16:03','2026-01-25 20:16:03'),(256,2,13,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(257,2,14,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(258,2,15,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(259,2,16,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(260,2,17,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(261,2,18,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(262,2,19,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(263,2,20,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(264,2,21,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(273,2,42,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(274,2,43,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(275,2,44,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(276,2,45,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(277,2,46,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(278,2,47,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(279,2,48,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(280,2,49,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(281,2,59,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(282,2,60,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(283,2,61,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(284,2,62,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(289,2,75,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(290,2,76,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(291,2,77,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(292,2,78,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(293,2,79,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(294,2,80,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(295,2,81,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(296,2,82,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(297,2,87,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(298,2,88,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(299,2,89,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(300,2,90,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(301,2,100,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(302,2,101,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(303,2,102,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(304,2,103,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(305,2,124,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(306,2,125,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(307,2,126,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(308,2,127,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(309,2,128,'2026-01-25 20:16:08','2026-01-25 20:16:08'),(310,1,179,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(312,1,177,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(313,1,178,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(317,2,179,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(318,2,177,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(319,2,178,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(320,1,180,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(322,1,181,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(323,1,183,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(324,1,182,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(327,2,180,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(328,2,181,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(329,2,183,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(330,2,182,'2026-01-31 12:00:26','2026-01-31 12:00:26'),(342,1,196,'2026-02-23 02:09:51','2026-02-23 02:09:51'),(343,1,193,'2026-02-23 02:09:51','2026-02-23 02:09:51'),(344,1,197,'2026-02-23 02:09:51','2026-02-23 02:09:51'),(345,1,194,'2026-02-23 02:09:51','2026-02-23 02:09:51'),(346,1,198,'2026-02-23 02:09:51','2026-02-23 02:09:51'),(347,1,195,'2026-02-23 02:09:51','2026-02-23 02:09:51'),(349,3,13,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(350,3,179,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(351,3,124,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(352,3,14,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(353,3,15,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(354,3,177,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(355,3,16,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(356,3,178,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(357,3,17,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(358,3,18,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(359,3,19,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(360,3,20,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(361,3,21,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(362,3,30,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(363,3,31,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(364,3,32,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(365,3,33,'2026-02-24 11:55:53','2026-02-24 11:55:53'),(366,3,115,'2026-02-24 11:55:54','2026-02-24 11:55:54'),(367,3,116,'2026-02-24 11:55:54','2026-02-24 11:55:54'),(368,3,117,'2026-02-24 11:55:54','2026-02-24 11:55:54'),(369,3,118,'2026-02-24 11:55:54','2026-02-24 11:55:54'),(370,3,119,'2026-02-24 11:55:54','2026-02-24 11:55:54'),(371,3,120,'2026-02-24 11:55:54','2026-02-24 11:55:54'),(374,1,205,'2026-04-14 13:17:33','2026-04-14 13:17:33'),(375,2,205,'2026-04-14 13:17:33','2026-04-14 13:17:33'),(376,1,218,'2026-05-01 00:24:12','2026-05-01 00:24:12'),(386,1,234,'2026-05-29 00:00:00','2026-05-29 00:00:00'),(387,1,235,'2026-05-29 00:00:00','2026-05-29 00:00:00'),(388,1,236,'2026-05-29 00:00:00','2026-05-29 00:00:00'),(389,1,237,'2026-05-29 00:00:00','2026-05-29 00:00:00'),(390,1,238,'2026-05-29 00:00:00','2026-05-29 00:00:00'),(391,1,239,'2026-05-29 00:00:00','2026-05-29 00:00:00'),(392,1,219,'2026-05-06 01:42:17','2026-05-06 01:42:17'),(393,1,220,'2026-05-06 01:42:17','2026-05-06 01:42:17'),(394,1,221,'2026-05-06 01:42:17','2026-05-06 01:42:17'),(395,1,222,'2026-05-06 01:42:17','2026-05-06 01:42:17'),(396,1,228,'2026-05-06 01:53:04','2026-05-06 01:53:04'),(397,1,229,'2026-05-06 01:53:04','2026-05-06 01:53:04'),(398,1,230,'2026-05-06 01:53:04','2026-05-06 01:53:04'),(399,1,231,'2026-05-06 01:53:04','2026-05-06 01:53:04'),(400,1,232,'2026-05-06 01:53:04','2026-05-06 01:53:04'),(401,1,233,'2026-05-06 01:53:04','2026-05-06 01:53:04'),(402,1,240,'2026-06-15 02:05:41','2026-06-15 02:05:41'),(403,1,262,'2026-07-28 22:37:14','2026-07-28 22:37:14'),(404,1,264,'2026-07-28 22:37:14','2026-07-28 22:37:14'),(405,1,263,'2026-07-28 22:37:14','2026-07-28 22:37:14'),(406,1,260,'2026-07-28 22:37:14','2026-07-28 22:37:14'),(407,1,261,'2026-07-28 22:37:14','2026-07-28 22:37:14'),(410,1,267,'2026-07-28 22:37:46','2026-07-28 22:37:46'),(411,1,266,'2026-07-28 22:37:46','2026-07-28 22:37:46'),(412,1,269,'2026-07-28 22:37:46','2026-07-28 22:37:46'),(413,1,270,'2026-07-28 22:37:46','2026-07-28 22:37:46'),(414,1,265,'2026-07-28 22:37:46','2026-07-28 22:37:46'),(417,1,272,'2026-07-28 23:24:15','2026-07-28 23:24:15'),(418,1,271,'2026-07-28 23:24:15','2026-07-28 23:24:15'),(422,1,282,'2026-07-30 23:17:46','2026-07-30 23:17:46'),(423,1,284,'2026-07-30 23:17:46','2026-07-30 23:17:46'),(424,1,283,'2026-07-30 23:17:46','2026-07-30 23:17:46'),(425,1,280,'2026-07-30 23:17:46','2026-07-30 23:17:46'),(426,1,281,'2026-07-30 23:17:46','2026-07-30 23:17:46'),(427,1,277,'2026-07-30 23:17:46','2026-07-30 23:17:46'),(428,1,279,'2026-07-30 23:17:46','2026-07-30 23:17:46'),(429,1,278,'2026-07-30 23:17:46','2026-07-30 23:17:46'),(430,1,275,'2026-07-30 23:17:46','2026-07-30 23:17:46'),(431,1,276,'2026-07-30 23:17:46','2026-07-30 23:17:46'),(437,1,285,'2026-07-30 23:45:21','2026-07-30 23:45:21'),(438,1,286,'2026-07-30 23:45:21','2026-07-30 23:45:21'),(440,1,287,'2026-07-30 23:49:28','2026-07-30 23:49:28'),(441,1,288,'2026-07-30 23:53:39','2026-07-30 23:53:39'),(442,1,305,'2026-08-18 23:22:53','2026-08-18 23:22:53'),(443,1,291,'2026-08-18 23:28:34','2026-08-18 23:28:34'),(444,1,292,'2026-08-18 23:28:34','2026-08-18 23:28:34'),(445,1,293,'2026-08-18 23:28:34','2026-08-18 23:28:34'),(446,1,289,'2026-08-18 23:28:34','2026-08-18 23:28:34'),(447,1,306,'2026-08-18 23:28:34','2026-08-18 23:28:34'),(448,1,290,'2026-08-18 23:28:34','2026-08-18 23:28:34'),(450,1,307,'2026-08-18 23:45:21','2026-08-18 23:45:21'),(451,1,308,'2026-09-04 03:18:42','2026-09-04 03:18:42'),(452,1,309,'2026-09-04 03:18:55','2026-09-04 03:18:55'),(453,1,310,'2026-09-04 03:18:55','2026-09-04 03:18:55'),(455,1,311,'2026-09-09 03:05:11','2026-09-09 03:05:11'),(456,1,312,'2026-09-09 03:05:21','2026-09-09 03:05:21'),(457,1,313,'2026-09-09 15:16:20','2026-09-09 15:16:20'),(458,1,316,'2026-09-10 04:28:34','2026-09-10 04:28:34'),(459,1,317,'2026-09-10 04:28:34','2026-09-10 04:28:34'),(460,1,314,'2026-09-10 04:28:34','2026-09-10 04:28:34'),(461,1,315,'2026-09-10 04:28:34','2026-09-10 04:28:34'),(465,1,321,'2026-09-10 04:28:42','2026-09-10 04:28:42');
/*!40000 ALTER TABLE `roles_permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `data` text,
  `expires` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tratamientos`
--

DROP TABLE IF EXISTS `tratamientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tratamientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion` varchar(300) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `costo` decimal(10,2) NOT NULL,
  `monto_fijo_pago` decimal(10,2) NOT NULL DEFAULT '0.00',
  `gasto_materiales` decimal(10,2) NOT NULL DEFAULT '0.00',
  `estado` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=91 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tratamientos`
--

LOCK TABLES `tratamientos` WRITE;
/*!40000 ALTER TABLE `tratamientos` DISABLE KEYS */;
INSERT INTO `tratamientos` VALUES (1,'CONSULTA GINECOLOGICA ','',90.00,40.00,3.50,1,'2026-09-08 20:11:58','2026-09-08 21:08:26'),(2,'CONSULTA DE FERTILIDAD ','',100.00,50.00,3.50,1,'2026-09-08 20:13:16','2026-09-08 21:09:17'),(3,'ECOGRAFIA  DOPPLER','',200.00,70.00,4.20,1,'2026-09-08 20:17:55','2026-09-08 21:09:50'),(4,'ECOGRAFIA DE MAMA','',100.00,50.00,4.20,1,'2026-09-08 20:18:46','2026-09-08 21:10:09'),(5,'ECOGRAFIA GENETICA','',260.00,100.00,4.20,1,'2026-09-08 20:19:46','2026-09-08 21:10:17'),(6,'ECOGRAFIA MORFOLOGICA','',260.00,100.00,4.10,1,'2026-09-08 20:20:34','2026-09-08 21:10:25'),(7,'ECOGRAFIA OBSTETRICA','',90.00,40.00,4.10,1,'2026-09-08 20:26:38','2026-09-08 21:10:35'),(8,'ECOGRAFIA PELVICA','',100.00,50.00,4.10,1,'2026-09-08 20:27:41','2026-09-08 21:10:43'),(9,'ECOGRAFIA RESERVA OVARICA','',120.00,40.00,5.10,1,'2026-09-08 20:31:39','2026-09-08 21:11:14'),(10,'ECOGRAFIA TRANSVAGINAL','',90.00,40.00,5.10,1,'2026-09-08 20:32:11','2026-09-08 21:11:22'),(11,'HIFU X SESIÓN','',250.00,100.00,0.00,1,'2026-09-08 20:36:24','2026-09-08 21:55:25'),(12,'LASER CO2','',250.00,100.00,0.00,1,'2026-09-08 20:37:26','2026-09-08 20:37:26'),(13,'LASER CO2+RPR','',900.00,0.00,0.00,1,'2026-09-08 20:38:36','2026-09-08 20:38:36'),(14,'LEGRADO UTERINO ','',2500.00,300.00,0.00,1,'2026-09-08 20:39:28','2026-09-08 23:39:04'),(15,'LIGADURA DE TROMPAS CIRUGIA ABIERTA','',4500.00,0.00,0.00,1,'2026-09-08 20:40:32','2026-09-08 20:40:32'),(16,'LIGADURA DE TROMPAS Y LAPAROSCOPICA','',5500.00,0.00,0.00,1,'2026-09-08 20:41:47','2026-09-08 20:41:47'),(17,'OZONOTERAPIA (LAVADO CON OZONO MAS CLORURO + INSUFLACION CON OZONO + PLASMA RICO EN PLAQUETAS CON OZONO)','',250.00,60.00,0.00,0,'2026-09-08 20:46:53','2026-09-09 00:06:02'),(18,'OZONOTERAPIA DE MAMA X SESION','',200.00,0.00,0.00,1,'2026-09-08 20:47:31','2026-09-08 22:02:27'),(19,'PAPANICOLAOU','',100.00,20.00,2.96,0,'2026-09-08 20:50:06','2026-09-08 20:52:10'),(20,'PELVIMETRIA','',90.00,30.00,0.60,1,'2026-09-08 20:53:39','2026-09-08 20:53:39'),(21,'CONTROL MUJER','',180.00,60.00,21.70,1,'2026-09-08 21:00:11','2026-09-08 21:12:11'),(22,'CHEQUEO COMPLETO','',230.00,0.00,0.00,0,'2026-09-08 21:07:50','2026-09-08 21:14:37'),(23,'CHEQUEO COMPLETO','',230.00,70.00,17.40,1,'2026-09-08 21:14:15','2026-09-08 21:14:58'),(24,'CRIOTERAPIA C/SESIÓN X CERVICITIS','',500.00,100.00,1.50,1,'2026-09-08 21:18:57','2026-09-08 21:18:57'),(25,'BIOPSIA DE MAMA','',650.00,40.00,0.00,0,'2026-09-08 21:28:21','2026-09-08 23:51:26'),(26,'BIOPSIA DE POLIPO','',400.00,0.00,0.00,0,'2026-09-08 21:28:55','2026-09-08 23:53:44'),(27,'BIOPSIA DE VULVA','',400.00,40.00,0.00,1,'2026-09-08 21:29:17','2026-09-08 21:29:17'),(28,'BIOPSIA DE CERVIX','',400.00,40.00,0.00,1,'2026-09-08 21:30:45','2026-09-08 21:32:56'),(29,'BIOPSIA DE CONDILOMA','',350.00,0.00,0.00,0,'2026-09-08 21:33:43','2026-09-08 23:52:00'),(30,'COLPOSCOPIA','',160.00,50.00,0.00,1,'2026-09-08 21:34:18','2026-09-08 21:34:18'),(31,'CONSULTA DE CONTROL PRENATAL','',100.00,50.00,0.00,1,'2026-09-08 21:35:15','2026-09-08 21:35:15'),(32,'CONTROL ECOGRAFICO','',30.00,20.00,0.00,1,'2026-09-08 21:46:14','2026-09-08 21:46:14'),(33,'ECOGRAFIA  5D','',240.00,80.00,0.00,1,'2026-09-08 21:47:00','2026-09-08 21:47:00'),(34,'ECO DE MAPEO DE ENDOMETRIOSIS','',250.00,100.00,0.00,1,'2026-09-08 21:47:45','2026-09-08 21:47:45'),(35,'PAQUETE PREVENITS','',380.00,60.00,0.00,1,'2026-09-08 21:48:47','2026-09-08 21:48:47'),(36,'HISTEROSONOGRAFIA','',250.00,60.00,0.00,1,'2026-09-08 21:53:03','2026-09-08 21:53:03'),(37,'HISTEROSCOPIA','',4500.00,300.00,0.00,1,'2026-09-08 21:56:14','2026-09-08 21:56:14'),(38,'RETIRO DE DIU','',100.00,20.00,0.00,1,'2026-09-08 21:58:37','2026-09-08 21:58:37'),(39,'SESION DE SILLA ELECTROMAGNETICA','',60.00,10.00,0.00,1,'2026-09-08 21:59:44','2026-09-08 21:59:44'),(40,'VAPORIZACION','',600.00,100.00,0.00,1,'2026-09-08 22:01:07','2026-09-08 22:01:07'),(41,'CAUTERIZACION','',200.00,30.00,0.00,1,'2026-09-08 22:02:10','2026-09-08 22:02:10'),(42,'PLASMA RICO EN PLAQUETAS + OZONO','',350.00,40.00,0.00,1,'2026-09-08 22:04:51','2026-09-08 22:04:51'),(43,'CONO LEEP','',2500.00,300.00,0.00,1,'2026-09-08 22:05:26','2026-09-08 22:05:26'),(44,'CONO FRIO','',3000.00,400.00,0.00,1,'2026-09-08 22:07:20','2026-09-08 22:07:20'),(45,'HILOS TENSORES','',2500.00,300.00,0.00,1,'2026-09-08 23:45:25','2026-09-08 23:45:25'),(46,'LAPAROSCOPICA','',5000.00,1199.96,0.00,1,'2026-09-08 23:47:38','2026-09-08 23:47:38'),(47,'QUISTECTOMIA LAPAROSCOPICA','',6000.00,0.00,0.00,1,'2026-09-08 23:48:45','2026-09-08 23:48:45'),(48,'QUISTECTOMÍA CIRUGÍA ABIERTA','',4000.00,0.00,0.00,1,'2026-09-08 23:49:08','2026-09-08 23:49:08'),(49,'ATENUACION DE CICATRIZ','',300.00,0.00,0.00,1,'2026-09-08 23:50:09','2026-09-08 23:50:09'),(50,'EXTRACCION DE FORINCULO','',350.00,0.00,0.00,1,'2026-09-08 23:54:18','2026-09-08 23:54:18'),(51,'EXTRACCION DE QUISTE DE BARTOLINO EN SALA','',3000.00,0.00,0.00,1,'2026-09-08 23:54:39','2026-09-08 23:54:39'),(52,'EXTRACION DE FIBROADENOMA DE MAMA','',2000.00,0.00,0.00,1,'2026-09-08 23:55:57','2026-09-08 23:55:57'),(53,'EXTRACCION DE 1 CONDILOMA + BIOPSIA','',350.00,0.00,0.00,1,'2026-09-08 23:56:45','2026-09-08 23:56:45'),(54,'EXTRACCION DE 2  CONDILOMAS + BIOPSIA','',400.00,0.00,0.00,1,'2026-09-08 23:58:32','2026-09-08 23:58:32'),(55,'EXTRACCION DE 3 CONDILOMAS + BIOPSIA','',600.00,0.00,0.00,1,'2026-09-08 23:58:45','2026-09-08 23:58:45'),(56,'EXTRACION DE QUISTE DE BARTOLINO / MARSULIZACION','',500.00,0.00,0.00,1,'2026-09-08 23:59:38','2026-09-08 23:59:38'),(57,'HISTERECTOMIA','',7000.00,0.00,0.00,1,'2026-09-09 00:00:37','2026-09-09 00:00:37'),(58,'HISTERECTOMIA ABDOMINAL TOTAL+SALPINGESTOMIA +FORECTOMIA','',9000.00,0.00,0.00,1,'2026-09-09 00:00:54','2026-09-09 00:00:54'),(59,'HISTERECTOMIA LAPAROSCOPICA','',8500.00,0.00,0.00,1,'2026-09-09 00:01:08','2026-09-09 00:01:08'),(60,'871	NIU	HISTERECTOMIA RADICAL+SALPINGUECTOMIA+OFORECTOMIA		120000	-2		','',120000.00,0.00,0.00,1,'2026-09-09 00:01:26','2026-09-09 00:01:26'),(61,'HISTERECTOMIA VAGINAL- ABDOMINAL','',6500.00,0.00,0.00,1,'2026-09-09 00:01:47','2026-09-09 00:01:47'),(62,'HISTEROCTOMIA VAGINAL+COLPORRAFIA','',6500.00,0.00,0.00,1,'2026-09-09 00:02:13','2026-09-09 00:02:13'),(63,'HISTEROSALPINOGRAFIA','',550.00,0.00,0.00,1,'2026-09-09 00:02:27','2026-09-09 00:02:27'),(64,'LABIOPLASTIA','',3500.00,0.00,0.00,1,'2026-09-09 00:02:59','2026-09-09 00:02:59'),(65,'LAPARATOMÍA EXPLORATORIA EMB. ECTOPICO + LAPAROSCOPICA','',6000.00,0.00,0.00,1,'2026-09-09 00:03:20','2026-09-09 00:03:20'),(66,'LAPARATOMIA EXPLORATORIA EN EMB. ECTÓPICO (C. ABIERTA)','',5000.00,0.00,0.00,1,'2026-09-09 00:03:38','2026-09-09 00:03:38'),(67,' PAQUETE DE 10 SESIONES DE LAVADO CON OZONO + INSUFLASION + 2 SESIONES DE PLASMA','',1000.00,0.00,0.00,1,'2026-09-09 00:04:28','2026-09-09 00:05:26'),(68,'MIOMECTOMIA ABIERTA','',7000.00,0.00,0.00,1,'2026-09-09 00:07:24','2026-09-09 00:07:24'),(69,'MONITOREO FETAL','',80.00,0.00,0.00,1,'2026-09-09 00:07:40','2026-09-09 00:07:40'),(70,'PAPANICOLAU EN BASE LIQUIDA','',150.00,0.00,0.00,1,'2026-09-09 00:12:29','2026-09-09 00:12:29'),(71,'PAQUETE DE MATERNIDAD BASICO CUOTA I','',700.00,0.00,0.00,1,'2026-09-09 00:14:15','2026-09-09 00:14:35'),(72,'PAQUETE DE MATERNIDAD BASICO CUOTA II','',600.00,0.00,0.00,1,'2026-09-09 00:14:58','2026-09-09 00:14:58'),(73,'PAQUETE DE MATERNIDAD BASICO CUOTA III','',300.00,0.00,0.00,1,'2026-09-09 00:15:14','2026-09-09 00:15:26'),(74,'PAQUETE DE MATERNIDAD BASICO CUOTA IV','',100.00,0.00,0.00,1,'2026-09-09 00:31:38','2026-09-09 00:31:38'),(75,'PAQUETE DE MATERNIDAD BRONCE','',2300.00,0.00,0.00,1,'2026-09-09 00:32:00','2026-09-09 00:32:12'),(76,'PAQUETE DE MATERNIDAD BRONCE CUOTA  1','',900.00,0.00,0.00,1,'2026-09-09 00:33:39','2026-09-09 00:33:39'),(77,'PAQUETE DE MATERNIDAD BRONCE CUOTA 2','',800.00,0.00,0.00,1,'2026-09-09 00:33:59','2026-09-09 00:33:59'),(78,'PAQUETE DE MATERNIDAD BRONCE CUOTA 3','',400.00,0.00,0.00,1,'2026-09-09 00:34:30','2026-09-09 00:34:30'),(79,'PAQUETE DE MATERNIDAD BRONCE CUOTA 4','',200.00,0.00,0.00,1,'2026-09-09 00:34:48','2026-09-09 00:34:48'),(80,'PAQUETE DE MATERNIDAD ORO','',3100.00,0.00,0.00,1,'2026-09-09 00:35:39','2026-09-09 00:35:39'),(81,'PAQUETE DE MATERNIDAD ORO CUOTA 1','',1300.00,0.00,0.00,1,'2026-09-09 00:35:59','2026-09-09 00:35:59'),(82,'PAQUETE  DE MATERNIDAD ORO CUOTA 2','',1100.00,0.00,0.00,1,'2026-09-09 00:36:28','2026-09-09 00:36:28'),(83,'PAQUETE DE MATERNIDAD ORO CUOTA 3','',500.00,0.00,0.00,1,'2026-09-09 00:36:46','2026-09-09 00:36:46'),(84,'PAQUETE DE MATERNIDAD ORO CUOTA 4','',200.00,0.00,0.00,1,'2026-09-09 00:37:13','2026-09-09 00:37:13'),(85,'PAQUETE DE MATERNIDAD PLATINUM','',2500.00,0.00,0.00,1,'2026-09-09 00:37:38','2026-09-09 00:37:38'),(86,'PAQUETE DE MATERNIDAD PLATINUM CUOTA 1','',900.00,0.00,0.00,1,'2026-09-09 00:37:57','2026-09-09 00:37:57'),(87,'PAQUETE DE MATERNIDAD PLATINUM CUOTA 2','',800.00,0.00,0.00,1,'2026-09-09 00:38:17','2026-09-09 00:38:17'),(88,'PAQUETE DE MATERNIDAD PLATINUM CUOTA 3','',600.00,0.00,0.00,1,'2026-09-09 00:38:37','2026-09-09 00:38:37'),(89,'PAQUETE DE MATERNIDAD PLATINUM CUOTA 4','',200.00,0.00,0.00,1,'2026-09-09 00:39:02','2026-09-09 00:39:02'),(90,'RETIRO DE IMPLANTE','',100.00,0.00,0.00,1,'2026-09-09 00:39:53','2026-09-09 00:39:53');
/*!40000 ALTER TABLE `tratamientos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `rol_id` int NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `doctor_id` int DEFAULT NULL,
  `estado_user` varchar(1) COLLATE utf8mb4_general_ci DEFAULT 'A',
  PRIMARY KEY (`id`),
  KEY `rol_id` (`rol_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`rol_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'devsDK','$2y$10$RIQL.QrTKnH4ycRL8WjXy.s/z1Z6aFjwNMd.YUBdcyFoMjiwJvyDm',1,'2026-01-16 21:56:06','2025-06-14 23:50:06',NULL,'A'),(2,'factujyza','$2y$10$FN4n9D1Ql6NQCstZ.u0OgOwc3v6MVVj9MT2w8F5OZcQNVam5H1osm',1,'2026-09-07 23:36:12','2026-09-07 23:36:12',NULL,'A'),(3,'Recepcion','$2y$10$mky17xbB.wyprHy5Vjtin..pieyzCYXyug2GLdIddmnWIGIvl87au',3,'2026-09-07 23:38:32','2026-09-07 23:38:32',NULL,'A');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios_permisos`
--

DROP TABLE IF EXISTS `usuarios_permisos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios_permisos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `usuario_id` int NOT NULL,
  `permiso_id` int NOT NULL,
  `allow` tinyint(1) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_usuario_permiso` (`usuario_id`,`permiso_id`),
  KEY `fk_up_permiso` (`permiso_id`),
  CONSTRAINT `usuarios_permisos_ibfk_1` FOREIGN KEY (`permiso_id`) REFERENCES `permisos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `usuarios_permisos_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios_permisos`
--

LOCK TABLES `usuarios_permisos` WRITE;
/*!40000 ALTER TABLE `usuarios_permisos` DISABLE KEYS */;
INSERT INTO `usuarios_permisos` VALUES (1,2,241,1,'2026-08-21 01:18:14'),(2,2,242,1,'2026-08-21 01:18:14'),(3,2,243,1,'2026-08-21 01:18:14'),(4,2,244,1,'2026-08-21 01:18:14'),(5,2,245,1,'2026-08-21 01:18:14'),(6,2,259,1,'2026-08-21 01:18:14'),(7,2,294,1,'2026-08-21 01:18:14'),(8,2,295,1,'2026-08-21 01:18:14'),(9,2,296,1,'2026-08-21 01:18:14'),(10,2,297,1,'2026-08-21 01:18:14'),(11,2,298,1,'2026-08-21 01:18:14'),(12,2,299,1,'2026-08-21 01:18:14'),(13,2,300,1,'2026-08-21 01:18:14'),(14,2,301,1,'2026-08-21 01:18:14'),(15,2,302,1,'2026-08-21 01:18:14'),(16,2,303,1,'2026-08-21 01:18:14'),(17,2,304,1,'2026-08-21 01:18:14'),(27,3,42,1,'2026-08-31 00:26:27'),(28,3,43,1,'2026-08-31 00:26:27'),(29,3,44,1,'2026-08-31 00:26:27'),(30,3,45,1,'2026-08-31 00:26:27'),(31,3,46,1,'2026-08-31 00:26:27'),(32,3,47,1,'2026-08-31 00:26:27'),(33,3,48,1,'2026-08-31 00:26:27'),(34,3,49,1,'2026-08-31 00:26:27'),(35,3,59,1,'2026-08-31 00:26:27'),(36,3,60,1,'2026-08-31 00:26:27'),(37,3,61,1,'2026-08-31 00:26:27'),(38,3,62,1,'2026-08-31 00:26:27'),(39,3,75,1,'2026-08-31 00:26:27'),(40,3,76,1,'2026-08-31 00:26:27'),(41,3,77,1,'2026-08-31 00:26:27'),(42,3,78,1,'2026-08-31 00:26:27'),(43,3,79,1,'2026-08-31 00:26:27'),(44,3,80,1,'2026-08-31 00:26:27'),(45,3,81,1,'2026-08-31 00:26:27'),(46,3,82,1,'2026-08-31 00:26:27'),(47,3,87,1,'2026-08-31 00:26:27'),(48,3,88,1,'2026-08-31 00:26:27'),(49,3,89,1,'2026-08-31 00:26:27'),(50,3,90,1,'2026-08-31 00:26:27'),(56,3,125,1,'2026-08-31 00:26:27'),(57,3,126,1,'2026-08-31 00:26:27'),(58,3,127,1,'2026-08-31 00:26:27'),(59,3,128,1,'2026-08-31 00:26:27'),(63,3,180,1,'2026-08-31 00:26:27'),(64,3,181,1,'2026-08-31 00:26:27'),(65,3,182,1,'2026-08-31 00:26:27'),(66,3,183,1,'2026-08-31 00:26:27'),(67,3,205,1,'2026-08-31 00:26:27'),(68,4,13,1,'2026-08-31 00:26:47'),(69,4,14,1,'2026-08-31 00:26:47'),(70,4,15,1,'2026-08-31 00:26:47'),(71,4,16,1,'2026-08-31 00:26:47'),(72,4,17,1,'2026-08-31 00:26:47'),(73,4,18,1,'2026-08-31 00:26:47'),(74,4,19,1,'2026-08-31 00:26:47'),(75,4,20,1,'2026-08-31 00:26:47'),(76,4,21,1,'2026-08-31 00:26:47'),(77,4,42,1,'2026-08-31 00:26:47'),(78,4,43,1,'2026-08-31 00:26:47'),(79,4,44,1,'2026-08-31 00:26:47'),(80,4,45,1,'2026-08-31 00:26:47'),(81,4,46,1,'2026-08-31 00:26:47'),(82,4,47,1,'2026-08-31 00:26:47'),(83,4,48,1,'2026-08-31 00:26:47'),(84,4,49,1,'2026-08-31 00:26:47'),(85,4,59,1,'2026-08-31 00:26:47'),(86,4,60,1,'2026-08-31 00:26:47'),(87,4,61,1,'2026-08-31 00:26:47'),(88,4,62,1,'2026-08-31 00:26:47'),(89,4,75,1,'2026-08-31 00:26:47'),(90,4,76,1,'2026-08-31 00:26:47'),(91,4,77,1,'2026-08-31 00:26:47'),(92,4,78,1,'2026-08-31 00:26:47'),(93,4,79,1,'2026-08-31 00:26:47'),(94,4,80,1,'2026-08-31 00:26:47'),(95,4,81,1,'2026-08-31 00:26:47'),(96,4,82,1,'2026-08-31 00:26:47'),(97,4,87,1,'2026-08-31 00:26:47'),(98,4,88,1,'2026-08-31 00:26:47'),(99,4,89,1,'2026-08-31 00:26:47'),(100,4,90,1,'2026-08-31 00:26:47'),(101,4,100,1,'2026-08-31 00:26:47'),(102,4,101,1,'2026-08-31 00:26:47'),(103,4,102,1,'2026-08-31 00:26:47'),(104,4,103,1,'2026-08-31 00:26:47'),(105,4,124,1,'2026-08-31 00:26:47'),(106,4,125,1,'2026-08-31 00:26:47'),(107,4,126,1,'2026-08-31 00:26:47'),(108,4,127,1,'2026-08-31 00:26:47'),(109,4,128,1,'2026-08-31 00:26:47'),(110,4,177,1,'2026-08-31 00:26:47'),(111,4,178,1,'2026-08-31 00:26:47'),(112,4,179,1,'2026-08-31 00:26:47'),(113,4,180,1,'2026-08-31 00:26:47'),(114,4,181,1,'2026-08-31 00:26:47'),(115,4,182,1,'2026-08-31 00:26:47'),(116,4,183,1,'2026-08-31 00:26:47'),(117,4,205,1,'2026-08-31 00:26:47'),(118,1,294,1,'2026-09-01 19:57:02'),(119,1,295,1,'2026-09-01 19:57:02'),(120,1,296,1,'2026-09-01 19:57:02'),(121,1,297,1,'2026-09-01 19:57:02'),(122,1,298,1,'2026-09-01 19:57:02'),(123,1,299,1,'2026-09-01 19:57:02'),(124,1,300,1,'2026-09-01 19:57:02'),(125,1,301,1,'2026-09-01 19:57:02'),(126,1,302,1,'2026-09-01 19:57:02'),(127,1,303,1,'2026-09-01 19:57:02'),(128,1,304,1,'2026-09-01 19:57:02'),(129,3,193,1,'2026-09-07 23:42:53'),(130,3,194,1,'2026-09-07 23:42:53'),(131,3,195,1,'2026-09-07 23:42:53'),(132,3,196,1,'2026-09-07 23:42:53'),(133,3,197,1,'2026-09-07 23:42:53'),(134,3,218,1,'2026-09-07 23:42:53'),(135,3,228,1,'2026-09-07 23:42:53'),(136,3,229,1,'2026-09-07 23:42:53'),(137,3,230,1,'2026-09-07 23:42:53'),(138,3,231,1,'2026-09-07 23:42:53'),(139,3,232,1,'2026-09-07 23:42:53'),(140,3,240,1,'2026-09-07 23:42:53'),(141,3,241,1,'2026-09-07 23:42:53'),(142,3,242,1,'2026-09-07 23:42:53'),(143,3,243,1,'2026-09-07 23:42:53'),(144,3,244,1,'2026-09-07 23:42:53'),(145,3,245,1,'2026-09-07 23:42:53');
/*!40000 ALTER TABLE `usuarios_permisos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vista_reporte_productos`
--

DROP TABLE IF EXISTS `vista_reporte_productos`;
/*!50001 DROP VIEW IF EXISTS `vista_reporte_productos`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_reporte_productos` AS SELECT 
 1 AS `producto_id`,
 1 AS `nombre`,
 1 AS `codigo`,
 1 AS `categoria_id`,
 1 AS `categoria_nombre`,
 1 AS `proveedor_id`,
 1 AS `proveedor_nombre`,
 1 AS `proveedor_whatsapp`,
 1 AS `proveedor_email`,
 1 AS `precio_compra`,
 1 AS `precio_venta`,
 1 AS `margen_unitario`,
 1 AS `margen_porcentaje`,
 1 AS `stock`,
 1 AS `stock_minimo`,
 1 AS `valor_inventario`,
 1 AS `estado_stock`,
 1 AS `activo`,
 1 AS `created`,
 1 AS `modified`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `vista_reporte_productos`
--

/*!50001 DROP VIEW IF EXISTS `vista_reporte_productos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_reporte_productos` AS select `p`.`id` AS `producto_id`,`p`.`nombre` AS `nombre`,`p`.`codigo` AS `codigo`,`p`.`categoria_producto_id` AS `categoria_id`,`cp`.`nombre` AS `categoria_nombre`,`p`.`proveedor_id` AS `proveedor_id`,`pr`.`nombre` AS `proveedor_nombre`,`pr`.`whatsapp` AS `proveedor_whatsapp`,`pr`.`email` AS `proveedor_email`,`p`.`precio_compra` AS `precio_compra`,`p`.`precio` AS `precio_venta`,(`p`.`precio` - `p`.`precio_compra`) AS `margen_unitario`,(case when (`p`.`precio_compra` > 0) then round((((`p`.`precio` - `p`.`precio_compra`) / `p`.`precio_compra`) * 100),2) else 0 end) AS `margen_porcentaje`,`p`.`stock` AS `stock`,`p`.`stock_minimo` AS `stock_minimo`,(`p`.`stock` * `p`.`precio_compra`) AS `valor_inventario`,(case when (`p`.`stock` <= 0) then 'AGOTADO' when ((`p`.`stock_minimo` > 0) and (`p`.`stock` <= `p`.`stock_minimo`)) then 'BAJO' else 'NORMAL' end) AS `estado_stock`,`p`.`estado` AS `activo`,`p`.`created` AS `created`,`p`.`modified` AS `modified` from ((`productos` `p` left join `categorias_productos` `cp` on((`cp`.`id` = `p`.`categoria_producto_id`))) left join `proveedores` `pr` on((`pr`.`id` = `p`.`proveedor_id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-09-10  4:37:34
