-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: 16-Mar-2017 às 01:37
-- Versão do servidor: 10.1.19-MariaDB
-- PHP Version: 5.6.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `app-inv_r0`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetReceitaAjustada` (IN `mes` INT(2), IN `ano` INT(4), IN `aprofile_uid` VARCHAR(30))  NO SQL
BEGIN

DECLARE cOrcado DECIMAL(10,2) DEFAULT 0;
DECLARE cSobregasto DECIMAL(10,2) DEFAULT 0;
DECLARE cReceitaDoMes DECIMAL(10,2) DEFAULT 0;
DECLARE cReceitaMes DECIMAL(10,2) DEFAULT 0;
DECLARE cReceitaMesAnt DECIMAL(10,2) DEFAULT 0;
DECLARE cMesAnt DATE;
DECLARE cMesAtual DATE;

SELECT LAST_DAY(DATE_FORMAT(CONCAT(ano,'-',mes,'-','01'),'%Y-%m-01')) INTO cMesAtual;

SELECT DATE_SUB(DATE_FORMAT(CONCAT(ano,'-',mes,'-','01'),'%Y-%m-01'), INTERVAL 1 day) INTO cMesAnt;

SELECT SUM(budgetMes),sum(DespForaOrc) INTO cOrcado, cSobregasto FROM `vw_mes_budget_gasto` WHERE mesano <= date_format(cmesant,'%Y%m') AND profile_uid = aprofile_uid;

SELECT sum(ReceitaMes) INTO cReceitaMes FROM `vw_receitas` WHERE mesano <= date_format(cMesAtual,'%Y%m') AND profile_uid = aprofile_uid;

SELECT sum(ReceitaMes) INTO cReceitaMesAnt FROM `vw_receitas` WHERE mesano < date_format(cMesAtual,'%Y%m') AND profile_uid = aprofile_uid;

SELECT sum(ReceitaMes) INTO cReceitaDoMes FROM `vw_receitas` WHERE mesano = date_format(cMesAtual,'%Y%m') AND profile_uid = aprofile_uid;


SELECT cMesAtual as mesano, aprofile_uid AS profile_uid, cReceitaDoMes AS Receita, cReceitaMes-cOrcado+Csobregasto AS ReceitaAjustada, cReceitaMesAnt-cOrcado+cSobregasto AS Sobregasto;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `MostraBudgets` (IN `a_mesano` INT(6), IN `a_pfid` VARCHAR(23))  NO SQL
BEGIN

CREATE TEMPORARY TABLE IF NOT EXISTS tBud_SaldoMes (
categoriaitem_id INT,
budgetMes DECIMAL(10,2),
gastoMes DECIMAL(10,2),
DespForaOrc DECIMAL(10,2));

CREATE TEMPORARY TABLE IF NOT EXISTS tBud_SaldoAcum ( 
categoriaitem_id INT,
Disponivel DECIMAL(10,2));

TRUNCATE tBud_SaldoAcum;
TRUNCATE tBud_SaldoMes;
    
INSERT INTO tBud_SaldoAcum
SELECT categoriaitem_id, sum(if(carryNegValues =1, SaldoMes+DespForaOrc, SaldoMes)) as Disponivel FROM `vw_mes_budget_gasto` LEFT JOIN vw_categorias ON vw_mes_budget_gasto.categoriaitem_id = vw_categorias.id WHERE mesano < a_mesano AND vw_mes_budget_gasto.profile_uid = a_pfid GROUP BY categoriaitem_id;

INSERT INTO tBud_SaldoMes
SELECT vw_mes_budget_gasto.categoriaitem_id, vw_mes_budget_gasto.budgetMes, vw_mes_budget_gasto.gastoMes, vw_mes_budget_gasto.DespForaOrc FROM vw_mes_budget_gasto WHERE vw_mes_budget_gasto.mesano = a_mesano AND vw_mes_budget_gasto.profile_uid = a_pfid;

SELECT a_mesano AS mesano, vw_categorias.id AS categoriaitem_id, vw_categorias.categoria AS categoriaitem, vw_categorias.categoria_grupo AS categoria_grupo, vw_categorias.categoria_grupo_id AS categoria_grupo_id, tBud_SaldoMes.budgetMes AS budgetMes, tBud_SaldoMes.gastoMes AS gastoMes, tBud_SaldoAcum.Disponivel+tBud_SaldoMes.budgetMes+tBud_SaldoMes.gastoMes AS Disponivel, tBud_SaldoMes.DespForaOrc FROM vw_categorias LEFT JOIN tBud_SaldoAcum ON vw_categorias.id = tBud_SaldoAcum.categoriaitem_id LEFT JOIN tBud_SaldoMes ON vw_categorias.id = tBud_SaldoMes.categoriaitem_id WHERE vw_categorias.profile_uid = a_pfid ORDER BY vw_categorias.ordem_grupo, vw_categorias.ordem;
    
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

CREATE DEFINER=`root`@`localhost` PROCEDURE `MostraBudgets_2` (IN `a_mesano` VARCHAR(6), IN `a_pfid` VARCHAR(23))  BEGIN

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
-- Estrutura da tabela `bud_budgets`
--

CREATE TABLE `bud_budgets` (
  `id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `categoriaitem_id` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `bud_budgets`
--

INSERT INTO `bud_budgets` (`id`, `profile_id`, `date`, `categoriaitem_id`, `valor`) VALUES
(1, 1, '1970-01-01', 3, '0.00'),
(2, 1, '2016-12-01', 4, '0.00'),
(3, 1, '2016-12-01', 3, '0.00'),
(4, 1, '2016-12-01', 5, '0.00'),
(5, 1, '2016-12-01', 6, '0.00'),
(6, 1, '2016-12-01', 7, '0.00'),
(7, 1, '2016-12-01', 8, '0.00'),
(8, 1, '2016-12-01', 9, '0.00'),
(9, 1, '2016-12-01', 10, '0.00'),
(10, 1, '2016-12-01', 11, '0.00'),
(11, 1, '2016-12-01', 12, '0.00'),
(12, 1, '2016-12-01', 13, '0.00'),
(13, 1, '2016-12-01', 14, '0.00'),
(14, 1, '2016-12-01', 15, '0.00'),
(15, 1, '2016-12-01', 16, '0.00'),
(16, 1, '2016-12-01', 17, '0.00'),
(17, 1, '2016-12-01', 18, '0.00'),
(18, 1, '2016-12-01', 19, '0.00'),
(19, 1, '2017-01-01', 3, '0.00'),
(20, 1, '2017-03-01', 4, '0.00'),
(21, 1, '2017-03-01', 3, '0.00'),
(22, 1, '2017-01-01', 4, '0.00'),
(23, 1, '2017-02-01', 3, '5.53'),
(24, 1, '2017-02-01', 4, '100.00'),
(25, 1, '2017-02-01', 5, '484.47'),
(26, 1, '2017-02-01', 6, '0.00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `bud_categorias`
--

CREATE TABLE `bud_categorias` (
  `id` int(11) NOT NULL,
  `nome` varchar(25) NOT NULL,
  `profile_id` int(11) DEFAULT NULL,
  `ordem` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `bud_categorias`
--

INSERT INTO `bud_categorias` (`id`, `nome`, `profile_id`, `ordem`, `created`, `modified`) VALUES
(1, 'Recursos', NULL, -1, NULL, NULL),
(2, 'Contas Fixas', 1, 0, '2016-12-03 20:25:39', '2016-12-03 20:25:39'),
(3, 'Despesas do dia-a-dia', 1, 1, '2016-12-03 20:25:39', '2016-12-03 20:25:39'),
(4, 'Poupança', 1, 2, '2016-12-03 20:25:40', '2016-12-03 20:25:40');

-- --------------------------------------------------------

--
-- Estrutura da tabela `bud_categoriasitens`
--

CREATE TABLE `bud_categoriasitens` (
  `id` int(11) NOT NULL,
  `catmaster_id` int(11) NOT NULL,
  `nome` varchar(25) NOT NULL,
  `descricao` mediumtext NOT NULL,
  `ordem` int(11) NOT NULL,
  `carryNegValues` smallint(1) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `bud_categoriasitens`
--

INSERT INTO `bud_categoriasitens` (`id`, `catmaster_id`, `nome`, `descricao`, `ordem`, `carryNegValues`, `created`, `modified`) VALUES
(1, 1, 'Fundos para este mês', 'Esta opção é usada para contabilizar o fundo para o orçamento deste mês.', 0, 0, NULL, NULL),
(2, 1, 'Fundos para o próximo mês', 'Esta opção é usada para contabilizar o fundo para o orçamento do próximo mês.', 1, 0, NULL, NULL),
(3, 2, 'Água', '', 0, 0, '2016-12-03 20:25:39', '2016-12-03 20:25:39'),
(4, 2, 'Aluguel', '', 1, 0, '2016-12-03 20:25:39', '2016-12-03 20:25:39'),
(5, 2, 'Luz', '', 2, 0, '2016-12-03 20:25:39', '2016-12-03 20:25:39'),
(6, 2, 'Internet', '', 3, 0, '2016-12-03 20:25:39', '2016-12-03 20:25:39'),
(7, 2, 'Telefone', '', 4, 0, '2016-12-03 20:25:39', '2016-12-03 20:25:39'),
(8, 2, 'Mensalidades', '', 5, 0, '2016-12-03 20:25:39', '2016-12-03 20:25:39'),
(9, 3, 'Mercado', '', 6, 0, '2016-12-03 20:25:40', '2016-12-03 20:25:40'),
(10, 3, 'Restaurantes', '', 7, 0, '2016-12-03 20:25:40', '2016-12-03 20:25:40'),
(11, 3, 'Passeios', '', 8, 0, '2016-12-03 20:25:40', '2016-12-03 20:25:40'),
(12, 3, 'Transporte', '', 9, 0, '2016-12-03 20:25:40', '2016-12-03 20:25:40'),
(13, 3, 'Vestuário', '', 10, 0, '2016-12-03 20:25:40', '2016-12-03 20:25:40'),
(14, 3, 'Saúde', '', 11, 0, '2016-12-03 20:25:40', '2016-12-03 20:25:40'),
(15, 3, 'Besteiras', '', 12, 0, '2016-12-03 20:25:40', '2016-12-03 20:25:40'),
(16, 4, 'Imóvel', '', 13, 0, '2016-12-03 20:25:40', '2016-12-03 20:25:40'),
(17, 4, 'Férias', '', 14, 0, '2016-12-03 20:25:40', '2016-12-03 20:25:40'),
(18, 4, 'Saúde', '', 15, 0, '2016-12-03 20:25:40', '2016-12-03 20:25:40'),
(19, 4, 'Emergências', '', 16, 0, '2016-12-03 20:25:40', '2016-12-03 20:25:40');

-- --------------------------------------------------------

--
-- Estrutura da tabela `bud_contas`
--

CREATE TABLE `bud_contas` (
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

--
-- Extraindo dados da tabela `bud_contas`
--

INSERT INTO `bud_contas` (`id`, `conta_nome`, `conta_descricao`, `profile_id`, `reconciliado_valor`, `reconciliado_data`, `created`, `modified`, `budget`) VALUES
(1, 'Santander', '', 1, '0.00', '2016-12-05', '2016-12-05 20:42:25', '2016-12-05 20:42:25', 1),
(2, 'Itau', '', 1, '0.00', '2016-12-06', '2016-12-06 17:29:38', '2016-12-06 17:29:38', 1),
(3, 'Teste (crédito)', '', 1, '0.00', '2016-12-08', '2016-12-08 00:49:15', '2016-12-08 00:49:15', 1),
(4, 'HEHE', '', 1, '0.00', '2016-12-12', '2016-12-12 15:10:43', '2016-12-12 15:10:43', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `bud_transacoes`
--

CREATE TABLE `bud_transacoes` (
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

--
-- Extraindo dados da tabela `bud_transacoes`
--

INSERT INTO `bud_transacoes` (`id`, `conta_id`, `data`, `sacado_nome`, `memo`, `valor`, `created`, `modified`, `tranNum`, `conciliado`, `aprovado`) VALUES
(29, 2, '2017-02-01', 'ITAUCARD MC   4503-4475', '', '-517.53', '2017-03-11 12:24:13', '2017-03-11 12:44:12', '20170201001ITAUCARD MC   4503-44751', NULL, NULL),
(30, 2, '2017-02-01', 'REND PAGO APLIC AUT MAIS', '', '500.00', '2017-03-11 12:24:13', '2017-03-14 01:27:54', '20170201002REND PAGO APLIC AUT MAIS1', NULL, NULL),
(31, 2, '2017-02-22', 'TED 104.2185JOAO PERCI A', '', '590.00', '2017-03-11 12:24:13', '2017-03-12 17:02:39', '20170222001TED 104.2185JOAO PERCI A1', NULL, NULL),
(32, 2, '2017-02-25', 'CXE 000422 SAQUE 25/02', '', '-90.00', '2017-03-11 12:24:13', '2017-03-12 17:02:46', '20170225001CXE 000422 SAQUE 25/021', NULL, NULL),
(33, 2, '2017-02-28', 'TBI 9671.07082-0     C/C', '', '-88.00', '2017-03-11 12:24:14', '2017-03-12 17:02:51', '20170228001TBI 9671.07082-0     C/C1', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `bud_transacoesitens`
--

CREATE TABLE `bud_transacoesitens` (
  `id` int(11) NOT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `transacao_id` int(11) NOT NULL,
  `transf_para_conta_id` int(11) DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `bud_transacoesitens`
--

INSERT INTO `bud_transacoesitens` (`id`, `categoria_id`, `transacao_id`, `transf_para_conta_id`, `valor`, `created`, `modified`) VALUES
(35, 3, 29, NULL, '-517.53', '2017-03-11 12:44:12', '2017-03-11 12:44:12'),
(36, 2, 30, NULL, '500.00', '2017-03-11 13:11:26', '2017-03-14 01:27:54'),
(37, 1, 31, NULL, '590.00', '2017-03-12 17:02:39', '2017-03-12 17:02:39'),
(38, 4, 32, NULL, '-90.00', '2017-03-12 17:02:46', '2017-03-12 17:02:46'),
(39, 3, 33, NULL, '-88.00', '2017-03-12 17:02:51', '2017-03-12 17:02:51');

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
-- Estrutura da tabela `groups`
--

CREATE TABLE `groups` (
  `id` mediumint(8) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  `created_from_ip` varchar(15) DEFAULT NULL,
  `updated_from_ip` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `groups`
--

INSERT INTO `groups` (`id`, `name`, `description`, `created_from_ip`, `updated_from_ip`) VALUES
(1, 'admin', 'Administrator', '', ''),
(2, 'members', 'General User', '', '');

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

--
-- Extraindo dados da tabela `profiles`
--

INSERT INTO `profiles` (`id`, `uniqueid`, `user_id`, `nome`, `created`, `modified`, `created_from_ip`, `updated_from_ip`) VALUES
(1, '58431c33240b3', 1, 'Rafael', '2016-12-03 20:25:39', '2016-12-03 20:25:39', NULL, NULL);

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

--
-- Extraindo dados da tabela `users`
--

INSERT INTO `users` (`id`, `ip_address`, `username`, `password`, `salt`, `email`, `activation_code`, `forgotten_password_code`, `forgotten_password_time`, `remember_code`, `created_on`, `last_login`, `active`, `first_name`, `last_name`, `company`, `phone`) VALUES
(1, '127.0.0.1', 'rafaacla@gmail.com', '$2y$08$6sIc6JLpcqJaiwczcDf1fOgGDmCxxjmVzAVuq0e1smYl38ITRWxL.', '', 'rafaacla@gmail.com', '', NULL, NULL, 'z4beMDPzoXknMvmA4mXWAO', 1268889823, 1489321254, 1, 'Rafael', 'Claudio', 'ADMIN', '0');

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

--
-- Extraindo dados da tabela `users_groups`
--

INSERT INTO `users_groups` (`id`, `user_id`, `group_id`, `created_from_ip`, `updated_from_ip`) VALUES
(1, 1, 1, '', '');

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_accounts`
-- (See below for the actual view)
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
-- (See below for the actual view)
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
-- (See below for the actual view)
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
,`carryNegValues` smallint(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_contas`
-- (See below for the actual view)
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
-- (See below for the actual view)
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
-- (See below for the actual view)
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
-- (See below for the actual view)
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
-- (See below for the actual view)
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
-- (See below for the actual view)
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
-- (See below for the actual view)
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
-- (See below for the actual view)
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
-- (See below for the actual view)
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
-- (See below for the actual view)
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
-- (See below for the actual view)
--
CREATE TABLE `vw_transacoes_classificadas` (
`id` int(11)
,`conta_id` bigint(11)
,`countNClas` int(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `vw_transacoes_count`
-- (See below for the actual view)
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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_accounts`  AS  select `contas`.`conta_nome` AS `conta_nome`,date_format(`vw_transacoes`.`data`,'%d/%m/%Y') AS `data`,`vw_transacoes`.`sacado_nome` AS `sacado_nome`,concat_ws(': ',`bud_categorias`.`nome`,`bud_categoriasitens`.`nome`) AS `categoria`,`vw_transacoes`.`memo` AS `memo`,`vw_transacoes`.`valor` AS `valor`,`t1`.`valor` AS `valor_item`,`contas`.`profile_id` AS `profile_id`,`contas`.`profile_uid` AS `profile_uid`,`vw_transacoes`.`id` AS `transacao_id`,`contas`.`id` AS `conta_id`,`vw_transacoes`.`Editavel` AS `editavel`,`t1`.`id` AS `tritem_id`,`t1`.`categoria_id` AS `catitem_id`,`vw_transacoes_count`.`count_itens` AS `count_filhas`,`t1`.`conta_para_id` AS `conta_para_id`,`contas2`.`conta_nome` AS `conta_para_nome`,`vw_transacoes`.`data` AS `data_un`,`vw_transacoes`.`conciliado` AS `conciliado`,`vw_transacoes`.`aprovado` AS `aprovado` from ((((((`vw_transacoes` left join `vw_contas` `contas` on((`vw_transacoes`.`conta_id` = `contas`.`id`))) left join `vw_transacoesitens` `t1` on(((`vw_transacoes`.`id` = `t1`.`transacao_id`) and (`vw_transacoes`.`conta_id` = `t1`.`conta_id`)))) left join `bud_categoriasitens` on((`t1`.`categoria_id` = `bud_categoriasitens`.`id`))) left join `bud_categorias` on((`bud_categoriasitens`.`catmaster_id` = `bud_categorias`.`id`))) left join `vw_transacoes_count` on((`vw_transacoes`.`id` = `vw_transacoes_count`.`transacao_id`))) left join `vw_contas` `contas2` on((`t1`.`conta_para_id` = `contas2`.`id`))) ;

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_categorias`  AS  select `bud_categoriasitens`.`id` AS `id`,`bud_categoriasitens`.`nome` AS `categoria`,`bud_categorias`.`nome` AS `categoria_grupo`,`bud_categorias`.`id` AS `categoria_grupo_id`,`bud_categoriasitens`.`ordem` AS `ordem`,`bud_categorias`.`ordem` AS `ordem_grupo`,`bud_categorias`.`profile_id` AS `profile_id`,`profiles`.`uniqueid` AS `profile_uid`,`bud_categoriasitens`.`carryNegValues` AS `carryNegValues` from ((`bud_categorias` left join `bud_categoriasitens` on((`bud_categoriasitens`.`catmaster_id` = `bud_categorias`.`id`))) left join `profiles` on((`bud_categorias`.`profile_id` = `profiles`.`id`))) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_contas`
--
DROP TABLE IF EXISTS `vw_contas`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_contas`  AS  select `bud_contas`.`id` AS `id`,`bud_contas`.`conta_nome` AS `conta_nome`,`bud_contas`.`conta_descricao` AS `conta_descricao`,`bud_contas`.`reconciliado_valor` AS `reconciliado_valor`,`bud_contas`.`reconciliado_data` AS `reconciliado_data`,`bud_contas`.`created` AS `created`,`bud_contas`.`modified` AS `modified`,`bud_contas`.`budget` AS `budget`,`bud_contas`.`profile_id` AS `profile_id`,`profiles`.`uniqueid` AS `profile_uid` from (`bud_contas` join `profiles` on((`bud_contas`.`profile_id` = `profiles`.`id`))) ;

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_mes_budget`  AS  select `bud_budgets`.`profile_id` AS `profile_id`,`profiles`.`uniqueid` AS `profile_uid`,date_format(`bud_budgets`.`date`,'%Y%m') AS `mesano`,`bud_budgets`.`categoriaitem_id` AS `categoriaitem_id`,sum(`bud_budgets`.`valor`) AS `budgetMes` from (`bud_budgets` left join `profiles` on((`bud_budgets`.`profile_id` = `profiles`.`id`))) group by `bud_budgets`.`profile_id`,date_format(`bud_budgets`.`date`,'%Y%m'),`bud_budgets`.`categoriaitem_id` ;

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_transacoes`  AS  select `bud_transacoes`.`id` AS `id`,ifnull(`vw_transacoesitens`.`conta_id`,`bud_transacoes`.`conta_id`) AS `conta_id`,`bud_transacoes`.`data` AS `data`,`bud_transacoes`.`sacado_nome` AS `sacado_nome`,`bud_transacoes`.`memo` AS `memo`,sum(ifnull(`vw_transacoesitens`.`valor`,`bud_transacoes`.`valor`)) AS `valor`,if((ifnull(`vw_transacoesitens`.`conta_id`,`bud_transacoes`.`conta_id`) = `bud_transacoes`.`conta_id`),1,0) AS `Editavel`,`bud_transacoes`.`conciliado` AS `conciliado`,`bud_transacoes`.`aprovado` AS `aprovado` from (`bud_transacoes` left join `vw_transacoesitens` on((`bud_transacoes`.`id` = `vw_transacoesitens`.`transacao_id`))) group by `bud_transacoes`.`id`,ifnull(`vw_transacoesitens`.`conta_id`,`bud_transacoes`.`conta_id`) ;

-- --------------------------------------------------------

--
-- Structure for view `vw_transacoesitens`
--
DROP TABLE IF EXISTS `vw_transacoesitens`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `vw_transacoesitens`  AS  select `bud_transacoesitens`.`id` AS `id`,`bud_transacoes`.`conta_id` AS `conta_id`,`bud_transacoesitens`.`transf_para_conta_id` AS `conta_para_id`,`bud_transacoes`.`data` AS `data`,`bud_transacoes`.`sacado_nome` AS `sacado_nome`,`bud_transacoes`.`memo` AS `memo`,`bud_transacoesitens`.`categoria_id` AS `categoria_id`,`bud_transacoesitens`.`transacao_id` AS `transacao_id`,`bud_transacoesitens`.`valor` AS `valor`,`contas`.`profile_id` AS `profile_id`,`contas`.`profile_uid` AS `profile_uid` from ((`bud_transacoesitens` left join `bud_transacoes` on((`bud_transacoesitens`.`transacao_id` = `bud_transacoes`.`id`))) left join `vw_contas` `contas` on((`bud_transacoes`.`conta_id` = `contas`.`id`))) union select `bud_transacoesitens`.`id` AS `id`,`bud_transacoesitens`.`transf_para_conta_id` AS `conta_id`,`bud_transacoes`.`conta_id` AS `conta_para_id`,`bud_transacoes`.`data` AS `data`,`bud_transacoes`.`sacado_nome` AS `sacado_nome`,`bud_transacoes`.`memo` AS `memo`,`bud_transacoesitens`.`categoria_id` AS `categoria_id`,`bud_transacoesitens`.`transacao_id` AS `transacao_id`,(-(1) * `bud_transacoesitens`.`valor`) AS `(-1)*transacoesitens.valor`,`contas`.`profile_id` AS `profile_id`,`contas`.`profile_uid` AS `profile_uid` from ((`bud_transacoesitens` left join `bud_transacoes` on((`bud_transacoesitens`.`transacao_id` = `bud_transacoes`.`id`))) left join `vw_contas` `contas` on((`bud_transacoes`.`conta_id` = `contas`.`id`))) where (`bud_transacoesitens`.`transf_para_conta_id` is not null) ;

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
-- Indexes for table `bud_budgets`
--
ALTER TABLE `bud_budgets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`),
  ADD KEY `categoriaitem_id` (`categoriaitem_id`);

--
-- Indexes for table `bud_categorias`
--
ALTER TABLE `bud_categorias`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indexes for table `bud_categoriasitens`
--
ALTER TABLE `bud_categoriasitens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `catmaster_id` (`catmaster_id`);

--
-- Indexes for table `bud_contas`
--
ALTER TABLE `bud_contas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `profile_id` (`profile_id`);

--
-- Indexes for table `bud_transacoes`
--
ALTER TABLE `bud_transacoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tranNum` (`tranNum`),
  ADD KEY `conta_id` (`conta_id`);

--
-- Indexes for table `bud_transacoesitens`
--
ALTER TABLE `bud_transacoesitens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`),
  ADD KEY `transacao_id` (`transacao_id`),
  ADD KEY `transf_para_conta_id` (`transf_para_conta_id`);

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
-- AUTO_INCREMENT for table `bud_budgets`
--
ALTER TABLE `bud_budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;
--
-- AUTO_INCREMENT for table `bud_categorias`
--
ALTER TABLE `bud_categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `bud_categoriasitens`
--
ALTER TABLE `bud_categoriasitens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
--
-- AUTO_INCREMENT for table `bud_contas`
--
ALTER TABLE `bud_contas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT for table `bud_transacoes`
--
ALTER TABLE `bud_transacoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT for table `bud_transacoesitens`
--
ALTER TABLE `bud_transacoesitens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;
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
-- Limitadores para a tabela `bud_budgets`
--
ALTER TABLE `bud_budgets`
  ADD CONSTRAINT `budgets_profile_ct` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `bud_categorias`
--
ALTER TABLE `bud_categorias`
  ADD CONSTRAINT `categorias_profile_ct` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `bud_categoriasitens`
--
ALTER TABLE `bud_categoriasitens`
  ADD CONSTRAINT `itens_categoria_ct` FOREIGN KEY (`catmaster_id`) REFERENCES `bud_categorias` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `bud_contas`
--
ALTER TABLE `bud_contas`
  ADD CONSTRAINT `contas_profile_ct` FOREIGN KEY (`profile_id`) REFERENCES `profiles` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `bud_transacoes`
--
ALTER TABLE `bud_transacoes`
  ADD CONSTRAINT `transacoes_conta_ct` FOREIGN KEY (`conta_id`) REFERENCES `bud_contas` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `bud_transacoesitens`
--
ALTER TABLE `bud_transacoesitens`
  ADD CONSTRAINT `tritens_cateitem` FOREIGN KEY (`categoria_id`) REFERENCES `bud_categoriasitens` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tritens_contas_ct` FOREIGN KEY (`transf_para_conta_id`) REFERENCES `bud_contas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tritens_transacao_ct` FOREIGN KEY (`transacao_id`) REFERENCES `bud_transacoes` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `profiles`
--
ALTER TABLE `profiles`
  ADD CONSTRAINT `profiles_user_ct` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
