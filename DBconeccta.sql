-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 11/06/2025 às 04:14
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
  `status` enum('pendente','visualizada','aprovada','rejeitada') NOT NULL DEFAULT 'pendente',
  `data_atualizacao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `motivo_rejeicao` varchar(255) DEFAULT NULL,
  `recrutador_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `candidaturas`
--

INSERT INTO `candidaturas` (`id_candidatura`, `vaga_id`, `user_id`, `respostas`, `data_candidatura`, `status`, `data_atualizacao`, `motivo_rejeicao`, `recrutador_id`) VALUES
(4, 41, 2, '{\"interesse\":\"12313\",\"expectativas\":\"1313\",\"valores\":\"13131313\"}', '2025-06-01 16:52:44', 'rejeitada', '2025-06-10 23:10:29', NULL, 25),
(5, 41, 24, '{\"interesse\":\"Filippie\",\"expectativas\":\"Filippie\",\"valores\":\"Filippie\"}', '2025-06-04 16:57:39', 'aprovada', '2025-06-10 23:10:31', NULL, 25);

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
  `imagem_perfil` varchar(255) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `CNPJ` varchar(50) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `fcm_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `firebase_uid`, `nome`, `username`, `genero`, `idade`, `telefone`, `email`, `setor`, `descricao`, `experiencia_profissional`, `formacao_academica`, `certificados`, `imagem_perfil`, `tipo`, `CNPJ`, `website`, `fcm_token`) VALUES
(2, '2IpuGWWdVYSO7f4Qm6VdU2JIwDR2', 'Felipe Lula', 'lulafelipe7', '', NULL, '', 'lulafelipe7@gmail.com', '', '', '[]', '[]', '[]', '', 'Física', '', '', NULL),
(4, 'Zz3B0U6OvkOOzcQPxFisRDMfGkx1', 'Teste', 'Felipethums07', '', NULL, '', '0', '', '', '[]', '[]', '[]', '', NULL, NULL, NULL, NULL),
(23, 'ca0vzfwcNaaCszxd3uCnQLu5dta2', 'Felipe Lula', 'lulagadogurilla', '', NULL, '', 'lulagadogurilla@gmail.com', 'rsrs', '', '', '', '', '', NULL, NULL, NULL, NULL),
(24, 'ayJU8fpgcPc0sK1qY7KSPsXShJ32', 'Filippie Amaral', 'reservac31', '', NULL, '', 'reservac31@gmail.com', '', '', '[]', '[]', '[]', '', 'Física', '', '', NULL),
(25, 'Yp8AlhokRyN5JVFjZ5Rc9Z05n0S2', 'TechSolutions LTDA.', 'reservac32', '', NULL, '', 'reservac32@gmail.com', '', '', '[]', '[]', '[]', '', 'Jurídica', '123123123122311', 'TechSolutions.com.br', NULL),
(26, 'WJshVxy4RQPiBHG16Kaq8iVD8dn1', 'Gustavo', 'gustavopthums', '', NULL, '', 'gustavopthums@gmail.com', '', '', '[]', '[]', '[]', '', NULL, NULL, NULL, NULL),
(28, 'xgjkhJ9P8fO9jRf8apXBZ0rbN5j1', 'Matheus', 'zorkynanyomae', '', NULL, '', 'zorkynanyomae@gmail.com', '', '', '[]', '[]', '[]', '', 'Física', NULL, NULL, NULL),
(29, 'GDQGTej1qeRiPhPYPI4jLUjjBB32', 'ksksks', 'lulacssbuy13', '', NULL, '', 'lulacssbuy13@gmail.com', '', '', '[]', '[]', '[]', '', 'Física', '', '', NULL),
(30, 'FvdQNhiFapY1XQCC7YeoyGzOe9s2', '1231', 'filippieamaral', '', NULL, '', 'filippieamaral@gmail.com', '', '', '[]', '[]', '[]', '', 'Jurídica', '213', '213213', NULL),
(32, 'gFZ42GnGRbNmKuR4zQnDAQSZMeX2', 'matheus', 'matheuszor14', '', NULL, '', 'matheuszor14@gmail.com', '', '', '[]', '[]', '[]', '', 'Física', '', '', NULL),
(33, 'gBCXwrfl1zNNYThLI6LL7laUjLj1', '123123', 'filippielindoo', '', NULL, '', 'filippielindoo@gmail.com', '', '', '[]', '[]', '[]', '', 'Física', '', '', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `vagas`
--

CREATE TABLE `vagas` (
  `id_vagas` int(11) NOT NULL,
  `id_usuario` bigint(20) UNSIGNED NOT NULL,
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

INSERT INTO `vagas` (`id_vagas`, `id_usuario`, `id_candidato`, `titulo_vagas`, `descricao_vagas`, `local_vagas`, `requisitos_vagas`, `salario_vagas`, `vinculo_vagas`, `beneficios_vagas`, `ramo_vagas`, `nivel_experiencia`, `tipo_contrato`, `area_atuacao`, `habilidades_desejaveis`) VALUES
(41, 25, 3, 'Desenvolvedor PHP e MySQL', 'Atuação em São Paulo com foco em desenvolvimento PHP e MySQL. Necessário conhecimento nas tecnologias citadas. Salário de R$ 3.500,00, CLT, benefícios de vale transporte e vale refeição. Área de Tecnologia.', 'São Paulo', 'Conhecimento em PHP e MySQL', 3500.00, 'CLT', 'Vale transporte, Vale refeição', 'Tecnologia', NULL, NULL, NULL, NULL),
(42, 2, 2, 'Desenvolvedor Java Pleno', 'Vaga para o Rio de Janeiro, experiência em Java. Contratação PJ, home office e seguro saúde. Salário de R$ 4.200,00. Área de Desenvolvimento.', 'Rio de Janeiro', 'Experiência em Java', 4200.00, 'PJ', 'Home office, Seguro saúde', 'Desenvolvimento', NULL, NULL, NULL, NULL),
(63, 2, 1, 'Full Stack PHP/JavaScript', 'Oportunidade em São Paulo para desenvolvedor com conhecimentos em PHP, MySQL e JavaScript. Salário de R$ 4.000,00, CLT, benefícios de vale transporte e vale refeição. Área de Tecnologia.', 'São Paulo', 'PHP, MySQL, JavaScript', 4000.00, 'CLT', 'Vale transporte, Vale refeição', 'Tecnologia', NULL, NULL, NULL, NULL),
(64, 2, 2, 'Desenvolvedor Java com Spring', 'Atuação no Rio de Janeiro, experiência em Java, Spring e SQL. Contratação PJ, home office e plano de saúde. Salário de R$ 4.500,00. Área de Desenvolvimento.', 'Rio de Janeiro', 'Java, Spring, SQL', 4500.00, 'PJ', 'Home office, Plano de saúde', 'Desenvolvimento', NULL, NULL, NULL, NULL),
(65, 2, 2, 'Desenvolvedor Python/Django', 'Vaga em Belo Horizonte para desenvolvedor com experiência em Python, Django e REST APIs. Salário de R$ 4.200,00, CLT, benefícios de vale alimentação e seguro odontológico. Área de Desenvolvimento Web.', 'Belo Horizonte', 'Python, Django, REST APIs', 4200.00, 'CLT', 'Vale alimentação, Seguro odontológico', 'Desenvolvimento Web', NULL, NULL, NULL, NULL),
(66, 2, 2, 'Desenvolvedor C# .NET', 'Oportunidade em Curitiba para desenvolvedor com conhecimentos em C#, .NET e SQL Server. Salário de R$ 4.300,00, CLT, benefícios de vale transporte e bônus anual. Área de Desenvolvimento.', 'Curitiba', 'C#, .NET, SQL Server', 4300.00, 'CLT', 'Vale transporte, Bônus anual', 'Desenvolvimento', NULL, NULL, NULL, NULL),
(67, 2, 1, 'Desenvolvedor Front-end React', 'Vaga em Fortaleza para desenvolvedor com experiência em JavaScript, React e Node.js. Contratação PJ, home office e seguro saúde. Salário de R$ 4.700,00. Área de Front-end.', 'Fortaleza', 'JavaScript, React, Node.js', 4700.00, 'PJ', 'Home office, Seguro saúde', 'Front-end', NULL, NULL, NULL, NULL),
(68, 2, 1, 'Desenvolvedor Back-end PHP', 'Oportunidade em Salvador para desenvolvedor com conhecimentos em PHP, Laravel e MySQL. Salário de R$ 4.100,00, CLT, benefícios de vale refeição e plano de saúde. Área de Back-end.', 'Salvador', 'PHP, Laravel, MySQL', 4100.00, 'CLT', 'Vale refeição, Plano de saúde', 'Back-end', NULL, NULL, NULL, NULL),
(69, 2, 2, 'Desenvolvedor Ruby on Rails', 'Vaga para Porto Alegre, experiência em Ruby on Rails e PostgreSQL. Contratação PJ, home office e bônus por desempenho. Salário de R$ 4.400,00. Área de Desenvolvimento.', 'Porto Alegre', 'Ruby on Rails, PostgreSQL', 4400.00, 'PJ', 'Home office, Bônus por desempenho', 'Desenvolvimento', NULL, NULL, NULL, NULL),
(71, 2, 1, 'Web Designer WordPress', 'Vaga em Manaus para profissional com experiência em PHP, WordPress e SEO. Salário de R$ 3.900,00, CLT, benefícios de vale alimentação e plano odontológico. Área de Web Design.', 'Manaus', 'PHP, WordPress, SEO', 3900.00, 'CLT', 'Vale alimentação, Plano odontológico', 'Web Design', NULL, NULL, NULL, NULL),
(72, 2, 2, 'Novo Título', 'Nova descrição da vaga', 'Recife', 'UX/UI Design, Adobe XD, Figma', 4200.00, 'PJ', 'Home office, Vale refeição', 'Design', NULL, NULL, NULL, NULL),
(84, 2, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(85, 2, NULL, 'Desenvolvedor Android Studio', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(86, 2, NULL, 'Desenvolvedor Android Studio', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto77', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(87, 2, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(88, 2, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(89, 2, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(90, 2, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(91, 2, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(92, 25, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, 'CLT', NULL, 'TI', NULL, NULL, NULL, NULL),
(105, 25, NULL, 'Desenvolvedor Android', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, NULL, 'VT, VR, Plano de saúde', 'Tecnologia', 'Júnior', 'CLT', 'Desenvolvimento Mobile', 'Experiência com arquitetura MVVM'),
(107, 25, NULL, 'OPA', 'Desenvolver aplicativos móveis com Kotlin', 'Remoto', 'Kotlin, Java, Android SDK', 5000.00, NULL, 'VT, VR, Plano de saúde', 'Tecnologia', 'Júnior', 'CLT', 'Desenvolvimento Mobile', 'Experiência com arquitetura MVVM'),
(108, 25, NULL, 'oi', 'oi', 'oi', 'oi', 111.00, NULL, 'oi', 'oi', 'Pleno', 'CLT', 'oi', 'oi'),
(109, 25, NULL, 'teste', 'teste', 'teste', 'teste', 123123.00, NULL, 'teste', 'teste', 'Pleno', 'Estágio', 'teste', 'teste'),
(110, 25, NULL, 'oioi', 'oi', 'oi', 'oi', 0.00, NULL, 'oi', 'oi', 'Júnior', 'PJ', 'ioi', 'oioi'),
(111, 25, NULL, 'ji', 'ji', 'ji', 'ji', 1111.00, NULL, 'ji', 'Tecnologia', 'Júnior', 'CLT', 'Tecnologia da Informação', 'Android SDK'),
(112, 30, NULL, 'jiji', 'jiji', 'jiji', 'jiiji', 123123.00, NULL, 'ji', 'Tecnologia', 'Júnior', 'CLT', 'Tecnologia da Informação', 'Kotlin');

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
  ADD KEY `status` (`status`),
  ADD KEY `fk_recrutador` (`recrutador_id`);

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
  ADD KEY `id_candidato` (`id_candidato`),
  ADD KEY `fk_vagas_usuarios` (`id_usuario`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `candidaturas`
--
ALTER TABLE `candidaturas`
  MODIFY `id_candidatura` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT de tabela `vagas`
--
ALTER TABLE `vagas`
  MODIFY `id_vagas` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=113;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `candidaturas`
--
ALTER TABLE `candidaturas`
  ADD CONSTRAINT `candidaturas_ibfk_1` FOREIGN KEY (`vaga_id`) REFERENCES `vagas` (`id_vagas`),
  ADD CONSTRAINT `fk_recrutador` FOREIGN KEY (`recrutador_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `vagas`
--
ALTER TABLE `vagas`
  ADD CONSTRAINT `fk_vagas_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
