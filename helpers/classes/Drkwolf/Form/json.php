<?php
defined('SYSPATH') or die('No direct access allowed.');

/**
 * 
 * @author Mikito Takada
 * @package default
 * @version 1.0
 *
 */

/* bismi-llāhi r-raḥmāni r-raḥīm, was salat wa salem al Moahamed abdih wa rasoulih */

/**
 * helper methode for array names
 * 
 * @package name.controller
 *
 * @author   drkwolf@gmail.com
 * @created  2013-04-16
 * @since   1.0
 */
class Drkwolf_Form_JSON extends Drkwolf_Form_Widget  {

  
  /**
   * check if the sting is a name for an array
	 * @see parent::load_values
   * 
   * @author   drkwolf@gmail.com
   * @created  2013-04-16
   * @since   1.0
   */
	protected function load_values ($name, &$value, &$attributes)
  {	
    
    // TODO user (?<name>) and match to get the path,
    //FIXME look here http://kohanaframework.org/3.2/guide/api/Config#load to a popre way 
    if ( preg_match("/^[_A-z]\w+(\[[_A-z]\w+\])+$/", $name, $match) ) // is array ?
    {
      $name = str_replace('[','.',  str_replace(']','',$name)); //bb[xx][yy][zz] => bb.xx.yy.zz
    }

    if (isset($this->errors[$name]))
		{
			$attributes = self::add_class($attributes, 'error');
		}
    if ($value == NULL)
		{
			$value = Arr::path($this->defaults, $name);
		}

  }
}
