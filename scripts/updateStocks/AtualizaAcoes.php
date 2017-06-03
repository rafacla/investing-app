<?php

include_once('YahooFinanceAPI2.php');
include_once('Database.php');

class AtualizaAcoes 
{
	public function AtualizaAcoes_DiaAtual() {
		$db = new Database();
		$sql = "SELECT codigo_yahoo FROM appinv_catalogo_acoes";
			
		$rows = $db -> select($sql);
		
		$acoes = (array_column($rows,"codigo_yahoo"));
		
		$cotacoes = new YahooFinanceAPI();
		$valores = $cotacoes->DiaAtual($acoes);
		
		$sql=array();
		for ($i=1;$i<sizeof($valores);$i++) {
			$sql[$i] = "INSERT INTO `appinv_acoes_historico` (`ativo`, `data`, `abertura`, `alta`, `baixa`, `fechamento`, `fechamento_ajustado`, `volume`) VALUES ('".$valores[$i]['ativo']."', CURDATE(), '".$valores[$i]['abertura']."', '".$valores[$i]['alta']."', '".$valores[$i]['baixa']."', '".$valores[$i]['ultima']."', '".$valores[$i]['ultima']."', '".$valores[$i]['volume']."') ON DUPLICATE KEY UPDATE `abertura` = '".$valores[$i]['abertura']."',`alta`='".$valores[$i]['alta']."', `baixa` = '".$valores[$i]['baixa']."', `fechamento` = '".$valores[$i]['ultima']."', `fechamento_ajustado` = '".$valores[$i]['ultima']."', `volume` = '".$valores[$i]['volume']."';";
		}
		if (sizeof($sql)) {
			$db = new Database();
			$db->query($sql);
			return true;
		} else {
			return false;
		}
	}
	
	public function AcaoAleatoriaDesatualizada($codigo='',$blacklist) {		
		$db = new Database();
		
		if (sizeof($blacklist)>0) {
			$blacklist_s = "WHERE `t1`.`codigo_yahoo` NOT IN ('" . implode($blacklist, "', '") . "')";
		} else {
			$blacklist_s = "";
		}
		
		$sql = "SELECT `t1`.`ativo`,`t1`.`codigo_yahoo` FROM `appinv_catalogo_acoes` `t1` LEFT JOIN `appinv_acoes_historico` `t2` ON (`t1`.`ativo`=`t2`.`ativo`) " . $blacklist_s . " GROUP BY `t1`.`ativo` HAVING max(ifnull(`t2`.`ultima_atualizacao`,'2001-01-01')) < CURDATE()";
			
		$rows = $db -> select($sql);
		
		
		$linha = rand(0,sizeof($rows)-1);
		if (sizeof($rows)==0) {
			return false;
		} else if ($codigo=="yahoo") {
			return $rows[$linha]['codigo_yahoo'];
		} else {
			return $rows[$linha]['ativo'];
		}
	}
	
	public function AtualizaAcaoYahoo($acaoYahoo,$dataInicio='',$dataFinal='',$acaoNome='') {
		if ($dataInicio=='') {
			$dataInicio = strtotime("2010-01-01");
		}
		
		if ($dataFinal=='') {
			$dataFinal = strtotime("now");
		}
		
		$cotacoes = new YahooFinanceAPI();
		if ($acaoNome=="") {
			$acao = substr($acaoYahoo,0,5);
		} else {
			$acao = $acaoNome;
		} 
			
		$historico = $cotacoes->Historico($acaoYahoo,$dataInicio,$dataFinal);
		$sql1=array();
		for ($i=1;$i<=$historico[0]['nrPregoes'];$i++) {
			if (is_numeric($historico[$i]['Abertura'])) {
				$sql1[$i] = "INSERT INTO `appinv_acoes_historico` (`id`, `ativo`, `data`, `abertura`, `alta`, `baixa`, `fechamento`, `fechamento_ajustado`, `volume`, `ultima_atualizacao`) VALUES (NULL, '".$acao."', '".$historico[$i]['Data']."', '".$historico[$i]['Abertura']."', '".$historico[$i]['Alta']."', '".$historico[$i]['Baixa']."', '".$historico[$i]['Fechamento']."', '".$historico[$i]['Fechamento_Ajustado']."', '".$historico[$i]['Volume']."', CURDATE()) ON DUPLICATE KEY UPDATE `abertura` = '".$historico[$i]['Abertura']."',`alta`='".$historico[$i]['Alta']."', `baixa` = '".$historico[$i]['Baixa']."', `fechamento` = '".$historico[$i]['Fechamento']."', `fechamento_ajustado` = '".$historico[$i]['Fechamento_Ajustado']."', `volume` = '".$historico[$i]['Volume']."', `ultima_atualizacao` = CURDATE();";
			}
		}
		sleep(4);
		$ajustes = $cotacoes->Ajustes($acaoYahoo,$dataInicio,$dataFinal);
		$sql2=array();
		for ($i=1;$i<=$ajustes['resumo']['nrDividendos'];$i++) {
			$sql2[$i] = "INSERT INTO `appinv_acoes_dividendos` (`id`, `ativo`, `data`, `valor`) VALUES (NULL, '".$acao."', '".$ajustes['dividendos'][$i]['data']."', '".$ajustes['dividendos'][$i]['valor']."') ON DUPLICATE KEY UPDATE `valor` = '".$ajustes['dividendos'][$i]['valor']."';";
		}
		$sql3=array();
		for ($i=1;$i<=$ajustes['resumo']['nrSplits'];$i++) {
			$sql3[$i] = "INSERT INTO `appinv_acoes_splits` (`id`, `ativo`, `data`, `origem`,`fim`) VALUES (NULL, '".$acao."', '".$ajustes['splits'][$i]['data']."', '".$ajustes['splits'][$i]['origem']."', '".$ajustes['splits'][$i]['final']."') ON DUPLICATE KEY UPDATE `origem` = '".$ajustes['splits'][$i]['origem']."',`fim` = '".$ajustes['splits'][$i]['final']."';";
		}
		$sql = array();
		if (sizeof($sql1)) {
			$sql = array_merge($sql,$sql1);
		}
		if (sizeof($sql2)) {
		$sql = array_merge($sql,$sql2);
		}
		if (sizeof($sql3)) {
		$sql = array_merge($sql,$sql3);
		}
		
		if (sizeof($sql)) {
			//var_dump($sql);
			$db = new Database();
			$db->query($sql);
			return true;
		} else {
			return false;
		}
	}	
}