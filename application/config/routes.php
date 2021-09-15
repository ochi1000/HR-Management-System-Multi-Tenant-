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
|	https://codeigniter.com/user_guide/general/routing.html
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

$subdomain = join('.', explode('.', $_SERVER['HTTP_HOST'], -1));
if($subdomain){
	$mysqli = mysqli_init();
	if (!$mysqli) {
		die('mysqli_init failed');
	}
	$con = mysqli_connect('localhost', 'root', '', "genhr");
	$select = mysqli_query($con, "SELECT `subdomain_name` FROM `tenants` WHERE `subdomain_name` = '$subdomain'") or exit(mysqli_error($con));
	if(mysqli_num_rows($select)) {
		$route['default_controller'] = 'login';
	}else{
		show_404();
	}
}else{
	$route['default_controller'] = 'register';
	// $route['default_controller'] = 'login';
}
// $route['default_controller'] = 'register';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
