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
    }

    public function index($profile_uid,$mesano='') {
		//$profile_data = $this->profile->get($profile_uid);
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
			
			
			//$sql1 = "SELECT ReceitaMes FROM `vw_receitas` WHERE profile_uid='" . $this->profile->uniqueid . "' AND mesano='" . $mesano . "'";
			$sql1 = "SELECT sum(ReceitaMes) AS ReceitaMes FROM `vw_sumaria_receita_budget_gasto` WHERE profile_uid='" . $this->profile->uniqueid . "' AND mesano<='" . $mesano . "'";
			$query = $this->db->query($sql1);
			if ($query->num_rows()>0)
				$receitaMes = $query->row(0)->ReceitaMes;
			else
				$receitaMes = 0;
			
			$sql1 = "SELECT sum(budgets) AS budgetMes, sum(despForaOrc) AS despForaOrc FROM `vw_sumaria_receita_budget_gasto` WHERE profile_uid='" . $this->profile->uniqueid . "' AND mesano<'" . $mesano . "'";
			$query = $this->db->query($sql1);
			if ($query->num_rows()>0) {
				$budgetMes = $query->row(0)->budgetMes;
				$despForaOrc = $query->row(0)->despForaOrc;
			}
			else {
				$budgetMes = 0;
				$despForaOrc = 0;
			}
			
			$data['ReceitaMes'] = $receitaMes - $budgetMes;
			/*$sql1 = "SELECT ReceitaMes FROM `vw_receitas` WHERE profile_uid='" . $this->profile->uniqueid . "' AND mesano='" . $mesano_ant . "'";
			$query = $this->db->query($sql1);
			if ($query->num_rows()>0)
				$receitaAnterior = $query->row(0)->ReceitaMes;
			else
				$receitaAnterior = 0;
			
			*/
			$sql1 = "SELECT sum(budgetMes) AS BudgetMes FROM `vw_mes_budget` WHERE profile_uid='" . $this->profile->uniqueid . "' AND mesano='" . $mesano_ant . "'";
			$query = $this->db->query($sql1);
			if ($query->num_rows()>0)
				$budgetAnterior = $query->row(0)->BudgetMes;
			else
				$budgetAnterior = 0;
			
			//$data['SaldoMesAnterior'] = $receitaAnterior-$budgetAnterior;
			
			$budgets_ant = $this->vw_budgets->get_receitas($profile_uid,$mesano);
			
			$data['sobreGastoMesAnterior'] = (-1)*array_sum(array_column($budgets_ant,'Sobregasto'));
			
			$budgets = $this->vw_budgets->get_all_bymes($profile_uid,$mesano);
			$data['budgetMes'] = array_sum(array_column($budgets,'budgetMes'));
			$data['gastosMes'] = array_sum(array_column($budgets,'gastoMes'));
			$data['receitas']=0;
			$data['receitasAnterior']=0;
			$data['budgets'] = $budgets;
			$data['profile_uid'] = $profile_uid;
			$data['page'] = $this->config->item('ci_budget_template_dir_admin') . "budgets_list";
			
			$this->load->view($this->_container, $data);
		}
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
}
