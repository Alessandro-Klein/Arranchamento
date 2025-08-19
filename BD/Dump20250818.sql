-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: localhost    Database: sistema_arranchamento
-- ------------------------------------------------------
-- Server version	8.0.40

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
-- Table structure for table `arranchamento`
--

DROP TABLE IF EXISTS `arranchamento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `arranchamento` (
  `id` int NOT NULL AUTO_INCREMENT,
  `data` date NOT NULL,
  `refeicao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int DEFAULT NULL,
  `presente` tinyint(1) DEFAULT '0',
  `faltou` tinyint(1) DEFAULT '0',
  `status` enum('ativo','cancelado') COLLATE utf8mb4_unicode_ci DEFAULT 'ativo',
  PRIMARY KEY (`id`),
  KEY `arranchamento_ibfk_1` (`user_id`),
  CONSTRAINT `arranchamento_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arranchamento`
--

LOCK TABLES `arranchamento` WRITE;
/*!40000 ALTER TABLE `arranchamento` DISABLE KEYS */;
INSERT INTO `arranchamento` VALUES (93,'2025-07-21','Café da Manhã',1,0,0,'ativo'),(94,'2025-07-21','Almoço',1,0,0,'ativo'),(95,'2025-07-21','Jantar',1,0,0,'ativo'),(104,'2025-07-23','Café da Manhã',1,0,0,'ativo'),(108,'2025-07-20','Almoço',1,1,0,'ativo'),(109,'2025-07-22','Café da Manhã',1,0,0,'ativo'),(110,'2025-07-22','Almoço',1,0,0,'ativo'),(122,'2025-08-21','Jantar',1,0,0,'ativo');
/*!40000 ALTER TABLE `arranchamento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `arranchamentos`
--

DROP TABLE IF EXISTS `arranchamentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `arranchamentos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `refeicao` enum('Café da Manhã','Almoço','Jantar') COLLATE utf8mb4_unicode_ci NOT NULL,
  `data` date NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `arranchamentos`
--

LOCK TABLES `arranchamentos` WRITE;
/*!40000 ALTER TABLE `arranchamentos` DISABLE KEYS */;
/*!40000 ALTER TABLE `arranchamentos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cardapio`
--

DROP TABLE IF EXISTS `cardapio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cardapio` (
  `dia` date NOT NULL,
  `cafe` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `almoco` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `janta` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`dia`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cardapio`
--

LOCK TABLES `cardapio` WRITE;
/*!40000 ALTER TABLE `cardapio` DISABLE KEYS */;
INSERT INTO `cardapio` VALUES ('2025-07-16','Pão francês fresquinho, fatias de queijo prato e bebida quente à escolha (café, leite ou chá)','Arroz branco soltinho, feijão temperado, frango grelhado suculento e salada verde','Sopa caseira de legumes, pão integral e chá de ervas para uma refeição leve\r\n\r\n'),('2025-07-18','teste','teste','teste'),('2025-07-20','teste','teste','teste');
/*!40000 ALTER TABLE `cardapio` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `confirmacoes`
--

DROP TABLE IF EXISTS `confirmacoes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `confirmacoes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `arranchamento_id` int NOT NULL,
  `confirmado` tinyint(1) DEFAULT '1',
  `confirmado_em` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `arranchamento_id` (`arranchamento_id`),
  CONSTRAINT `confirmacoes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `confirmacoes_ibfk_2` FOREIGN KEY (`arranchamento_id`) REFERENCES `arranchamentos` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `confirmacoes`
--

LOCK TABLES `confirmacoes` WRITE;
/*!40000 ALTER TABLE `confirmacoes` DISABLE KEYS */;
/*!40000 ALTER TABLE `confirmacoes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_guerra` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `posto` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('admin','gerente','usuario') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'usuario',
  `om` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_militar` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `email_2` (`email`),
  UNIQUE KEY `nome` (`nome`),
  UNIQUE KEY `unq_nome` (`nome`),
  UNIQUE KEY `unq_email` (`email`),
  UNIQUE KEY `unq_guerra_posto` (`nome_guerra`,`posto`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Administrador B Adm Ap 3ªRM','ALESSANDRO','klein98@gmail.com','$2y$10$FcTW.1yUHyOM5oc30weQte/zsK6orDcIwctnLNJmz159BJVbRIEwy','2025-04-29 02:59:43','Major','admin','B Adm Ap3ªRM',''),(61,'Filipe Lira Sobrinho','32660675Ale','maximos@3.com.br','$2y$10$sWcor6bk8hrTupIoE856ZOm3kNFPaMo/Qu59xRQsg1vhaEDRJlT9u','2025-08-03 14:00:22','1º Tenente','gerente','3ºGPTLOG',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-18 23:17:15
