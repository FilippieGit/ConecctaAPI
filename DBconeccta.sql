-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 02/06/2025 às 06:14
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
-- Estrutura para tabela `candidaturas`
--

CREATE TABLE `candidaturas` (
  `id_candidatura` int(11) NOT NULL,
  `vaga_id` int(11) NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `respostas` text DEFAULT NULL,
  `data_candidatura` datetime DEFAULT current_timestamp(),
  `status` enum('pendente','visualizada','aprovada','rejeitada') DEFAULT 'pendente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `candidaturas`
--

INSERT INTO `candidaturas` (`id_candidatura`, `vaga_id`, `user_id`, `respostas`, `data_candidatura`, `status`) VALUES
(4, 41, 2, '{\"interesse\":\"12313\",\"expectativas\":\"1313\",\"valores\":\"13131313\"}', '2025-06-01 16:52:44', 'pendente');

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
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `firebase_uid` varchar(255) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `username` varchar(100) NOT NULL,
  `genero` varchar(50) DEFAULT NULL,
  `idade` int(10) UNSIGNED DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `setor` varchar(100) DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `experiencia_profissional` text DEFAULT NULL,
  `formacao_academica` text DEFAULT NULL,
  `certificados` text DEFAULT NULL,
  `imagem_perfil` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `firebase_uid`, `nome`, `username`, `genero`, `idade`, `telefone`, `email`, `setor`, `descricao`, `experiencia_profissional`, `formacao_academica`, `certificados`, `imagem_perfil`) VALUES
(2, 'azHGeFjqcFXsRtdt3Xo2s3iC7Cd2', 'Felip', '', '', 0, '', 'lulafelipe7@gmail.com', '', '', 'Array', 'Array', '[]', ''),
(4, 'Zz3B0U6OvkOOzcQPxFisRDMfGkx1', 'Teste', 'Felipethums07', '', NULL, '', '0', '', '', '[]', '[]', '[]', ''),
(23, 'ca0vzfwcNaaCszxd3uCnQLu5dta2', 'Felipe Lula', 'lulagadogurilla', '', NULL, '', 'lulagadogurilla@gmail.com', 'rsrs', '', '', '', '', '');

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
  `salario_vagas` decimal(10,2) DEFAULT NULL,
  `vinculo_vagas` varchar(50) DEFAULT NULL,
  `beneficios_vagas` text DEFAULT NULL,
  `ramo_vagas` varchar(50) DEFAULT NULL,
  `nivel_experiencia` varchar(255) DEFAULT NULL,
  `tipo_contrato` varchar(255) DEFAULT NULL,
  `area_atuacao` varchar(255) DEFAULT NULL,
  `habilidades_desejaveis` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `vagas`
--

INSERT INTO `vagas` (`id_vagas`, `id_empresa`, `id_candidato`, `titulo_vagas`, `descricao_vagas`, `local_vagas`, `requisitos_vagas`, `salario_vagas`, `vinculo_vagas`, `beneficios_vagas`, `ramo_vagas`, `nivel_experiencia`, `tipo_contrato`, `area_atuacao`, `habilidades_desejaveis`) VALUES
(41, 1, 2, 'Desenvolvedor PHP e MySQL', 'Atuação em São Paulo com foco em desenvolvimento PHP e MySQL. Necessário conhecimento nas tecnologias citadas. Salário de R$ 3.500,00, CLT, benefícios de vale transporte e vale refeição. Área de Tecnologia.', 'São Paulo', 'Conhecimento em PHP e MySQL', 3500.00, 'CLT', 'Vale transporte, Vale refeição', 'Tecnologia', NULL, NULL, NULL, NULL),
(42, 2, 2, 'Desenvolvedor Java Pleno', 'Vaga para o Rio de Janeiro, experiência em Java. Contratação PJ, home office e seguro saúde. Salário de R$ 4.200,00. Área de Desenvolvimento.', 'Rio de Janeiro', 'Experiência em Java', 4200.00, 'PJ', 'Home office, Seguro saúde', 'Desenvolvimento', NULL, NULL, NULL, NULL),
(63, 1, 1, 'Full Stack PHP/JavaScript', 'Oportunidade em São Paulo para desenvolvedor com conhecimentos em PHP, MySQL e JavaScript. Salário de R$ 4.000,00, CLT, benefícios de vale transporte e vale refeição. Área de Tecnologia.', 'São Paulo', 'PHP, MySQL, JavaScript', 4000.00, 'CLT', 'Vale transporte, Vale refeição', 'Tecnologia', NULL, NULL, NULL, NULL),
(64, 2, 2, 'Desenvolvedor Java com Spring', 'Atuação no Rio de Janeiro, experiência em Java, Spring e SQL. Contratação PJ, home office e plano de saúde. Salário de R$ 4.500,00. Área de Desenvolvimento.', 'Rio de Janeiro', 'Java, Spring, SQL', 4500.00, 'PJ', 'Home office, Plano de saúde', 'Desenvolvimento', NULL, NULL, NULL, NULL),
(65, 2, 2, 'Desenvolvedor Python/Django', 'Vaga em Belo Horizonte para desenvolvedor com experiência em Python, Django e REST APIs. Salário de R$ 4.200,00, CLT, benefícios de vale alimentação e seguro odontológico. Área de Desenvolvimento Web.', 'Belo Horizonte', 'Python, Django, REST APIs', 4200.00, 'CLT', 'Vale alimentação, Seguro odontológico', 'Desenvolvimento Web', NULL, NULL, NULL, NULL),
(66, 2, 2, 'Desenvolvedor C# .NET', 'Oportunidade em Curitiba para desenvolvedor com conhecimentos em C#, .NET e SQL Server. Salário de R$ 4.300,00, CLT, benefícios de vale transporte e bônus anual. Área de Desenvolvimento.', 'Curitiba', 'C#, .NET, SQL Server', 4300.00, 'CLT', 'Vale transporte, Bônus anual', 'Desenvolvimento', NULL, NULL, NULL, NULL),
(67, 1, 1, 'Desenvolvedor Front-end React', 'Vaga em Fortaleza para desenvolvedor com experiência em JavaScript, React e Node.js. Contratação PJ, home office e seguro saúde. Salário de R$ 4.700,00. Área de Front-end.', 'Fortaleza', 'JavaScript, React, Node.js', 4700.00, 'PJ', 'Home office, Seguro saúde', 'Front-end', NULL, NULL, NULL, NULL),
(68, 2, 1, 'Desenvolvedor Back-end PHP', 'Oportunidade em Salvador para desenvolvedor com conhecimentos em PHP, Laravel e MySQL. Salário de R$ 4.100,00, CLT, benefícios de vale refeição e plano de saúde. Área de Back-end.', 'Salvador', 'PHP, Laravel, MySQL', 4100.00, 'CLT', 'Vale refeição, Plano de saúde', 'Back-end', NULL, NULL, NULL, NULL),
(69, 2, 2, 'Desenvolvedor Ruby on Rails', 'Vaga para Porto Alegre, experiência em Ruby on Rails e PostgreSQL. Contratação PJ, home office e bônus por desempenho. Salário de R$ 4.400,00. Área de Desenvolvimento.', 'Porto Alegre', 'Ruby on Rails, PostgreSQL', 4400.00, 'PJ', 'Home office, Bônus por desempenho', 'Desenvolvimento', NULL, NULL, NULL, NULL),
(70, 1, 2, 'Arquiteto de Software Java', 'Oportunidade em Brasília para arquiteto de software com experiência em Java, Angular e Microservices. Salário de R$ 4.600,00, CLT, benefícios de vale transporte e seguro de vida. Área de Arquitetura de Software.', 'Brasília', 'Java, Angular, Microservices', 4600.00, 'CLT', 'Vale transporte, Seguro de vida', 'Arquitetura de Software', NULL, NULL, NULL, NULL),
(71, 2, 1, 'Web Designer WordPress', 'Vaga em Manaus para profissional com experiência em PHP, WordPress e SEO. Salário de R$ 3.900,00, CLT, benefícios de vale alimentação e plano odontológico. Área de Web Design.', 'Manaus', 'PHP, WordPress, SEO', 3900.00, 'CLT', 'Vale alimentação, Plano odontológico', 'Web Design', NULL, NULL, NULL, NULL),
(72, 1, 2, 'Novo Título', 'Nova descrição da vaga', 'Recife', 'UX/UI Design, Adobe XD, Figma', 4200.00, 'PJ', 'Home office, Vale refeição', 'Design', NULL, NULL, NULL, NULL),
(84, 1, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(85, 1, NULL, 'Desenvolvedor Android Studio', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(86, 1, NULL, 'Desenvolvedor Android Studio', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto77', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(87, 1, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(88, 1, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(89, 1, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(90, 1, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(91, 1, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(92, 1, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `candidaturas`
--
ALTER TABLE `candidaturas`
  ADD PRIMARY KEY (`id_candidatura`),
  ADD KEY `vaga_id` (`vaga_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`);

--
-- Índices de tabela `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_empresa`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `firebase_uid` (`firebase_uid`);

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
-- AUTO_INCREMENT de tabela `candidaturas`
--
ALTER TABLE `candidaturas`
  MODIFY `id_candidatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_empresa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de tabela `vagas`
--
ALTER TABLE `vagas`
  MODIFY `id_vagas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=103;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `candidaturas`
--
ALTER TABLE `candidaturas`
  ADD CONSTRAINT `candidaturas_ibfk_1` FOREIGN KEY (`vaga_id`) REFERENCES `vagas` (`id_vagas`),
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `vagas`
--
ALTER TABLE `vagas`
  ADD CONSTRAINT `vagas_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
