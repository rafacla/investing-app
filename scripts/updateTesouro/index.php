<?php
require_once 'excel_reader2.php';
include_once(realpath(dirname(__FILE__).'/../Database.php'));

//Esta funçao atualiza a lista de arquivos (URLs) do site do Tesouro para atualizar o BD:
function atualizaListaArquivos() {
	global $arquivos;
	//desabilita as mensagens de html mal formado:
	libxml_use_internal_errors(true);

	//Aqui vamos recuperar a página do tesouro direto onde estao os links para os arquivos das taxas:
	require_once('get_html.php');

	$base_url = "http://sisweb.tesouro.gov.br/";
	$link = $base_url . "apex/f?p=2031:2";
	$html = getPage($link);

	$DOM = new DOMDocument;
	$DOM->loadHTML($html);

	$xpath = new DOMXPath($DOM);

	$html = '';
	foreach ($xpath->query('//div[@class="bl-body"]/*') as $node)
	{
		$html .= $DOM->saveXML($node);
	}

	$dom2 = new DOMDocument;
	$dom2->loadHTML($html);

	$i = 0;
	foreach ($dom2->getElementsByTagName('a') as $node) {
		$arquivos[$i] = $base_url."apex/".$node->getAttribute( 'href' );
		$i++;
	}
};

function logMsg ($msg,$verbose=true) {
	if ($verbose) { 
		$date = new DateTime();
		$date = $date->format("Y-m-d h:i:s");
		echo "[".$date."] " . $msg ."<br>";
		file_put_contents (__DIR__ ."/log.txt","[".$date."] " . $msg."\r\n",FILE_APPEND);
	}
}
function atualizaArquivo($url, $atualizaExistente = false, $verbose=false) {
	error_reporting(E_ALL ^ E_NOTICE);
	global $db;
	
	$arquivo = file_get_contents($url);
	if ($arquivo) {
		file_put_contents('tempTitulo.xls',$arquivo);
		$data = new Spreadsheet_Excel_Reader('tempTitulo.xls',false);
	}
	if ($data) {
		logMsg("Arquivo carregado",$verbose);
		logMsg("Identificado ".$data->sheetcount()." guias",$verbose);
		for ($i=0;$i<$data->sheetcount();$i++) {
			logMsg("Lendo a guia ".$data->sheetname($i));
			$tipo_titulo = substr($data->sheetname($i),0,-7);
			$vencimento = DateTime::createFromFormat('d/m/Y',$data->value(1,2,$i));
			logMsg(" - Tipo Titulo: ".$tipo_titulo);
			logMsg(" - Vencimento do Titulo: ".$vencimento->format('Y-m-d'));
			logMsg(" - Linhas do documento: ".$data->rowcount($i));
			for ($j=3;$j<=(int)$data->rowcount($i);$j++) {
				for ($k=0;$k<5;$k++)
					$taxas[$k] = str_replace("%","",$data->value($j,$k+2,$i));
				$pos = sizeof($dados);
				$dataTsf = DateTime::createFromFormat('d/m/Y',$data->value($j,1,$i));
				if ($dataTsf) {
					$dataT = $dataTsf->format('Y-m-d');
					$dados[$pos] = "INSERT INTO `appinv_tesouro_historico` VALUES (NULL,";
					$dados[$pos] .= "'".$tipo_titulo."','".$vencimento->format('Y-m-d')."','".$dataT."'";
					$dados[$pos] .= ",'".$taxas[0]."'";
					$dados[$pos] .= ",'".$taxas[1]."'";
					$dados[$pos] .= ",'".$taxas[2]."'";
					$dados[$pos] .= ",'".$taxas[3]."'";
					$dados[$pos] .= ",'".$taxas[4]."')";
					if ($atualizaExistente) { //Se marcado esta opçao como true, vamos atualizar os valores caso já exista:
						$dados[$pos] .= " ON DUPLICATE KEY UPDATE ";
						$dados[$pos] .= "taxa_compra_m ='".$taxas[0]."'";
						$dados[$pos] .= ",taxa_venda_m ='".$taxas[1]."'";
						$dados[$pos] .= ",pu_compra_m ='".$taxas[2]."'";
						$dados[$pos] .= ",pu_venda_m ='".$taxas[3]."'";
						$dados[$pos] .= ",pu_base_m ='".$taxas[4]."'";
					}
					$dados[$pos] .= ";";
				}
			}
			logMsg(" - Executando SQL de insercao/atualizacao...",$verbose);
			$db->query($dados);
			$sql = "INSERT INTO `appinv_tesouro_tipotitulos` VALUES (NULL,'".$tipo_titulo."','".$tipo_titulo." ".$vencimento->format('d/m/Y')."','".$vencimento->format('Y-m-d')."')";
			$db->query($sql);
			if ($db->error()) {
				logMsg(" - Erro de banco de dados: ".$db->error());
			} else {
				logMsg(" - Arquivo atualizado com sucesso no BD.");	 
			}
		}
	} else {
		logMsg("Arquivo nao encontrado.",$verbose);
	}
};
$verbose = true;

if ($verbose) {
	//Limpa o log anterior:
	file_put_contents (__DIR__ ."/log.txt","Log reiniciado.\r\n");
}
logMsg("Iniciando... Abrindo conexao com o banco de dados",$verbose);
$db = new Database();
logMsg("Atualizando a lista de arquivos disponiveis no site do tesouro",$verbose);
atualizaListaArquivos();
logMsg("Foram encontrados ".sizeof($arquivos)." no site do Tesouro!",$verbose);
for ($a=0;$a<sizeof($arquivos);$a++) {
	$aa = $a+1;
	sleep(5);
	logMsg("Atualizando o arquivo numero ".($aa).":",$verbose);
	logMsg("Caminho: ".$arquivos[$a]);
	set_time_limit (300);
	atualizaArquivo($arquivos[$a],true,true);
}
logMsg("Concluído. Encerrando.");
?>