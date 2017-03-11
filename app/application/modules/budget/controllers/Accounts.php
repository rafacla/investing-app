<?php

class Accounts extends Admin_Controller {
    function __construct() {
        parent::__construct();
		
		$this->load->helper('date');
		$this->load->library('session');
		$this->load->model(array('budget/user'));
        $this->load->model(array('budget/account'));
		$this->load->model(array('budget/conta'));
		$this->load->model(array('budget/vw_categorias'));
		$this->load->model(array('budget/vw_contas_saldo'));
    }

    public function index($profile_uid,$conta_id='') {
		//$profile_data = $this->profile->get($profile_uid);
		
		
        if ($this->profile->user_id!==$this->logged_in_user_id) {
			die ("<link href=\"" . base_url() . "assets/admin/css/bootstrap.min.css\" rel=\"stylesheet\"><div class=\"alert alert-danger\" role=\"alert\">Parece que você está tentando acessar informações que não te pertencem ;)</div>");
		} else {
			
			$contas = $this->conta->get_all(array('id','conta_nome'),array('profile_uid'=>$profile_uid));
			$categorias = $this->vw_categorias->get_all('',array('categoria !=' => NULL,'profile_uid'=>$profile_uid),'','','ordem_grupo, ordem','','profile_uid is NULL');
			
			if ($conta_id == '') {
				$accounts = $this->account->get_all('',array('profile_uid'=>$profile_uid),'','','data_un,valor');
				$data['contaNome'] = lang('accounts_name_all');
				$data['contaID'] = 0;
				$saldos = $this->vw_contas_saldo->get_all('',array('profile_uid'=>$profile_uid));
			} else {
				$contaNome = $this->conta->get($conta_id)->conta_nome;
				$data['contaID'] = $conta_id;
				
				$accounts = $this->account->get_all('',array('profile_uid' => $profile_uid,'conta_id'=>$conta_id),'','','data_un,valor');
				
				$data['contaNome'] = $contaNome;
				$saldos = $this->vw_contas_saldo->get_all('',array('profile_uid'=>$profile_uid,'conta_id'=>$conta_id));
			}
			
			$data['saldo'] = array_sum(array_column($saldos,'saldo'));
			$data['saldo_conciliado'] = array_sum(array_column($saldos,'saldo_conciliado'));
			$data['accounts'] = $accounts;
			$data['contas'] = $contas;
			$data['categorias'] = $categorias;
			$data['profile_uid'] = $profile_uid;
			
			$data['page'] = $this->config->item('ci_budget_template_dir_admin') . "accounts_list";
			$this->load->view($this->_container, $data);
		}
    }

    public function create() {
        $data['page'] = $this->config->item('ci_budget_template_dir_admin') . "accounts_create";
        $this->load->view($this->_container, $data);
    }

    /*public function edit($id) {
        $data['page'] = $this->config->item('ci_budget_template_dir_admin') . "accounts_edit";
        $this->load->view($this->_container, $data);
    }*/
	public function edit($id) {
        if ($this->input->post('sacado_nome')) {
            $sacado_nome = $this->input->post('sacado_nome');

            $transacaoitem_update = $this->account->update($sacado_nome,$id);

            redirect('/budget/accounts', 'refresh');
        }
    }
	
	public function editaConciliado() {
		$data['conciliado'] = $this->input->post('conciliado');
		$where = "id = " .  $this->input->post('transacaoID');
		$sql = $this->db->update_string('bud_transacoes', $data, $where);
		$this->db->query($sql);
	}
	
	public function editaTransacao() {
		if ($this->input->post('countTr')) {
			$aTritem_ID = [];
			$countTr = $this->input->post('countTr');
			$valor = ($this->input->post('totalEntrada')-$this->input->post('totalSaida'));
			//inserir/atualizar a transacao, depois vemos as filhas:
			
			$data['conta_id'] = $this->input->post('contaID');
			$dataA = explode('/',$this->input->post('dataTr'));
			$dataF = $dataA[2] . '-' . $dataA[1] . '-' . $dataA[0];
			
			$data['data'] = $dataF;
			$data['sacado_nome'] = $this->input->post('sacado');
			$data['memo'] = $this->input->post('memo');
			$data['valor'] = $valor;
			$data['modified'] = date("Y-m-d H:i:s");
			$resposta = array();
			if ($this->input->post('transacaoID')) {
				$where = "id = " .  $this->input->post('transacaoID');
				$sql = $this->db->update_string('bud_transacoes', $data, $where);
				$transacaoID = $this->input->post('transacaoID');
				$this->db->query($sql);
				$id = $transacaoID;
				array_push($resposta,$transacaoID);
			} else {
				$data['created'] =  date("Y-m-d H:i:s");
				$sql = $this->db->insert_string('bud_transacoes', $data);
				$this->db->query($sql);
				$id = $this->db->insert_id();
				$transacaoID = $id;
				array_push($resposta,$transacaoID);
			}
			
			//insere/atualiza subtransacoes
			unset($subData);					
			if ($this->input->post('split')=="false") {
				if (intval($this->input->post('categoria'))==0)
					$subData['categoria_id'] = NULL;
				else 
					$subData['categoria_id'] = intval($this->input->post('categoria'));
				$subData['transf_para_conta_id'] = NULL;
				$subData['transacao_id'] = $transacaoID;
				$subData['valor'] = floatval($this->input->post('totalEntrada')) - floatval($this->input->post('totalSaida'));
				if ($this->input->post('tritem_id')) {
					$subwhere = "id = " . $this->input->post('tritem_id');
					$subData['modified'] = date("Y-m-d H:i:s");
					$subsql = $this->db->update_string('bud_transacoesitens',$subData,$subwhere);
					$this->db->query($subsql);
					array_push($aTritem_ID,$this->input->post('tritem_id'));
				} else {
					$subData['created'] = date("Y-m-d H:i:s");
					$subData['modified'] = date("Y-m-d H:i:s");
					$subsql = $this->db->insert_string('bud_transacoesitens',$subData);
					$this->db->query($subsql);
					array_push($aTritem_ID,$this->db->insert_id());
				}
			} else {
				for ($i=0;$i<$countTr;$i++) {
					unset($subData);
					unset($subwhere);
					$subData['transacao_id'] = $transacaoID;
					if ($this->input->post('transferir_para_id_' . $i)!="") {
						if (intval($this->input->post('transferir_para_id_' . $i))==0) 
							$subData['transf_para_conta_id'] = NULL;
						else
							$subData['transf_para_conta_id'] = intval($this->input->post('transferir_para_id_' . $i));
						$subData['categoria_id'] = NULL;
					} else {
						$subData['transf_para_conta_id'] = NULL;
						if (intval($this->input->post('categoria_' . $i))==0)
							$subData['categoria_id'] = NULL;
						else
							$subData['categoria_id'] = intval($this->input->post('categoria_' . $i));
					}
					$subData['valor'] = floatval($this->input->post('entrada_' . $i)) - floatval($this->input->post('saida_' . $i));
					unset($subsql);
					if ($this->input->post('tritem_id_' . $i)) {
						$subwhere = "id = " . $this->input->post('tritem_id_' . $i);
						$subData['modified'] = date("Y-m-d H:i:s");
						$subsql = $this->db->update_string('bud_transacoesitens',$subData,$subwhere);
						$this->db->query($subsql);
						array_push($aTritem_ID,$this->input->post('tritem_id_' . $i));
					} else {
						$subData['created'] = date("Y-m-d H:i:s");
						$subData['modified'] = date("Y-m-d H:i:s");
						$subsql = $this->db->insert_string('bud_transacoesitens',$subData);
						$this->db->query($subsql);
						array_push($aTritem_ID,$this->db->insert_id());
					}
				}
			}
			//deleta subtransacoes removidas:
			if ($id ==0) {
				$tritens = $this->account->get_all(array('tritem_id'),array('transacao_id' => $this->input->post('transacaoID')),'','','',"tritem_id");
			} else {				
				$tritens = $this->account->get_all(array('transacao_id','tritem_id'),array('transacao_id' => $id),'','','',"tritem_id");
			}
			if (count($tritens)) {
				foreach ($tritens as $key => $list) {
					if (!in_array($list['tritem_id'],$aTritem_ID)) {
						$this->db->delete('bud_transacoesitens', array('id' => $list['tritem_id']));
					}
				}
			}
			array_push($resposta,count($aTritem_ID));
			array_push($resposta,$aTritem_ID);
			echo json_encode($resposta);
        }
    }
	
	public function deletaTransacao() {
		if (intval($this->input->post('trid'))!=0) {
			$this->db->delete('bud_transacoesitens', array('transacao_id' => intval($this->input->post('trid'))));
			$this->db->delete('bud_transacoes', array('id' => intval($this->input->post('trid'))));
		}
	}
	
	public function validateDate($date) {
		$d = DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') === $date;
	}
	
	public function importaOFX() {
		function toNumber($target) {
			$switched = str_replace(',', '.', $target);
			/*if(is_numeric($target)){
				return floatval($target);
			}elseif(is_numeric($switched)){
				return floatval($switched);
			}
			*/
			if(is_numeric($switched)){
				log_message('error',$target."-".$switched);
				return floatval($switched);
			}
		}
		$ofx = (json_decode($this->input->post('OFX'),true));
		$countInserido = 0;
		if (count($ofx["statement"]["transactions"])) {
			foreach ($ofx["statement"]["transactions"] as $key => $list) {
				unset($data);
				if (strtotime(substr($list['date']["date"],0,10))) {
					$date = date('Y-m-d',strtotime(substr($list['date']["date"],0,10)));
					$data['conta_id']=$this->input->post('conta');
					$data['data']=$date;
					if ($list['name']=="")
						$data['sacado_nome']= $list['memo'];
					else {
						$data['sacado_nome']= $list['name'];
						$data['memo']= $list['memo'];
					}
					$data['valor']= toNumber($list['amount']);
					$data['created'] =  date("Y-m-d H:i:s");
					$data['modified'] =  date("Y-m-d H:i:s");
					$data['tranNum'] =  $list['uniqueId'] . $data['sacado_nome'] . $this->profile->id;
					$sql = $this->db->insert_string('bud_transacoes',$data) . " ON DUPLICATE KEY UPDATE tranNum=tranNum";
					$this->db->query($sql);
					if ($this->db->insert_id())
						$countInserido++;
				}
			}
			header("refresh:3; url=" . $this->input->post('old_url'));
			echo "<html><body>";
			echo "<p>Inseridas: " . $countInserido . " transações de um total de " . count($ofx["statement"]["transactions"]) . ".</p>";
			echo "<p>Transações repetidas foram ignoradas.</p>";
			echo "<p>Redirecionando em 2s.</p>";
			echo "</html></body>";
			die();
		}
	}
}
