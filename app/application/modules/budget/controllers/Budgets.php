<?php

class Budgets extends Admin_Controller {
    function __construct() {
        parent::__construct();
		
		$this->load->helper('date');
		$this->load->library('session');
		$this->load->model(array('budget/user'));
        $this->load->model(array('budget/vw_budgets'));
		$this->load->model(array('budget/conta'));
		$this->load->model(array('budget/vw_categorias'));
		$this->load->model(array('budget/vw_mes_budget'));
		$this->load->model(array('budget/vw_mes_gasto'));
		$this->load->model(array('budget/vw_receitas'));
    }
	
	function ifnull($var, $default=null) {
		return !isset($var) ? $default : $var;
	}
	
	function mesAnterior($mes) {
		$mesant = substr($mes,4,2)-1;
		if ($mesant <1) {
			$mesant = 12;
			$anoant = substr($mes,0,4)-1;
		} else {
			$anoant = substr($mes,0,4);
		}
		
		$mesano_ant = sprintf('%04d',$anoant).sprintf('%02d',$mesant);
		return $mesano_ant;
	}
	
	public function adicionaCategoriaGrupo() {
		if ($this->input->post('categoria')!='') {
			$data['nome']=$this->input->post('categoria');
			$data['profile_id'] = $this->profile->id;
			$sql1 = "SELECT max(ordem) as max_ordem FROM `vw_categorias` WHERE profile_uid='" . $this->profile->uniqueid . "'";
			$query = $this->db->query($sql1);
			$data['ordem']=$query->row(0)->max_ordem+1;
			$data['created'] =  date("Y-m-d H:i:s");
			$data['modified'] =  date("Y-m-d H:i:s");
			$this->db->insert('bud_categorias', $data);
		}
		header("Location: ".$this->input->post('url'));
		die();
	}
	
	public function listaGastos() {
		if ($this->input->post('categoriaitem_id')!='' && $this->input->post('mesano')!='') {
			$mesano = substr($this->input->post('mesano'),0,4)."-".substr($this->input->post('mesano'),-2);
			$sql1 = "SELECT DISTINCT transacao_id, data, sacado_nome, valor FROM `vw_accounts` WHERE catitem_id IN (" . $this->input->post('categoriaitem_id') . ") AND LEFT(data_un,7) ='".$mesano."'";
			$query = $this->db->query($sql1);
			echo json_encode($query->result_array());
		}	
	}
	
	public function editaCategoriaGrupo() {
		if ($this->input->post('categoria')!='') {
			$data['nome']=$this->input->post('categoria');
			$data['modified'] =  date("Y-m-d H:i:s");
			$this->db->where('id',$this->input->post('categoriagrupo_id'));
			$this->db->update('bud_categorias', $data); 
		}
		header("Location: ".$this->input->post('url'));
		die();
	}
	
	public function adicionaCategoriaItem() {
		if ($this->input->post('categoria')!='') {
			if ($this->input->post('categoriaitem_id')==0) {
				$data['nome']=$this->input->post('categoria');
				$data['catmaster_id']=$this->input->post('categoriagrupo_id');
				$sql1 = "SELECT max(ordem) as max_ordem FROM `bud_categoriasitens` WHERE catmaster_id='" . $data['catmaster_id'] . "'";
				$query = $this->db->query($sql1);
				$data['ordem']=$query->row(0)->max_ordem+1;
				$data['created'] =  date("Y-m-d H:i:s");
				$data['modified'] =  date("Y-m-d H:i:s");
				$this->db->insert('bud_categoriasitens', $data);
			} else {
				$data['nome']=$this->input->post('categoria');
				$data['modified'] =  date("Y-m-d H:i:s");
				$this->db->where('id',$this->input->post('categoriaitem_id'));
				$this->db->update('bud_categoriasitens', $data); 
			}
		}
		header("Location: ".$this->input->post('url'));
		die();
	}
	
	public function alteraBudget() {
		function toNumber($target) {
			$switched = str_replace(',', '.', $target);
			if(is_numeric($target)){
				return floatval($target);
			}elseif(is_numeric($switched)){
				return floatval($switched);
			} else {
				return "invalido";
			}
		}
		if ($this->input->post('categoriaitem_id')!='') {
			$data['profile_id'] = $this->profile->id;
			$tempDate = strtotime(substr($this->input->post('mesano'),0,4)."-".substr($this->input->post('mesano'),4,2)."-01");
			$data['date'] = date("Y-m-d",$tempDate);
			$data['categoriaitem_id'] = $this->input->post('categoriaitem_id');
			if (toNumber($this->input->post('budget_valor')) != "ivalido")
				$data['valor'] = toNumber($this->input->post('budget_valor'));
			else 
				$data['valor'] = 0;
			
			$sql1 = "SELECT `id` FROM `bud_budgets` WHERE (`date`='" . $data['date'] . "' AND `categoriaitem_id`='". $data['categoriaitem_id'] . "')";
			$query = $this->db->query($sql1);
			
			
			if ($query->num_rows() > 0) {
				$where = array('id' => $query->row(0)->id);
				$this->db->update('bud_budgets', $data, $where);
				
			} else {
				$this->db->insert('bud_budgets', $data);
			}			
			var_dump($query);
			var_dump($data);
			var_dump($where);
		}
	}
	
	public function chartMonth($profile_uid) {
		for ($i=0;$i<6;$i++) {
			$datas[$i] = date("Y-m-d",strtotime("-".(5-$i)." month"));
			$categorias[$i] = date_format(date_create($datas[$i]),'ym');
			$getReceita = $this->vw_budgets->get_receitas($profile_uid,date_format(date_create($datas[$i]),'Ym'));
			$receitas[$i] = array_sum(array_column($getReceita,'Receita'));
			$despesas[$i] = (-1)*array_sum(array_column($getReceita,'DespMes'));
			$patrimonio[$i] = array_sum(array_column($getReceita,'Patrimonio'));
		}
		
		$data['categorias'] = $categorias;
		$data['receitas'] = $receitas;
		$data['despesas'] = $despesas;
		$data['patrimonio'] = $patrimonio;		
	
		$data['modulo'] = "orcamento";
		$data['title'] = lang('sidemenu_links_budget_report');
		$data['page'] = $this->config->item('ci_budget_template_dir_admin') . "budgets_chart_month";
			
		$this->load->view($this->_container, $data);
	}
	
	public function index($profile_uid,$mesano='') {
		function mesAnterior($mes) {
			$mesant = substr($mes,4,2)-1;
			if ($mesant <1) {
				$mesant = 12;
				$anoant = substr($mes,0,4)-1;
			} else {
				$anoant = substr($mes,0,4);
			}
			
			$mesano_ant = sprintf('%04d',$anoant).sprintf('%02d',$mesant);
			return $mesano_ant;
		}
		
        if ($this->profile->user_id!==$this->logged_in_user_id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} else {
			if ($mesano!='') {
				$data['mesano']=$mesano;
				$where = array('profile_uid' => $profile_uid,'mesano' => $mesano);
			} else {
				$where = array('profile_uid' => $profile_uid);
				$mesano=date("Ym");
				$data['mesano']=$mesano;
			}
			$mesano_ant = mesAnterior($mesano);
			//Para esta função, vamos pegar do BD todas as categorias deste profile-
			//Todos os budegets e gastos realizados para este profile até o mes atual (bola fehcada)
			//E ai vamos verificar cada item para montar o relatório de orçamento
			$where = array('profile_uid'=>$profile_uid);
			$categorias = $this->vw_categorias->get_all('',$where,'','','ordem_grupo,ordem');
			$where['mesano <='] = $mesano;
			$budgets = $this->vw_mes_budget->get_all('',$where,'','','');
			$gastos = $this->vw_mes_gasto->get_all('',$where,'','','');
			$receitas = $this->vw_receitas->get_receitas($profile_uid);
			
			//Aqui nós definimos o limite de nossa busca, ou seja, o primeiro mes que houve orçamento ou gasto e o ultimo na mesma situação:
			$bMinmesano = (isset($budgets['minmesano']) ? +$budgets['minmesano'] : 999999);
			$bMaxmesano = (isset($budgets['maxmesano']) ? +$budgets['maxmesano'] : 0);
			$gMinmesano = (isset($gastos['minmesano']) ? +$gastos['minmesano'] : 999999);
			$gMaxmesano = (isset($gastos['maxmesano']) ? +$gastos['maxmesano'] : 0);
			
			$minmesano = min($bMinmesano,$gMinmesano);
			$maxmesano = max($bMaxmesano,$gMaxmesano,$mesano);
			
			if ($minmesano !=999999) {
				//Vamos olhar mês a mês para ver como nos saímos em cada um
				//Ponto de observação: aqui temos um loop que obviamente vai ficando cada vez pior (quantos mais meses no orçamento)
				//Porem faz diferença em performance?
				for ($i=$minmesano;$i<=$maxmesano;$i++) {
					//O ano só tem 12 meses, o mesano é dado no formato AAAAMM, os dois IFs a seguir forçam que só busquemos datas válidas:
					if (substr($i,-2) > 12) {
						$ano = +substr($i,0,4)+1;
						$i = $ano."01";
					}
					if (substr($i,-2) < 1) {
						$ano = +substr($i,0,4)-1;
						$i = $ano."12";
					}
					//Abaixo recuperamos os valores ou iniciamos as variáveis para cada mês (detalhes em cada linha)
					$matrizano[$i]['budgeted'] = 0; // a cada categoria verificaremos quanto o usuario alocou e somamos para chegar ao total orçado no mes
					$matrizano[$i]['sobregasto'] = 0; //a cada categoria verificaremos se foi gasto mais do que o orçado, neste caso os valores de sobregasto serão descontados do mês seguinte
					$matrizano[$i]['gastos'] = 0;
					//Beleza, se passou pelo IF, entao estamos no range correto:
					foreach ($categorias as $categoria) {
						//Agora que estamos em um mesano válido, vamos conferir todas as categorias:
						//Se houve gasto na categoria, orçamento, qual o saldo, etc...
						$matrizano[$i][$categoria['id']]['categoriaitem_id'] = +$categoria['id'];
						$matrizano[$i][$categoria['id']]['categoriaitem'] = $categoria['categoria'];
						$matrizano[$i][$categoria['id']]['cat_ordem'] = +$categoria['ordem'];
						$matrizano[$i][$categoria['id']]['categoria_grupo_id'] = +$categoria['categoria_grupo_id'];
						$matrizano[$i][$categoria['id']]['categoria_grupo'] = $categoria['categoria_grupo'];
						$matrizano[$i][$categoria['id']]['grupo_ordem'] = +$categoria['ordem_grupo'];
						$matrizano[$i][$categoria['id']]['carryNegValues'] = $categoria['carryNegValues'];
						$matrizano[$i][$categoria['id']]['budgetMes'] = (isset($budgets[$i][$categoria['id']]['budgetMes']) ? +$budgets[$i][$categoria['id']]['budgetMes'] : 0 );
						$matrizano[$i]['budgeted'] += $matrizano[$i][$categoria['id']]['budgetMes'];
						$matrizano[$i][$categoria['id']]['gastoMes'] = (isset($gastos[$i][$categoria['id']]['gastoMes']) ? +$gastos[$i][$categoria['id']]['gastoMes'] : 0 );
						$matrizano[$i]['gastos'] += $matrizano[$i][$categoria['id']]['gastoMes'];
						//Agora que temos os valores de budget e gasto da categoria
						//conseguimos calcular se foi orçado mais do que foi o gasto, ou vice-versa
						$diferenca = +$matrizano[$i][$categoria['id']]['budgetMes']+$matrizano[$i][$categoria['id']]['gastoMes'];
						
						//precisamos ver o saldo da categoria no Mês anterior...
						$saldoAnterior = (isset($matrizano[$this->mesAnterior($i)][$categoria['id']]['Disponivel']) ? $matrizano[$this->mesAnterior($i)][$categoria['id']]['Disponivel'] : 0);
						//... para ver se neste mês ficou positivo ou não o balanço
						$saldoAtual = +$saldoAnterior+$diferenca;
						
						if ($saldoAtual >= 0)
							$matrizano[$i][$categoria['id']]['Disponivel'] = $saldoAtual; //se o saldo é positivo, ótimo, acumula pro mes seguinte
						elseif ($categoria['carryNegValues']==1) //se é negativo, mas é uma categoria reembolsável que acumula saldo negativo, rolamos para o mês seguinte
							$matrizano[$i][$categoria['id']]['Disponivel'] = $saldoAtual;
						else { // porém se é negativo e é uma categoria que zera saldo negativo todo mês, então zeramos ela e descontamos do orçamento do proximo mês como sobregasto
							if ($i==$mesano) { //caso o loop atual seja o do mês atual, então muda um pouco
								//Aqui queremos mostrar ao usuário (dar feedback) quais itens estão estourando e não jogar como sobreGastoAnt
								$matrizano[$i][$categoria['id']]['Disponivel'] = $saldoAtual;
							} else {
								$matrizano[$i][$categoria['id']]['Disponivel'] = 0;
								$matrizano[$i][$categoria['id']]['sobregasto'] = $saldoAtual;
								$matrizano[$i]['sobregasto'] += (-1)*$saldoAtual;
							}
						}
					}
					//Agora que temos o valor orcado total, os sobregastos
					//Só nos falta recuperar as receitas que tivemos este mês e os saldos do mês anterior para termos uma visão completa de como estamos:
					//Vamos começar:
					$sobreGastoAnt = (isset($matrizano[$this->mesAnterior($i)]['sobregasto']) ? $matrizano[$this->mesAnterior($i)]['sobregasto'] : 0);
					$sobreOrcadoAnt = (isset($matrizano[$this->mesAnterior($i)]['sobreorcado']) ? $matrizano[$this->mesAnterior($i)]['sobreorcado'] : 0);
					$saldoAnt = (isset($matrizano[$this->mesAnterior($i)]['saldoPosAnt']) ? $matrizano[$this->mesAnterior($i)]['saldoPosAnt'] : 0);
					$receitaBruta = (isset($receitas[$i]) ? $receitas[$i] : 0);
					$matrizano[$i]['receita_bruta'] = $receitaBruta;
					$matrizano[$i]['receita_ajustada'] = +$receitaBruta-$sobreGastoAnt-$sobreOrcadoAnt+$saldoAnt;
					$matrizano[$i]['excedente'] = +$sobreGastoAnt +$sobreOrcadoAnt;
					if (+$matrizano[$i]['budgeted'] > +$matrizano[$i]['receita_ajustada']) {
						$matrizano[$i]['sobreorcado'] = +$matrizano[$i]['budgeted']-$matrizano[$i]['receita_ajustada'];
						$matrizano[$i]['saldoPosAnt'] = 0;
					} else {
						$matrizano[$i]['sobreorcado'] = 0;
						$matrizano[$i]['saldoPosAnt'] = +$matrizano[$i]['receita_ajustada']-$matrizano[$i]['budgeted'];
					}
				}
				
				//Agora compilamos a matriz final:
				$dados['receita_bruta'] = $matrizano[$mesano]['receita_bruta'];
				$dados['receita_ajustada'] = $matrizano[$mesano]['receita_ajustada'];
				$dados['excedentes'] = $matrizano[$mesano]['excedente'];
				$dados['sobregasto_mes'] = $matrizano[$mesano]['sobregasto'];
				$dados['sobreorcado_mes'] = $matrizano[$mesano]['sobreorcado'];
				$dados['orcado'] = $matrizano[$mesano]['budgeted'];
				$dados['gastos'] = $matrizano[$mesano]['gastos'];
				foreach ($categorias as $categoria) {
					$matrizano[$i][$categoria['id']]['cat_id'] = +$categoria['id'];
					$dados['itens'][$categoria['id']] = $matrizano[$mesano][$categoria['id']];
				}
			} else { //caso não haja nada, nenhum gasto, nenhum orçamento, NAAADA, inciamos um vetor em branco:
				$receitaAcum = 0;
				foreach ($receitas as $key => $receita) {
					if (+$key <= $mesano) {
						$receitaAcum += +$receita;
					}
				}
				$dados['receita_bruta'] = $receitaAcum;
				$dados['receita_ajustada'] = $dados['receita_bruta'];
				$dados['excedentes'] = 0;
				$dados['orcado'] = 0;
				$dados['gastos'] = 0;
				foreach ($categorias as $categoria) {
					$dados['itens'][$categoria['id']]['categoriaitem_id'] = +$categoria['id'];
					$dados['itens'][$categoria['id']]['categoriaitem'] = $categoria['categoria'];
					$dados['itens'][$categoria['id']]['cat_ordem'] = +$categoria['ordem'];
					$dados['itens'][$categoria['id']]['categoria_grupo_id'] = +$categoria['categoria_grupo_id'];
					$dados['itens'][$categoria['id']]['categoria_grupo'] = $categoria['categoria_grupo'];
					$dados['itens'][$categoria['id']]['grupo_ordem'] = +$categoria['ordem_grupo'];
					$dados['itens'][$categoria['id']]['carryNegValues'] = $categoria['carryNegValues'];
					$dados['itens'][$categoria['id']]['budgetMes'] = 0;
					$dados['itens'][$categoria['id']]['gastoMes'] = 0;
					$dados['itens'][$categoria['id']]['Disponivel'] = 0;
				}
			}
			
			//Aqui verificamos se há transações não classificadas:
			$sql = "SELECT `t2`.`profile_id`, sum(countNClas) as countNClas FROM `vw_transacoes_classificadas` `t1` JOIN `bud_contas` `t2` on `t1`.`conta_id` = `t2`.`id` WHERE `profile_id` = ".$this->profile->id." GROUP BY `profile_id`";
			$query = $this->db->query($sql);
			$data['countNClas']=$query->row(0)->countNClas;
			
			//fim
			$receitaMes = $dados['receita_ajustada'];
			$budgetMes = $dados['orcado'];
			//Beleza, agora que temos todos os dados, vamos montar o relatório:
			$data['ReceitaMes'] = $receitaMes;
			$data['ReceitaBruta'] = $dados['receita_bruta'];
			$data['sobreGastoMesAnterior'] = $dados['excedentes'];
			
			$data['modulo'] = "orcamento";
			$data['title'] = lang('sidemenu_links_budgets');
			
			$data['budgetMes'] = $budgetMes;
			$data['gastosMes'] = $dados['gastos'];
			$data['receitas']=0;
			$data['receitasAnterior']=0;
			$data['budgets'] = $dados['itens'];
			$data['profile_uid'] = $profile_uid;
			$data['page'] = $this->config->item('ci_budget_template_dir_admin') . "budgets_list";
			/*$data['lista'] = $dados;
			$data['page'] = $this->config->item('ci_budget_template_dir_admin') . "test";
			*/	
			$this->load->view($this->_container, $data);
		}
	}
	
}
