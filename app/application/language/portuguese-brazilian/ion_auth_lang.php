<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
* Name:  Ion Auth Lang - Portuguese (UTF-8)
*
* Author: André Brás Simões
*       andrebrassimoes@gmail.com
*
* Adjustments by @Dentxinho and @MichelAlonso and @marcelod
*
* Location: http://github.com/benedmunds/CodeIgniter-Ion-Auth
*
* Created:  17.05.2010
*
* Description:  Portuguese language file for Ion Auth messages and errors
*
*/

// Account Creation
$lang['account_creation_successful']         	= 'Conta criada com sucesso';
$lang['account_creation_unsuccessful']       	= 'Não foi possível criar a conta';
$lang['account_creation_duplicate_email']    	= 'Email em uso ou inválido';
$lang['account_creation_duplicate_identity'] 	= 'Nome de usuário em uso ou inválido';
$lang['account_creation_missing_default_group'] = 'Grupo padrão não está definido';
$lang['account_creation_invalid_default_group'] = 'Nome padrão do grupo definido é inválido';

// Password
$lang['password_change_successful']         = 'Senha alterada com sucesso';
$lang['password_change_unsuccessful']       = 'Não foi possível alterar a senha';
$lang['forgot_password_successful']         = 'Nova senha enviada por email';
$lang['forgot_password_unsuccessful']       = 'Não foi possível criar uma nova senha';

// Activation
$lang['activate_successful']                = 'Conta ativada';
$lang['activate_unsuccessful']              = 'Não foi possível ativar a conta';
$lang['deactivate_successful']              = 'Conta desativada';
$lang['deactivate_unsuccessful']            = 'Não foi possível desativar a conta';
$lang['activation_email_successful']        = 'Email de ativação enviado com sucesso';
$lang['activation_email_unsuccessful']      = 'Não foi possível enviar o email de ativação';

// Login / Logout
$lang['login_successful']                   = 'Sessão iniciada com sucesso';
$lang['login_unsuccessful']                 = 'Usuário ou senha inválidos';
$lang['login_unsuccessful_not_active']      = 'A conta está desativada';
$lang['login_timeout']                      = 'Conta temporariamente bloqueada. Tente novamente mais tarde';
$lang['logout_successful']            		= 'Sessão encerrada com sucesso';

// Account Changes
$lang['update_successful']                  = 'Informações da conta atualizadas com sucesso';
$lang['update_unsuccessful']                = 'Não foi possível atualizar as informações da conta';
$lang['delete_successful']                  = 'Usuário excluído com sucesso';
$lang['delete_unsuccessful']                = 'Não foi possível excluir o usuário';

// Groups
$lang['group_creation_successful']          = 'Grupo criado com sucesso';
$lang['group_already_exists']               = 'Um grupo com este nome já existe';
$lang['group_update_successful']            = 'Dados do grupo atualizados com sucesso';
$lang['group_delete_successful']            = 'Grupo excluído com sucesso';
$lang['group_delete_unsuccessful']          = 'Não foi possível excluir o grupo';
$lang['group_delete_notallowed']    		= 'Não é possível excluir o grupo de administradores';
$lang['group_name_required'] 				= 'Nome do grupo é um campo obrigatório';
$lang['group_name_admin_not_alter'] 		= 'Nome do grupo administrador não pode ser alterado';

// Activation Email
$lang['email_activation_subject']           = 'Ativação da conta';
$lang['email_activate_heading']    			= 'Ative sua conta para %s';
$lang['email_activate_subheading'] 			= 'Por favor, clique neste link para %s.';
$lang['email_activate_link']       			= 'Ative sua conta';

// Forgot Password Email
$lang['email_forgotten_password_subject']   = 'Esqueci a senha';
$lang['email_forgot_password_heading']    	= 'Redefinido a senha para %s';
$lang['email_forgot_password_subheading'] 	= 'Por favor, clique neste link para %s.';
$lang['email_forgot_password_link']       	= 'Redefina sua senha';

// New Password Email
$lang['email_new_password_subject']         = 'Nova senha';
$lang['email_new_password_heading']    		= 'Nova senha para %s';
$lang['email_new_password_subheading'] 		= 'Sua senha foi redefinida para: %s';
$lang['error_csrf'] = 'O envio desse formulario não atendeu a requisitos de segurança.';

// Login
$lang['login_heading']         = 'Login';
$lang['login_subheading']      = 'Por favor entre com seu login/email e senha abaixo.';
$lang['login_identity_label']  = 'Login/Email:';
$lang['login_password_label']  = 'Senha:';
$lang['login_remember_label']  = 'Lembre-me:';
$lang['login_submit_btn']      = 'Login';
$lang['login_forgot_password'] = 'Esqueceu sua senha?';

// Index
$lang['index_heading']           = 'Usuários';
$lang['index_subheading']        = 'Abaixo uma lista com os usuários.';
$lang['index_fname_th']          = 'Nome';
$lang['index_lname_th']          = 'Sobrenome';
$lang['index_email_th']          = 'Email';
$lang['index_groups_th']         = 'Grupos';
$lang['index_status_th']         = 'Estado';
$lang['index_action_th']         = 'Ação';
$lang['index_active_link']       = 'Ativo';
$lang['index_inactive_link']     = 'Inativo';
$lang['index_create_user_link']  = 'Criar novo usuário';
$lang['index_create_group_link'] = 'Criar novo grupo';

// Deactivate User
$lang['deactivate_heading']                  = 'Desativar Usuário';
$lang['deactivate_subheading']               = 'Você tem certeza que deseja desativar esse usuário \'%s\'';
$lang['deactivate_confirm_y_label']          = 'Sim:';
$lang['deactivate_confirm_n_label']          = 'Não:';
$lang['deactivate_submit_btn']               = 'Enviar';
$lang['deactivate_validation_confirm_label'] = 'confirmação';
$lang['deactivate_validation_user_id_label'] = 'user ID';

// Create User
$lang['create_user_heading']                           = 'Criar Usuário';
$lang['create_user_subheading']                        = 'Por favor informe os dados do usuário.';
$lang['create_user_fname_label']                       = 'Nome:';
$lang['create_user_lname_label']                       = 'Sobrenome:';
$lang['create_user_identity_label']                    = 'Identity:';
$lang['create_user_company_label']                     = 'Empresa:';
$lang['create_user_email_label']                       = 'Email:';
$lang['create_user_phone_label']                       = 'Telefone:';
$lang['create_user_password_label']                    = 'Senha:';
$lang['create_user_password_confirm_label']            = 'Confirmar senha:';
$lang['create_user_submit_btn']                        = 'Criar Usuário';
$lang['create_user_validation_fname_label']            = 'Nome';
$lang['create_user_validation_lname_label']            = 'Sobrenome';
$lang['create_user_validation_identity_label']         = 'Identity';
$lang['create_user_validation_email_label']            = 'Email';
$lang['create_user_validation_phone1_label']           = 'Primeira parte do telefone';
$lang['create_user_validation_phone2_label']           = 'Segunda parte do telefone';
$lang['create_user_validation_phone3_label']           = 'Terceira parte do telefone';
$lang['create_user_validation_company_label']          = 'Empresa';
$lang['create_user_validation_password_label']         = 'Senha';
$lang['create_user_validation_password_confirm_label'] = 'Confirmação de Senha';

// Edit User
$lang['edit_user_heading']                           = 'Editar Usuário';
$lang['edit_user_subheading']                        = 'Por favor informe os dados sobre o usuário abaixo.';
$lang['edit_user_fname_label']                       = 'Nome:';
$lang['edit_user_lname_label']                       = 'Sobrenome:';
$lang['edit_user_company_label']                     = 'Empresa:';
$lang['edit_user_email_label']                       = 'Email:';
$lang['edit_user_phone_label']                       = 'Telefone:';
$lang['edit_user_password_label']                    = 'Senha: (se quiser mudar a senha)';
$lang['edit_user_password_confirm_label']            = 'Confirme a senha: (se quiser mudar a senha)';
$lang['edit_user_groups_heading']                    = 'Membro dos grupos';
$lang['edit_user_submit_btn']                        = 'Salvar Usuário';
$lang['edit_user_validation_fname_label']            = 'Nome';
$lang['edit_user_validation_lname_label']            = 'Sobrenome';
$lang['edit_user_validation_email_label']            = 'Email';
$lang['edit_user_validation_phone1_label']           = 'Primeira parte do fone';
$lang['edit_user_validation_phone2_label']           = 'Segunda parte do fone';
$lang['edit_user_validation_phone3_label']           = 'Terceira parte do fone';
$lang['edit_user_validation_company_label']          = 'Empresa';
$lang['edit_user_validation_groups_label']           = 'Grupos';
$lang['edit_user_validation_password_label']         = 'Senha';
$lang['edit_user_validation_password_confirm_label'] = 'Confirme a senha';

// Create Group
$lang['create_group_title']                  = 'Criar Grupo';
$lang['create_group_heading']                = 'Criar Grupo';
$lang['create_group_subheading']             = 'Por favor informe os dados sobre o grupo abaixo.';
$lang['create_group_name_label']             = 'Nome:';
$lang['create_group_desc_label']             = 'Descrição:';
$lang['create_group_submit_btn']             = 'Criar Grupo';
$lang['create_group_validation_name_label']  = 'Nome';
$lang['create_group_validation_desc_label']  = 'Descrição';

// Edit Group
$lang['edit_group_title']                  = 'Editar Grupo';
$lang['edit_group_saved']                  = 'Grupo Salvo';
$lang['edit_group_heading']                = 'Editar Group';
$lang['edit_group_subheading']             = 'Por favor informe os dados sobre o grupo abaixo.';
$lang['edit_group_name_label']             = 'Nome:';
$lang['edit_group_desc_label']             = 'Descrição:';
$lang['edit_group_submit_btn']             = 'Salvar Grupo';
$lang['edit_group_validation_name_label']  = 'Nome';
$lang['edit_group_validation_desc_label']  = 'Descrição';

// Change Password
$lang['change_password_heading']                               = 'Mudar Senha';
$lang['change_password_old_password_label']                    = 'Senha Antiga:';
$lang['change_password_new_password_label']                    = 'Nova senha: (mínimo de %s caracteres)';
$lang['change_password_new_password_confirm_label']            = 'Confirme sua Nova Senha:';
$lang['change_password_submit_btn']                            = 'Mudar senha!';
$lang['change_password_validation_old_password_label']         = 'Senha Antiga';
$lang['change_password_validation_new_password_label']         = 'Nova Senha';
$lang['change_password_validation_new_password_confirm_label'] = 'Confirme sua Nova Senha';

// Forgot Password
$lang['forgot_password_heading']                 = 'Esqueceu a Senha';
$lang['forgot_password_subheading']              = 'Por favor, informe seu %s para que possamos enviar para você uma mensagem para recuparar sua senha.';
$lang['forgot_password_email_label']             = '%s:';
$lang['forgot_password_submit_btn']              = 'Enviar';
$lang['forgot_password_validation_email_label']  = 'Email';
$lang['forgot_password_username_identity_label'] = 'Login';
$lang['forgot_password_email_identity_label']    = 'Email';
$lang['forgot_password_email_not_found']         = 'Este email não foi encontrado.';

// Reset Password
$lang['reset_password_heading']                               = 'Mudar Senha:';
$lang['reset_password_new_password_label']                    = 'Nova senha: (mínimo de %s caracteres)';
$lang['reset_password_new_password_confirm_label']            = 'Confirme sua Nova Senha:';
$lang['reset_password_submit_btn']                            = 'Mudar';
$lang['reset_password_validation_new_password_label']         = 'Senha Antiga';
$lang['reset_password_validation_new_password_confirm_label'] = 'Confirme sua Nova Senha';

//User Profile
$lang['user_profile_heading']									= 'Editar perfil do usuário';
$lang['user_select_language']									= 'Selecione o idioma a ser utilizado:';
$lang['user_select_language_button']							= 'Selecionar idioma!';
$lang['user_back_to_home']										= 'Voltar a homepage';