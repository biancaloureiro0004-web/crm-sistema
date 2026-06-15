-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 15-Jun-2026 às 18:16
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `crm_servicos`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `calendario_tarefas`
--

CREATE TABLE `calendario_tarefas` (
  `id` int(10) UNSIGNED NOT NULL,
  `titulo` varchar(200) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data_hora` datetime NOT NULL,
  `tipo` enum('individual','equipa') NOT NULL DEFAULT 'individual',
  `criado_por` int(10) UNSIGNED NOT NULL,
  `atribuido_a` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `calendario_tarefas`
--

INSERT INTO `calendario_tarefas` (`id`, `titulo`, `descricao`, `data_hora`, `tipo`, `criado_por`, `atribuido_a`, `created_at`) VALUES
(1, 'Kick-off 2023', 'Objetivos 2023.', '2023-01-09 10:00:00', 'equipa', 1, NULL, '2023-01-09 08:53:09'),
(2, 'Formação CRM', 'Formação interna.', '2023-02-14 14:00:00', 'equipa', 1, NULL, '2023-02-11 16:44:45'),
(3, 'Visita — Metalúrgica do Norte', 'Avaliação de equipamentos.', '2023-03-22 09:30:00', 'individual', 2, 4, '2023-03-20 18:42:44'),
(4, 'Almoço de equipa Q2/2023', 'Celebração semestral.', '2023-06-30 13:00:00', 'equipa', 1, NULL, '2023-06-30 10:35:40'),
(5, 'Avaliação de desempenho 2023', 'Revisão anual.', '2023-12-18 10:00:00', 'equipa', 1, NULL, '2023-12-15 21:12:50'),
(6, 'Kick-off 2024', 'Objetivos 2024.', '2024-01-08 10:00:00', 'equipa', 1, NULL, '2024-01-07 05:30:27'),
(7, 'Workshop de Negociação', 'Formação externa.', '2024-04-15 13:00:00', 'equipa', 1, NULL, '2024-04-14 00:41:25'),
(8, 'Revisão de pipeline Q2/2024', 'Ponto de situação.', '2024-06-03 16:00:00', 'equipa', 1, NULL, '2024-06-01 12:30:21'),
(9, 'Balanço anual 2024', 'Revisão de KPIs.', '2024-12-16 14:00:00', 'equipa', 1, NULL, '2024-12-13 21:23:31'),
(10, 'Reunião de equipa Jan/2025', 'Arranque de ano.', '2025-01-13 10:00:00', 'equipa', 1, NULL, '2025-01-10 22:14:33'),
(11, 'Webinar Manutenção Preditiva', 'Evento para clientes.', '2025-05-20 15:00:00', 'equipa', 1, NULL, '2025-05-18 04:55:25'),
(12, 'Auditoria ISO 9001', 'Renovação certificação.', '2025-07-14 09:00:00', 'equipa', 1, NULL, '2025-07-12 22:46:08'),
(13, 'Revisão de preços 2026', 'Tabela de preços 2026.', '2025-11-10 14:00:00', 'equipa', 1, NULL, '2025-11-09 01:20:14'),
(14, 'Revisão de pipeline Q4', 'Q4 trimestre.', '2026-07-19 16:00:00', 'equipa', 1, NULL, '2026-07-18 14:26:15'),
(15, 'Avaliação de desempenho anual', 'KPIs do ano.', '2026-08-13 10:00:00', 'equipa', 1, NULL, '2026-08-11 00:27:05'),
(16, 'Feria da Indústria 2026', 'Exponor, Porto.', '2026-09-12 09:00:00', 'equipa', 1, NULL, '2026-09-10 15:42:25'),
(17, 'Reunião comercial mensal', 'Ponto de situação das leads.', '2026-06-01 17:00:00', 'equipa', 1, NULL, '2026-06-01 10:04:41'),
(18, 'Follow-up — Hotel Quinta do Lago', 'Retomar proposta da semana passada.', '2026-06-02 13:00:00', 'individual', 3, 4, '2026-05-31 06:28:42'),
(19, 'Formação interna — Produto X', 'Apresentação do novo serviço.', '2026-06-04 15:00:00', 'equipa', 1, NULL, '2026-06-01 18:24:18'),
(20, 'Visita técnica — Clínica Médica', 'Avaliação de equipamento de imagem.', '2026-06-05 12:00:00', 'individual', 4, 3, '2026-06-04 15:22:41'),
(21, 'Alinhamento de objetivos Q3', 'Metas do trimestre.', '2026-06-06 17:00:00', 'equipa', 1, NULL, '2026-06-05 12:04:36'),
(22, 'Demo — Biotecnologia InnovaLab', 'Demonstração no laboratório.', '2026-06-07 11:00:00', 'individual', 4, 3, '2026-06-07 02:50:32'),
(23, 'Revisão de propostas abertas', 'Análise propostas sem resposta.', '2026-06-09 16:00:00', 'equipa', 1, NULL, '2026-06-08 22:21:13'),
(24, 'Visita — Transportes Atlas', 'Follow-up pós-proposta.', '2026-06-19 16:00:00', 'individual', 2, 2, '2026-06-19 10:38:59'),
(25, 'Check-in semanal de equipa', 'Reunião rápida de 30 minutos.', '2026-06-20 17:00:00', 'equipa', 1, NULL, '2026-06-19 19:40:11'),
(26, 'Proposta — Centro Logístico Sul', 'Preparar proposta contrato anual.', '2026-06-23 08:00:00', 'individual', 3, 2, '2026-06-20 13:36:36'),
(27, 'Fecho de mês — Análise leads', 'Oportunidades do mês.', '2026-06-26 16:00:00', 'equipa', 1, NULL, '2026-06-25 04:51:54'),
(28, 'Formação Segurança Trabalho', 'Obrigatória — renovação certificados.', '2026-06-27 13:00:00', 'equipa', 1, NULL, '2026-06-27 04:43:55');

-- --------------------------------------------------------

--
-- Estrutura da tabela `documentos_funcionarios`
--

CREATE TABLE `documentos_funcionarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `utilizador_id` int(10) UNSIGNED NOT NULL,
  `nome_documento` varchar(200) NOT NULL,
  `caminho_ficheiro` varchar(500) NOT NULL,
  `data_upload` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `documentos_funcionarios`
--

INSERT INTO `documentos_funcionarios` (`id`, `utilizador_id`, `nome_documento`, `caminho_ficheiro`, `data_upload`) VALUES
(1, 1, 'Contrato de Trabalho', 'uploads/documentos_funcionarios/contrato_de_trabalho_uid1.pdf', '2022-12-15 22:21:16'),
(2, 1, 'Cartão de Cidadão', 'uploads/documentos_funcionarios/cartão_de_cidadão_uid1.pdf', '2023-11-18 14:04:11'),
(3, 2, 'Contrato de Trabalho', 'uploads/documentos_funcionarios/contrato_de_trabalho_uid2.pdf', '2022-11-26 18:41:02'),
(4, 2, 'Cartão de Cidadão', 'uploads/documentos_funcionarios/cartão_de_cidadão_uid2.pdf', '2023-02-16 17:34:47'),
(5, 3, 'Contrato de Trabalho', 'uploads/documentos_funcionarios/contrato_de_trabalho_uid3.pdf', '2023-08-07 19:28:14'),
(6, 3, 'Cartão de Cidadão', 'uploads/documentos_funcionarios/cartão_de_cidadão_uid3.pdf', '2023-11-14 05:27:51'),
(7, 3, 'NIF', 'uploads/documentos_funcionarios/nif_uid3.pdf', '2024-01-09 20:30:31'),
(8, 4, 'Contrato de Trabalho', 'uploads/documentos_funcionarios/contrato_de_trabalho_uid4.pdf', '2023-01-29 02:08:59'),
(9, 4, 'Cartão de Cidadão', 'uploads/documentos_funcionarios/cartão_de_cidadão_uid4.pdf', '2023-10-15 14:03:02'),
(10, 4, 'NIF', 'uploads/documentos_funcionarios/nif_uid4.pdf', '2024-04-28 04:56:14'),
(11, 5, 'Contrato de Trabalho', 'uploads/documentos_funcionarios/contrato_de_trabalho_uid5.pdf', '2023-01-17 23:02:49'),
(12, 5, 'Cartão de Cidadão', 'uploads/documentos_funcionarios/cartão_de_cidadão_uid5.pdf', '2022-11-04 06:15:39'),
(13, 5, 'NIF', 'uploads/documentos_funcionarios/nif_uid5.pdf', '2022-11-08 13:41:53');

-- --------------------------------------------------------

--
-- Estrutura da tabela `documentos_leads`
--

CREATE TABLE `documentos_leads` (
  `id` int(10) UNSIGNED NOT NULL,
  `lead_id` int(10) UNSIGNED NOT NULL,
  `nome_documento` varchar(200) NOT NULL,
  `caminho_ficheiro` varchar(500) NOT NULL,
  `enviado_por` int(10) UNSIGNED NOT NULL,
  `data_upload` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `documentos_leads`
--

INSERT INTO `documentos_leads` (`id`, `lead_id`, `nome_documento`, `caminho_ficheiro`, `enviado_por`, `data_upload`) VALUES
(1, 1, 'Proposta Comercial 2024', 'uploads/propostas/proposta_1_2024.pdf', 4, '2024-02-08 10:00:00'),
(2, 2, 'Proposta Comercial 2026', 'uploads/propostas/proposta_2_2026.pdf', 3, '2026-01-25 10:00:00'),
(3, 4, 'Proposta Comercial 2026', 'uploads/propostas/proposta_4_2026.pdf', 4, '2026-11-13 10:00:00'),
(4, 14, 'Proposta Comercial 2026', 'uploads/propostas/proposta_14_2026.pdf', 4, '2026-08-23 10:00:00'),
(5, 15, 'Proposta Comercial 2023', 'uploads/propostas/proposta_15_2023.pdf', 3, '2023-12-03 10:00:00'),
(6, 17, 'Proposta Comercial 2023', 'uploads/propostas/proposta_17_2023.pdf', 3, '2023-12-11 10:00:00'),
(7, 18, 'Proposta Comercial 2025', 'uploads/propostas/proposta_18_2025.pdf', 4, '2025-07-27 10:00:00'),
(8, 19, 'Proposta Comercial 2026', 'uploads/propostas/proposta_19_2026.pdf', 2, '2026-04-12 10:00:00'),
(9, 21, 'Proposta Comercial 2026', 'uploads/propostas/proposta_21_2026.pdf', 2, '2026-11-03 10:00:00'),
(10, 29, 'Proposta Comercial 2026', 'uploads/propostas/proposta_29_2026.pdf', 2, '2026-02-19 10:00:00'),
(11, 31, 'Proposta Comercial 2024', 'uploads/propostas/proposta_31_2024.pdf', 2, '2024-02-18 10:00:00'),
(12, 33, 'Proposta Comercial 2026', 'uploads/propostas/proposta_33_2026.pdf', 2, '2026-06-14 10:00:00'),
(13, 37, 'Proposta Comercial 2026', 'uploads/propostas/proposta_37_2026.pdf', 4, '2026-10-06 10:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico_contacto`
--

CREATE TABLE `historico_contacto` (
  `id` int(10) UNSIGNED NOT NULL,
  `lead_id` int(10) UNSIGNED NOT NULL,
  `utilizador_id` int(10) UNSIGNED NOT NULL,
  `descricao` text NOT NULL,
  `data_registo` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `historico_contacto`
--

INSERT INTO `historico_contacto` (`id`, `lead_id`, `utilizador_id`, `descricao`, `data_registo`) VALUES
(1, 1, 4, 'Instalação concluída. Cliente muito satisfeito.', '2025-07-05 09:01:23'),
(2, 1, 3, 'Demonstração realizada com sucesso na sede do cliente.', '2024-08-04 23:31:50'),
(3, 1, 3, 'Reunião presencial agendada para a próxima semana.', '2023-12-30 09:03:44'),
(4, 2, 3, 'Follow-up realizado. Cliente em decisão interna.', '2025-01-09 22:14:07'),
(5, 3, 2, 'Visita técnica realizada. Avaliação positiva.', '2026-02-19 08:38:57'),
(6, 3, 2, 'Proposta enviada por email. Aguardar resposta.', '2025-09-22 13:46:50'),
(7, 3, 2, 'Proposta aceite verbalmente. Aguardar confirmação.', '2023-08-28 08:43:21'),
(8, 4, 4, 'Instalação concluída. Cliente muito satisfeito.', '2026-06-03 06:03:29'),
(9, 4, 3, 'Contrato assinado. Serviço inicia brevemente.', '2023-11-18 09:59:08'),
(10, 5, 3, 'Visita técnica realizada. Avaliação positiva.', '2025-01-17 22:35:34'),
(11, 5, 4, 'Instalação concluída. Cliente muito satisfeito.', '2023-12-13 06:12:40'),
(12, 5, 4, 'Visita técnica realizada. Avaliação positiva.', '2026-01-18 06:31:09'),
(13, 6, 2, 'Primeiro contacto por telefone. Cliente mostrou interesse.', '2025-03-23 12:07:54'),
(14, 6, 2, 'Reunião presencial agendada para a próxima semana.', '2024-04-13 03:12:41'),
(15, 6, 2, 'Proposta aceite verbalmente. Aguardar confirmação.', '2024-03-25 21:02:09'),
(16, 6, 2, 'Visita técnica realizada. Avaliação positiva.', '2025-03-18 11:02:56'),
(17, 7, 2, 'Proposta aceite verbalmente. Aguardar confirmação.', '2025-08-26 19:59:15'),
(18, 7, 2, 'Primeiro contacto por telefone. Cliente mostrou interesse.', '2023-01-31 06:06:50'),
(19, 7, 3, 'Orçamento revisto e reenviado.', '2025-06-02 21:16:59'),
(20, 8, 2, 'Contrato assinado. Serviço inicia brevemente.', '2023-03-30 09:37:40'),
(21, 8, 4, 'Email enviado com informações gerais sobre os serviços.', '2024-11-10 01:34:29'),
(22, 9, 4, 'Cliente solicitou orçamento detalhado.', '2025-09-21 20:01:23'),
(23, 9, 3, 'Instalação concluída. Cliente muito satisfeito.', '2025-04-14 14:48:28'),
(24, 10, 3, 'Cliente optou pela concorrência por prazo.', '2025-05-29 01:37:01'),
(25, 10, 2, 'Reunião presencial agendada para a próxima semana.', '2025-07-29 06:24:02'),
(26, 11, 4, 'Visita técnica realizada. Avaliação positiva.', '2026-04-09 17:47:31'),
(27, 11, 2, 'Proposta enviada por email. Aguardar resposta.', '2025-09-30 04:37:29'),
(28, 11, 2, 'Visita técnica realizada. Avaliação positiva.', '2025-05-20 09:31:47'),
(29, 11, 3, 'Primeiro contacto por telefone. Cliente mostrou interesse.', '2026-03-26 23:03:38'),
(30, 12, 4, 'Demonstração realizada com sucesso na sede do cliente.', '2026-04-21 07:03:38'),
(31, 13, 3, 'Reunião presencial agendada para a próxima semana.', '2025-11-23 14:45:47'),
(32, 13, 3, 'Follow-up realizado. Cliente em decisão interna.', '2024-06-01 07:00:33'),
(33, 14, 3, 'Instalação concluída. Cliente muito satisfeito.', '2024-02-10 04:36:26'),
(34, 14, 4, 'Proposta aceite verbalmente. Aguardar confirmação.', '2026-02-16 22:38:57'),
(35, 14, 2, 'Email enviado com informações gerais sobre os serviços.', '2023-09-20 01:37:34'),
(36, 15, 4, 'Demonstração realizada com sucesso na sede do cliente.', '2024-01-18 11:51:29'),
(37, 15, 2, 'Contrato assinado. Serviço inicia brevemente.', '2023-08-01 07:08:11'),
(38, 16, 2, 'Negociação de preço em curso. Pediu desconto de 10%.', '2025-10-01 01:25:15'),
(39, 17, 4, 'Cliente solicitou orçamento detalhado.', '2024-04-02 16:28:51'),
(40, 17, 3, 'Orçamento revisto e reenviado.', '2024-10-08 05:45:47'),
(41, 18, 3, 'Instalação concluída. Cliente muito satisfeito.', '2023-04-06 00:02:52'),
(42, 18, 4, 'Cliente solicitou orçamento detalhado.', '2025-10-28 18:32:14'),
(43, 18, 4, 'Contrato assinado. Serviço inicia brevemente.', '2023-10-23 04:58:14'),
(44, 19, 2, 'Visita técnica realizada. Avaliação positiva.', '2023-03-25 23:02:45'),
(45, 19, 2, 'Orçamento revisto e reenviado.', '2024-09-09 03:58:07'),
(46, 20, 3, 'Orçamento revisto e reenviado.', '2024-01-03 10:30:45'),
(47, 20, 4, 'Contrato assinado. Serviço inicia brevemente.', '2026-03-12 17:26:18'),
(48, 20, 4, 'Follow-up realizado. Cliente em decisão interna.', '2023-03-18 20:28:54'),
(49, 21, 3, 'Proposta enviada por email. Aguardar resposta.', '2024-09-11 00:12:18'),
(50, 21, 4, 'Follow-up realizado. Cliente em decisão interna.', '2025-04-27 03:32:29'),
(51, 21, 3, 'Follow-up realizado. Cliente em decisão interna.', '2023-09-30 07:45:51'),
(52, 22, 4, 'Cliente optou pela concorrência por prazo.', '2023-08-01 23:27:30'),
(53, 22, 3, 'Email enviado com informações gerais sobre os serviços.', '2026-03-25 23:12:49'),
(54, 22, 3, 'Primeiro contacto por telefone. Cliente mostrou interesse.', '2025-11-17 15:01:17'),
(55, 23, 3, 'Negociação de preço em curso. Pediu desconto de 10%.', '2023-10-09 18:13:55'),
(56, 23, 3, 'Email enviado com informações gerais sobre os serviços.', '2025-05-12 05:00:48'),
(57, 24, 3, 'Proposta aceite verbalmente. Aguardar confirmação.', '2026-04-18 08:15:01'),
(58, 24, 4, 'Negociação de preço em curso. Pediu desconto de 10%.', '2023-09-01 22:20:03'),
(59, 24, 2, 'Instalação concluída. Cliente muito satisfeito.', '2025-01-27 11:04:26'),
(60, 25, 3, 'Contrato assinado. Serviço inicia brevemente.', '2025-03-26 18:01:16'),
(61, 25, 3, 'Cliente optou pela concorrência por prazo.', '2024-09-04 22:57:34'),
(62, 26, 2, 'Cliente optou pela concorrência por prazo.', '2023-06-27 06:39:02'),
(63, 26, 4, 'Orçamento revisto e reenviado.', '2024-11-07 04:32:11'),
(64, 27, 4, 'Reunião presencial agendada para a próxima semana.', '2024-10-10 19:28:06'),
(65, 28, 2, 'Cliente solicitou orçamento detalhado.', '2025-08-21 05:44:57'),
(66, 28, 3, 'Demonstração realizada com sucesso na sede do cliente.', '2024-08-23 13:39:13'),
(67, 28, 4, 'Instalação concluída. Cliente muito satisfeito.', '2026-01-29 05:51:02'),
(68, 29, 3, 'Proposta enviada por email. Aguardar resposta.', '2024-05-20 18:22:13'),
(69, 29, 3, 'Follow-up realizado. Cliente em decisão interna.', '2025-08-18 12:35:13'),
(70, 29, 2, 'Proposta enviada por email. Aguardar resposta.', '2023-05-20 13:04:59'),
(71, 29, 2, 'Email enviado com informações gerais sobre os serviços.', '2025-08-22 01:54:54'),
(72, 30, 4, 'Orçamento revisto e reenviado.', '2023-04-05 04:16:37'),
(73, 30, 2, 'Email enviado com informações gerais sobre os serviços.', '2023-08-10 00:51:24'),
(74, 30, 3, 'Primeiro contacto por telefone. Cliente mostrou interesse.', '2025-09-22 02:03:24'),
(75, 31, 2, 'Email enviado com informações gerais sobre os serviços.', '2024-10-31 20:43:07'),
(76, 31, 2, 'Cliente optou pela concorrência por prazo.', '2025-06-19 10:00:01'),
(77, 31, 4, 'Proposta aceite verbalmente. Aguardar confirmação.', '2025-05-23 09:26:39'),
(78, 31, 3, 'Orçamento revisto e reenviado.', '2023-10-08 11:53:42'),
(79, 32, 4, 'Proposta aceite verbalmente. Aguardar confirmação.', '2025-08-02 22:15:57'),
(80, 32, 4, 'Primeiro contacto por telefone. Cliente mostrou interesse.', '2024-01-18 01:26:24'),
(81, 32, 3, 'Primeiro contacto por telefone. Cliente mostrou interesse.', '2025-04-30 21:30:52'),
(82, 33, 4, 'Demonstração realizada com sucesso na sede do cliente.', '2023-05-27 19:43:51'),
(83, 33, 3, 'Contrato assinado. Serviço inicia brevemente.', '2024-08-24 14:17:36'),
(84, 33, 4, 'Email enviado com informações gerais sobre os serviços.', '2024-11-24 09:54:02'),
(85, 34, 3, 'Proposta aceite verbalmente. Aguardar confirmação.', '2023-04-11 13:26:37'),
(86, 34, 4, 'Contrato assinado. Serviço inicia brevemente.', '2023-11-13 05:24:28'),
(87, 34, 3, 'Orçamento revisto e reenviado.', '2025-10-05 16:31:19'),
(88, 35, 4, 'Proposta enviada por email. Aguardar resposta.', '2026-01-09 00:14:52'),
(89, 35, 3, 'Cliente optou pela concorrência por prazo.', '2023-08-17 16:08:59'),
(90, 35, 4, 'Email enviado com informações gerais sobre os serviços.', '2026-02-23 13:18:18'),
(91, 36, 4, 'Demonstração realizada com sucesso na sede do cliente.', '2023-01-09 00:07:01'),
(92, 36, 3, 'Proposta aceite verbalmente. Aguardar confirmação.', '2023-02-10 08:22:06'),
(93, 37, 3, 'Primeiro contacto por telefone. Cliente mostrou interesse.', '2023-11-28 10:26:20'),
(94, 37, 2, 'Visita técnica realizada. Avaliação positiva.', '2025-07-24 16:08:35'),
(95, 37, 3, 'Follow-up realizado. Cliente em decisão interna.', '2023-02-21 08:37:56'),
(96, 38, 2, 'Primeiro contacto por telefone. Cliente mostrou interesse.', '2023-02-15 23:44:49'),
(97, 38, 2, 'Proposta enviada por email. Aguardar resposta.', '2024-01-11 20:16:22');

-- --------------------------------------------------------

--
-- Estrutura da tabela `leads`
--

CREATE TABLE `leads` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome_cliente` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `morada` text DEFAULT NULL,
  `nif` varchar(20) DEFAULT NULL,
  `cc` varchar(20) DEFAULT NULL,
  `estado` enum('Nova Lead','Contacto Efetuado','Proposta Enviada','Ganho','Perdido') NOT NULL DEFAULT 'Nova Lead',
  `criado_por` int(10) UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `leads`
--

INSERT INTO `leads` (`id`, `nome_cliente`, `email`, `telefone`, `morada`, `nif`, `cc`, `estado`, `criado_por`, `created_at`, `updated_at`) VALUES
(1, 'Armazéns Ferreira & Filhos, Lda', 'geral@ferreiraf.pt', '211 234 567', 'Setúbal', '501234567', NULL, 'Ganho', 4, '2024-01-28 18:37:15', '2024-01-28 18:37:15'),
(2, 'Transportes Atlas, S.A.', 'compras@atlastrans.pt', '252 345 678', 'Santarém', '502345678', NULL, 'Perdido', 2, '2025-10-24 00:32:41', '2025-10-24 00:32:41'),
(3, 'Clínica Médica do Rossio', 'admin@clinrossio.pt', '213 456 789', 'Lisboa', '503456789', NULL, 'Nova Lead', 3, '2024-09-14 06:41:16', '2024-09-14 06:41:16'),
(4, 'Hotel Quinta do Lago', 'manutencao@hql.pt', '289 567 890', 'Almancil', '504567890', NULL, 'Perdido', 2, '2024-02-07 20:16:37', '2024-02-07 20:16:37'),
(5, 'Metalúrgica do Norte, S.A.', 'compras@metalnorte.pt', '225 678 901', 'Porto', '505678901', NULL, 'Contacto Efetuado', 2, '2025-01-31 07:21:05', '2025-01-31 07:21:05'),
(6, 'Supermercados FrescaMart', 'logistica@frescamart.pt', '261 789 012', 'Cascais', '506789012', NULL, 'Nova Lead', 4, '2025-10-31 01:42:02', '2025-10-31 01:42:02'),
(7, 'Herdade das Vinhas', 'vinhas@herdadevinhas.pt', '268 890 123', 'Beja', '507890123', NULL, 'Ganho', 3, '2025-01-15 12:33:48', '2025-01-15 12:33:48'),
(8, 'Construções Cavaco & Irmãos', 'obra@cavacoi.pt', '239 901 234', 'Coimbra', '508901234', NULL, 'Contacto Efetuado', 4, '2023-11-02 13:03:40', '2023-11-02 13:03:40'),
(9, 'Auto-Ribeiro (Concessionário)', 'servicos@autoribeiro.pt', '253 012 345', 'Braga', '509012345', NULL, 'Proposta Enviada', 2, '2025-11-27 07:14:05', '2025-11-27 07:14:05'),
(10, 'Escola Superior de Tecnologia', 'infra@est.edu.pt', '212 123 456', 'Leiria', '510123456', NULL, 'Ganho', 4, '2024-11-13 07:34:23', '2024-11-13 07:34:23'),
(11, 'Parques Naturais & Turismo', 'reservas@pnt.pt', '266 234 567', 'Beja', '511234567', NULL, 'Proposta Enviada', 3, '2024-02-03 14:27:21', '2024-02-03 14:27:21'),
(12, 'Grupo Editorial Palavras Vivas', 'producao@palavrasvivas.pt', '214 345 678', 'Lisboa', '512345678', NULL, 'Ganho', 4, '2025-06-26 18:21:24', '2025-06-26 18:21:24'),
(13, 'Farmácias Saúde Total', 'central@saudetotal.pt', '217 456 789', 'Lisboa', '513456789', NULL, 'Ganho', 4, '2024-10-25 22:16:42', '2024-10-25 22:16:42'),
(14, 'Renascimento Têxtil, S.A.', 'manut@renasc.pt', '253 567 890', 'Guimarães', '514567890', NULL, 'Ganho', 2, '2025-01-16 00:36:11', '2025-01-16 00:36:11'),
(15, 'Solar do Tejo (Pousada)', 'gerencia@solardotejo.pt', '243 678 901', 'Santarém', '515678901', NULL, 'Ganho', 4, '2024-01-22 15:13:54', '2024-01-22 15:13:54'),
(16, 'Centro Logístico Sul, Lda', 'ops@cls.pt', '265 789 012', 'Faro', '516789012', NULL, 'Proposta Enviada', 3, '2025-03-22 03:13:01', '2025-03-22 03:13:01'),
(17, 'Mercearia Tradicional do Porto', 'loja@merctrad.pt', '226 890 123', 'Porto', '517890123', NULL, 'Perdido', 4, '2024-09-22 07:53:51', '2024-09-22 07:53:51'),
(18, 'Biotecnologia InnovaLab', 'lab@innovalab.pt', '219 901 234', 'Oeiras', '518901234', NULL, 'Proposta Enviada', 4, '2025-08-17 12:12:23', '2025-08-17 12:12:23'),
(19, 'Quinta Pedagógica dos Templários', 'visitas@qptemplarios.pt', '249 012 345', 'Tomar', '519012345', NULL, 'Ganho', 4, '2025-08-07 16:28:05', '2025-08-07 16:28:05'),
(20, 'Vera Silva (Particular)', 'vera@gmail.com', '968 574 312', 'Viseu', '215987346', NULL, 'Nova Lead', 2, '2023-04-29 16:56:55', '2023-04-29 16:56:55'),
(21, 'TechVision Startups, Lda', 'info@techvision.pt', '210 987 654', 'Aveiro', '521987654', NULL, 'Perdido', 4, '2026-06-18 13:55:00', '2026-06-18 13:55:00'),
(22, 'Distribuidora Santarém & Cia', 'dir@santaremcia.pt', '243 111 222', 'Santarém', '522111222', NULL, 'Proposta Enviada', 3, '2026-06-08 14:32:00', '2026-06-08 14:32:00'),
(23, 'Oficina Auto Progresso', 'oficina@progresso.pt', '232 222 333', 'Aveiro', '523222333', NULL, 'Nova Lead', 2, '2026-06-10 14:56:00', '2026-06-10 14:56:00'),
(24, 'Turismo Rural Casa das Nogueiras', 'casa@nogueiras.pt', '272 333 444', 'Castelo Branco', '524333444', NULL, 'Proposta Enviada', 2, '2026-06-23 11:18:00', '2026-06-23 11:18:00'),
(25, 'Engenharia Electro-Norte', 'proj@electronorte.pt', '253 444 555', 'Viana do Castelo', '525444555', NULL, 'Ganho', 4, '2026-06-26 11:18:00', '2026-06-26 11:18:00'),
(26, 'Artes Gráficas Impresso Total', 'orcamento@impresso.pt', '214 555 666', 'Setúbal', '526555666', NULL, 'Nova Lead', 2, '2026-06-18 12:12:00', '2026-06-18 12:12:00'),
(27, 'Padaria & Pastelaria Alenquer', 'padaria@alenquer.pt', '263 666 777', 'Alenquer', '527666777', NULL, 'Proposta Enviada', 4, '2026-06-20 15:13:00', '2026-06-20 15:13:00'),
(28, 'Reparações Eletrodomésticos LX', 'reparalx@gmail.com', '211 777 888', 'Lisboa', '528777888', NULL, 'Contacto Efetuado', 4, '2026-06-26 18:50:00', '2026-06-26 18:50:00'),
(29, 'Centro Médico Atlântico', 'admin@cmatlantico.pt', '289 888 999', 'Faro', '529888999', NULL, 'Ganho', 4, '2026-06-09 15:23:00', '2026-06-09 15:23:00'),
(30, 'Enoteca do Sul, Lda', 'vinho@enotecasul.pt', '284 999 000', 'Évora', '530999000', NULL, 'Proposta Enviada', 4, '2026-06-11 09:51:00', '2026-06-11 09:51:00'),
(31, 'Construtora Pinhal Verde', 'obra@pinhalverde.pt', '244 000 111', 'Leiria', '531000111', NULL, 'Perdido', 3, '2026-06-27 13:52:00', '2026-06-27 13:52:00'),
(32, 'Academia Desportiva Coimbra', 'secretaria@adcoimbra.pt', '239 111 222', 'Coimbra', '532111222', NULL, 'Ganho', 3, '2026-06-01 12:00:00', '2026-06-01 12:00:00'),
(33, 'Frigorífico Industrial Sado', 'compras@frigosado.pt', '265 222 333', 'Alcácer do Sal', '533222333', NULL, 'Ganho', 3, '2026-06-12 14:20:00', '2026-06-12 14:20:00'),
(34, 'Clínica Veterinária Amigos', 'vet@amigos.pt', '226 333 444', 'Porto', '534333444', NULL, 'Proposta Enviada', 3, '2026-06-11 18:34:00', '2026-06-11 18:34:00'),
(35, 'Papelaria e Livraria Alvorada', 'loja@alvorada.pt', '231 444 555', 'Aveiro', '535444555', NULL, 'Nova Lead', 4, '2026-06-01 11:22:00', '2026-06-01 11:22:00'),
(36, 'Seguros Proteção Total', 'protecao@st.pt', '213 555 666', 'Lisboa', '536555666', NULL, 'Ganho', 4, '2026-06-15 11:03:00', '2026-06-15 11:03:00'),
(37, 'Apartamentos Turísticos Algarve', 'reservas@apta.pt', '282 666 777', 'Albufeira', '537666777', NULL, 'Ganho', 4, '2026-06-18 10:29:00', '2026-06-18 10:29:00'),
(38, 'Manutenção Industrial Ferrolho', 'manut@ferrolho.pt', '256 777 888', 'Viseu', '538777888', NULL, 'Proposta Enviada', 3, '2026-06-24 12:04:00', '2026-06-24 12:04:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `recibos_vencimento`
--

CREATE TABLE `recibos_vencimento` (
  `id` int(10) UNSIGNED NOT NULL,
  `utilizador_id` int(10) UNSIGNED NOT NULL,
  `mes_referencia` varchar(7) NOT NULL,
  `valor_pago` decimal(10,2) NOT NULL,
  `ficheiro_pdf` varchar(500) DEFAULT NULL,
  `data_emissao` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `recibos_vencimento`
--

INSERT INTO `recibos_vencimento` (`id`, `utilizador_id`, `mes_referencia`, `valor_pago`, `ficheiro_pdf`, `data_emissao`) VALUES
(1, 2, '2023-01', 1490.00, 'uploads/recibos/recibo_jan_2023_uid2.pdf', '2023-01-28 18:00:00'),
(2, 2, '2023-02', 1476.00, 'uploads/recibos/recibo_fev_2023_uid2.pdf', '2023-02-28 18:00:00'),
(3, 2, '2023-03', 1495.00, 'uploads/recibos/recibo_mar_2023_uid2.pdf', '2023-03-28 18:00:00'),
(4, 2, '2023-04', 1330.00, 'uploads/recibos/recibo_abr_2023_uid2.pdf', '2023-04-28 18:00:00'),
(5, 2, '2023-05', 1400.00, 'uploads/recibos/recibo_mai_2023_uid2.pdf', '2023-05-28 18:00:00'),
(6, 2, '2023-06', 1482.00, 'uploads/recibos/recibo_jun_2023_uid2.pdf', '2023-06-28 18:00:00'),
(7, 2, '2023-07', 1451.00, 'uploads/recibos/recibo_jul_2023_uid2.pdf', '2023-07-28 18:00:00'),
(8, 2, '2023-08', 1443.00, 'uploads/recibos/recibo_ago_2023_uid2.pdf', '2023-08-28 18:00:00'),
(9, 2, '2023-09', 1327.00, 'uploads/recibos/recibo_set_2023_uid2.pdf', '2023-09-28 18:00:00'),
(10, 2, '2023-10', 1381.00, 'uploads/recibos/recibo_out_2023_uid2.pdf', '2023-10-28 18:00:00'),
(11, 2, '2023-11', 1301.00, 'uploads/recibos/recibo_nov_2023_uid2.pdf', '2023-11-28 18:00:00'),
(12, 2, '2023-12', 1360.00, 'uploads/recibos/recibo_dez_2023_uid2.pdf', '2023-12-28 18:00:00'),
(13, 2, '2024-01', 1331.00, 'uploads/recibos/recibo_jan_2024_uid2.pdf', '2024-01-28 18:00:00'),
(14, 2, '2024-02', 1490.00, 'uploads/recibos/recibo_fev_2024_uid2.pdf', '2024-02-28 18:00:00'),
(15, 2, '2024-03', 1300.00, 'uploads/recibos/recibo_mar_2024_uid2.pdf', '2024-03-28 18:00:00'),
(16, 2, '2024-04', 1470.00, 'uploads/recibos/recibo_abr_2024_uid2.pdf', '2024-04-28 18:00:00'),
(17, 2, '2024-05', 1406.00, 'uploads/recibos/recibo_mai_2024_uid2.pdf', '2024-05-28 18:00:00'),
(18, 2, '2024-06', 1467.00, 'uploads/recibos/recibo_jun_2024_uid2.pdf', '2024-06-28 18:00:00'),
(19, 2, '2024-07', 1411.00, 'uploads/recibos/recibo_jul_2024_uid2.pdf', '2024-07-28 18:00:00'),
(20, 2, '2024-08', 1460.00, 'uploads/recibos/recibo_ago_2024_uid2.pdf', '2024-08-28 18:00:00'),
(21, 2, '2024-09', 1375.00, 'uploads/recibos/recibo_set_2024_uid2.pdf', '2024-09-28 18:00:00'),
(22, 2, '2024-10', 1324.00, 'uploads/recibos/recibo_out_2024_uid2.pdf', '2024-10-28 18:00:00'),
(23, 2, '2024-11', 1305.00, 'uploads/recibos/recibo_nov_2024_uid2.pdf', '2024-11-28 18:00:00'),
(24, 2, '2024-12', 1397.00, 'uploads/recibos/recibo_dez_2024_uid2.pdf', '2024-12-28 18:00:00'),
(25, 2, '2025-01', 1311.00, 'uploads/recibos/recibo_jan_2025_uid2.pdf', '2025-01-28 18:00:00'),
(26, 2, '2025-02', 1408.00, 'uploads/recibos/recibo_fev_2025_uid2.pdf', '2025-02-28 18:00:00'),
(27, 2, '2025-03', 1376.00, 'uploads/recibos/recibo_mar_2025_uid2.pdf', '2025-03-28 18:00:00'),
(28, 2, '2025-04', 1393.00, 'uploads/recibos/recibo_abr_2025_uid2.pdf', '2025-04-28 18:00:00'),
(29, 2, '2025-05', 1342.00, 'uploads/recibos/recibo_mai_2025_uid2.pdf', '2025-05-28 18:00:00'),
(30, 2, '2025-06', 1347.00, 'uploads/recibos/recibo_jun_2025_uid2.pdf', '2025-06-28 18:00:00'),
(31, 2, '2025-07', 1343.00, 'uploads/recibos/recibo_jul_2025_uid2.pdf', '2025-07-28 18:00:00'),
(32, 2, '2025-08', 1465.00, 'uploads/recibos/recibo_ago_2025_uid2.pdf', '2025-08-28 18:00:00'),
(33, 2, '2025-09', 1367.00, 'uploads/recibos/recibo_set_2025_uid2.pdf', '2025-09-28 18:00:00'),
(34, 2, '2025-10', 1357.00, 'uploads/recibos/recibo_out_2025_uid2.pdf', '2025-10-28 18:00:00'),
(35, 2, '2025-11', 1335.00, 'uploads/recibos/recibo_nov_2025_uid2.pdf', '2025-11-28 18:00:00'),
(36, 2, '2025-12', 1415.00, 'uploads/recibos/recibo_dez_2025_uid2.pdf', '2025-12-28 18:00:00'),
(37, 2, '2026-01', 1498.00, 'uploads/recibos/recibo_jan_2026_uid2.pdf', '2026-01-28 18:00:00'),
(38, 2, '2026-02', 1377.00, 'uploads/recibos/recibo_fev_2026_uid2.pdf', '2026-02-28 18:00:00'),
(39, 2, '2026-03', 1455.00, 'uploads/recibos/recibo_mar_2026_uid2.pdf', '2026-03-28 18:00:00'),
(40, 2, '2026-04', 1302.00, 'uploads/recibos/recibo_abr_2026_uid2.pdf', '2026-04-28 18:00:00'),
(41, 2, '2026-05', 1413.00, 'uploads/recibos/recibo_mai_2026_uid2.pdf', '2026-05-28 18:00:00'),
(42, 2, '2026-06', 1342.00, 'uploads/recibos/recibo_jun_2026_uid2.pdf', '2026-06-28 18:00:00'),
(43, 3, '2023-01', 1270.00, 'uploads/recibos/recibo_jan_2023_uid3.pdf', '2023-01-28 18:00:00'),
(44, 3, '2023-02', 1159.00, 'uploads/recibos/recibo_fev_2023_uid3.pdf', '2023-02-28 18:00:00'),
(45, 3, '2023-03', 1246.00, 'uploads/recibos/recibo_mar_2023_uid3.pdf', '2023-03-28 18:00:00'),
(46, 3, '2023-04', 1187.00, 'uploads/recibos/recibo_abr_2023_uid3.pdf', '2023-04-28 18:00:00'),
(47, 3, '2023-05', 1175.00, 'uploads/recibos/recibo_mai_2023_uid3.pdf', '2023-05-28 18:00:00'),
(48, 3, '2023-06', 1340.00, 'uploads/recibos/recibo_jun_2023_uid3.pdf', '2023-06-28 18:00:00'),
(49, 3, '2023-07', 1345.00, 'uploads/recibos/recibo_jul_2023_uid3.pdf', '2023-07-28 18:00:00'),
(50, 3, '2023-08', 1330.00, 'uploads/recibos/recibo_ago_2023_uid3.pdf', '2023-08-28 18:00:00'),
(51, 3, '2023-09', 1327.00, 'uploads/recibos/recibo_set_2023_uid3.pdf', '2023-09-28 18:00:00'),
(52, 3, '2023-10', 1252.00, 'uploads/recibos/recibo_out_2023_uid3.pdf', '2023-10-28 18:00:00'),
(53, 3, '2023-11', 1260.00, 'uploads/recibos/recibo_nov_2023_uid3.pdf', '2023-11-28 18:00:00'),
(54, 3, '2023-12', 1161.00, 'uploads/recibos/recibo_dez_2023_uid3.pdf', '2023-12-28 18:00:00'),
(55, 3, '2024-01', 1207.00, 'uploads/recibos/recibo_jan_2024_uid3.pdf', '2024-01-28 18:00:00'),
(56, 3, '2024-02', 1327.00, 'uploads/recibos/recibo_fev_2024_uid3.pdf', '2024-02-28 18:00:00'),
(57, 3, '2024-03', 1168.00, 'uploads/recibos/recibo_mar_2024_uid3.pdf', '2024-03-28 18:00:00'),
(58, 3, '2024-04', 1170.00, 'uploads/recibos/recibo_abr_2024_uid3.pdf', '2024-04-28 18:00:00'),
(59, 3, '2024-05', 1228.00, 'uploads/recibos/recibo_mai_2024_uid3.pdf', '2024-05-28 18:00:00'),
(60, 3, '2024-06', 1312.00, 'uploads/recibos/recibo_jun_2024_uid3.pdf', '2024-06-28 18:00:00'),
(61, 3, '2024-07', 1196.00, 'uploads/recibos/recibo_jul_2024_uid3.pdf', '2024-07-28 18:00:00'),
(62, 3, '2024-08', 1341.00, 'uploads/recibos/recibo_ago_2024_uid3.pdf', '2024-08-28 18:00:00'),
(63, 3, '2024-09', 1201.00, 'uploads/recibos/recibo_set_2024_uid3.pdf', '2024-09-28 18:00:00'),
(64, 3, '2024-10', 1317.00, 'uploads/recibos/recibo_out_2024_uid3.pdf', '2024-10-28 18:00:00'),
(65, 3, '2024-11', 1199.00, 'uploads/recibos/recibo_nov_2024_uid3.pdf', '2024-11-28 18:00:00'),
(66, 3, '2024-12', 1231.00, 'uploads/recibos/recibo_dez_2024_uid3.pdf', '2024-12-28 18:00:00'),
(67, 3, '2025-01', 1217.00, 'uploads/recibos/recibo_jan_2025_uid3.pdf', '2025-01-28 18:00:00'),
(68, 3, '2025-02', 1312.00, 'uploads/recibos/recibo_fev_2025_uid3.pdf', '2025-02-28 18:00:00'),
(69, 3, '2025-03', 1291.00, 'uploads/recibos/recibo_mar_2025_uid3.pdf', '2025-03-28 18:00:00'),
(70, 3, '2025-04', 1257.00, 'uploads/recibos/recibo_abr_2025_uid3.pdf', '2025-04-28 18:00:00'),
(71, 3, '2025-05', 1323.00, 'uploads/recibos/recibo_mai_2025_uid3.pdf', '2025-05-28 18:00:00'),
(72, 3, '2025-06', 1231.00, 'uploads/recibos/recibo_jun_2025_uid3.pdf', '2025-06-28 18:00:00'),
(73, 3, '2025-07', 1274.00, 'uploads/recibos/recibo_jul_2025_uid3.pdf', '2025-07-28 18:00:00'),
(74, 3, '2025-08', 1301.00, 'uploads/recibos/recibo_ago_2025_uid3.pdf', '2025-08-28 18:00:00'),
(75, 3, '2025-09', 1303.00, 'uploads/recibos/recibo_set_2025_uid3.pdf', '2025-09-28 18:00:00'),
(76, 3, '2025-10', 1240.00, 'uploads/recibos/recibo_out_2025_uid3.pdf', '2025-10-28 18:00:00'),
(77, 3, '2025-11', 1227.00, 'uploads/recibos/recibo_nov_2025_uid3.pdf', '2025-11-28 18:00:00'),
(78, 3, '2025-12', 1342.00, 'uploads/recibos/recibo_dez_2025_uid3.pdf', '2025-12-28 18:00:00'),
(79, 3, '2026-01', 1154.00, 'uploads/recibos/recibo_jan_2026_uid3.pdf', '2026-01-28 18:00:00'),
(80, 3, '2026-02', 1280.00, 'uploads/recibos/recibo_fev_2026_uid3.pdf', '2026-02-28 18:00:00'),
(81, 3, '2026-03', 1166.00, 'uploads/recibos/recibo_mar_2026_uid3.pdf', '2026-03-28 18:00:00'),
(82, 3, '2026-04', 1231.00, 'uploads/recibos/recibo_abr_2026_uid3.pdf', '2026-04-28 18:00:00'),
(83, 3, '2026-05', 1306.00, 'uploads/recibos/recibo_mai_2026_uid3.pdf', '2026-05-28 18:00:00'),
(84, 3, '2026-06', 1342.00, 'uploads/recibos/recibo_jun_2026_uid3.pdf', '2026-06-28 18:00:00'),
(85, 4, '2023-01', 1177.00, 'uploads/recibos/recibo_jan_2023_uid4.pdf', '2023-01-28 18:00:00'),
(86, 4, '2023-02', 1111.00, 'uploads/recibos/recibo_fev_2023_uid4.pdf', '2023-02-28 18:00:00'),
(87, 4, '2023-03', 1158.00, 'uploads/recibos/recibo_mar_2023_uid4.pdf', '2023-03-28 18:00:00'),
(88, 4, '2023-04', 1059.00, 'uploads/recibos/recibo_abr_2023_uid4.pdf', '2023-04-28 18:00:00'),
(89, 4, '2023-05', 1103.00, 'uploads/recibos/recibo_mai_2023_uid4.pdf', '2023-05-28 18:00:00'),
(90, 4, '2023-06', 1195.00, 'uploads/recibos/recibo_jun_2023_uid4.pdf', '2023-06-28 18:00:00'),
(91, 4, '2023-07', 1039.00, 'uploads/recibos/recibo_jul_2023_uid4.pdf', '2023-07-28 18:00:00'),
(92, 4, '2023-08', 1047.00, 'uploads/recibos/recibo_ago_2023_uid4.pdf', '2023-08-28 18:00:00'),
(93, 4, '2023-09', 1001.00, 'uploads/recibos/recibo_set_2023_uid4.pdf', '2023-09-28 18:00:00'),
(94, 4, '2023-10', 1123.00, 'uploads/recibos/recibo_out_2023_uid4.pdf', '2023-10-28 18:00:00'),
(95, 4, '2023-11', 1094.00, 'uploads/recibos/recibo_nov_2023_uid4.pdf', '2023-11-28 18:00:00'),
(96, 4, '2023-12', 1047.00, 'uploads/recibos/recibo_dez_2023_uid4.pdf', '2023-12-28 18:00:00'),
(97, 4, '2024-01', 1162.00, 'uploads/recibos/recibo_jan_2024_uid4.pdf', '2024-01-28 18:00:00'),
(98, 4, '2024-02', 1141.00, 'uploads/recibos/recibo_fev_2024_uid4.pdf', '2024-02-28 18:00:00'),
(99, 4, '2024-03', 1191.00, 'uploads/recibos/recibo_mar_2024_uid4.pdf', '2024-03-28 18:00:00'),
(100, 4, '2024-04', 1108.00, 'uploads/recibos/recibo_abr_2024_uid4.pdf', '2024-04-28 18:00:00'),
(101, 4, '2024-05', 1010.00, 'uploads/recibos/recibo_mai_2024_uid4.pdf', '2024-05-28 18:00:00'),
(102, 4, '2024-06', 1073.00, 'uploads/recibos/recibo_jun_2024_uid4.pdf', '2024-06-28 18:00:00'),
(103, 4, '2024-07', 1160.00, 'uploads/recibos/recibo_jul_2024_uid4.pdf', '2024-07-28 18:00:00'),
(104, 4, '2024-08', 1032.00, 'uploads/recibos/recibo_ago_2024_uid4.pdf', '2024-08-28 18:00:00'),
(105, 4, '2024-09', 1183.00, 'uploads/recibos/recibo_set_2024_uid4.pdf', '2024-09-28 18:00:00'),
(106, 4, '2024-10', 1117.00, 'uploads/recibos/recibo_out_2024_uid4.pdf', '2024-10-28 18:00:00'),
(107, 4, '2024-11', 1079.00, 'uploads/recibos/recibo_nov_2024_uid4.pdf', '2024-11-28 18:00:00'),
(108, 4, '2024-12', 1132.00, 'uploads/recibos/recibo_dez_2024_uid4.pdf', '2024-12-28 18:00:00'),
(109, 4, '2025-01', 1023.00, 'uploads/recibos/recibo_jan_2025_uid4.pdf', '2025-01-28 18:00:00'),
(110, 4, '2025-02', 1173.00, 'uploads/recibos/recibo_fev_2025_uid4.pdf', '2025-02-28 18:00:00'),
(111, 4, '2025-03', 1004.00, 'uploads/recibos/recibo_mar_2025_uid4.pdf', '2025-03-28 18:00:00'),
(112, 4, '2025-04', 1126.00, 'uploads/recibos/recibo_abr_2025_uid4.pdf', '2025-04-28 18:00:00'),
(113, 4, '2025-05', 1056.00, 'uploads/recibos/recibo_mai_2025_uid4.pdf', '2025-05-28 18:00:00'),
(114, 4, '2025-06', 1151.00, 'uploads/recibos/recibo_jun_2025_uid4.pdf', '2025-06-28 18:00:00'),
(115, 4, '2025-07', 1109.00, 'uploads/recibos/recibo_jul_2025_uid4.pdf', '2025-07-28 18:00:00'),
(116, 4, '2025-08', 1173.00, 'uploads/recibos/recibo_ago_2025_uid4.pdf', '2025-08-28 18:00:00'),
(117, 4, '2025-09', 1181.00, 'uploads/recibos/recibo_set_2025_uid4.pdf', '2025-09-28 18:00:00'),
(118, 4, '2025-10', 1139.00, 'uploads/recibos/recibo_out_2025_uid4.pdf', '2025-10-28 18:00:00'),
(119, 4, '2025-11', 1077.00, 'uploads/recibos/recibo_nov_2025_uid4.pdf', '2025-11-28 18:00:00'),
(120, 4, '2025-12', 1144.00, 'uploads/recibos/recibo_dez_2025_uid4.pdf', '2025-12-28 18:00:00'),
(121, 4, '2026-01', 1190.00, 'uploads/recibos/recibo_jan_2026_uid4.pdf', '2026-01-28 18:00:00'),
(122, 4, '2026-02', 1160.00, 'uploads/recibos/recibo_fev_2026_uid4.pdf', '2026-02-28 18:00:00'),
(123, 4, '2026-03', 1123.00, 'uploads/recibos/recibo_mar_2026_uid4.pdf', '2026-03-28 18:00:00'),
(124, 4, '2026-04', 1050.00, 'uploads/recibos/recibo_abr_2026_uid4.pdf', '2026-04-28 18:00:00'),
(125, 4, '2026-05', 1184.00, 'uploads/recibos/recibo_mai_2026_uid4.pdf', '2026-05-28 18:00:00'),
(126, 4, '2026-06', 1187.00, 'uploads/recibos/recibo_jun_2026_uid4.pdf', '2026-06-28 18:00:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizadores`
--

CREATE TABLE `utilizadores` (
  `id` int(10) UNSIGNED NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `cc` varchar(20) DEFAULT NULL,
  `nif` varchar(20) DEFAULT NULL,
  `morada` text DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL,
  `salario_base` decimal(10,2) DEFAULT 0.00,
  `estado` enum('ativo','inativo') NOT NULL DEFAULT 'ativo',
  `role` enum('admin','funcionario') NOT NULL DEFAULT 'funcionario',
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Extraindo dados da tabela `utilizadores`
--

INSERT INTO `utilizadores` (`id`, `nome`, `email`, `password`, `telefone`, `cc`, `nif`, `morada`, `cargo`, `salario_base`, `estado`, `role`, `created_at`) VALUES
(1, 'Administrador', 'admin@empresa.pt', '$2y$10$fmqtKXhiMlzXkjDgKKZbTubHCeezlcPqKvJ4h.Wjk6GOVWYjIvC0.', NULL, NULL, NULL, NULL, 'Diretor Geral', 2800.00, 'ativo', 'admin', '2022-10-01 09:00:00'),
(2, 'Ricardo Pereira', 'ricardo@empresa.pt', '$2y$10$fmqtKXhiMlzXkjDgKKZbTubHCeezlcPqKvJ4h.Wjk6GOVWYjIvC0.', NULL, NULL, NULL, NULL, 'Gestor de Conta Sénior', 1350.00, 'ativo', 'funcionario', '2022-10-02 09:00:00'),
(3, 'Ana Rita Santos', 'ana.santos@empresa.pt', '$2y$10$fmqtKXhiMlzXkjDgKKZbTubHCeezlcPqKvJ4h.Wjk6GOVWYjIvC0.', NULL, NULL, NULL, NULL, 'Comercial de Frotas', 1200.00, 'ativo', 'funcionario', '2022-10-03 09:00:00'),
(4, 'Tiago Sousa', 'tiago.sousa@empresa.pt', '$2y$10$fmqtKXhiMlzXkjDgKKZbTubHCeezlcPqKvJ4h.Wjk6GOVWYjIvC0.', NULL, NULL, NULL, NULL, 'Técnico de Reparações', 1050.00, 'ativo', 'funcionario', '2022-10-04 09:00:00'),
(5, 'Sofia Carvalho', 'sofia.carvalho@empresa.pt', '$2y$10$fmqtKXhiMlzXkjDgKKZbTubHCeezlcPqKvJ4h.Wjk6GOVWYjIvC0.', NULL, NULL, NULL, NULL, 'Assistente Comercial', 980.00, 'inativo', 'funcionario', '2022-10-05 09:00:00');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `calendario_tarefas`
--
ALTER TABLE `calendario_tarefas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `criado_por` (`criado_por`),
  ADD KEY `idx_data_hora` (`data_hora`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_atribuido` (`atribuido_a`);

--
-- Índices para tabela `documentos_funcionarios`
--
ALTER TABLE `documentos_funcionarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_utilizador_id` (`utilizador_id`);

--
-- Índices para tabela `documentos_leads`
--
ALTER TABLE `documentos_leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `enviado_por` (`enviado_por`),
  ADD KEY `idx_lead_id` (`lead_id`);

--
-- Índices para tabela `historico_contacto`
--
ALTER TABLE `historico_contacto`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilizador_id` (`utilizador_id`),
  ADD KEY `idx_lead_id` (`lead_id`);

--
-- Índices para tabela `leads`
--
ALTER TABLE `leads`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_estado` (`estado`),
  ADD KEY `idx_criado_por` (`criado_por`);

--
-- Índices para tabela `recibos_vencimento`
--
ALTER TABLE `recibos_vencimento`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_utilizador_mes` (`utilizador_id`,`mes_referencia`);

--
-- Índices para tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_estado` (`estado`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `calendario_tarefas`
--
ALTER TABLE `calendario_tarefas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de tabela `documentos_funcionarios`
--
ALTER TABLE `documentos_funcionarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `documentos_leads`
--
ALTER TABLE `documentos_leads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `historico_contacto`
--
ALTER TABLE `historico_contacto`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT de tabela `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de tabela `recibos_vencimento`
--
ALTER TABLE `recibos_vencimento`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=127;

--
-- AUTO_INCREMENT de tabela `utilizadores`
--
ALTER TABLE `utilizadores`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `calendario_tarefas`
--
ALTER TABLE `calendario_tarefas`
  ADD CONSTRAINT `calendario_tarefas_ibfk_1` FOREIGN KEY (`criado_por`) REFERENCES `utilizadores` (`id`),
  ADD CONSTRAINT `calendario_tarefas_ibfk_2` FOREIGN KEY (`atribuido_a`) REFERENCES `utilizadores` (`id`) ON DELETE SET NULL;

--
-- Limitadores para a tabela `documentos_funcionarios`
--
ALTER TABLE `documentos_funcionarios`
  ADD CONSTRAINT `documentos_funcionarios_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `documentos_leads`
--
ALTER TABLE `documentos_leads`
  ADD CONSTRAINT `documentos_leads_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documentos_leads_ibfk_2` FOREIGN KEY (`enviado_por`) REFERENCES `utilizadores` (`id`);

--
-- Limitadores para a tabela `historico_contacto`
--
ALTER TABLE `historico_contacto`
  ADD CONSTRAINT `historico_contacto_ibfk_1` FOREIGN KEY (`lead_id`) REFERENCES `leads` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `historico_contacto_ibfk_2` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`);

--
-- Limitadores para a tabela `leads`
--
ALTER TABLE `leads`
  ADD CONSTRAINT `leads_ibfk_1` FOREIGN KEY (`criado_por`) REFERENCES `utilizadores` (`id`);

--
-- Limitadores para a tabela `recibos_vencimento`
--
ALTER TABLE `recibos_vencimento`
  ADD CONSTRAINT `recibos_vencimento_ibfk_1` FOREIGN KEY (`utilizador_id`) REFERENCES `utilizadores` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
