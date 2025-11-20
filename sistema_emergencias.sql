-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/11/2025 às 00:19
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
-- Banco de dados: `sistema_emergencias`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `bairros_ourinhos`
--

CREATE TABLE `bairros_ourinhos` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `zona` enum('Norte','Sul','Leste','Oeste','Centro') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `bairros_ourinhos`
--

INSERT INTO `bairros_ourinhos` (`id`, `nome`, `zona`, `created_at`) VALUES
(1, 'Centro', 'Centro', '2025-11-20 21:00:59'),
(2, 'Vila Brasil', 'Centro', '2025-11-20 21:00:59'),
(3, 'Vila Moraes', 'Centro', '2025-11-20 21:00:59'),
(4, 'Jardim Matilde', 'Leste', '2025-11-20 21:00:59'),
(5, 'Jardim Santa Fé', 'Leste', '2025-11-20 21:00:59'),
(6, 'Jardim das Oliveiras', 'Leste', '2025-11-20 21:00:59'),
(7, 'Jardim Europa', 'Leste', '2025-11-20 21:00:59'),
(8, 'Jardim Ouro Verde', 'Leste', '2025-11-20 21:00:59'),
(9, 'Jardim Paulista', 'Leste', '2025-11-20 21:00:59'),
(10, 'Jardim Princesa', 'Leste', '2025-11-20 21:00:59'),
(11, 'Jardim Santa Clara', 'Leste', '2025-11-20 21:00:59'),
(12, 'Jardim São Lucas', 'Leste', '2025-11-20 21:00:59'),
(13, 'Conjunto Habitacional Deputado Eduardo Sadao Ito', 'Leste', '2025-11-20 21:00:59'),
(14, 'Jardim das Rosas', 'Sul', '2025-11-20 21:00:59'),
(15, 'Jardim Flórida', 'Sul', '2025-11-20 21:00:59'),
(16, 'Jardim Itamarati', 'Sul', '2025-11-20 21:00:59'),
(17, 'Jardim Novo Itamarati', 'Sul', '2025-11-20 21:00:59'),
(18, 'Jardim Santos Dumont', 'Sul', '2025-11-20 21:00:59'),
(19, 'Jardim Silvana', 'Sul', '2025-11-20 21:00:59'),
(20, 'Jardim das Azaléias', 'Sul', '2025-11-20 21:00:59'),
(21, 'Jardim das Flores', 'Sul', '2025-11-20 21:00:59'),
(22, 'Jardim das Palmeiras', 'Sul', '2025-11-20 21:00:59'),
(23, 'Jardim Estoril', 'Sul', '2025-11-20 21:00:59'),
(24, 'Jardim Maria Luiza', 'Sul', '2025-11-20 21:00:59'),
(25, 'Jardim Maria Luiza IV', 'Sul', '2025-11-20 21:00:59'),
(26, 'Jardim Mônaco', 'Sul', '2025-11-20 21:00:59'),
(27, 'Jardim Oasis', 'Sul', '2025-11-20 21:00:59'),
(28, 'Jardim Ouro Branco', 'Sul', '2025-11-20 21:00:59'),
(29, 'Jardim Panorama', 'Sul', '2025-11-20 21:00:59'),
(30, 'Jardim Santa Maria', 'Sul', '2025-11-20 21:00:59'),
(31, 'Jardim Santiago', 'Sul', '2025-11-20 21:00:59'),
(32, 'Jardim São Jorge', 'Sul', '2025-11-20 21:00:59'),
(33, 'Jardim São José', 'Sul', '2025-11-20 21:00:59'),
(34, 'Jardim Tangará', 'Sul', '2025-11-20 21:00:59'),
(35, 'Jardim das Nações', 'Oeste', '2025-11-20 21:00:59'),
(36, 'Jardim das Orquídeas', 'Oeste', '2025-11-20 21:00:59'),
(37, 'Jardim Dona Evangelina', 'Oeste', '2025-11-20 21:00:59'),
(38, 'Jardim dos Girassóis', 'Oeste', '2025-11-20 21:00:59'),
(39, 'Jardim Altos da Cidade', 'Oeste', '2025-11-20 21:00:59'),
(40, 'Jardim Alvorada', 'Oeste', '2025-11-20 21:00:59'),
(41, 'Jardim América', 'Oeste', '2025-11-20 21:00:59'),
(42, 'Jardim Bandeirantes', 'Oeste', '2025-11-20 21:00:59'),
(43, 'Jardim Bela Vista', 'Oeste', '2025-11-20 21:00:59'),
(44, 'Jardim Brasil', 'Oeste', '2025-11-20 21:00:59'),
(45, 'Jardim Califórnia', 'Oeste', '2025-11-20 21:00:59'),
(46, 'Jardim Canadá', 'Oeste', '2025-11-20 21:00:59'),
(47, 'Jardim Colina', 'Oeste', '2025-11-20 21:00:59'),
(48, 'Jardim das Paineiras', 'Oeste', '2025-11-20 21:00:59'),
(49, 'Jardim das Tulipas', 'Oeste', '2025-11-20 21:00:59'),
(50, 'Jardim Europa', 'Oeste', '2025-11-20 21:00:59'),
(51, 'Jardim Guaiapó', 'Oeste', '2025-11-20 21:00:59'),
(52, 'Jardim Guaíra', 'Oeste', '2025-11-20 21:00:59'),
(53, 'Jardim Icarai', 'Oeste', '2025-11-20 21:00:59'),
(54, 'Jardim Ipanema', 'Oeste', '2025-11-20 21:00:59'),
(55, 'Jardim Itaipu', 'Oeste', '2025-11-20 21:00:59'),
(56, 'Jardim Marabá', 'Oeste', '2025-11-20 21:00:59'),
(57, 'Jardim Maringá', 'Oeste', '2025-11-20 21:00:59'),
(58, 'Jardim Maristela', 'Oeste', '2025-11-20 21:00:59'),
(59, 'Jardim Novo Mundo', 'Oeste', '2025-11-20 21:00:59'),
(60, 'Jardim Oasis', 'Oeste', '2025-11-20 21:00:59'),
(61, 'Jardim Ouro Verde', 'Oeste', '2025-11-20 21:00:59'),
(62, 'Jardim Paraná', 'Oeste', '2025-11-20 21:00:59'),
(63, 'Jardim Paulista', 'Oeste', '2025-11-20 21:00:59'),
(64, 'Jardim Planalto', 'Oeste', '2025-11-20 21:00:59'),
(65, 'Jardim Primavera', 'Oeste', '2025-11-20 21:00:59'),
(66, 'Jardim Real', 'Oeste', '2025-11-20 21:00:59'),
(67, 'Jardim Santa Mônica', 'Oeste', '2025-11-20 21:00:59'),
(68, 'Jardim Santo Antônio', 'Oeste', '2025-11-20 21:00:59'),
(69, 'Jardim São Domingos', 'Oeste', '2025-11-20 21:00:59'),
(70, 'Jardim São Francisco', 'Oeste', '2025-11-20 21:00:59'),
(71, 'Jardim São Luiz', 'Oeste', '2025-11-20 21:00:59'),
(72, 'Jardim Tropical', 'Oeste', '2025-11-20 21:00:59'),
(73, 'Jardim Universal', 'Oeste', '2025-11-20 21:00:59'),
(74, 'Jardim Vale Verde', 'Oeste', '2025-11-20 21:00:59'),
(75, 'Jardim Virgínia', 'Oeste', '2025-11-20 21:00:59'),
(76, 'Parque Minas Gerais', 'Oeste', '2025-11-20 21:00:59'),
(77, 'Vila Boa Esperança', 'Oeste', '2025-11-20 21:00:59'),
(78, 'Vila Industrial', 'Oeste', '2025-11-20 21:00:59'),
(79, 'Vila Perino', 'Oeste', '2025-11-20 21:00:59'),
(80, 'Jardim dos Ipês', 'Norte', '2025-11-20 21:00:59'),
(81, 'Jardim Santa Catarina', 'Norte', '2025-11-20 21:00:59'),
(82, 'Jardim Santa Rosa', 'Norte', '2025-11-20 21:00:59'),
(83, 'Jardim São Carlos', 'Norte', '2025-11-20 21:00:59'),
(84, 'Jardim São João', 'Norte', '2025-11-20 21:00:59'),
(85, 'Jardim Vitória', 'Norte', '2025-11-20 21:00:59'),
(86, 'Conjunto Habitacional João Turquino', 'Norte', '2025-11-20 21:00:59'),
(87, 'Jardim Alvorada', 'Norte', '2025-11-20 21:00:59'),
(88, 'Jardim América', 'Norte', '2025-11-20 21:00:59'),
(89, 'Jardim Bandeirantes', 'Norte', '2025-11-20 21:00:59'),
(90, 'Jardim Bela Vista', 'Norte', '2025-11-20 21:00:59'),
(91, 'Jardim Brasília', 'Norte', '2025-11-20 21:00:59'),
(92, 'Jardim Califórnia', 'Norte', '2025-11-20 21:00:59'),
(93, 'Jardim Canadá', 'Norte', '2025-11-20 21:00:59'),
(94, 'Jardim das Flores', 'Norte', '2025-11-20 21:00:59'),
(95, 'Jardim das Nações', 'Norte', '2025-11-20 21:00:59'),
(96, 'Jardim das Oliveiras', 'Norte', '2025-11-20 21:00:59'),
(97, 'Jardim Europa', 'Norte', '2025-11-20 21:00:59'),
(98, 'Jardim Flórida', 'Norte', '2025-11-20 21:00:59'),
(99, 'Jardim Ipiranga', 'Norte', '2025-11-20 21:00:59'),
(100, 'Jardim Itaipu', 'Norte', '2025-11-20 21:00:59'),
(101, 'Jardim Marabá', 'Norte', '2025-11-20 21:00:59'),
(102, 'Jardim Maringá', 'Norte', '2025-11-20 21:00:59'),
(103, 'Jardim Novo Mundo', 'Norte', '2025-11-20 21:00:59'),
(104, 'Jardim Oasis', 'Norte', '2025-11-20 21:00:59'),
(105, 'Jardim Ouro Verde', 'Norte', '2025-11-20 21:00:59'),
(106, 'Jardim Panorama', 'Norte', '2025-11-20 21:00:59'),
(107, 'Jardim Paulista', 'Norte', '2025-11-20 21:00:59'),
(108, 'Jardim Planalto', 'Norte', '2025-11-20 21:00:59'),
(109, 'Jardim Primavera', 'Norte', '2025-11-20 21:00:59'),
(110, 'Jardim Real', 'Norte', '2025-11-20 21:00:59'),
(111, 'Jardim Santa Fé', 'Norte', '2025-11-20 21:00:59'),
(112, 'Jardim Santa Mônica', 'Norte', '2025-11-20 21:00:59'),
(113, 'Jardim Santo Antônio', 'Norte', '2025-11-20 21:00:59'),
(114, 'Jardim São Domingos', 'Norte', '2025-11-20 21:00:59'),
(115, 'Jardim São Francisco', 'Norte', '2025-11-20 21:00:59'),
(116, 'Jardim São Luiz', 'Norte', '2025-11-20 21:00:59'),
(117, 'Jardim Tropical', 'Norte', '2025-11-20 21:00:59'),
(118, 'Jardim Universal', 'Norte', '2025-11-20 21:00:59'),
(119, 'Jardim Virgínia', 'Norte', '2025-11-20 21:00:59'),
(120, 'Parque Valeriano', 'Norte', '2025-11-20 21:00:59'),
(121, 'Vila Aparecida', 'Norte', '2025-11-20 21:00:59'),
(122, 'Vila Operária', 'Norte', '2025-11-20 21:00:59'),
(123, 'Centro', 'Centro', '2025-11-20 21:44:41'),
(124, 'Vila Industrial', 'Leste', '2025-11-20 21:44:41'),
(125, 'Jardim Europa', 'Sul', '2025-11-20 21:44:41'),
(126, 'Parque Minas Gerais', 'Norte', '2025-11-20 21:44:41'),
(127, 'Jardim Santa Fé', 'Oeste', '2025-11-20 21:44:41'),
(128, 'Vila Odilon', 'Leste', '2025-11-20 21:44:41'),
(129, 'Jardim das Oliveiras', 'Sul', '2025-11-20 21:44:41'),
(130, 'Conjunto Habitacional Ana Jacinta', 'Norte', '2025-11-20 21:44:41'),
(131, 'Jardim Ouro Verde', 'Oeste', '2025-11-20 21:44:41'),
(132, 'Vila Brasil', 'Leste', '2025-11-20 21:44:41'),
(133, 'Jardim Paulista', 'Sul', '2025-11-20 21:44:41'),
(134, 'Parque São Jorge', 'Norte', '2025-11-20 21:44:41'),
(135, 'Vila Perino', 'Oeste', '2025-11-20 21:44:41'),
(136, 'Jardim das Rosas', 'Sul', '2025-11-20 21:44:41'),
(137, 'Conjunto Habitacional Doutor Antonio R. de Lima', 'Leste', '2025-11-20 21:44:41');

-- --------------------------------------------------------

--
-- Estrutura para tabela `configuracoes_sistema`
--

CREATE TABLE `configuracoes_sistema` (
  `id` int(11) NOT NULL,
  `chave` varchar(100) NOT NULL,
  `valor` text DEFAULT NULL,
  `descricao` text DEFAULT NULL,
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `equipes`
--

CREATE TABLE `equipes` (
  `id` int(11) NOT NULL,
  `motorista_id` int(11) NOT NULL,
  `veiculo_id` int(11) NOT NULL,
  `funcionario1_id` int(11) DEFAULT NULL,
  `funcionario2_id` int(11) DEFAULT NULL,
  `funcionario3_id` int(11) DEFAULT NULL,
  `status` enum('disponivel','em_rota','manutencao') DEFAULT 'disponivel',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `equipes`
--

INSERT INTO `equipes` (`id`, `motorista_id`, `veiculo_id`, `funcionario1_id`, `funcionario2_id`, `funcionario3_id`, `status`, `data_criacao`, `latitude`, `longitude`) VALUES
(1, 2, 1, 4, 5, NULL, 'disponivel', '2025-11-20 17:52:55', NULL, NULL),
(2, 3, 2, 4, NULL, NULL, 'disponivel', '2025-11-20 17:52:55', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `acao` varchar(200) NOT NULL,
  `tabela_afetada` varchar(50) DEFAULT NULL,
  `registro_id` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `logs`
--

INSERT INTO `logs` (`id`, `usuario_id`, `acao`, `tabela_afetada`, `registro_id`, `data_criacao`) VALUES
(1, 1, 'Login no sistema', 'usuarios', NULL, '2025-11-20 17:54:21'),
(2, 1, 'Login no sistema', 'usuarios', NULL, '2025-11-20 18:12:56'),
(3, 1, 'Login no sistema', 'usuarios', NULL, '2025-11-20 18:15:10'),
(4, 2, 'Login no sistema', 'usuarios', NULL, '2025-11-20 18:20:29'),
(5, 4, 'Login no sistema', 'usuarios', NULL, '2025-11-20 18:21:10'),
(6, 1, 'Login no sistema', 'usuarios', NULL, '2025-11-20 20:15:14'),
(7, 2, 'Login no sistema', 'usuarios', NULL, '2025-11-20 20:16:13'),
(8, 4, 'Login no sistema', 'usuarios', NULL, '2025-11-20 20:17:00'),
(9, 1, 'Login no sistema', 'usuarios', NULL, '2025-11-20 22:42:59'),
(10, 2, 'Login no sistema', 'usuarios', NULL, '2025-11-20 22:52:40'),
(11, 2, 'Login no sistema', 'usuarios', NULL, '2025-11-20 22:52:49'),
(12, 3, 'Login no sistema', 'usuarios', NULL, '2025-11-20 22:53:04'),
(13, 3, 'Login no sistema', 'usuarios', NULL, '2025-11-20 22:53:20'),
(14, 3, 'Login no sistema', 'usuarios', NULL, '2025-11-20 22:53:36'),
(15, 3, 'Login no sistema', 'usuarios', NULL, '2025-11-20 22:53:54'),
(16, 4, 'Login no sistema', 'usuarios', NULL, '2025-11-20 22:55:22'),
(17, 2, 'Login no sistema', 'usuarios', NULL, '2025-11-20 23:17:31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `relatos`
--

CREATE TABLE `relatos` (
  `id` int(11) NOT NULL,
  `bairro` varchar(100) NOT NULL,
  `rua` varchar(200) NOT NULL,
  `numero` varchar(20) DEFAULT NULL,
  `referencia` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `nivel_emergencia` int(11) NOT NULL,
  `tipo_servico` enum('corte_arvore','poda','recolher_galhos') NOT NULL,
  `descricao` text NOT NULL,
  `foto_path` varchar(500) DEFAULT NULL,
  `status` enum('pendente','avaliacao','aprovado','em_rota','em_execucao','concluido','cancelado') DEFAULT 'pendente',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `usuario_avaliacao_id` int(11) DEFAULT NULL,
  `observacao_avaliacao` text DEFAULT NULL,
  `equipe_id` int(11) DEFAULT NULL,
  `titulo` varchar(200) DEFAULT NULL,
  `tipo_incidente_id` int(11) DEFAULT NULL,
  `prioridade` enum('baixa','media','alta') DEFAULT NULL,
  `bairro_id` int(11) DEFAULT NULL,
  `cep` varchar(10) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL,
  `complemento` text DEFAULT NULL,
  `localizacao_verificada` tinyint(1) DEFAULT 0,
  `foto_analisada` tinyint(1) DEFAULT 0,
  `confianca_foto` decimal(3,2) DEFAULT 0.00,
  `tempo_estimado_minutos` int(11) DEFAULT NULL,
  `material_necessario` text DEFAULT NULL,
  `risco_ambiental` tinyint(1) DEFAULT 0,
  `data_agendamento` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `relatos`
--

INSERT INTO `relatos` (`id`, `bairro`, `rua`, `numero`, `referencia`, `latitude`, `longitude`, `nivel_emergencia`, `tipo_servico`, `descricao`, `foto_path`, `status`, `data_criacao`, `data_atualizacao`, `usuario_avaliacao_id`, `observacao_avaliacao`, `equipe_id`, `titulo`, `tipo_incidente_id`, `prioridade`, `bairro_id`, `cep`, `endereco`, `complemento`, `localizacao_verificada`, `foto_analisada`, `confianca_foto`, `tempo_estimado_minutos`, `material_necessario`, `risco_ambiental`, `data_agendamento`) VALUES
(1, 'Centro', 'Rua Prudente de Moraes', '123', NULL, NULL, NULL, 3, 'corte_arvore', 'Árvore caída bloqueando via pública', NULL, 'pendente', '2025-11-20 17:52:55', '2025-11-20 17:52:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0.00, NULL, NULL, 0, NULL),
(2, 'Jardim Europa', 'Avenida Europa', '456', NULL, NULL, NULL, 2, 'poda', 'Galhos ameaçando cair sobre fiação', NULL, 'pendente', '2025-11-20 17:52:55', '2025-11-20 17:52:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0.00, NULL, NULL, 0, NULL),
(3, 'Vila Industrial', 'Rua das Indústrias', '789', NULL, NULL, NULL, 1, 'recolher_galhos', 'Galhos espalhados após tempestade', NULL, 'pendente', '2025-11-20 17:52:55', '2025-11-20 17:52:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0.00, NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `relato_fotos`
--

CREATE TABLE `relato_fotos` (
  `id` int(11) NOT NULL,
  `relato_id` int(11) NOT NULL,
  `caminho_arquivo` varchar(500) NOT NULL,
  `confianca_ia` decimal(3,2) DEFAULT 0.00,
  `tags_ia` text DEFAULT NULL,
  `data_upload` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `relato_localizacao`
--

CREATE TABLE `relato_localizacao` (
  `id` int(11) NOT NULL,
  `relato_id` int(11) NOT NULL,
  `lat_usuario` decimal(10,8) DEFAULT NULL,
  `lon_usuario` decimal(11,8) DEFAULT NULL,
  `lat_endereco` decimal(10,8) DEFAULT NULL,
  `lon_endereco` decimal(11,8) DEFAULT NULL,
  `distancia_metros` decimal(8,2) DEFAULT NULL,
  `validacao_aprovada` tinyint(1) DEFAULT 0,
  `motivo_validacao` text DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `relato_status`
--

CREATE TABLE `relato_status` (
  `id` int(11) NOT NULL,
  `relato_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `observacao` text DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `foto_path` varchar(500) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `rotas`
--

CREATE TABLE `rotas` (
  `id` int(11) NOT NULL,
  `equipe_id` int(11) NOT NULL,
  `relatos_ids` text NOT NULL,
  `status` enum('planejada','iniciada','concluida','cancelada') DEFAULT 'planejada',
  `data_inicio` timestamp NULL DEFAULT NULL,
  `data_fim` timestamp NULL DEFAULT NULL,
  `distancia_total` decimal(8,2) DEFAULT NULL,
  `tempo_estimado` int(11) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `tipos_incidente`
--

CREATE TABLE `tipos_incidente` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descricao` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `tipos_incidente`
--

INSERT INTO `tipos_incidente` (`id`, `nome`, `descricao`) VALUES
(1, 'Árvore Caída', 'Árvore caída bloqueando via pública'),
(2, 'Poda de Galhos', 'Galhos precisando de poda'),
(3, 'Recolhimento de Galhos', 'Galhos no chão precisando ser recolhidos'),
(4, 'Risco de Queda', 'Árvore com risco de queda');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('admin','motorista','funcionario') NOT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `telefone` varchar(20) DEFAULT NULL,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `ativo`, `telefone`, `data_criacao`) VALUES
(1, 'Administrador Principal', 'admin@ourinhos.sp.gov.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, '(14) 99999-9999', '2025-11-20 17:52:54'),
(2, 'João Silva - Motorista', 'motorista@ourinhos.sp.gov.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'motorista', 1, '(14) 98888-8888', '2025-11-20 17:52:54'),
(3, 'Pedro Santos - Motorista', 'motorista2@ourinhos.sp.gov.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'motorista', 1, '(14) 97777-7777', '2025-11-20 17:52:54'),
(4, 'Maria Oliveira - Funcionária', 'funcionario@ourinhos.sp.gov.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'funcionario', 1, '(14) 96666-6666', '2025-11-20 17:52:54'),
(5, 'Carlos Souza - Funcionário', 'funcionario2@ourinhos.sp.gov.br', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'funcionario', 1, '(14) 95555-5555', '2025-11-20 17:52:54');

-- --------------------------------------------------------

--
-- Estrutura para tabela `veiculos`
--

CREATE TABLE `veiculos` (
  `id` int(11) NOT NULL,
  `placa` varchar(10) NOT NULL,
  `modelo` varchar(50) NOT NULL,
  `capacidade` varchar(50) DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `veiculos`
--

INSERT INTO `veiculos` (`id`, `placa`, `modelo`, `capacidade`, `ativo`, `data_criacao`) VALUES
(1, 'ABC-1234', 'Ford Cargo 814', 'Caminhão Poda', 1, '2025-11-20 17:52:55'),
(2, 'DEF-5678', 'Volkswagen Delivery', 'Caminhão Coleta', 1, '2025-11-20 17:52:55'),
(3, 'GHI-9012', 'Mercedes-Benz Accelo', 'Caminhão Misto', 1, '2025-11-20 17:52:55');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `bairros_ourinhos`
--
ALTER TABLE `bairros_ourinhos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `configuracoes_sistema`
--
ALTER TABLE `configuracoes_sistema`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`);

--
-- Índices de tabela `equipes`
--
ALTER TABLE `equipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `motorista_id` (`motorista_id`),
  ADD KEY `veiculo_id` (`veiculo_id`),
  ADD KEY `funcionario1_id` (`funcionario1_id`),
  ADD KEY `funcionario2_id` (`funcionario2_id`),
  ADD KEY `funcionario3_id` (`funcionario3_id`),
  ADD KEY `idx_equipes_status` (`status`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `relatos`
--
ALTER TABLE `relatos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_avaliacao_id` (`usuario_avaliacao_id`),
  ADD KEY `equipe_id` (`equipe_id`),
  ADD KEY `idx_relatos_status` (`status`),
  ADD KEY `idx_relatos_prioridade` (`prioridade`),
  ADD KEY `idx_relatos_data_criacao` (`data_criacao`);

--
-- Índices de tabela `relato_fotos`
--
ALTER TABLE `relato_fotos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `relato_id` (`relato_id`);

--
-- Índices de tabela `relato_localizacao`
--
ALTER TABLE `relato_localizacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `relato_id` (`relato_id`);

--
-- Índices de tabela `relato_status`
--
ALTER TABLE `relato_status`
  ADD PRIMARY KEY (`id`),
  ADD KEY `relato_id` (`relato_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices de tabela `rotas`
--
ALTER TABLE `rotas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `equipe_id` (`equipe_id`);

--
-- Índices de tabela `tipos_incidente`
--
ALTER TABLE `tipos_incidente`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Índices de tabela `veiculos`
--
ALTER TABLE `veiculos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `placa` (`placa`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `bairros_ourinhos`
--
ALTER TABLE `bairros_ourinhos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=138;

--
-- AUTO_INCREMENT de tabela `configuracoes_sistema`
--
ALTER TABLE `configuracoes_sistema`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `equipes`
--
ALTER TABLE `equipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de tabela `relatos`
--
ALTER TABLE `relatos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `relato_fotos`
--
ALTER TABLE `relato_fotos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `relato_localizacao`
--
ALTER TABLE `relato_localizacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `relato_status`
--
ALTER TABLE `relato_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `rotas`
--
ALTER TABLE `rotas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `tipos_incidente`
--
ALTER TABLE `tipos_incidente`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `veiculos`
--
ALTER TABLE `veiculos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `equipes`
--
ALTER TABLE `equipes`
  ADD CONSTRAINT `equipes_ibfk_1` FOREIGN KEY (`motorista_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `equipes_ibfk_2` FOREIGN KEY (`veiculo_id`) REFERENCES `veiculos` (`id`),
  ADD CONSTRAINT `equipes_ibfk_3` FOREIGN KEY (`funcionario1_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `equipes_ibfk_4` FOREIGN KEY (`funcionario2_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `equipes_ibfk_5` FOREIGN KEY (`funcionario3_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `relatos`
--
ALTER TABLE `relatos`
  ADD CONSTRAINT `relatos_ibfk_1` FOREIGN KEY (`usuario_avaliacao_id`) REFERENCES `usuarios` (`id`),
  ADD CONSTRAINT `relatos_ibfk_2` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`);

--
-- Restrições para tabelas `relato_fotos`
--
ALTER TABLE `relato_fotos`
  ADD CONSTRAINT `relato_fotos_ibfk_1` FOREIGN KEY (`relato_id`) REFERENCES `relatos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `relato_localizacao`
--
ALTER TABLE `relato_localizacao`
  ADD CONSTRAINT `relato_localizacao_ibfk_1` FOREIGN KEY (`relato_id`) REFERENCES `relatos` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `relato_status`
--
ALTER TABLE `relato_status`
  ADD CONSTRAINT `relato_status_ibfk_1` FOREIGN KEY (`relato_id`) REFERENCES `relatos` (`id`),
  ADD CONSTRAINT `relato_status_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);

--
-- Restrições para tabelas `rotas`
--
ALTER TABLE `rotas`
  ADD CONSTRAINT `rotas_ibfk_1` FOREIGN KEY (`equipe_id`) REFERENCES `equipes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
