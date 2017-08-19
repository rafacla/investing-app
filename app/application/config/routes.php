<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'budget/home';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

//Rotas para a seção de contas do app
$route['home'] = 'budget/home';	
//Usuarios
$route['novousuario'] = 'budget/NewUsers/create';
$route['auth/activate/(:any)/(:any)'] = 'budget/NewUsers/activate/$1/$2';
$route['auth/reset_password/(:any)'] = 'budget/NewUsers/reset_password/$1';
$route['esqueciasenha'] = 'budget/NewUsers/forgot_password';
$route['user'] = 'auth/change_password';
$route['change_language'] = 'auth/change_language';

//classe profiles
$route['profiles'] = 'budget/profiles';	
$route['profiles/create'] = 'budget/profiles/create';	
$route['profiles/abreProfile/(:any)'] = 'budget/profiles/abreprofile/$1';
$route['profiles/deletaProfile/(:any)'] = 'budget/profiles/deletaprofile/$1';
//criaConta
$route['criaConta'] = 'budget/contas/criaConta/$1';
//classe accounts
$route['(:any)/accountslist'] = 'budget/accounts/list_accounts/$1';
$route['(:any)/accounts'] = 'budget/accounts/index/$1';
$route['editaTransacao'] = 'budget/accounts/editaTransacao';
$route['editaConciliado'] = 'budget/accounts/editaConciliado';
$route['importaOFX'] = 'budget/accounts/importaOFX';
$route['getTransacoes'] = 'budget/accounts/getTransacoes';
$route['importaOFX_final'] = 'budget/accounts/importaOFX_final';
$route['deletaTransacao'] = 'budget/accounts/deletaTransacao';
$route['(:any)/accounts/(:any)'] = 'budget/accounts/index/$1/$2';
$route['(:any)/accounts/(:any)/(:any)'] = 'budget/accounts/index/$1/$2/$3';
$route['(:any)/accounts/(:any)/(:any)/(:any)'] = 'budget/accounts/index/$1/$2/$3/$4';

//budget:
$route['(:any)/budget/editaCategoriaGrupo'] = 'budget/budgets/editaCategoriaGrupo';
$route['(:any)/budget/adicionaCategoriaGrupo'] = 'budget/budgets/adicionaCategoriaGrupo';
$route['(:any)/budget/adicionaCategoriaItem'] = 'budget/budgets/adicionaCategoriaItem';
$route['alteraBudget'] = 'budget/budgets/alteraBudget';
$route['(:any)/budget/chartMonth'] = 'budget/budgets/chartMonth/$1';
$route['(:any)/budget'] = 'budget/budgets/index/$1';
$route['(:any)/budget/(:any)'] = 'budget/budgets/index/$1/$2';
$route['listaGastos'] = 'budget/budgets/listaGastos';
//corretoras:
$route['(:any)/corretoras'] = 'acoes/Corretoras/index/$1';
$route['(:any)/corretoras/add'] = 'acoes/Corretoras/add/$1';
$route['(:any)/corretoras/edit/(:any)'] = 'acoes/Corretoras/edit/$1/$2';
$route['(:any)/corretoras/delete/(:any)'] = 'acoes/Corretoras/delete/$1/$2';
//acoes:
$route['(:any)/acoes/custodia'] = 'acoes/AcoesCustodia/index/$1';
$route['(:any)/acoes/resultados'] = 'acoes/AcoesResultados/index/$1';
$route['(:any)/acoes/cotas'] = 'acoes/AcoesCotas/index/$1';
  //notas
  $route['(:any)/acoes/notas/(:any)'] = 'acoes/AcoesNotas/index/$1/$2';
  $route['(:any)/acoes/notas'] = 'acoes/AcoesNotas/index/$1';
  $route['(:any)/acoes/nota/delete/(:any)'] = 'acoes/AcoesNotas/delete/$2';
  $route['(:any)/acoes/notas/edit/(:any)'] = 'acoes/AcoesNotas/edit/$1/$2';
  //ajustes
  $route['(:any)/acoes/ajustes/(:any)'] = 'acoes/AcoesAjustes/index/$1/$2';
  $route['(:any)/acoes/ajustes'] = 'acoes/AcoesAjustes/index/$1';
  $route['(:any)/acoes/ajustes/delete/(:any)'] = 'acoes/AcoesAjustes/delete/$2';
  $route['(:any)/acoes/ajustes/edit/(:any)'] = 'acoes/AcoesAjustes/edit/$1/$2';
  $route['acoes/ajustes/getPosicao'] = 'acoes/AcoesAjustes/getPosicao';
  
  //ordens
  $route['(:any)/acoes/notas/deletaOrdem/(:any)'] = 'acoes/AcoesNotas/deletaOrdem/$2';
  
/*
$route['budget'] = 'budget/admin';
$route['budget/dashboard'] = 'budget/admin';
*/
