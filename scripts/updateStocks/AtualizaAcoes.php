<?php
include_once('GoogleFinance.php');
include_once(realpath(dirname(__FILE__).'/../Database.php'));

class AtualizaAcoes 
{
	public function AtualizaAcoes_DiaAtual() {
		$db = new Database();
		$sql = "SELECT codigo_yahoo FROM appinv_catalogo_acoes";
			
		$rows = $db -> select($sql);
		
		$acoes = (array_column($rows,"codigo_yahoo"));
		
		$cotacoes = new GoogleFinanceAPI();
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
	
	public function AcaoAleatoriaDesatualizada($blacklist) {		
		$db = new Database();
		
		if (sizeof($blacklist)>0) {
			$blacklist_s = "WHERE `t1`.`ativo` NOT IN ('" . implode($blacklist, "', '") . "')";
		} else {
			$blacklist_s = "";
		}
		
		$sql = "SELECT `t1`.`ativo`,`t1`.`codigo_yahoo` FROM `appinv_catalogo_acoes` `t1` LEFT JOIN `appinv_acoes_historico` `t2` ON (`t1`.`ativo`=`t2`.`ativo`) " . $blacklist_s . " GROUP BY `t1`.`ativo` HAVING max(ifnull(`t2`.`ultima_atualizacao`,'2001-01-01')) < CURDATE()";
			
		$rows = $db -> select($sql);
		
		
		$linha = rand(0,sizeof($rows)-1);
		if (sizeof($rows)==0) {
			return false;
		} else {
			return $rows[$linha]['ativo'];
		}
	}
	
	public function AtualizaAcaoGoogle($acao,$dataInicio='',$dataFinal='') {
		if ($dataInicio=='') {
			$dataInicio = strtotime("2010-01-01");
		}
		
		if ($dataFinal=='') {
			$dataFinal = strtotime("now");
		}
		
		$cotacoes = new GoogleFinanceAPI();
			
		$historico = $cotacoes->Historico($acao,$dataInicio,$dataFinal);
		$sql1=array();
		for ($i=1;$i<=$historico[0]['nrPregoes'];$i++) {
			if (is_numeric($historico[$i]['Abertura'])) {
				$sql1[$i] = "INSERT INTO `appinv_acoes_historico` (`id`, `ativo`, `data`, `abertura`, `alta`, `baixa`, `fechamento`, `fechamento_ajustado`, `volume`, `ultima_atualizacao`) VALUES (NULL, '".$acao."', '".$historico[$i]['Data']."', '".$historico[$i]['Abertura']."', '".$historico[$i]['Alta']."', '".$historico[$i]['Baixa']."', '".$historico[$i]['Fechamento']."', '".$historico[$i]['Fechamento_Ajustado']."', '".$historico[$i]['Volume']."', CURDATE()) ON DUPLICATE KEY UPDATE `abertura` = '".$historico[$i]['Abertura']."',`alta`='".$historico[$i]['Alta']."', `baixa` = '".$historico[$i]['Baixa']."', `fechamento` = '".$historico[$i]['Fechamento']."', `fechamento_ajustado` = '".$historico[$i]['Fechamento_Ajustado']."', `volume` = '".$historico[$i]['Volume']."', `ultima_atualizacao` = CURDATE();";
			}
		}
		if (sizeof($sql1)) {
			//var_dump($sql);
			$db = new Database();
			$db->query($sql1);
			return true;
		} else {
			return false;
		}
	}	
}