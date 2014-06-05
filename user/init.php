<?php defined('SYSPATH') or die('No direct script access.');

// Static file serving (CSS, JS, images)
Route::set('user/reset', 'user/reset(/<reset_token>)', array('team' => '[0-9]+'))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'reset',
		'reset_token'       => NULL,
	));

//FIXME chande the default route controller/action/id
//Route::set('ajax_setter', '<controller>(/<action>/<name>)', array('name' => '.*'))
//->defaults(array(
    //'controller' => 'app',
	//'action'     => 'setdata',
    //'reset_token'       => NULL,
//));
Route::set('user/confirm_account', 'user/confirm_account(/<reset_token>)')
	->defaults(array(
		'controller' => 'user',
		'action'     => 'confirm_account',
		'reset_token'       => NULL,
	));

# dependences 
# drkwolf.helper


// Simple autoloader used to encourage PHPUnit to behave itself.
// class Markdown_Autoloader {
// 	public static function autoload($class)
// 	{
// 		if ($class == 'Markdown_Parser' OR $class == 'MarkdownExtra_Parser')
// 		{
// 			include_once Kohana::find_file('vendor', 'markdown/markdown');
// 		}
// 	}
// }
// 
// // Register the autoloader
// spl_autoload_register(array('Markdown_Autoloader', 'autoload'));
