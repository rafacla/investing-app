<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Sidemenu
$lang['sidemenu_profile_change'] 	= 'Trocar Livro';
$lang['sidemenu_profile_newbegin'] 	= 'Novo começo';
$lang['sidemenu_profile_edit'] 		= 'Idioma/senha';
$lang['sidemenu_profile_logout'] 	= 'Logout';

$lang['sidemenu_links_accounts'] 	= 'Todas as contas';
$lang['sidemenu_links_budgets'] 	= 'Orçamento';

$lang['sidemenu_accounts_summary'] 	= 'Contas:';
$lang['sidemenu_accounts_add'] 		= 'Adicionar';

//Profiles open
$lang['profiles_select_heading'] 		= 'Selecione um livro de orçamentos para continuar:';
$lang['profiles_select_noresults'] 		= 'Nenhum livro de orçamentos criados!';
$lang['profiles_create_heading'] 		= 'Ou crie um novo:';
$lang['profiles_create_placeholder'] 	= 'Nome do livro';
$lang['profiles_create_submit']		 	= 'Criar novo!';

//Accounts controller
$lang['accounts_name_all']								= 'Todas as contas';
$lang['accounts_value_cleared']							= 'Saldo conciliado';
$lang['accounts_value_not_cleared']						= 'Saldo não conciliado';
$lang['accounts_value_working']							= 'Saldo atual';
$lang['accounts_errors_subtransaction']					= 'Esta é uma subtransação de transferência e não pode ser editada diretamente.';
$lang['accounts_list_addtransaction']					= 'Adicionar transação';
$lang['accounts_list_edittransaction']					= 'Editar';
$lang['accounts_list_edittransaction_markcleared']		= 'Marcar como conciliado';
$lang['accounts_list_edittransaction_marknotcleared']	= 'Marcar como não conciliado';
$lang['accounts_list_edittransaction_delete']			= 'Excluir selecionadas';
$lang['accounts_list_importOFX']						= 'Importar OFX';
$lang['accounts_heads_account']							= 'Conta';
$lang['accounts_heads_date']							= 'Data';
$lang['accounts_heads_payee']							= 'Sacado';
$lang['accounts_heads_category']						= 'Categoria';
$lang['accounts_heads_memo']							= 'Memo';
$lang['accounts_heads_outflow']							= 'Saída';
$lang['accounts_heads_inflow']							= 'Entrada';
$lang['accounts_heads_balance']							= 'Saldo';
$lang['accounts_multiple_transfer']						= '(Múltiplas categorias/transferência)...';
$lang['accounts_no_category']							= 'Classifique este item!';
$lang['accounts_add_subtransaction']					= 'Subtransação/Transferência';
$lang['accounts_add_transferto']						= 'Transferir para:';
$lang['accounts_value_left']							= 'Faltando distribuir:';
$lang['accounts_button_save']							= 'Salvar';
$lang['accounts_button_cancel']							= 'Cancelar';
$lang['accounts_ofx_select'] 							= 'Selecione um arquivo OFX compátivel para importar transações:';
$lang['accounts_ofx_button'] 							= 'Escolher arquivo...';
$lang['accounts_ofx_heading'] 							= 'Importar arquivo OFX';
$lang['accounts_button_close']							= 'Fechar';
$lang['accounts_button_import']							= 'Importar';

//Accounts JS
$lang['accountsJS_error_noaccounts'] = 'Você deve adicionar uma conta antes de adicionar uma transação!';
$lang['accountsJS_error_remaining_value'] ='Os subitens da sua transação não coincidem com os valores da sua transação.\n\nCorrija os valores usando a linha Faltando distribuir!';
$lang['accountsJS_error_noaccounts_found'] = 'Conta não encontrada';