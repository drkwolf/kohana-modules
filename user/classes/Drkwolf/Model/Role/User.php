<?php defined('SYSPATH') OR die('No Direct Script Access');

/**
 * bismi-llāhi r-raḥmāni r-raḥīm, was salat wa salem al Moahamed abdih wa rasoulih
 */
Class Drkwolf_Model_Role_User extends ORM {
  
  protected $_belongs_to = array('role' => array(), 'user' => array());
}
