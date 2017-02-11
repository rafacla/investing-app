-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 07-Jan-2017 às 11:42
-- Versão do servidor: 10.1.19-MariaDB
-- PHP Version: 5.6.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app-budget_r6`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `MostraBudgets` (IN `a_mesano` VARCHAR(6), IN `a_pfid` VARCHAR(23))  BEGIN

DECLARE MaxDate INT;
DECLARE cSaldoMes DECIMAL(10,2) DEFAULT 0;
DECLARE cDisponivel DECIMAL(10,2) DEFAULT 0;
DECLARE somaDespFora DECIMAL(10,2) DEFAULT 0;
DECLARE somaSaldo DECIMAL(10,2) DEFAULT 0;
DECLARE v_finished INTEGER DEFAULT 0;
DECLARE cMesano varchar(6) DEFAULT "";
DECLARE cCategoria_id INT DEFAULT 1;
DECLARE cCategoria varchar(100) DEFAULT "";
DECLARE cCategoria_grupo varchar(100) DEFAULT "";
DECLARE cCategoria_grupo_id INT DEFAULT 1;
DECLARE cBudgetMes DECIMAL(10,2) DEFAULT 0;
DECLARE cGastoMes DECIMAL(10,2) DEFAULT 0;

DEClARE saldoCursor CURSOR FOR SELECT ifnull(vw_mes_budget_gasto.mesano,a_mesano) as mesAno_,vw_categorias.id AS categoriaitem_id,vw_categorias.categoria AS categoriaitem,vw_categorias.categoria_grupo,vw_categorias.categoria_grupo_id, vw_mes_budget_gasto.budgetMes, vw_mes_budget_gasto.gastoMes FROM vw_categorias LEFT JOIN vw_mes_budget_gasto ON (vw_categorias.id = vw_mes_budget_gasto.categoriaitem_id) WHERE (vw_categorias.profile_uid = a_pfid) ORDER BY cast(mesAno_ as unsigned);

DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_finished = 1;

CREATE TEMPORARY TABLE IF NOT EXISTS tempBudget (
mesano varchar(6),
categoriaitem_id INT,
categoriaitem varchar(100),
categoria_grupo varchar(100),
categoria_grupo_id INT,
budgetMes DECIMAL(10,2),
gastoMes DECIMAL(10,2),
saldoMes DECIMAL(10,2),
Disponivel DECIMAL(10,2) NULL,
DespForaOrc DECIMAL (10,2) NULL);

TRUNCATE tempBudget;

OPEN saldoCursor;

get_saldo: LOOP

FETCH saldoCursor INTO cMesano, cCategoria_id, cCategoria, cCategoria_grupo, cCategoria_grupo_id, cBudgetMes, cGastoMes;

IF v_finished = 1 THEN
LEAVE get_saldo;
END IF;

SET MaxDate =0;
SET cSaldoMes =0;
SET cDisponivel =0;
SET somaDespFora =0;
SET somaSaldo =0;

select ifnull(max(cast(mesano AS UNSIGNED)),0) INTO MaxDate from tempBudget WHERE categoriaitem_id = cCategoria_id GROUP BY categoriaitem_id;

SET v_finished = 0;

SELECT Disponivel Into cDisponivel FROM tempBudget WHERE categoriaitem_id = cCategoria_id AND mesano = MaxDate;

SET v_finished = 0;

SET cSaldoMes = ifnull(cBudgetMes,0)+ifnull(cGastoMes,0);

IF (ifnull(cDisponivel,0)+cSaldoMes)>0 THEN
	SET cDisponivel = ifnull(cDisponivel,0)+cSaldoMes;
    SET somaDespFora = 0;
ELSE
	SET cDisponivel = 0;
    SET somaDespFora = (-1)*(ifnull(cDisponivel,0)+cSaldoMes);
END IF;

INSERT INTO tempBudget VALUES (cMesano, cCategoria_id, cCategoria, cCategoria_grupo, cCategoria_grupo_id, cBudgetMes, cGastoMes, cSaldoMes,cDisponivel,somaDespFora);

END LOOP get_saldo;


CREATE TEMPORARY TABLE IF NOT EXISTS tempMaxDate (
categoriaitem_id INT,
maxDate INT);

TRUNCATE tempMaxDate;

Insert Into tempMaxDate
select categoriaitem_id, max(mesano) AS maxDate from tempBudget WHERE mesano <= a_mesano group by categoriaitem_id;

CREATE TEMPORARY TABLE IF NOT EXISTS tempBudget_final (
mesano varchar(6),
categoriaitem_id INT,
categoriaitem varchar(100),
categoria_grupo varchar(100),
categoria_grupo_id INT,
budgetMes DECIMAL(10,2),
gastoMes DECIMAL(10,2),
saldoMes DECIMAL(10,2),
Disponivel DECIMAL(10,2) NULL,
DespForaOrc DECIMAL (10,2) NULL);

TRUNCATE tempBudget_final;

insert into tempBudget_final
SELECT a_mesano, t1.categoriaitem_id, t1.categoriaitem, t1.categoria_grupo, t1.categoria_grupo_id, t1.budgetMes, t1.gastoMes, t1.saldoMes, t1.Disponivel, t1.DespForaOrc FROM tempBudget t1 INNER JOIN tempMaxDate t2 ON t1.categoriaitem_id = t2.categoriaitem_id AND t1.mesano = t2.maxDate;

Select a_mesano AS mesano, t2.id AS categoriaitem_id, t2.categoria AS categoriaitem, t2.categoria_grupo, t2.categoria_grupo_id, t3.budgetMes, t3.gastoMes, t3.saldoMes, t1.Disponivel, t3.DespForaOrc from tempBudget_final t1 RIGHT JOIN vw_categorias t2 ON t1.categoriaitem_id = t2.id LEFT JOIN tempBudget t3 ON t1.categoriaitem_id = t3.categoriaitem_id and t1.mesano = t3.mesano WHERE t2.profile_uid = a_pfid ORDER BY t2.ordem_grupo, t2.ordem;

CLOSE saldoCursor;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `MostraBudgets_1` (IN `a_mesano` VARCHAR(6), IN `a_pfid` VARCHAR(23))  NO SQL
BEGIN

DECLARE MaxDate INT;
DECLARE cSaldoMes DECIMAL(10,2) DEFAULT 0;
DECLARE cDisponivel DECIMAL(10,2) DEFAULT 0;
DECLARE somaDespFora DECIMAL(10,2) DEFAULT 0;
DECLARE somaSaldo DECIMAL(10,2) DEFAULT 0;
DECLARE v_finished INTEGER DEFAULT 0;
DECLARE cMesano varchar(6) DEFAULT "";
DECLARE cCategoria_id INT DEFAULT 1;
DECLARE cCategoria varchar(100) DEFAULT "";
DECLARE cCategoria_grupo varchar(100) DEFAULT "";
DECLARE cCategoria_grupo_id INT DEFAULT 1;
DECLARE cBudgetMes DECIMAL(10,2) DEFAULT 0;
DECLARE cGastoMes DECIMAL(10,2) DEFAULT 0;

DEClARE saldoCursor CURSOR FOR SELECT ifnull(vw_mes_budget_gasto.mesano,a_mesano) as mesAno_,vw_categorias.id AS categoriaitem_id,vw_categorias.categoria AS categoriaitem,vw_categorias.categoria_grupo,vw_categorias.categoria_grupo_id, vw_mes_budget_gasto.budgetMes, vw_mes_budget_gasto.gastoMes FROM vw_categorias LEFT JOIN vw_mes_budget_gasto ON (vw_categorias.id = vw_mes_budget_gasto.categoriaitem_id) WHERE (vw_categorias.profile_uid = a_pfid) ORDER BY cast(mesAno_ as unsigned);

DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_finished = 1;

CREATE TEMPORARY TABLE IF NOT EXISTS tempBudget (
mesano varchar(6),
categoriaitem_id INT,
categoriaitem varchar(100),
categoria_grupo varchar(100),
categoria_grupo_id INT,
budgetMes DECIMAL(10,2),
gastoMes DECIMAL(10,2),
saldoMes DECIMAL(10,2),
Disponivel DECIMAL(10,2) NULL,
DespForaOrc DECIMAL (10,2) NULL);

TRUNCATE tempBudget;

OPEN saldoCursor;

get_saldo: LOOP

FETCH saldoCursor INTO cMesano, cCategoria_id, cCategoria, cCategoria_grupo, cCategoria_grupo_id, cBudgetMes, cGastoMes;

IF v_finished = 1 THEN
LEAVE get_saldo;
END IF;

SET MaxDate =0;
SET cSaldoMes =0;
SET cDisponivel =0;
SET somaDespFora =0;
SET somaSaldo =0;

select ifnull(max(cast(mesano AS UNSIGNED)),0) INTO MaxDate from tempBudget WHERE categoriaitem_id = cCategoria_id GROUP BY categoriaitem_id;

SET v_finished = 0;

SELECT Disponivel Into cDisponivel FROM tempBudget WHERE categoriaitem_id = cCategoria_id AND mesano = MaxDate;

SET v_finished = 0;

SET cSaldoMes = ifnull(cBudgetMes,0)+ifnull(cGastoMes,0);

IF (ifnull(cDisponivel,0)+cSaldoMes)>0 THEN
	SET cDisponivel = ifnull(cDisponivel,0)+cSaldoMes;
    SET somaDespFora = 0;
ELSE
	SET cDisponivel = 0;
    SET somaDespFora = (-1)*(ifnull(cDisponivel,0)+cSaldoMes);
END IF;

INSERT INTO tempBudget VALUES (cMesano, cCategoria_id, cCategoria, cCategoria_grupo, cCategoria_grupo_id, cBudgetMes, cGastoMes, cSaldoMes,cDisponivel,somaDespFora);

END LOOP get_saldo;


CREATE TEMPORARY TABLE IF NOT EXISTS tempMaxDate (
categoriaitem_id INT,
maxDate INT);

TRUNCATE tempMaxDate;

Insert Into tempMaxDate
select categoriaitem_id, max(mesano) AS maxDate from tempBudget WHERE mesano <= a_mesano group by categoriaitem_id;

CREATE TEMPORARY TABLE IF NOT EXISTS tempBudget_final (
mesano varchar(6),
categoriaitem_id INT,
categoriaitem varchar(100),
categoria_grupo varchar(100),
categoria_grupo_id INT,
budgetMes DECIMAL(10,2),
gastoMes DECIMAL(10,2),
saldoMes DECIMAL(10,2),
Disponivel DECIMAL(10,2) NULL,
DespForaOrc DECIMAL (10,2) NULL);

TRUNCATE tempBudget_final;

insert into tempBudget_final
SELECT a_mesano, t1.categoriaitem_id, t1.categoriaitem, t1.categoria_grupo, t1.categoria_grupo_id, t1.budgetMes, t1.gastoMes, t1.saldoMes, t1.Disponivel, t1.DespForaOrc FROM tempBudget t1 INNER JOIN tempMaxDate t2 ON t1.categoriaitem_id = t2.categoriaitem_id AND t1.mesano = t2.maxDate;

Select a_mesano AS mesano, t2.id AS categoriaitem_id, t2.categoria AS categoriaitem, t2.categoria_grupo, t2.categoria_grupo_id, t3.budgetMes, t3.gastoMes, t3.saldoMes, t1.Disponivel, t3.DespForaOrc from tempBudget_final t1 RIGHT JOIN vw_categorias t2 ON t1.categoriaitem_id = t2.id LEFT JOIN tempBudget t3 ON t1.categoriaitem_id = t3.categoriaitem_id and t1.mesano = t3.mesano WHERE t2.profile_uid = a_pfid ORDER BY t2.ordem_grupo, t2.ordem;

CLOSE saldoCursor;

END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ReiniciaBudgets_BD` ()  NO SQL
BEGIN

set foreign_key_checks=0;
truncate table transacoesitens;
truncate table budgets;
truncate table categoriasitens;
truncate table transacoes;
truncate table categorias;
truncate table categorias_default;
truncate table contas;
truncate table groups;
truncate table login_attempts;
truncate table profiles;
truncate table users;
truncate table users_groups;
set foreign_key_checks=1;

INSERT INTO `groups` (`id`, `name`, `description`, `created_from_ip`, `updated_from_ip`) VALUES
(1, 'admin', 'Administrator', '', ''),
(2, 'members', 'General User', '', '');

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
(1, '127.0.0.1', 'rafaacla@gmail.com', '$2y$08$6sIc6JLpcqJaiwczcDf1fOgGDmCxxjmVzAVuq0e1smYl38ITRWxL.', '', 'rafaacla@gmail.com', '', NULL, NULL, 'zX10kAoHoG5EdSwBisu1Mu', 1268889823, 1480261035, 1, 'Rafael', 'Claudio', 'ADMIN', '0');

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`, `created_from_ip`, `updated_from_ip`) VALUES
(1, 1, 1, '', '');

INSERT INTO `categorias_default` (`id`, `categoria`, `grupo`, `ordem`, `ordem_grupo`) VALUES
(1, 'Água', 'Contas Fixas', 0, 0),
(2, 'Aluguel', 'Contas Fixas', 1, 0),
(3, 'Luz', 'Contas Fixas', 2, 0),
(4, 'Internet', 'Contas Fixas', 3, 0),
(5, 'Telefone', 'Contas Fixas', 4, 0),
(7, 'Mensalidades', 'Contas Fixas', 6, 0),
(8, 'Mercado', 'Despesas do dia-a-dia', 0, 1),
(9, 'Restaurantes', 'Despesas do dia-a-dia', 1, 1),
(10, 'Passeios', 'Despesas do dia-a-dia', 2, 1),
(11, 'Transporte', 'Despesas do dia-a-dia', 3, 1),
(12, 'Vestuário', 'Despesas do dia-a-dia', 4, 1),
(13, 'Saúde', 'Despesas do dia-a-dia', 5, 1),
(14, 'Besteiras', 'Despesas do dia-a-dia', 6, 1),
(15, 'Imóvel', 'Poupança', 0, 2),
(16, 'Férias', 'Poupança', 1, 2),
(17, 'Saúde', 'Poupança', 2, 2),
(18, 'Emergências', 'Poupança', 3, 2);

INSERT INTO `categorias` (`id`, `nome`, `profile_id`, `ordem`, `created`, `modified`) VALUES
(1, 'Recursos', NULL, -1, NULL, NULL);

INSERT INTO `categoriasitens` (`id`, `catmaster_id`, `nome`, `descricao`, `ordem`, `created`, `modified`) VALUES
(1, 1, 'Fundos para este mês', 'Esta opção é usada para contabilizar o fundo para o orçamento deste mês.', 0, NULL, NULL),
(2, 1, 'Fundos para o próximo mês', 'Esta opção é usada para contabilizar o fundo para o orçamento do próximo mês.', 1, NULL, NULL);

END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estrutura da tabela `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `categoriaitem_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(25) NOT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `ordem` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `categoriasitens`
--

CREATE TABLE `categoriasitens` (
  `id` int(11) NOT NULL,
  `catmaster_id` int(11) NOT NULL,
  `nome` varchar(25) NOT NULL,
  `descricao` mediumtext NOT NULL,
  `ordem` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `categorias_default`
--

CREATE TABLE `categorias_default` (
  `id` int(11) NOT NULL,
  `categoria` varchar(50) NOT NULL,
  `grupo` varchar(50) NOT NULL,
  `ordem` int(11) NOT NULL,
  `ordem_grupo` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `categorias_default`
--

INSERT INTO `categorias_default` (`id`, `categoria`, `grupo`, `ordem`, `ordem_grupo`) VALUES
(1, 'Água', 'Contas Fixas', 0, 0),
(2, 'Aluguel', 'Contas Fixas', 1, 0),
(3, 'Luz', 'Contas Fixas', 2, 0),
(4, 'Internet', 'Contas Fixas', 3, 0),
(5, 'Telefone', 'Contas Fixas', 4, 0),
(7, 'Mensalidades', 'Contas Fixas', 6, 0),
(8, 'Mercado', 'Despesas do dia-a-dia', 0, 1),
(9, 'Restaurantes', 'Despesas do dia-a-dia', 1, 1),
(10, 'Passeios', 'Despesas do dia-a-dia', 2, 1),
(11, 'Transporte', 'Despesas do dia-a-dia', 3, 1),
(12, 'Vestuário', 'Despesas do dia-a-dia', 4, 1),
(13, 'Saúde', 'Despesas do dia-a-dia', 5, 1),
(14, 'Besteiras', 'Despesas do dia-a-dia', 6, 1),
(15, 'Imóvel', 'Poupança', 0, 2),
(16, 'Férias', 'Poupança', 1, 2),
(17, 'Saúde', 'Poupança', 2, 2),
(18, 'Emergências', 'Poupança', 3, 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `contas`
--

CREATE TABLE `contas` (
  `id` int(11) NOT NULL,
  `conta_nome` varchar(25) NOT NULL,
  `conta_descricao` mediumtext,
  `profile_id` int(11) NOT NULL,
  `reconciliado_valor` decimal(10,2) NOT NULL,
  `reconciliado_data` date NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `budget` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  `created_from_ip` varchar(15) DEFAULT NULL,
  `updated_from_ip` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `ip_address` varchar(16) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `profiles`
--

CREATE TABLE `profiles` (
  `id` int(11) NOT NULL,
  `uniqueid` varchar(23) NOT NULL,
  `user_id` mediumint(8) UNSIGNED NOT NULL,
  `nome` varchar(25) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `created_from_ip` varchar(15) DEFAULT NULL,
  `updated_from_ip` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `transacoes`
--

CREATE TABLE `transacoes` (
  `id` int(11) NOT NULL,
  `conta_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `sacado_nome` varchar(50) NOT NULL,
  `memo` mediumtext,
  `valor` decimal(10,2) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `tranNum` varchar(50) DEFAULT NULL,
  `conciliado` tinyint(1) DEFAULT NULL,
  `aprovado` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `transacoesitens`
--

CREATE TABLE `transacoesitens` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `transacao_id` int(11) NOT NULL,
  `transf_para_conta_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `users`
--

CREATE TABLE `users` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `ip_address` varchar(16) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(80) NOT NULL,
  `salt` varchar(40) NOT NULL,
  `email` varchar(100) NOT NULL,
  `activation_code` varchar(40) DEFAULT NULL,
  `forgotten_password_code` varchar(40) DEFAULT NULL,
  `forgotten_password_time` int(11) UNSIGNED DEFAULT NULL,
  `remember_code` varchar(40) DEFAULT NULL,
  `created_on` int(11) UNSIGNED NOT NULL,
  `last_login` int(11) UNSIGNED DEFAULT NULL,
  `active` tinyint(1) UNSIGNED DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `users_groups`
--

CREATE TABLE `users_groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `user_id` mediumint(8) UNSIGNED NOT NULL,
  `group_id` mediumint(8) UNSIGNED NOT NULL,
  `created_from_ip` varchar(15) DEFAULT NULL,
  `updated_from_ip` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_accounts`
--
CREATE TABLE `vw_accounts` (
`conta_nome` varchar(25)
,`data` varchar(10)
,`sacado_nome` varchar(50)
,`categoria` varchar(52)
,`memo` mediumtext
,`valor` decimal(33,2)
,`valor_item` decimal(11,2)
,`profile_id` int(11)
,`profile_uid` varchar(23)
,`transacao_id` int(11)
,`conta_id` int(11)
,`editavel` int(1)
,`tritem_id` int(11)
,`catitem_id` int(11)
,`count_filhas` bigint(21)
,`conta_para_id` int(11)
,`conta_para_nome` varchar(25)
,`data_un` date
,`conciliado` tinyint(1)
,`aprovado` tinyint(4)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_accounts_distinct`
--
CREATE TABLE `vw_accounts_distinct` (
`conta_id` int(11)
,`conta_nome` varchar(25)
,`profile_id` int(11)
,`profile_uid` varchar(23)
,`transacao_id` int(11)
,`saldo` decimal(33,2)
,`saldo_aprovado` decimal(33,2)
,`saldo_conciliado` decimal(33,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_categorias`
--
CREATE TABLE `vw_categorias` (
`id` int(11)
,`categoria` varchar(25)
,`categoria_grupo` varchar(25)
,`categoria_grupo_id` int(11)
,`ordem` int(11)
,`ordem_grupo` int(11)
,`profile_id` int(11)
,`profile_uid` varchar(23)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_contas`
--
CREATE TABLE `vw_contas` (
`id` int(11)
,`conta_nome` varchar(25)
,`conta_descricao` mediumtext
,`reconciliado_valor` decimal(10,2)
,`reconciliado_data` date
,`created` datetime
,`modified` datetime
,`budget` tinyint(1)
,`profile_id` int(11)
,`profile_uid` varchar(23)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_contas_saldo`
--
CREATE TABLE `vw_contas_saldo` (
`conta_id` int(11)
,`conta_nome` varchar(25)
,`saldo` decimal(55,2)
,`saldo_aprovado` decimal(55,2)
,`saldo_conciliado` decimal(55,2)
,`profile_id` int(11)
,`profile_uid` varchar(23)
,`countNClas` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_mes_budget`
--
CREATE TABLE `vw_mes_budget` (
`profile_id` int(11)
,`profile_uid` varchar(23)
,`mesano` varchar(6)
,`categoriaitem_id` int(11)
,`budgetMes` decimal(32,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_mes_budget_gasto`
--
CREATE TABLE `vw_mes_budget_gasto` (
`profile_id` int(11)
,`profile_uid` varchar(23)
,`mesano` varchar(6)
,`categoriaitem_id` int(11)
,`budgetMes` decimal(32,2)
,`gastoMes` decimal(33,2)
,`SaldoMes` decimal(34,2)
,`DespForaOrc` decimal(34,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_mes_gasto`
--
CREATE TABLE `vw_mes_gasto` (
`profile_id` int(11)
,`profile_uid` varchar(23)
,`mesano` varchar(6)
,`categoriaitem_id` int(11)
,`gastoMes` decimal(33,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_receitas`
--
CREATE TABLE `vw_receitas` (
`mesano` varchar(6)
,`profile_id` int(11)
,`profile_uid` varchar(23)
,`ReceitaMes` decimal(33,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_sumaria_budget_gasto`
--
CREATE TABLE `vw_sumaria_budget_gasto` (
`profile_uid` varchar(23)
,`mesano` varchar(6)
,`budgetMes` decimal(54,2)
,`gastoMes` decimal(55,2)
,`DespForaOrc` decimal(56,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_sumaria_receita_budget_gasto`
--
CREATE TABLE `vw_sumaria_receita_budget_gasto` (
`profile_uid` varchar(23)
,`mesano` varchar(6)
,`ReceitaMes` decimal(33,2)
,`budgets` decimal(54,2)
,`gastoMes` decimal(55,2)
,`despForaOrc` decimal(56,2)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_transacoes`
--
CREATE TABLE `vw_transacoes` (
`id` int(11)
,`conta_id` bigint(11)
,`data` date
,`sacado_nome` varchar(50)
,`memo` mediumtext
,`valor` decimal(33,2)
,`Editavel` int(1)
,`conciliado` tinyint(1)
,`aprovado` tinyint(4)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_transacoesitens`
--
CREATE TABLE `vw_transacoesitens` (
`id` int(11)
,`conta_id` int(11)
,`conta_para_id` int(11)
,`data` date
,`sacado_nome` varchar(50)
,`memo` longtext
,`categoria_id` int(11)
,`transacao_id` int(11)
,`valor` decimal(11,2)
,`profile_id` int(11)
,`profile_uid` varchar(23)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_transacoes_classificadas`
--
CREATE TABLE `vw_transacoes_classificadas` (
`id` int(11)
,`conta_id` bigint(11)
,`countNClas` int(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_transacoes_count`
--
CREATE TABLE `vw_transacoes_count` (
`transacao_id` int(11)
,`count_itens` bigint(21)
);

-- --------------------------------------------------------

--
-- Structure for view `vw_accounts`
--
DROP TABLE IF EXISTS `vw_accounts`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_accounts`  AS  select `contas`.`conta_nome` AS `conta_nome`,date_format(`vw_transacoes`.`data`,'%d/%m/%Y') AS `data`,`vw_transacoes`.`sacado_nome` AS `sacado_nome`,concat_ws(': ',`categorias`.`nome`,`categoriasitens`.`nome`) AS `categoria`,`vw_transacoes`.`memo` AS `memo`,`vw_transacoes`.`valor` AS `valor`,`t1`.`valor` AS `valor_item`,`contas`.`profile_id` AS `profile_id`,`contas`.`profile_uid` AS `profile_uid`,`vw_transacoes`.`id` AS `transacao_id`,`contas`.`id` AS `conta_id`,`vw_transacoes`.`Editavel` AS `editavel`,`t1`.`id` AS `tritem_id`,`t1`.`categoria_id` AS `catitem_id`,`vw_transacoes_count`.`count_itens` AS `count_filhas`,`t1`.`conta_para_id` AS `conta_para_id`,`contas2`.`conta_nome` AS `conta_para_nome`,`vw_transacoes`.`data` AS `data_un`,`vw_transacoes`.`conciliado` AS `conciliado`,`vw_transacoes`.`aprovado` AS `aprovado` from ((((((`vw_transacoes` left join `vw_contas` `contas` on((`vw_transacoes`.`conta_id` = `contas`.`id`))) left join `vw_transacoesitens` `t1` on(((`vw_transacoes`.`id` = `t1`.`transacao_id`) and (`vw_transacoes`.`conta_id` = `t1`.`conta_id`)))) left join `categoriasitens` on((`t1`.`categoria_id` = `categoriasitens`.`id`))) left join `categorias` on((`categoriasitens`.`catmaster_id` = `categorias`.`id`))) left join `vw_transacoes_count` on((`vw_transacoes`.`id` = `vw_transacoes_count`.`transacao_id`))) left join `vw_contas` `contas2` on((`t1`.`conta_para_id` = `contas2`.`id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_accounts_distinct`
--
DROP TABLE IF EXISTS `vw_accounts_distinct`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_accounts_distinct`  AS  select distinct `contas`.`id` AS `conta_id`,`contas`.`conta_nome` AS `conta_nome`,`contas`.`profile_id` AS `profile_id`,`contas`.`profile_uid` AS `profile_uid`,`vw_accounts`.`transacao_id` AS `transacao_id`,`vw_accounts`.`valor` AS `saldo`,if((`vw_accounts`.`aprovado` = 1),`vw_accounts`.`valor`,0) AS `saldo_aprovado`,if((`vw_accounts`.`conciliado` = 1),`vw_accounts`.`valor`,0) AS `saldo_conciliado` from (`vw_contas` `contas` left join `vw_accounts` on((`contas`.`id` = `vw_accounts`.`conta_id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_categorias`
--
DROP TABLE IF EXISTS `vw_categorias`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_categorias`  AS  select `categoriasitens`.`id` AS `id`,`categoriasitens`.`nome` AS `categoria`,`categorias`.`nome` AS `categoria_grupo`,`categorias`.`id` AS `categoria_grupo_id`,`categoriasitens`.`ordem` AS `ordem`,`categorias`.`ordem` AS `ordem_grupo`,`categorias`.`profile_id` AS `profile_id`,`profiles`.`uniqueid` AS `profile_uid` from ((`categorias` left join `categoriasitens` on((`categoriasitens`.`catmaster_id` = `categorias`.`id`))) left join `profiles` on((`categorias`.`profile_id` = `profiles`.`id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_contas`
--
DROP TABLE IF EXISTS `vw_contas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_contas`  AS  select `contas`.`id` AS `id`,`contas`.`conta_nome` AS `conta_nome`,`contas`.`conta_descricao` AS `conta_descricao`,`contas`.`reconciliado_valor` AS `reconciliado_valor`,`contas`.`reconciliado_data` AS `reconciliado_data`,`contas`.`created` AS `created`,`contas`.`modified` AS `modified`,`contas`.`budget` AS `budget`,`contas`.`profile_id` AS `profile_id`,`profiles`.`uniqueid` AS `profile_uid` from (`contas` join `profiles` on((`contas`.`profile_id` = `profiles`.`id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_contas_saldo`
--
DROP TABLE IF EXISTS `vw_contas_saldo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_contas_saldo`  AS  select `vw_accounts_distinct`.`conta_id` AS `conta_id`,`vw_accounts_distinct`.`conta_nome` AS `conta_nome`,sum(`vw_accounts_distinct`.`saldo`) AS `saldo`,sum(`vw_accounts_distinct`.`saldo_aprovado`) AS `saldo_aprovado`,sum(`vw_accounts_distinct`.`saldo_conciliado`) AS `saldo_conciliado`,`vw_accounts_distinct`.`profile_id` AS `profile_id`,`vw_accounts_distinct`.`profile_uid` AS `profile_uid`,sum(`vw_transacoes_classificadas`.`countNClas`) AS `countNClas` from (`vw_accounts_distinct` left join `vw_transacoes_classificadas` on(((`vw_accounts_distinct`.`transacao_id` = `vw_transacoes_classificadas`.`id`) and (`vw_accounts_distinct`.`conta_id` = `vw_transacoes_classificadas`.`conta_id`)))) group by `vw_accounts_distinct`.`conta_id` ;

-- --------------------------------------------------------

--
-- Structure for view `vw_mes_budget`
--
DROP TABLE IF EXISTS `vw_mes_budget`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_mes_budget`  AS  select `budgets`.`profile_id` AS `profile_id`,`profiles`.`uniqueid` AS `profile_uid`,date_format(`budgets`.`date`,'%Y%m') AS `mesano`,`budgets`.`categoriaitem_id` AS `categoriaitem_id`,sum(`budgets`.`valor`) AS `budgetMes` from (`budgets` left join `profiles` on((`budgets`.`profile_id` = `profiles`.`id`))) group by `budgets`.`profile_id`,date_format(`budgets`.`date`,'%Y%m'),`budgets`.`categoriaitem_id` ;

-- --------------------------------------------------------

--
-- Structure for view `vw_mes_budget_gasto`
--
DROP TABLE IF EXISTS `vw_mes_budget_gasto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_mes_budget_gasto`  AS  select ifnull(`vw_mes_gasto`.`profile_id`,`vw_mes_budget`.`profile_id`) AS `profile_id`,ifnull(convert(`vw_mes_gasto`.`profile_uid` using utf8),`vw_mes_budget`.`profile_uid`) AS `profile_uid`,ifnull(convert(`vw_mes_budget`.`mesano` using latin1),convert(`vw_mes_gasto`.`mesano` using latin1)) AS `mesano`,ifnull(`vw_mes_budget`.`categoriaitem_id`,`vw_mes_gasto`.`categoriaitem_id`) AS `categoriaitem_id`,ifnull(`vw_mes_budget`.`budgetMes`,0) AS `budgetMes`,ifnull(`vw_mes_gasto`.`gastoMes`,0) AS `gastoMes`,if(((ifnull(`vw_mes_budget`.`budgetMes`,0) + ifnull(`vw_mes_gasto`.`gastoMes`,0)) > 0),(ifnull(`vw_mes_budget`.`budgetMes`,0) + ifnull(`vw_mes_gasto`.`gastoMes`,0)),0) AS `SaldoMes`,if(((ifnull(`vw_mes_budget`.`budgetMes`,0) + ifnull(`vw_mes_gasto`.`gastoMes`,0)) < 0),(ifnull(`vw_mes_budget`.`budgetMes`,0) + ifnull(`vw_mes_gasto`.`gastoMes`,0)),0) AS `DespForaOrc` from (`vw_mes_gasto` left join `vw_mes_budget` on(((`vw_mes_budget`.`categoriaitem_id` = `vw_mes_gasto`.`categoriaitem_id`) and (convert(`vw_mes_budget`.`mesano` using latin1) = convert(`vw_mes_gasto`.`mesano` using latin1))))) where (ifnull(`vw_mes_gasto`.`profile_id`,`vw_mes_budget`.`profile_id`) is not null) union select ifnull(`vw_mes_budget`.`profile_id`,`vw_mes_gasto`.`profile_id`) AS `profile_id`,ifnull(`vw_mes_budget`.`profile_uid`,convert(`vw_mes_gasto`.`profile_uid` using utf8)) AS `profile_uid`,ifnull(convert(`vw_mes_budget`.`mesano` using latin1),convert(`vw_mes_gasto`.`mesano` using latin1)) AS `mesano`,ifnull(`vw_mes_budget`.`categoriaitem_id`,`vw_mes_gasto`.`categoriaitem_id`) AS `categoriaitem_id`,ifnull(`vw_mes_budget`.`budgetMes`,0) AS `budgetMes`,ifnull(`vw_mes_gasto`.`gastoMes`,0) AS `gastoMes`,if(((ifnull(`vw_mes_budget`.`budgetMes`,0) + ifnull(`vw_mes_gasto`.`gastoMes`,0)) > 0),(ifnull(`vw_mes_budget`.`budgetMes`,0) + ifnull(`vw_mes_gasto`.`gastoMes`,0)),0) AS `SaldoMes`,if(((ifnull(`vw_mes_budget`.`budgetMes`,0) + ifnull(`vw_mes_gasto`.`gastoMes`,0)) < 0),(ifnull(`vw_mes_budget`.`budgetMes`,0) + ifnull(`vw_mes_gasto`.`gastoMes`,0)),0) AS `DespForaOrc` from (`vw_mes_budget` left join `vw_mes_gasto` on(((`vw_mes_budget`.`categoriaitem_id` = `vw_mes_gasto`.`categoriaitem_id`) and (convert(`vw_mes_budget`.`mesano` using latin1) = convert(`vw_mes_gasto`.`mesano` using latin1))))) where (ifnull(`vw_mes_budget`.`profile_id`,`vw_mes_gasto`.`profile_id`) is not null) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_mes_gasto`
--
DROP TABLE IF EXISTS `vw_mes_gasto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_mes_gasto`  AS  select `vw_categorias`.`profile_id` AS `profile_id`,`vw_categorias`.`profile_uid` AS `profile_uid`,date_format(`vw_transacoesitens`.`data`,'%Y%m') AS `mesano`,`vw_transacoesitens`.`categoria_id` AS `categoriaitem_id`,sum(`vw_transacoesitens`.`valor`) AS `gastoMes` from (`vw_transacoesitens` left join `vw_categorias` on((`vw_transacoesitens`.`categoria_id` = `vw_categorias`.`id`))) where isnull(`vw_transacoesitens`.`conta_para_id`) group by date_format(`vw_transacoesitens`.`data`,'%Y%m'),`vw_transacoesitens`.`categoria_id` ;

-- --------------------------------------------------------

--
-- Structure for view `vw_receitas`
--
DROP TABLE IF EXISTS `vw_receitas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_receitas`  AS  select if((`vw_transacoesitens`.`categoria_id` = 2),date_format((`vw_transacoesitens`.`data` + interval 1 month),'%Y%m'),date_format(`vw_transacoesitens`.`data`,'%Y%m')) AS `mesano`,`vw_transacoesitens`.`profile_id` AS `profile_id`,`vw_transacoesitens`.`profile_uid` AS `profile_uid`,sum(`vw_transacoesitens`.`valor`) AS `ReceitaMes` from `vw_transacoesitens` where ((`vw_transacoesitens`.`categoria_id` = 1) or (`vw_transacoesitens`.`categoria_id` = 2)) group by `vw_transacoesitens`.`profile_id`,`vw_transacoesitens`.`profile_uid`,if((`vw_transacoesitens`.`categoria_id` = 2),date_format((`vw_transacoesitens`.`data` + interval 1 month),'%Y%m'),date_format(`vw_transacoesitens`.`data`,'%Y%m')) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_sumaria_budget_gasto`
--
DROP TABLE IF EXISTS `vw_sumaria_budget_gasto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_sumaria_budget_gasto`  AS  select `vw_mes_budget_gasto`.`profile_uid` AS `profile_uid`,`vw_mes_budget_gasto`.`mesano` AS `mesano`,sum(`vw_mes_budget_gasto`.`budgetMes`) AS `budgetMes`,sum(`vw_mes_budget_gasto`.`gastoMes`) AS `gastoMes`,sum(`vw_mes_budget_gasto`.`DespForaOrc`) AS `DespForaOrc` from `vw_mes_budget_gasto` group by `vw_mes_budget_gasto`.`profile_uid`,`vw_mes_budget_gasto`.`mesano` ;

-- --------------------------------------------------------

--
-- Structure for view `vw_sumaria_receita_budget_gasto`
--
DROP TABLE IF EXISTS `vw_sumaria_receita_budget_gasto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_sumaria_receita_budget_gasto`  AS  select `t1`.`profile_uid` AS `profile_uid`,`t1`.`mesano` AS `mesano`,ifnull(`t1`.`ReceitaMes`,0) AS `ReceitaMes`,ifnull(`t2`.`budgetMes`,0) AS `budgets`,ifnull(`t2`.`gastoMes`,0) AS `gastoMes`,ifnull(`t2`.`DespForaOrc`,0) AS `despForaOrc` from (`vw_receitas` `t1` left join `vw_sumaria_budget_gasto` `t2` on(((convert(`t1`.`profile_uid` using utf8) = `t2`.`profile_uid`) and (convert(`t1`.`mesano` using latin1) = `t2`.`mesano`)))) group by `t1`.`profile_uid`,`t1`.`mesano` union select `t2`.`profile_uid` AS `profile_uid`,`t2`.`mesano` AS `mesano`,ifnull(`t1`.`ReceitaMes`,0) AS `ReceitaMes`,ifnull(`t2`.`budgetMes`,0) AS `budgets`,ifnull(`t2`.`gastoMes`,0) AS `gastoMes`,ifnull(`t2`.`DespForaOrc`,0) AS `despForaOrc` from (`vw_sumaria_budget_gasto` `t2` left join `vw_receitas` `t1` on(((convert(`t1`.`profile_uid` using utf8) = `t2`.`profile_uid`) and (convert(`t1`.`mesano` using latin1) = `t2`.`mesano`)))) group by `t2`.`profile_uid`,`t2`.`mesano` ;

-- --------------------------------------------------------

--
-- Structure for view `vw_transacoes`
--
DROP TABLE IF EXISTS `vw_transacoes`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_transacoes`  AS  select `transacoes`.`id` AS `id`,ifnull(`vw_transacoesitens`.`conta_id`,`transacoes`.`conta_id`) AS `conta_id`,`transacoes`.`data` AS `data`,`transacoes`.`sacado_nome` AS `sacado_nome`,`transacoes`.`memo` AS `memo`,sum(ifnull(`vw_transacoesitens`.`valor`,`transacoes`.`valor`)) AS `valor`,if((ifnull(`vw_transacoesitens`.`conta_id`,`transacoes`.`conta_id`) = `transacoes`.`conta_id`),1,0) AS `Editavel`,`transacoes`.`conciliado` AS `conciliado`,`transacoes`.`aprovado` AS `aprovado` from (`transacoes` left join `vw_transacoesitens` on((`transacoes`.`id` = `vw_transacoesitens`.`transacao_id`))) group by `transacoes`.`id`,ifnull(`vw_transacoesitens`.`conta_id`,`transacoes`.`conta_id`) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_transacoesitens`
--
DROP TABLE IF EXISTS `vw_transacoesitens`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_transacoesitens`  AS  select `transacoesitens`.`id` AS `id`,`transacoes`.`conta_id` AS `conta_id`,`transacoesitens`.`transf_para_conta_id` AS `conta_para_id`,`transacoes`.`data` AS `data`,`transacoes`.`sacado_nome` AS `sacado_nome`,`transacoes`.`memo` AS `memo`,`transacoesitens`.`categoria_id` AS `categoria_id`,`transacoesitens`.`transacao_id` AS `transacao_id`,`transacoesitens`.`valor` AS `valor`,`contas`.`profile_id` AS `profile_id`,`contas`.`profile_uid` AS `profile_uid` from ((`transacoesitens` left join `transacoes` on((`transacoesitens`.`transacao_id` = `transacoes`.`id`))) left join `vw_contas` `contas` on((`transacoes`.`conta_id` = `contas`.`id`))) union select `transacoesitens`.`id` AS `id`,`transacoesitens`.`transf_para_conta_id` AS `conta_id`,`transacoes`.`conta_id` AS `conta_para_id`,`transacoes`.`data` AS `data`,`transacoes`.`sacado_nome` AS `sacado_nome`,`transacoes`.`memo` AS `memo`,`transacoesitens`.`categoria_id` AS `categoria_id`,`transacoesitens`.`transacao_id` AS `transacao_id`,(-(1) * `transacoesitens`.`valor`) AS `(-1)*transacoesitens.valor`,`contas`.`profile_id` AS `profile_id`,`contas`.`profile_uid` AS `profile_uid` from ((`transacoesitens` left join `transacoes` on((`transacoesitens`.`transacao_id` = `transacoes`.`id`))) left join `vw_contas` `contas` on((`transacoes`.`conta_id` = `contas`.`id`))) where (`transacoesitens`.`transf_para_conta_id` is not null) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_transacoes_classificadas`
--
DROP TABLE IF EXISTS `vw_transacoes_classificadas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_transacoes_classificadas`  AS  select `vw_transacoes`.`id` AS `id`,`vw_transacoes`.`conta_id` AS `conta_id`,if(((count(0) - sum(if(((`vw_transacoesitens`.`categoria_id` is not null) or (`vw_transacoesitens`.`conta_para_id` is not null)),1,0))) = 0),0,1) AS `countNClas` from (`vw_transacoes` left join `vw_transacoesitens` on((`vw_transacoes`.`id` = `vw_transacoesitens`.`transacao_id`))) group by `vw_transacoes`.`conta_id`,`vw_transacoes`.`id` ;

-- --------------------------------------------------------

--
-- Structure for view `vw_transacoes_count`
--
DROP TABLE IF EXISTS `vw_transacoes_count`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_transacoes_count`  AS  select `vw_transacoes`.`id` AS `transacao_id`,count(0) AS `count_itens` from (`vw_transacoes` left join `vw_transacoesitens` on((`vw_transacoes`.`id` = `vw_transacoesitens`.`transacao_id`))) group by `vw_transacoes`.`id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`),
  ADD KEY `categoriaitem_id` (`categoriaitem_id`);

--
-- Indexes for table `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indexes for table `categoriasitens`
--
ALTER TABLE `categoriasitens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `catmaster_id` (`catmaster_id`);

--
-- Indexes for table `contas`
--
ALTER TABLE `contas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indexes for table `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `profiles`
--
ALTER TABLE `profiles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniqueid` (`uniqueid`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `transacoes`
--
ALTER TABLE `transacoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tranNum` (`tranNum`),
  ADD KEY `conta_id` (`conta_id`);

--
-- Indexes for table `transacoesitens`
--
ALTER TABLE `transacoesitens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `transacao_id` (`transacao_id`),
  ADD KEY `transf_para_conta_id` (`transf_para_conta_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users_groups`
--
ALTER TABLE `users_groups`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `group_id` (`group_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `categoriasitens`
--
ALTER TABLE `categoriasitens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `contas`
--
ALTER TABLE `contas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `groups`
--
ALTER TABLE `groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT for table `profiles`
--
ALTER TABLE `profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `transacoes`
--
ALTER TABLE `transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- AUTO_INCREMENT for table `transacoesitens`
--
ALTER TABLE `transacoesitens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;
--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `users_groups`
--
ALTER TABLE `users_groups`
  MODIFY `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `budgets_profile_ct` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `categorias`
--
ALTER TABLE `categorias`
  ADD CONSTRAINT `categorias_profile_ct` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `categoriasitens`
--
ALTER TABLE `categoriasitens`
  ADD CONSTRAINT `itens_categoria_ct` FOREIGN KEY (`catmaster_id`) REFERENCES `categorias` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `contas`
--
ALTER TABLE `contas`
  ADD CONSTRAINT `contas_profile_ct` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_user_ct` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `transacoes`
--
ALTER TABLE `transacoes`
  ADD CONSTRAINT `transacoes_conta_ct` FOREIGN KEY (`conta_id`) REFERENCES `contas` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `transacoesitens`
--
ALTER TABLE `transacoesitens`
  ADD CONSTRAINT `tritens_cateitem` FOREIGN KEY (`categoria_id`) REFERENCES `categoriasitens` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tritens_contas_ct` FOREIGN KEY (`transf_para_conta_id`) REFERENCES `contas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tritens_transacao_ct` FOREIGN KEY (`transacao_id`) REFERENCES `transacoes` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
