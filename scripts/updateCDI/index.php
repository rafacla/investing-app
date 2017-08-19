<?php

include_once(realpath(dirname(__FILE__).'/../Database.php'));
$blacklist = array();

function getLista_CDI_Cetip() {
	$url = "ftp://ftp.cetip.com.br/MediaCDI/";
	$lista = scandir($url,1);
	$db = new Database();
		
	$i=0;
	for ($i=0;$i<sizeof($lista);$i++) {
		$date = new DateTime();
		$date = $date->format("Y-m-d h:i:s");

		$sql = "SELECT id FROM appinv_cdi WHERE id = '".substr($lista[$i],0,8)."'";
			
		$rows = $db -> select($sql);
		
		if($rows) {
			file_put_contents (__DIR__ ."/log.txt","[" . $date."] ". "Dia já atualizado: ".substr($lista[$i],0,8).".\r\n",FILE_APPEND);
		} else {
			//blz, vamos trabalhar:
			$arquivo = fopen($url.$lista[$i],"r") or file_put_contents (__DIR__ ."/log.txt","[" . $date."] ". "Falha ao abrir arquivo: ".substr($lista[$i],0,8).".\r\n",FILE_APPEND);
			$taxa_str_aa = fread($arquivo,filesize($url.$lista[$i]));
			fclose($arquivo);

			$taxa_aa = floatval($taxa_str_aa)/10000+1;
			$taxa_ad = $taxa_aa**(1/253); //aproximação boa o suficiente para um ano util
			$sql_i = "INSERT INTO `appinv_cdi` (`id`,`data`,`taxa_anual`,`taxa_diaria`) ";
			$sql_i = $sql_i . "VALUES ('".substr($lista[$i],0,8)."','".substr($lista[$i],0,4)."-".substr($lista[$i],4,2)."-".substr($lista[$i],6,2)."',";
			$sql_i = $sql_i . "'".$taxa_aa."','".$taxa_ad."');";
			$resultado = $db->query($sql_i) or file_put_contents (__DIR__ ."/log.txt","[" . $date."] - Erro de banco de dados: ".$sql_i." ".$db->error());
			if ($resultado)
				file_put_contents (__DIR__ ."/log.txt","[" . $date."] ". "Atualizando dia: ".substr($lista[$i],0,8)." - Taxa a.a.: ".$taxa_aa." Taxa a.m: ".$taxa_ad.".\r\n",FILE_APPEND);
			sleep(10);
		}
	}
}
file_put_contents(__DIR__ ."/log.txt","Iniciando atualização.\r\n");

getLista_CDI_Cetip();
?>