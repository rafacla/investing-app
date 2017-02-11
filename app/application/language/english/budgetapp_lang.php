<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//Sidemenu
$lang['sidemenu_profile_change'] = 'Switch profile';
$lang['sidemenu_profile_newbegin'] = 'New begin';
$lang['sidemenu_profile_edit'] = 'Language/password';
$lang['sidemenu_profile_logout'] = 'Logout';

$lang['sidemenu_links_accounts'] = 'All accounts';
$lang['sidemenu_links_budgets'] = 'Budget';

$lang['sidemenu_accounts_summary'] = 'Accounts:';
$lang['sidemenu_accounts_add'] = 'Add';

//Profiles open
$lang['profiles_select_heading'] 		= 'Select a profile to continue:';
$lang['profiles_select_noresults'] 		= 'No profile has been found.';
$lang['profiles_create_heading'] 		= 'Or create a new one:';
$lang['profiles_create_placeholder'] 	= 'Profile name';
$lang['profiles_create_submit']		 	= 'Create new!';

//Accounts controller
$lang['accounts_name_all']								= 'All Accounts';
$lang['accounts_value_cleared']							= 'Cleared balance';
$lang['accounts_value_not_cleared']						= 'Uncleared balance';
$lang['accounts_value_working']							= 'Working balance';
$lang['accounts_errors_subtransaction']					= 'This is a subtransaction and cannot be edited directly.';
$lang['accounts_list_addtransaction']					= 'Add transaction';
$lang['accounts_list_edittransaction']					= 'Edit';
$lang['accounts_list_edittransaction_markcleared']		= 'Mark as cleared';
$lang['accounts_list_edittransaction_marknotcleared']	= 'Mark as uncleared';
$lang['accounts_list_edittransaction_delete']			= 'Delete selected';
$lang['accounts_list_importOFX']						= 'Import OFX';
$lang['accounts_heads_account']							= 'Account';
$lang['accounts_heads_date']							= 'Date';
$lang['accounts_heads_payee']							= 'Payee';
$lang['accounts_heads_category']						= 'Category';
$lang['accounts_heads_memo']							= 'Memo';
$lang['accounts_heads_outflow']							= 'Outflow';
$lang['accounts_heads_inflow']							= 'Inflow';
$lang['accounts_heads_balance']							= 'Balance';
$lang['accounts_multiple_transfer']						= '(Multiple categories/transfer)...';
$lang['accounts_no_category']							= 'Categorize this item!';
$lang['accounts_add_subtransaction']					= 'Subtransaction/Transfer';
$lang['accounts_add_transferto']						= 'Transfer to:';
$lang['accounts_value_left']							= 'Amount remaining to assign:';
$lang['accounts_button_save']							= 'Save';
$lang['accounts_button_cancel']							= 'Cancel';
$lang['accounts_ofx_select'] 							= 'Select an OFX file to import transactions:';
$lang['accounts_ofx_button'] 							= 'Open a file...';
$lang['accounts_ofx_heading'] 							= 'OFX file import';
$lang['accounts_button_close']							= 'Close';
$lang['accounts_button_import']							= 'Import';

//Accounts JS
$lang['accountsJS_error_noaccounts'] = 'You must add an account before add a transaction!';
$lang['accountsJS_error_remaining_value'] ='The subtransactions values of your transaction don\'t match.\n\nFix it using the value at \'Amount remaining to asign\'!';
$lang['accountsJS_error_noaccounts_found'] = 'Account not found';