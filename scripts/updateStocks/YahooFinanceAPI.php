<?php

class YahooFinanceAPI
{    
	public function Ajustes($ticker,$startDate,$endDate) {
		//retorna os Dividendos e Splits
		$base_url = "http://ichart.finance.yahoo.com/x?";
		$ativo = "s=".$ticker;
		$mesI="&a=".(date('m',$startDate)-1);
		$diaI="&b=".date('d',$startDate);
		$anoI="&c=".date('Y',$startDate);
		$mesF="&d=".(date('m',$endDate)-1);
		$diaF="&e=".date('d',$endDate);
		$anoF="&f=".date('Y',$endDate);
		$compl = "&g=v&y=0&z=30000";
		
		$url = $base_url . $ativo . $mesI . $diaI . $anoI . $mesF . $diaF . $anoF . $compl;
		$csv = @file_get_contents($url);
		
		$linhas = explode("\n",$csv);
		
		$data = array();
		
		//inicia as variaiveis de loop
		$nrPregoes =0;
		$nrDividendos =0;
		$nrSplits =0;
		
		$data['resumo']['ativo'] = $ticker;
		$data['resumo']['dtInicio'] = date('d/m/Y',$startDate);
		$data['resumo']['dtTermino'] = date('d/m/Y',$endDate);
		
		for ($i=1;$i<=sizeof($linhas)-5;$i++) {
			$tempArray = explode(",",$linhas[$i]);
			if ($tempArray[0]=="DIVIDEND") {
				++$nrDividendos;
				$data['dividendos'][$nrDividendos]['data']=substr(trim($tempArray[1]),0,4)."-".substr(trim($tempArray[1]),4,2)."-".substr(trim($tempArray[1]),6,2);
				$data['dividendos'][$nrDividendos]['valor']=$tempArray[2];
			} else if ($tempArray[0]=="SPLIT") {
				++$nrSplits;
				$data['splits'][$nrSplits]['data']=substr(trim($tempArray[1]),0,4)."-".substr(trim($tempArray[1]),4,2)."-".substr(trim($tempArray[1]),6,2);
				$data['splits'][$nrSplits]['origem']=substr(trim($tempArray[2]),0,strpos(trim($tempArray[2]),":"));
				$data['splits'][$nrSplits]['final']=substr(trim($tempArray[2]),strpos(trim($tempArray[2]),":")+1,strlen(trim($tempArray[2]))-strpos(trim($tempArray[2]),":"));
			} else if (is_numeric($tempArray[0])) {
				++$nrPregoes;
				$data['pregoes'][$nrPregoes]['data']=$tempArray[0];
				$data['pregoes'][$nrPregoes]['abertura']=$tempArray[1];
				$data['pregoes'][$nrPregoes]['alta']=$tempArray[2];
				$data['pregoes'][$nrPregoes]['baixa']=$tempArray[3];
				$data['pregoes'][$nrPregoes]['fechamento']=$tempArray[4];
				$data['pregoes'][$nrPregoes]['fechamento_ajustado']=$tempArray[6];
				$data['pregoes'][$nrPregoes]['volume']=$tempArray[5];
			}
		}
		
		$data['resumo']['nrPregoes'] = $nrPregoes;		
		$data['resumo']['nrDividendos'] = $nrDividendos;		
		$data['resumo']['nrSplits'] = $nrSplits;		
		
		return $data;
	}
	
	public function Historico($ticker,$startDate,$endDate) {
		$url = "http://real-chart.finance.yahoo.com/table.csv?s=".$ticker."&a=".(date('m',$startDate)-1)."&b=".date('d',$startDate)."&c=".date('Y',$startDate)."&d=".(date('m',$endDate)-1)."&e=".date('d',$endDate)."&f=".date('Y',$endDate)."&g=d&ignore=.csv";
		$csv = @file_get_contents($url);
		
		if ($csv === false) {
			return false;
		}
		$linhas = explode("\n",$csv);
		
		$data = array();
		
		$data[0]['ativo'] = $ticker;
		$data[0]['dtInicio'] = date('d/m/Y',$startDate);
		$data[0]['dtTermino'] = date('d/m/Y',$endDate);
		$data[0]['nrPregoes'] = sizeof($linhas)-2;
		
		for ($i=1;$i<=$data[0]['nrPregoes'];$i++) {
			$tempArray = explode(",",$linhas[$i]);
			$data[$i]['Data']=$tempArray[0];
			$data[$i]['Abertura']=$tempArray[1];
			$data[$i]['Alta']=$tempArray[2];
			$data[$i]['Baixa']=$tempArray[3];
			$data[$i]['Fechamento']=$tempArray[4];
			$data[$i]['Fechamento_Ajustado']=$tempArray[6];
			$data[$i]['Volume']=$tempArray[5];
			//$data[$i]['Dividends']=$tempArray[7];
		}
        return $data;
    }

	public function DiaAtual($tickers) {
		$splitAcoes = array_chunk($tickers,150);
		$linhasFinal = array();
		foreach ($splitAcoes as $acoes) {
			$tickers_s = implode(',',$acoes);
			$url = "http://download.finance.yahoo.com/d/quotes.csv?s=";
			$url .= $tickers_s;
			//s=nome,d1=data ult trade,o=abertura,h=altadodia,g=baixa do dia,l1=ultima v=volume do dia
			//http://www.jarloo.com/yahoo_finance/
			$url .= "&f=sd1ohgl1v";
			
			$csv = @file_get_contents($url);
			
			if ($csv === false) {
				return false;
			}
			$linhas = explode("\n",$csv);
			
			$linhasFinal = array_merge($linhasFinal,$linhas);
			sleep(3);
		}
		$arrayFinal = array();
		$i=0;
		
		foreach ($linhasFinal as $linha) {
			if (strlen($linha)>0) {
				++$i;
				$tempArray = explode(",",$linha);
				$arrayFinal[$i]['codigo_yahoo'] = trim(trim($tempArray[0],'"'));
				$arrayFinal[$i]['ativo'] = substr($arrayFinal[$i]['codigo_yahoo'],0,5);
				$arrayFinal[$i]['ultTrade'] = substr(trim(trim($tempArray[1],'"')),6,4)."-".substr(trim(trim($tempArray[1],'"')),0,2)."-".substr(trim(trim($tempArray[1],'"')),3,2);
				$arrayFinal[$i]['abertura'] = $tempArray[2];
				$arrayFinal[$i]['alta'] = $tempArray[3];
				$arrayFinal[$i]['baixa'] = $tempArray[4];
				$arrayFinal[$i]['ultima'] = $tempArray[5];
				$arrayFinal[$i]['volume'] = $tempArray[6];
			}
		}
		
		return $arrayFinal;
	}
	
    public function DiaAtual_dep ($tickers,$fields=true) {
        // set url
		$api_url = 'http://query.yahooapis.com/v1/public/yql';
		
        $url = $api_url;
        $url .= '?q=select%20*%20from%20yahoo.finance.quotes%20where%20symbol%20in%20%28%22'.implode(',',$tickers).'%22%29&env=store://datatables.org/alltableswithkeys';
		
        // set fields
        if ($fields===true || empty($fields)) {
            $fields = array(
                    'Symbol','Name','Change','ChangeRealtime','PERatio',
                    'PERatioRealtime','Volume','PercentChange','DividendYield',
                    'LastTradeRealtimeWithTime','LastTradeWithTime','LastTradePriceOnly','LastTradeTime',
                    'LastTradeDate'
                    );
        }
        // make request
        $ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $resp = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch); 
        // parse response
        if (!empty($fields)) {
            $xml = new SimpleXMLElement($resp);
            $data = array();
            $row = array();
            $time = time();
            if(is_object($xml)){
                foreach($xml->results->quote as $quote){
                    $row = array();
                    foreach ($fields as $field) {
                        $row[$field] = (string) $quote->$field;
                    }
                    $data[] = $row;
                }
            }
        } else {
            $data = $resp;
        }
        return $data;
    }
}
