-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 23/05/2025 às 23:01
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `dbconeccta`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `candidato`
--

CREATE TABLE `candidato` (
  `id_candidato` int(11) NOT NULL,
  `cpf_candidato` char(14) NOT NULL,
  `nome_candidato` varchar(50) NOT NULL,
  `telefone_candidato` char(12) DEFAULT NULL,
  `email_candidato` varchar(100) DEFAULT NULL,
  `local_candidato` varchar(100) DEFAULT NULL,
  `data_nasc_candidato` date DEFAULT NULL,
  `estado_civil_candidato` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `candidato`
--

INSERT INTO `candidato` (`id_candidato`, `cpf_candidato`, `nome_candidato`, `telefone_candidato`, `email_candidato`, `local_candidato`, `data_nasc_candidato`, `estado_civil_candidato`) VALUES
(1, '12345678901', 'João Silva', '11999998888', 'joao@email.com', 'São Paulo', '1990-01-01', 'Solteiro'),
(2, '98765432100', 'Maria Souza', '21988887777', 'maria@email.com', 'Rio de Janeiro', '1985-05-15', 'Casado'),
(3, '12345678901', 'João Silva', '11999998888', 'joao@email.com', 'São Paulo', '1990-01-01', 'Solteiro'),
(4, '98765432100', 'Maria Souza', '21988887777', 'maria@email.com', 'Rio de Janeiro', '1985-05-15', 'Casado'),
(5, '12345678901', 'João Silva', '11999998888', 'joao@email.com', 'São Paulo', '1990-01-01', 'Solteiro'),
(6, '98765432100', 'Maria Souza', '21988887777', 'maria@email.com', 'Rio de Janeiro', '1985-05-15', 'Casado');

-- --------------------------------------------------------

--
-- Estrutura para tabela `curriculo`
--

CREATE TABLE `curriculo` (
  `id_curriculo` int(11) NOT NULL,
  `id_candidato` int(11) NOT NULL,
  `descricao_curriculo` varchar(250) DEFAULT NULL,
  `exper_profissional_curriculo` text DEFAULT NULL,
  `exper_academico_curriculo` text DEFAULT NULL,
  `certificados_curriculo` text DEFAULT NULL,
  `endereco_curriculo` varchar(50) DEFAULT NULL,
  `linkedln_curriculo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `empresa`
--

CREATE TABLE `empresa` (
  `id_empresa` int(11) NOT NULL,
  `cnpj_empresa` char(14) NOT NULL,
  `nome_empresa` varchar(50) NOT NULL,
  `email_empresa` varchar(100) DEFAULT NULL,
  `local_empresa` varchar(100) DEFAULT NULL,
  `porte_empresa` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `empresa`
--

INSERT INTO `empresa` (`id_empresa`, `cnpj_empresa`, `nome_empresa`, `email_empresa`, `local_empresa`, `porte_empresa`) VALUES
(1, '12345678000199', 'Tech Solutions Ltda', 'contato@techsolutions.com', 'Av. Paulista, 1000 - São Paulo/SP', 'Grande'),
(2, '98765432000188', 'Inova Tech', 'contato@inovatech.com', 'Rua das Flores, 200 - Rio de Janeiro/RJ', 'Médio'),
(3, '12345678000199', 'Tech Solutions Ltda', 'contato@techsolutions.com', 'Av. Paulista, 1000 - São Paulo/SP', 'Grande'),
(4, '98765432000188', 'Inova Tech', 'contato@inovatech.com', 'Rua das Flores, 200 - Rio de Janeiro/RJ', 'Médio'),
(5, '12345678000199', 'Tech Solutions Ltda', 'contato@techsolutions.com', 'Av. Paulista, 1000 - São Paulo/SP', 'Grande'),
(6, '98765432000188', 'Inova Tech', 'contato@inovatech.com', 'Rua das Flores, 200 - Rio de Janeiro/RJ', 'Médio'),
(7, '12345678000101', 'Tech Solutions Ltda', 'contato@techsolutions.com', 'Av. Paulista, 1000 - São Paulo/SP', 'Grande'),
(8, '22345678000102', 'Inova Tech', 'contato@inovatech.com', 'Rua das Flores, 200 - Rio de Janeiro/RJ', 'Médio'),
(9, '32345678000103', 'SoftDev Ltda', 'contato@softdev.com', 'Av. Brasil, 500 - Belo Horizonte/MG', 'Pequeno'),
(10, '42345678000104', 'Code Masters', 'contato@codemasters.com', 'Rua das Palmeiras, 300 - Curitiba/PR', 'Médio'),
(11, '52345678000105', 'DevExperts', 'contato@devexperts.com', 'Av. Amazonas, 1500 - Fortaleza/CE', 'Grande'),
(12, '62345678000106', 'ByteWorks', 'contato@byteworks.com', 'Rua do Comércio, 100 - Salvador/BA', 'Pequeno'),
(13, '72345678000107', 'AppCreators', 'contato@appcreators.com', 'Av. Independência, 700 - Porto Alegre/RS', 'Médio'),
(14, '82345678000108', 'NextGen Soft', 'contato@nextgensoft.com', 'Rua das Acácias, 400 - Brasília/DF', 'Grande'),
(15, '92345678000109', 'Smart Solutions', 'contato@smartsolutions.com', 'Av. das Nações, 900 - Manaus/AM', 'Pequeno'),
(16, '02345678000110', 'Digital Innovators', 'contato@digitalinnovators.com', 'Rua Central, 250 - Recife/PE', 'Médio');

-- --------------------------------------------------------

--
-- Estrutura para tabela `vagas`
--

CREATE TABLE `vagas` (
  `id_vagas` int(11) NOT NULL,
  `id_empresa` int(11) NOT NULL,
  `id_candidato` int(11) DEFAULT NULL,
  `titulo_vagas` varchar(100) DEFAULT NULL,
  `descricao_vagas` text DEFAULT NULL,
  `local_vagas` varchar(100) DEFAULT NULL,
  `requisitos_vagas` text DEFAULT NULL,
  `nivel_experiencia` varchar(50) DEFAULT NULL,
  `tipo_contrato` varchar(50) DEFAULT NULL,
  `area_atuacao` varchar(50) DEFAULT NULL,
  `salario_vagas` decimal(10,2) DEFAULT NULL,
  `vinculo_vagas` varchar(50) DEFAULT NULL,
  `beneficios_vagas` text DEFAULT NULL,
  `habilidades_desejaveis` text DEFAULT NULL,
  `ramo_vagas` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vagas`
--

INSERT INTO `vagas` (`id_vagas`, `id_empresa`, `id_candidato`, `titulo_vagas`, `descricao_vagas`, `local_vagas`, `requisitos_vagas`, `nivel_experiencia`, `tipo_contrato`, `area_atuacao`, `salario_vagas`, `vinculo_vagas`, `beneficios_vagas`, `habilidades_desejaveis`, `ramo_vagas`) VALUES
(42, 2, 2, 'Desenvolvedor Java Pleno', 'Vaga para o Rio de Janeiro, experiência em Java. Contratação PJ, home office e seguro saúde. Salário de R$ 4.200,00. Área de Desenvolvimento.', 'Rio de Janeiro', 'Experiência em Java', NULL, NULL, NULL, 4200.00, 'PJ', 'Home office, Seguro saúde', NULL, 'Desenvolvimento'),
(92, 1, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', NULL, NULL, NULL, 5000.00, 'CLT', NULL, NULL, 'TI'),
(103, 1, NULL, 'tdte', 'ete', 'tet', '21', NULL, NULL, NULL, 1.00, 'CLT', NULL, NULL, 'Tecnologia da Informação'),
(104, 1, NULL, 'a', 'a', '1', '1', NULL, NULL, NULL, 1.00, 'CLT', NULL, NULL, 'Tecnologia da Informação'),
(105, 1, NULL, '11BC', '1', '11', '1', NULL, NULL, NULL, 111.00, 'CLT', NULL, NULL, 'Tecnologia da Informação'),
(107, 1, NULL, '999', '999', '9', '9', NULL, NULL, NULL, 999.00, 'CLT', NULL, NULL, 'Tecnologia da Informação'),
(108, 1, NULL, '888', '888', '888', '88', NULL, NULL, NULL, 888.00, 'CLT', NULL, NULL, 'Tecnologia da Informação'),
(109, 1, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', NULL, NULL, NULL, 5000.00, 'CLT', 'AA', NULL, 'TI'),
(110, 1, NULL, '777', '777', '777', '77', NULL, NULL, NULL, 77.00, 'CLT', '7', NULL, 'Tecnologia da Informação'),
(111, 1, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', NULL, NULL, NULL, 5000.00, 'CLT', 'eeeeee', NULL, 'TI'),
(112, 1, NULL, 'oi', 'oi', 'oi', 'oi', NULL, NULL, NULL, 11.00, 'CLT', 'oi', NULL, 'Tecnologia da Informação'),
(113, 1, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', NULL, NULL, NULL, 5000.00, 'CLT', '6', NULL, 'TI'),
(114, 1, NULL, 'a', 'a', '11', 'a', NULL, NULL, NULL, 111.00, 'CLT', 'aaa', NULL, 'Tecnologia da Informação'),
(115, 1, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 'Júnior', 'CLT', 'Desenvolvimento Mobile', 5000.00, NULL, 'VT, VR, Plano de saúde', 'Experiência com arquitetura MVVM', 'Tecnologia'),
(116, 1, NULL, 'teste', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 'Júnior', 'CLT', 'Desenvolvimento Mobile', 5000.00, NULL, 'VT, VR, Plano de saúde', 'Experiência com arquitetura MVVM', 'Tecnologia');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `candidato`
--
ALTER TABLE `candidato`
  ADD PRIMARY KEY (`id_candidato`);

--
-- Índices de tabela `curriculo`
--
ALTER TABLE `curriculo`
  ADD PRIMARY KEY (`id_curriculo`),
  ADD KEY `id_candidato` (`id_candidato`);

--
-- Índices de tabela `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_empresa`);

--
-- Índices de tabela `vagas`
--
ALTER TABLE `vagas`
  ADD PRIMARY KEY (`id_vagas`),
  ADD KEY `id_empresa` (`id_empresa`),
  ADD KEY `id_candidato` (`id_candidato`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `candidato`
--
ALTER TABLE `candidato`
  MODIFY `id_candidato` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `curriculo`
--
ALTER TABLE `curriculo`
  MODIFY `id_curriculo` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `vagas`
--
ALTER TABLE `vagas`
  MODIFY `id_vagas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `curriculo`
--
ALTER TABLE `curriculo`
  ADD CONSTRAINT `curriculo_ibfk_1` FOREIGN KEY (`id_candidato`) REFERENCES `candidato` (`id_candidato`) ON DELETE CASCADE;

--
-- Restrições para tabelas `vagas`
--
ALTER TABLE `vagas`
  ADD CONSTRAINT `vagas_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
