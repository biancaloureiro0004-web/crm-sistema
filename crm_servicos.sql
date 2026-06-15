-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 12-Jun-2026 às 15:03
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
(1, 'Reunião de arranque de ano', 'Definição de objetivos para 2023.', '2023-01-09 10:00:00', 'equipa', 1, NULL, '2023-01-07 21:55:36'),
(2, 'Formação CRM — Módulo Leads', 'Formação interna sobre o novo sistema.', '2023-02-14 14:00:00', 'equipa', 1, NULL, '2023-02-08 03:22:04'),
(3, 'Visita técnica — Metalúrgica do Norte', 'Avaliação de equipamentos em Porto.', '2023-03-22 09:30:00', 'individual', 2, 2, '2023-03-19 21:16:53'),
(4, 'Almoço de equipa Q2', 'Celebração dos resultados do semestre.', '2023-06-30 13:00:00', 'equipa', 1, NULL, '2023-06-29 12:27:08'),
(5, 'Proposta para Hotel Quinta do Lago', 'Preparar e enviar proposta de manutenção.', '2023-07-10 11:00:00', 'individual', 4, 4, '2023-07-08 00:10:02'),
(6, 'Revisão de processos internos', 'Auditoria interna de qualidade.', '2023-09-04 15:00:00', 'equipa', 1, NULL, '2023-08-31 14:10:05'),
(7, 'Avaliação de desempenho semestral', 'Reunião de avaliação de fim de ano.', '2023-12-18 10:00:00', 'equipa', 1, NULL, '2023-12-16 06:06:12'),
(8, 'Kick-off 2024', 'Objetivos e estratégia comercial 2024.', '2024-01-08 10:00:00', 'equipa', 1, NULL, '2024-01-02 01:44:33'),
(9, 'Visita — Farmácias Saúde Total', 'Apresentação de solução de manutenção.', '2024-02-19 09:00:00', 'individual', 2, 2, '2024-02-16 18:29:09'),
(10, 'Workshop de Negociação', 'Formação externa em técnicas de negociação.', '2024-04-15 13:00:00', 'equipa', 1, NULL, '2024-04-11 11:14:25'),
(11, 'Revisão de pipeline Q2', 'Ponto de situação de todas as leads ativas.', '2024-06-03 16:00:00', 'equipa', 1, NULL, '2024-05-29 21:01:20'),
(12, 'Manutenção preventiva — Clínica Médica', 'Manutenção programada de equipamentos.', '2024-08-20 08:00:00', 'individual', 3, 3, '2024-08-14 03:20:37'),
(13, 'Feria da Indústria — Stand', 'Participação na Exponor, Porto.', '2024-10-07 09:00:00', 'equipa', 1, NULL, '2024-09-30 10:17:40'),
(14, 'Balanço anual 2024', 'Revisão de KPIs e resultados do ano.', '2024-12-16 14:00:00', 'equipa', 1, NULL, '2024-12-11 00:19:01'),
(15, 'Reunião de equipa — Jan 2025', 'Arranque do novo ano comercial.', '2025-01-13 10:00:00', 'equipa', 1, NULL, '2025-01-09 08:40:54'),
(16, 'Follow-up — Biotecnologia InnovaLab', 'Retomar contacto após proposta enviada.', '2025-03-05 11:30:00', 'individual', 3, 2, '2025-02-27 00:32:49'),
(17, 'Webinar \"Manutenção Preditiva\"', 'Evento online para clientes.', '2025-05-20 15:00:00', 'equipa', 1, NULL, '2025-05-14 05:59:01'),
(18, 'Auditoria ISO 9001', 'Renovação da certificação de qualidade.', '2025-07-14 09:00:00', 'equipa', 1, NULL, '2025-07-08 09:35:23'),
(19, 'Visita — Renascimento Têxtil', 'Análise de equipamentos na fábrica.', '2025-09-22 10:00:00', 'individual', 5, 2, '2025-09-18 11:05:37'),
(20, 'Revisão de preços 2026', 'Ajuste da tabela de preços para o próximo ano.', '2025-11-10 14:00:00', 'equipa', 1, NULL, '2025-11-04 05:34:09'),
(21, 'Reunião comercial mensal', 'Ponto de situação das leads do mês.', '2026-06-16 10:00:00', 'equipa', 1, NULL, '2026-06-14 05:16:25'),
(22, 'Formação NovaSoftware v3', 'Atualização para nova versão do sistema.', '2026-06-23 14:00:00', 'equipa', 1, NULL, '2026-06-21 03:56:59'),
(23, 'Proposta — Centro Logístico Sul', 'Preparar proposta de contrato anual.', '2026-06-19 09:30:00', 'individual', 3, 3, '2026-06-17 13:47:23'),
(24, 'Revisão de pipeline Q3', 'Análise das oportunidades do trimestre.', '2026-07-06 16:00:00', 'equipa', 1, NULL, '2026-07-04 19:32:23'),
(25, 'Avaliação de satisfação de clientes', 'Envio de inquérito e análise de resultados.', '2026-07-28 11:00:00', 'equipa', 1, NULL, '2026-07-21 11:57:09'),
(26, 'Formação em Segurança no Trabalho', 'Obrigatória — renovação de certificados.', '2026-08-11 09:00:00', 'equipa', 1, NULL, '2026-08-06 14:26:04');

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
(1, 1, 'Contrato de Trabalho', 'uploads/documentos_funcionarios/contrato_de_trabalho_uid1.pdf', '2022-11-12 12:04:23'),
(2, 1, 'Cartão de Cidadão', 'uploads/documentos_funcionarios/cartão_de_cidadão_uid1.pdf', '2023-12-13 07:58:21'),
(3, 1, 'NIF', 'uploads/documentos_funcionarios/nif_uid1.pdf', '2023-10-05 17:33:01'),
(4, 2, 'Contrato de Trabalho', 'uploads/documentos_funcionarios/contrato_de_trabalho_uid2.pdf', '2023-10-31 03:13:50'),
(5, 2, 'Cartão de Cidadão', 'uploads/documentos_funcionarios/cartão_de_cidadão_uid2.pdf', '2023-08-10 23:24:23'),
(6, 2, 'NIF', 'uploads/documentos_funcionarios/nif_uid2.pdf', '2023-06-09 21:36:04'),
(7, 3, 'Contrato de Trabalho', 'uploads/documentos_funcionarios/contrato_de_trabalho_uid3.pdf', '2023-03-30 00:31:21'),
(8, 3, 'Cartão de Cidadão', 'uploads/documentos_funcionarios/cartão_de_cidadão_uid3.pdf', '2023-01-29 23:07:04'),
(9, 3, 'NIF', 'uploads/documentos_funcionarios/nif_uid3.pdf', '2023-11-07 07:00:16'),
(10, 4, 'Contrato de Trabalho', 'uploads/documentos_funcionarios/contrato_de_trabalho_uid4.pdf', '2023-12-04 12:27:36'),
(11, 4, 'Cartão de Cidadão', 'uploads/documentos_funcionarios/cartão_de_cidadão_uid4.pdf', '2023-11-20 04:40:30'),
(12, 4, 'NIF', 'uploads/documentos_funcionarios/nif_uid4.pdf', '2023-06-10 18:22:29'),
(13, 5, 'Contrato de Trabalho', 'uploads/documentos_funcionarios/contrato_de_trabalho_uid5.pdf', '2023-07-16 15:05:28'),
(14, 5, 'Cartão de Cidadão', 'uploads/documentos_funcionarios/cartão_de_cidadão_uid5.pdf', '2023-01-26 10:24:05');

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
(1, 1, 'Proposta Comercial 2025', 'uploads/propostas/proposta_lead1_2025.pdf', 3, '2025-10-12 10:00:00'),
(2, 4, 'Proposta Comercial 2025', 'uploads/propostas/proposta_lead4_2025.pdf', 2, '2025-08-20 10:00:00'),
(3, 6, 'Proposta Comercial 2023', 'uploads/propostas/proposta_lead6_2023.pdf', 3, '2023-10-02 10:00:00'),
(4, 7, 'Proposta Comercial 2025', 'uploads/propostas/proposta_lead7_2025.pdf', 3, '2025-05-01 10:00:00'),
(5, 8, 'Proposta Comercial 2023', 'uploads/propostas/proposta_lead8_2023.pdf', 4, '2023-03-07 10:00:00'),
(6, 10, 'Proposta Comercial 2026', 'uploads/propostas/proposta_lead10_2026.pdf', 5, '2026-06-05 10:00:00'),
(7, 16, 'Proposta Comercial 2026', 'uploads/propostas/proposta_lead16_2026.pdf', 2, '2026-06-25 10:00:00');

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
(1, 1, 3, 'Email enviado com informações gerais sobre os nossos serviços.', '2023-05-23 22:04:02'),
(2, 2, 5, 'Reunião de acompanhamento agendada para o mês seguinte.', '2024-01-11 04:14:54'),
(3, 3, 3, 'Orçamento revisto e reenviado com condições melhoradas.', '2025-08-11 15:29:31'),
(4, 3, 4, 'Problema técnico reportado. Agendada visita de reparação urgente.', '2025-07-18 13:39:56'),
(5, 3, 2, 'Cliente solicitou orçamento detalhado para reparação do equipamento.', '2023-11-19 18:53:00'),
(6, 4, 3, 'Reunião de acompanhamento agendada para o mês seguinte.', '2024-01-07 22:22:14'),
(7, 4, 5, 'Cliente solicitou orçamento detalhado para reparação do equipamento.', '2023-02-03 12:48:29'),
(8, 5, 3, 'Primeiro contacto estabelecido via telefone. Cliente mostrou interesse.', '2023-07-07 15:44:41'),
(9, 6, 5, 'Email enviado com informações gerais sobre os nossos serviços.', '2026-02-07 16:26:00'),
(10, 6, 3, 'Proposta enviada por email. À espera de resposta.', '2023-04-20 22:00:34'),
(11, 6, 3, 'Cliente solicitou orçamento detalhado para reparação do equipamento.', '2024-10-16 09:11:43'),
(12, 7, 3, 'Visita técnica realizada ao local. Avaliação positiva.', '2025-03-21 05:24:03'),
(13, 7, 5, 'Negociação de preço em curso. Cliente pediu desconto.', '2023-07-11 15:14:51'),
(14, 8, 5, 'Primeiro contacto estabelecido via telefone. Cliente mostrou interesse.', '2023-09-28 01:07:24'),
(15, 8, 5, 'Follow-up feito. Cliente em processo de decisão interna.', '2024-04-02 12:10:52'),
(16, 9, 3, 'Cliente optou por solução da concorrência por questões de preço.', '2024-02-08 17:16:23'),
(17, 9, 2, 'Visita técnica realizada ao local. Avaliação positiva.', '2025-02-07 12:23:13'),
(18, 9, 4, 'Visita técnica realizada ao local. Avaliação positiva.', '2023-01-07 19:00:33'),
(19, 10, 5, 'Contrato assinado. Serviço a iniciar brevemente.', '2023-02-03 23:26:22'),
(20, 11, 4, 'Reunião de acompanhamento agendada para o mês seguinte.', '2024-09-23 21:30:17'),
(21, 11, 2, 'Cliente solicitou orçamento detalhado para reparação do equipamento.', '2025-03-05 09:14:17'),
(22, 11, 4, 'Cliente solicitou orçamento detalhado para reparação do equipamento.', '2023-08-18 05:41:37'),
(23, 12, 4, 'Follow-up feito. Cliente em processo de decisão interna.', '2025-09-15 10:11:02'),
(24, 13, 4, 'Visita técnica realizada ao local. Avaliação positiva.', '2025-07-08 14:32:16'),
(25, 13, 4, 'Orçamento revisto e reenviado com condições melhoradas.', '2023-11-06 17:59:44'),
(26, 14, 4, 'Instalação concluída com sucesso. Cliente satisfeito.', '2025-03-28 11:07:09'),
(27, 15, 5, 'Reunião de acompanhamento agendada para o mês seguinte.', '2023-04-21 07:05:33'),
(28, 15, 5, 'Problema técnico reportado. Agendada visita de reparação urgente.', '2023-06-22 17:22:05'),
(29, 16, 5, 'Serviço de manutenção preventiva concluído. Sem anomalias.', '2026-05-03 19:42:20'),
(30, 16, 5, 'Cliente optou por solução da concorrência por questões de preço.', '2024-08-05 06:25:58'),
(31, 16, 3, 'Cliente solicitou orçamento detalhado para reparação do equipamento.', '2023-09-12 13:38:55'),
(32, 16, 2, 'Visita técnica realizada ao local. Avaliação positiva.', '2024-04-30 06:24:53'),
(33, 17, 5, 'Proposta enviada por email. À espera de resposta.', '2023-05-20 14:46:46'),
(34, 17, 3, 'Follow-up feito. Cliente em processo de decisão interna.', '2024-10-11 14:50:52'),
(35, 18, 4, 'Instalação concluída com sucesso. Cliente satisfeito.', '2024-01-13 05:29:04'),
(36, 18, 3, 'Negociação de preço em curso. Cliente pediu desconto.', '2026-05-12 08:30:01'),
(37, 19, 2, 'Serviço de manutenção preventiva concluído. Sem anomalias.', '2025-12-28 23:47:30'),
(38, 19, 4, 'Visita técnica realizada ao local. Avaliação positiva.', '2025-01-07 11:15:01');

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
(1, 'Armazéns Ferreira & Filhos, Lda', 'geral@ferreiraf.pt', '211 234 567', 'Zona Industrial de Setúbal', '501234567', '10000001 0 ZZ0', 'Proposta Enviada', 3, '2024-02-05 05:37:25', '2024-02-05 05:37:25'),
(2, 'Transportes Atlas, S.A.', 'compras@atlastrans.pt', '252 345 678', 'EN 1, km 45, Santarém', '502345678', '10000002 1 ZZ1', 'Ganho', 2, '2023-03-25 20:42:54', '2023-03-25 20:42:54'),
(3, 'Clínica Médica do Rossio', 'admin@clinrossio.pt', '213 456 789', 'Praça do Rossio, 12, Lisboa', '503456789', '10000003 2 ZZ2', 'Ganho', 4, '2023-01-14 12:12:11', '2023-01-14 12:12:11'),
(4, 'Hotel Quinta do Lago', 'manutencao@hql.pt', '289 567 890', 'Quinta do Lago, Almancil', '504567890', '10000004 3 ZZ3', 'Ganho', 5, '2025-09-11 18:02:21', '2025-09-11 18:02:21'),
(5, 'Metalúrgica do Norte, S.A.', 'compras@metalnorte.pt', '225 678 901', 'Rua Industrial, Porto', '505678901', '10000005 4 ZZ4', 'Nova Lead', 3, '2025-06-02 08:42:31', '2025-06-02 08:42:31'),
(6, 'Supermercados FrescaMart', 'logistica@frescamart.pt', '261 789 012', 'Parque Retail de Cascais', '506789012', '10000006 5 ZZ5', 'Nova Lead', 4, '2024-03-20 08:04:33', '2024-03-20 08:04:33'),
(7, 'Herdade das Vinhas (Viticultura)', 'vinhas@herdadevinhas.pt', '268 890 123', 'Vidigueira, Beja', '507890123', '10000007 6 ZZ6', 'Perdido', 4, '2023-12-17 14:23:56', '2023-12-17 14:23:56'),
(8, 'Construções Cavaco & Irmãos', 'obra@cavacoi.pt', '239 901 234', 'Coimbra', '508901234', '10000008 7 ZZ7', 'Contacto Efetuado', 4, '2024-10-23 05:49:56', '2024-10-23 05:49:56'),
(9, 'Auto-Ribeiro (Concessionário)', 'servicos@autoribeiro.pt', '253 012 345', 'Av. Central, Braga', '509012345', '10000009 8 ZZ8', 'Ganho', 2, '2023-09-07 20:33:30', '2023-09-07 20:33:30'),
(10, 'Escola Superior de Tecnologia', 'infraestrutura@est.edu.pt', '212 123 456', 'Campus Universitário, Leiria', '510123456', '10000010 9 ZZ9', 'Perdido', 2, '2026-05-25 02:03:55', '2026-05-25 02:03:55'),
(11, 'Parques Naturais & Turismo, Lda', 'reservas@pnt.pt', '266 234 567', 'Mértola, Beja', '511234567', '10000011 0 ZZ1', 'Ganho', 3, '2024-04-22 10:29:21', '2024-04-22 10:29:21'),
(12, 'Grupo Editorial Palavras Vivas', 'producao@palavrasvivas.pt', '214 345 678', 'Av. Roma, Lisboa', '512345678', '10000012 1 ZZ2', 'Proposta Enviada', 5, '2025-07-02 01:07:01', '2025-07-02 01:07:01'),
(13, 'Farmácias Saúde Total (rede)', 'central@saudetotal.pt', '217 456 789', 'Rua Augusta, 1, Lisboa', '513456789', '10000013 2 ZZ3', 'Ganho', 3, '2024-09-24 06:14:51', '2024-09-24 06:14:51'),
(14, 'Renascimento Têxtil, S.A.', 'manut@renasc.pt', '253 567 890', 'Guimarães', '514567890', '10000014 3 ZZ4', 'Ganho', 5, '2024-10-03 21:53:56', '2024-10-03 21:53:56'),
(15, 'Solar do Tejo (Pousada)', 'gerencia@solardotejo.pt', '243 678 901', 'Santarém', '515678901', '10000015 4 ZZ5', 'Nova Lead', 4, '2025-11-26 00:15:26', '2025-11-26 00:15:26'),
(16, 'Centro Logístico Sul, Lda', 'ops@cls.pt', '265 789 012', 'Faro', '516789012', '10000016 5 ZZ6', 'Nova Lead', 4, '2024-11-24 20:33:51', '2024-11-24 20:33:51'),
(17, 'Mercearia Tradicional do Porto', 'loja@merctrad.pt', '226 890 123', 'Rua de Cedofeita, Porto', '517890123', '10000017 6 ZZ7', 'Perdido', 5, '2023-11-29 02:43:26', '2023-11-29 02:43:26'),
(18, 'Biotecnologia InnovaLab', 'lab@innovalab.pt', '219 901 234', 'Taguspark, Oeiras', '518901234', '10000018 7 ZZ8', 'Ganho', 2, '2025-12-08 13:08:16', '2025-12-08 13:08:16'),
(19, 'Quinta Pedagógica dos Templários', 'visitas@qptemplarios.pt', '249 012 345', 'Tomar', '519012345', '10000019 8 ZZ9', 'Nova Lead', 4, '2023-09-21 19:58:13', '2023-09-21 19:58:13');

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
(1, 2, '2023-01', 1397.00, 'uploads/recibos/recibo_jan_2023_uid2.pdf', '2023-01-28 18:00:00'),
(2, 2, '2023-02', 1475.00, 'uploads/recibos/recibo_fev_2023_uid2.pdf', '2023-02-28 18:00:00'),
(3, 2, '2023-03', 1323.00, 'uploads/recibos/recibo_mar_2023_uid2.pdf', '2023-03-28 18:00:00'),
(4, 2, '2023-04', 1410.00, 'uploads/recibos/recibo_abr_2023_uid2.pdf', '2023-04-28 18:00:00'),
(5, 2, '2023-05', 1303.00, 'uploads/recibos/recibo_mai_2023_uid2.pdf', '2023-05-28 18:00:00'),
(6, 2, '2023-06', 1367.00, 'uploads/recibos/recibo_jun_2023_uid2.pdf', '2023-06-28 18:00:00'),
(7, 2, '2023-07', 1457.00, 'uploads/recibos/recibo_jul_2023_uid2.pdf', '2023-07-28 18:00:00'),
(8, 2, '2023-08', 1463.00, 'uploads/recibos/recibo_ago_2023_uid2.pdf', '2023-08-28 18:00:00'),
(9, 2, '2023-09', 1417.00, 'uploads/recibos/recibo_set_2023_uid2.pdf', '2023-09-28 18:00:00'),
(10, 2, '2023-10', 1366.00, 'uploads/recibos/recibo_out_2023_uid2.pdf', '2023-10-28 18:00:00'),
(11, 2, '2023-11', 1345.00, 'uploads/recibos/recibo_nov_2023_uid2.pdf', '2023-11-28 18:00:00'),
(12, 2, '2023-12', 1490.00, 'uploads/recibos/recibo_dez_2023_uid2.pdf', '2023-12-28 18:00:00'),
(13, 2, '2024-01', 1312.00, 'uploads/recibos/recibo_jan_2024_uid2.pdf', '2024-01-28 18:00:00'),
(14, 2, '2024-02', 1304.00, 'uploads/recibos/recibo_fev_2024_uid2.pdf', '2024-02-28 18:00:00'),
(15, 2, '2024-03', 1466.00, 'uploads/recibos/recibo_mar_2024_uid2.pdf', '2024-03-28 18:00:00'),
(16, 2, '2024-04', 1381.00, 'uploads/recibos/recibo_abr_2024_uid2.pdf', '2024-04-28 18:00:00'),
(17, 2, '2024-05', 1336.00, 'uploads/recibos/recibo_mai_2024_uid2.pdf', '2024-05-28 18:00:00'),
(18, 2, '2024-06', 1405.00, 'uploads/recibos/recibo_jun_2024_uid2.pdf', '2024-06-28 18:00:00'),
(19, 2, '2024-07', 1405.00, 'uploads/recibos/recibo_jul_2024_uid2.pdf', '2024-07-28 18:00:00'),
(20, 2, '2024-08', 1441.00, 'uploads/recibos/recibo_ago_2024_uid2.pdf', '2024-08-28 18:00:00'),
(21, 2, '2024-09', 1452.00, 'uploads/recibos/recibo_set_2024_uid2.pdf', '2024-09-28 18:00:00'),
(22, 2, '2024-10', 1321.00, 'uploads/recibos/recibo_out_2024_uid2.pdf', '2024-10-28 18:00:00'),
(23, 2, '2024-11', 1436.00, 'uploads/recibos/recibo_nov_2024_uid2.pdf', '2024-11-28 18:00:00'),
(24, 2, '2024-12', 1428.00, 'uploads/recibos/recibo_dez_2024_uid2.pdf', '2024-12-28 18:00:00'),
(25, 2, '2025-01', 1496.00, 'uploads/recibos/recibo_jan_2025_uid2.pdf', '2025-01-28 18:00:00'),
(26, 2, '2025-02', 1420.00, 'uploads/recibos/recibo_fev_2025_uid2.pdf', '2025-02-28 18:00:00'),
(27, 2, '2025-03', 1387.00, 'uploads/recibos/recibo_mar_2025_uid2.pdf', '2025-03-28 18:00:00'),
(28, 2, '2025-04', 1487.00, 'uploads/recibos/recibo_abr_2025_uid2.pdf', '2025-04-28 18:00:00'),
(29, 2, '2025-05', 1378.00, 'uploads/recibos/recibo_mai_2025_uid2.pdf', '2025-05-28 18:00:00'),
(30, 2, '2025-06', 1454.00, 'uploads/recibos/recibo_jun_2025_uid2.pdf', '2025-06-28 18:00:00'),
(31, 2, '2025-07', 1392.00, 'uploads/recibos/recibo_jul_2025_uid2.pdf', '2025-07-28 18:00:00'),
(32, 2, '2025-08', 1392.00, 'uploads/recibos/recibo_ago_2025_uid2.pdf', '2025-08-28 18:00:00'),
(33, 2, '2025-09', 1471.00, 'uploads/recibos/recibo_set_2025_uid2.pdf', '2025-09-28 18:00:00'),
(34, 2, '2025-10', 1488.00, 'uploads/recibos/recibo_out_2025_uid2.pdf', '2025-10-28 18:00:00'),
(35, 2, '2025-11', 1400.00, 'uploads/recibos/recibo_nov_2025_uid2.pdf', '2025-11-28 18:00:00'),
(36, 2, '2025-12', 1338.00, 'uploads/recibos/recibo_dez_2025_uid2.pdf', '2025-12-28 18:00:00'),
(37, 2, '2026-01', 1428.00, 'uploads/recibos/recibo_jan_2026_uid2.pdf', '2026-01-28 18:00:00'),
(38, 2, '2026-02', 1417.00, 'uploads/recibos/recibo_fev_2026_uid2.pdf', '2026-02-28 18:00:00'),
(39, 2, '2026-03', 1321.00, 'uploads/recibos/recibo_mar_2026_uid2.pdf', '2026-03-28 18:00:00'),
(40, 2, '2026-04', 1399.00, 'uploads/recibos/recibo_abr_2026_uid2.pdf', '2026-04-28 18:00:00'),
(41, 2, '2026-05', 1314.00, 'uploads/recibos/recibo_mai_2026_uid2.pdf', '2026-05-28 18:00:00'),
(42, 3, '2023-01', 1179.00, 'uploads/recibos/recibo_jan_2023_uid3.pdf', '2023-01-28 18:00:00'),
(43, 3, '2023-02', 1314.00, 'uploads/recibos/recibo_fev_2023_uid3.pdf', '2023-02-28 18:00:00'),
(44, 3, '2023-03', 1201.00, 'uploads/recibos/recibo_mar_2023_uid3.pdf', '2023-03-28 18:00:00'),
(45, 3, '2023-04', 1225.00, 'uploads/recibos/recibo_abr_2023_uid3.pdf', '2023-04-28 18:00:00'),
(46, 3, '2023-05', 1226.00, 'uploads/recibos/recibo_mai_2023_uid3.pdf', '2023-05-28 18:00:00'),
(47, 3, '2023-06', 1246.00, 'uploads/recibos/recibo_jun_2023_uid3.pdf', '2023-06-28 18:00:00'),
(48, 3, '2023-07', 1325.00, 'uploads/recibos/recibo_jul_2023_uid3.pdf', '2023-07-28 18:00:00'),
(49, 3, '2023-08', 1350.00, 'uploads/recibos/recibo_ago_2023_uid3.pdf', '2023-08-28 18:00:00'),
(50, 3, '2023-09', 1219.00, 'uploads/recibos/recibo_set_2023_uid3.pdf', '2023-09-28 18:00:00'),
(51, 3, '2023-10', 1325.00, 'uploads/recibos/recibo_out_2023_uid3.pdf', '2023-10-28 18:00:00'),
(52, 3, '2023-11', 1284.00, 'uploads/recibos/recibo_nov_2023_uid3.pdf', '2023-11-28 18:00:00'),
(53, 3, '2023-12', 1234.00, 'uploads/recibos/recibo_dez_2023_uid3.pdf', '2023-12-28 18:00:00'),
(54, 3, '2024-01', 1288.00, 'uploads/recibos/recibo_jan_2024_uid3.pdf', '2024-01-28 18:00:00'),
(55, 3, '2024-02', 1193.00, 'uploads/recibos/recibo_fev_2024_uid3.pdf', '2024-02-28 18:00:00'),
(56, 3, '2024-03', 1167.00, 'uploads/recibos/recibo_mar_2024_uid3.pdf', '2024-03-28 18:00:00'),
(57, 3, '2024-04', 1270.00, 'uploads/recibos/recibo_abr_2024_uid3.pdf', '2024-04-28 18:00:00'),
(58, 3, '2024-05', 1166.00, 'uploads/recibos/recibo_mai_2024_uid3.pdf', '2024-05-28 18:00:00'),
(59, 3, '2024-06', 1179.00, 'uploads/recibos/recibo_jun_2024_uid3.pdf', '2024-06-28 18:00:00'),
(60, 3, '2024-07', 1285.00, 'uploads/recibos/recibo_jul_2024_uid3.pdf', '2024-07-28 18:00:00'),
(61, 3, '2024-08', 1249.00, 'uploads/recibos/recibo_ago_2024_uid3.pdf', '2024-08-28 18:00:00'),
(62, 3, '2024-09', 1196.00, 'uploads/recibos/recibo_set_2024_uid3.pdf', '2024-09-28 18:00:00'),
(63, 3, '2024-10', 1319.00, 'uploads/recibos/recibo_out_2024_uid3.pdf', '2024-10-28 18:00:00'),
(64, 3, '2024-11', 1294.00, 'uploads/recibos/recibo_nov_2024_uid3.pdf', '2024-11-28 18:00:00'),
(65, 3, '2024-12', 1284.00, 'uploads/recibos/recibo_dez_2024_uid3.pdf', '2024-12-28 18:00:00'),
(66, 3, '2025-01', 1221.00, 'uploads/recibos/recibo_jan_2025_uid3.pdf', '2025-01-28 18:00:00'),
(67, 3, '2025-02', 1340.00, 'uploads/recibos/recibo_fev_2025_uid3.pdf', '2025-02-28 18:00:00'),
(68, 3, '2025-03', 1273.00, 'uploads/recibos/recibo_mar_2025_uid3.pdf', '2025-03-28 18:00:00'),
(69, 3, '2025-04', 1332.00, 'uploads/recibos/recibo_abr_2025_uid3.pdf', '2025-04-28 18:00:00'),
(70, 3, '2025-05', 1206.00, 'uploads/recibos/recibo_mai_2025_uid3.pdf', '2025-05-28 18:00:00'),
(71, 3, '2025-06', 1309.00, 'uploads/recibos/recibo_jun_2025_uid3.pdf', '2025-06-28 18:00:00'),
(72, 3, '2025-07', 1267.00, 'uploads/recibos/recibo_jul_2025_uid3.pdf', '2025-07-28 18:00:00'),
(73, 3, '2025-08', 1227.00, 'uploads/recibos/recibo_ago_2025_uid3.pdf', '2025-08-28 18:00:00'),
(74, 3, '2025-09', 1152.00, 'uploads/recibos/recibo_set_2025_uid3.pdf', '2025-09-28 18:00:00'),
(75, 3, '2025-10', 1221.00, 'uploads/recibos/recibo_out_2025_uid3.pdf', '2025-10-28 18:00:00'),
(76, 3, '2025-11', 1341.00, 'uploads/recibos/recibo_nov_2025_uid3.pdf', '2025-11-28 18:00:00'),
(77, 3, '2025-12', 1262.00, 'uploads/recibos/recibo_dez_2025_uid3.pdf', '2025-12-28 18:00:00'),
(78, 3, '2026-01', 1198.00, 'uploads/recibos/recibo_jan_2026_uid3.pdf', '2026-01-28 18:00:00'),
(79, 3, '2026-02', 1191.00, 'uploads/recibos/recibo_fev_2026_uid3.pdf', '2026-02-28 18:00:00'),
(80, 3, '2026-03', 1269.00, 'uploads/recibos/recibo_mar_2026_uid3.pdf', '2026-03-28 18:00:00'),
(81, 3, '2026-04', 1163.00, 'uploads/recibos/recibo_abr_2026_uid3.pdf', '2026-04-28 18:00:00'),
(82, 3, '2026-05', 1170.00, 'uploads/recibos/recibo_mai_2026_uid3.pdf', '2026-05-28 18:00:00'),
(83, 4, '2023-01', 1168.00, 'uploads/recibos/recibo_jan_2023_uid4.pdf', '2023-01-28 18:00:00'),
(84, 4, '2023-02', 1071.00, 'uploads/recibos/recibo_fev_2023_uid4.pdf', '2023-02-28 18:00:00'),
(85, 4, '2023-03', 1158.00, 'uploads/recibos/recibo_mar_2023_uid4.pdf', '2023-03-28 18:00:00'),
(86, 4, '2023-04', 1117.00, 'uploads/recibos/recibo_abr_2023_uid4.pdf', '2023-04-28 18:00:00'),
(87, 4, '2023-05', 1136.00, 'uploads/recibos/recibo_mai_2023_uid4.pdf', '2023-05-28 18:00:00'),
(88, 4, '2023-06', 1073.00, 'uploads/recibos/recibo_jun_2023_uid4.pdf', '2023-06-28 18:00:00'),
(89, 4, '2023-07', 1096.00, 'uploads/recibos/recibo_jul_2023_uid4.pdf', '2023-07-28 18:00:00'),
(90, 4, '2023-08', 1001.00, 'uploads/recibos/recibo_ago_2023_uid4.pdf', '2023-08-28 18:00:00'),
(91, 4, '2023-09', 1059.00, 'uploads/recibos/recibo_set_2023_uid4.pdf', '2023-09-28 18:00:00'),
(92, 4, '2023-10', 1073.00, 'uploads/recibos/recibo_out_2023_uid4.pdf', '2023-10-28 18:00:00'),
(93, 4, '2023-11', 1074.00, 'uploads/recibos/recibo_nov_2023_uid4.pdf', '2023-11-28 18:00:00'),
(94, 4, '2023-12', 1093.00, 'uploads/recibos/recibo_dez_2023_uid4.pdf', '2023-12-28 18:00:00'),
(95, 4, '2024-01', 1158.00, 'uploads/recibos/recibo_jan_2024_uid4.pdf', '2024-01-28 18:00:00'),
(96, 4, '2024-02', 1017.00, 'uploads/recibos/recibo_fev_2024_uid4.pdf', '2024-02-28 18:00:00'),
(97, 4, '2024-03', 1180.00, 'uploads/recibos/recibo_mar_2024_uid4.pdf', '2024-03-28 18:00:00'),
(98, 4, '2024-04', 1200.00, 'uploads/recibos/recibo_abr_2024_uid4.pdf', '2024-04-28 18:00:00'),
(99, 4, '2024-05', 1176.00, 'uploads/recibos/recibo_mai_2024_uid4.pdf', '2024-05-28 18:00:00'),
(100, 4, '2024-06', 1169.00, 'uploads/recibos/recibo_jun_2024_uid4.pdf', '2024-06-28 18:00:00'),
(101, 4, '2024-07', 1173.00, 'uploads/recibos/recibo_jul_2024_uid4.pdf', '2024-07-28 18:00:00'),
(102, 4, '2024-08', 1175.00, 'uploads/recibos/recibo_ago_2024_uid4.pdf', '2024-08-28 18:00:00'),
(103, 4, '2024-09', 1132.00, 'uploads/recibos/recibo_set_2024_uid4.pdf', '2024-09-28 18:00:00'),
(104, 4, '2024-10', 1082.00, 'uploads/recibos/recibo_out_2024_uid4.pdf', '2024-10-28 18:00:00'),
(105, 4, '2024-11', 1086.00, 'uploads/recibos/recibo_nov_2024_uid4.pdf', '2024-11-28 18:00:00'),
(106, 4, '2024-12', 1129.00, 'uploads/recibos/recibo_dez_2024_uid4.pdf', '2024-12-28 18:00:00'),
(107, 4, '2025-01', 1115.00, 'uploads/recibos/recibo_jan_2025_uid4.pdf', '2025-01-28 18:00:00'),
(108, 4, '2025-02', 1035.00, 'uploads/recibos/recibo_fev_2025_uid4.pdf', '2025-02-28 18:00:00'),
(109, 4, '2025-03', 1178.00, 'uploads/recibos/recibo_mar_2025_uid4.pdf', '2025-03-28 18:00:00'),
(110, 4, '2025-04', 1126.00, 'uploads/recibos/recibo_abr_2025_uid4.pdf', '2025-04-28 18:00:00'),
(111, 4, '2025-05', 1122.00, 'uploads/recibos/recibo_mai_2025_uid4.pdf', '2025-05-28 18:00:00'),
(112, 4, '2025-06', 1080.00, 'uploads/recibos/recibo_jun_2025_uid4.pdf', '2025-06-28 18:00:00'),
(113, 4, '2025-07', 1143.00, 'uploads/recibos/recibo_jul_2025_uid4.pdf', '2025-07-28 18:00:00'),
(114, 4, '2025-08', 1177.00, 'uploads/recibos/recibo_ago_2025_uid4.pdf', '2025-08-28 18:00:00'),
(115, 4, '2025-09', 1057.00, 'uploads/recibos/recibo_set_2025_uid4.pdf', '2025-09-28 18:00:00'),
(116, 4, '2025-10', 1077.00, 'uploads/recibos/recibo_out_2025_uid4.pdf', '2025-10-28 18:00:00'),
(117, 4, '2025-11', 1005.00, 'uploads/recibos/recibo_nov_2025_uid4.pdf', '2025-11-28 18:00:00'),
(118, 4, '2025-12', 1131.00, 'uploads/recibos/recibo_dez_2025_uid4.pdf', '2025-12-28 18:00:00'),
(119, 4, '2026-01', 1163.00, 'uploads/recibos/recibo_jan_2026_uid4.pdf', '2026-01-28 18:00:00'),
(120, 4, '2026-02', 1200.00, 'uploads/recibos/recibo_fev_2026_uid4.pdf', '2026-02-28 18:00:00'),
(121, 4, '2026-03', 1063.00, 'uploads/recibos/recibo_mar_2026_uid4.pdf', '2026-03-28 18:00:00'),
(122, 4, '2026-04', 1046.00, 'uploads/recibos/recibo_abr_2026_uid4.pdf', '2026-04-28 18:00:00'),
(123, 4, '2026-05', 1018.00, 'uploads/recibos/recibo_mai_2026_uid4.pdf', '2026-05-28 18:00:00'),
(124, 5, '2023-01', 1002.00, 'uploads/recibos/recibo_jan_2023_uid5.pdf', '2023-01-28 18:00:00'),
(125, 5, '2023-02', 1020.00, 'uploads/recibos/recibo_fev_2023_uid5.pdf', '2023-02-28 18:00:00'),
(126, 5, '2023-03', 956.00, 'uploads/recibos/recibo_mar_2023_uid5.pdf', '2023-03-28 18:00:00'),
(127, 5, '2023-04', 1042.00, 'uploads/recibos/recibo_abr_2023_uid5.pdf', '2023-04-28 18:00:00'),
(128, 5, '2023-05', 958.00, 'uploads/recibos/recibo_mai_2023_uid5.pdf', '2023-05-28 18:00:00'),
(129, 5, '2023-06', 966.00, 'uploads/recibos/recibo_jun_2023_uid5.pdf', '2023-06-28 18:00:00'),
(130, 5, '2023-07', 1087.00, 'uploads/recibos/recibo_jul_2023_uid5.pdf', '2023-07-28 18:00:00'),
(131, 5, '2023-08', 985.00, 'uploads/recibos/recibo_ago_2023_uid5.pdf', '2023-08-28 18:00:00'),
(132, 5, '2023-09', 957.00, 'uploads/recibos/recibo_set_2023_uid5.pdf', '2023-09-28 18:00:00'),
(133, 5, '2023-10', 937.00, 'uploads/recibos/recibo_out_2023_uid5.pdf', '2023-10-28 18:00:00'),
(134, 5, '2023-11', 945.00, 'uploads/recibos/recibo_nov_2023_uid5.pdf', '2023-11-28 18:00:00'),
(135, 5, '2023-12', 1026.00, 'uploads/recibos/recibo_dez_2023_uid5.pdf', '2023-12-28 18:00:00'),
(136, 5, '2024-01', 965.00, 'uploads/recibos/recibo_jan_2024_uid5.pdf', '2024-01-28 18:00:00'),
(137, 5, '2024-02', 1031.00, 'uploads/recibos/recibo_fev_2024_uid5.pdf', '2024-02-28 18:00:00'),
(138, 5, '2024-03', 977.00, 'uploads/recibos/recibo_mar_2024_uid5.pdf', '2024-03-28 18:00:00'),
(139, 5, '2024-04', 1129.00, 'uploads/recibos/recibo_abr_2024_uid5.pdf', '2024-04-28 18:00:00'),
(140, 5, '2024-05', 1105.00, 'uploads/recibos/recibo_mai_2024_uid5.pdf', '2024-05-28 18:00:00'),
(141, 5, '2024-06', 934.00, 'uploads/recibos/recibo_jun_2024_uid5.pdf', '2024-06-28 18:00:00'),
(142, 5, '2024-07', 1124.00, 'uploads/recibos/recibo_jul_2024_uid5.pdf', '2024-07-28 18:00:00'),
(143, 5, '2024-08', 1056.00, 'uploads/recibos/recibo_ago_2024_uid5.pdf', '2024-08-28 18:00:00'),
(144, 5, '2024-09', 1097.00, 'uploads/recibos/recibo_set_2024_uid5.pdf', '2024-09-28 18:00:00'),
(145, 5, '2024-10', 1099.00, 'uploads/recibos/recibo_out_2024_uid5.pdf', '2024-10-28 18:00:00'),
(146, 5, '2024-11', 1009.00, 'uploads/recibos/recibo_nov_2024_uid5.pdf', '2024-11-28 18:00:00'),
(147, 5, '2024-12', 936.00, 'uploads/recibos/recibo_dez_2024_uid5.pdf', '2024-12-28 18:00:00'),
(148, 5, '2025-01', 931.00, 'uploads/recibos/recibo_jan_2025_uid5.pdf', '2025-01-28 18:00:00'),
(149, 5, '2025-02', 972.00, 'uploads/recibos/recibo_fev_2025_uid5.pdf', '2025-02-28 18:00:00'),
(150, 5, '2025-03', 939.00, 'uploads/recibos/recibo_mar_2025_uid5.pdf', '2025-03-28 18:00:00'),
(151, 5, '2025-04', 1130.00, 'uploads/recibos/recibo_abr_2025_uid5.pdf', '2025-04-28 18:00:00'),
(152, 5, '2025-05', 1028.00, 'uploads/recibos/recibo_mai_2025_uid5.pdf', '2025-05-28 18:00:00'),
(153, 5, '2025-06', 992.00, 'uploads/recibos/recibo_jun_2025_uid5.pdf', '2025-06-28 18:00:00'),
(154, 5, '2025-07', 985.00, 'uploads/recibos/recibo_jul_2025_uid5.pdf', '2025-07-28 18:00:00'),
(155, 5, '2025-08', 1080.00, 'uploads/recibos/recibo_ago_2025_uid5.pdf', '2025-08-28 18:00:00'),
(156, 5, '2025-09', 1021.00, 'uploads/recibos/recibo_set_2025_uid5.pdf', '2025-09-28 18:00:00'),
(157, 5, '2025-10', 967.00, 'uploads/recibos/recibo_out_2025_uid5.pdf', '2025-10-28 18:00:00'),
(158, 5, '2025-11', 961.00, 'uploads/recibos/recibo_nov_2025_uid5.pdf', '2025-11-28 18:00:00'),
(159, 5, '2025-12', 1114.00, 'uploads/recibos/recibo_dez_2025_uid5.pdf', '2025-12-28 18:00:00'),
(160, 5, '2026-01', 988.00, 'uploads/recibos/recibo_jan_2026_uid5.pdf', '2026-01-28 18:00:00'),
(161, 5, '2026-02', 1021.00, 'uploads/recibos/recibo_fev_2026_uid5.pdf', '2026-02-28 18:00:00'),
(162, 5, '2026-03', 946.00, 'uploads/recibos/recibo_mar_2026_uid5.pdf', '2026-03-28 18:00:00'),
(163, 5, '2026-04', 1010.00, 'uploads/recibos/recibo_abr_2026_uid5.pdf', '2026-04-28 18:00:00'),
(164, 5, '2026-05', 950.00, 'uploads/recibos/recibo_mai_2026_uid5.pdf', '2026-05-28 18:00:00');

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
(1, 'Administrador', 'admin@empresa.pt', '$2y$10$mlAW0uQPPZaox84mMHOmnePQXFVobVUDNAgqATegzl5SOOru7rBO.', '210 000 001', '12345678 0 ZZ0', '123456789', 'Rua da Empresa, 1, Lisboa', 'Diretor Geral', 2800.00, 'ativo', 'admin', '2022-10-01 09:00:00'),
(2, 'Ricardo Pereira', 'ricardo@empresa.pt', '$2y$10$mlAW0uQPPZaox84mMHOmnePQXFVobVUDNAgqATegzl5SOOru7rBO.', '910 111 222', '23456789 1 ZZ1', '234567891', 'Av. da Liberdade, 45, Lisboa', 'Gestor de Conta Sénior', 1350.00, 'ativo', 'funcionario', '2022-10-02 09:00:00'),
(3, 'Ana Rita Santos', 'ana.santos@empresa.pt', '$2y$10$mlAW0uQPPZaox84mMHOmnePQXFVobVUDNAgqATegzl5SOOru7rBO.', '920 222 333', '34567890 2 ZZ2', '345678902', 'Rua das Flores, 12, Porto', 'Comercial de Frotas', 1200.00, 'ativo', 'funcionario', '2022-10-03 09:00:00'),
(4, 'Tiago Sousa', 'tiago.sousa@empresa.pt', '$2y$10$mlAW0uQPPZaox84mMHOmnePQXFVobVUDNAgqATegzl5SOOru7rBO.', '960 333 444', '45678901 3 ZZ3', '456789013', 'Travessa do Mercado, 7, Coimbra', 'Técnico de Reparações', 1050.00, 'ativo', 'funcionario', '2022-10-04 09:00:00'),
(5, 'Sofia Carvalho', 'sofia.carvalho@empresa.pt', '$2y$10$mlAW0uQPPZaox84mMHOmnePQXFVobVUDNAgqATegzl5SOOru7rBO.', '935 444 555', '56789012 4 ZZ4', '567890124', 'Rua do Almada, 3, Braga', 'Assistente Comercial', 980.00, 'inativo', 'funcionario', '2022-10-05 09:00:00');

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de tabela `documentos_funcionarios`
--
ALTER TABLE `documentos_funcionarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `documentos_leads`
--
ALTER TABLE `documentos_leads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `historico_contacto`
--
ALTER TABLE `historico_contacto`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT de tabela `leads`
--
ALTER TABLE `leads`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `recibos_vencimento`
--
ALTER TABLE `recibos_vencimento`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=165;

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
