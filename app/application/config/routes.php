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
$route['(:any)/accounts'] = 'budget/accounts/index/$1';
$route['editaTransacao'] = 'budget/accounts/editaTransacao';
$route['editaConciliado'] = 'budget/accounts/editaConciliado';
$route['importaOFX'] = 'budget/accounts/importaOFX';
$route['deletaTransacao'] = 'budget/accounts/deletaTransacao';
$route['(:any)/accounts/(:any)'] = 'budget/accounts/index/$1/$2';
//budget:
$route['(:any)/budget/editaCategoriaGrupo'] = 'budget/budgets/editaCategoriaGrupo';
$route['(:any)/budget/adicionaCategoriaGrupo'] = 'budget/budgets/adicionaCategoriaGrupo';
$route['(:any)/budget/adicionaCategoriaItem'] = 'budget/budgets/adicionaCategoriaItem';
$route['alteraBudget'] = 'budget/budgets/alteraBudget';
$route['(:any)/budget'] = 'budget/budgets/index/$1';
$route['(:any)/budget/(:any)'] = 'budget/budgets/index/$1/$2';
$route['listaGastos'] = 'budget/budgets/listaGastos';
/*
$route['budget'] = 'budget/admin';
$route['budget/dashboard'] = 'budget/admin';
*/