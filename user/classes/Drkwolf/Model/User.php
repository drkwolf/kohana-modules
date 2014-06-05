<?php defined('SYSPATH') OR die('No Direct Script Access');

/*
 * bismi-llāhi r-raḥmāni r-raḥīm, was salat wa salem al Moahamed abdih wa rasoulih
 */

/**
 * User model, all type action need by user to login register .,
 *
 * @user      modules.drkwolf.orm
 * @package   modules.user.drkwolf.model
 * @category  User Authentication
 * @author    drkwolf@gmail.com
 * @licence   http://kohanaphp.com/license.html
 * @since     1.0
 * @created   2012-10-10
 */

Class Drkwolf_Model_User extends Model_Auth_User {

  # relationships {{{
  protected $_has_many = array( 
    // New association: attachment 
    //'blog' => array('model' => 'blog'), 
    //'knowledge' => array('model' => 'knowledge'), 
    //'reference' => array('model' => 'reference'), 
    'ticket' => array('model' => 'ticket'), 
    // Copied from Auth_User
    'roles' => array('model'=> 'role', 'through' => 'roles_users'), 
    'user_tokens' => array(), 
    // for facebook / twitter / google / yahoo identities 
    'user_identity' => array(), 
    //TODO message box should be added dynamically
    'mbox'  => array()
  ); 
  # relationships }}}

  protected $_validation_plans = array(
    'login' => array('username', 'password'),
    'register' => array('username', 'email', 'password', 'password_confirm'),
    'u-e-p-pc' => array('username', 'email', 'password','password_confirm'),
    'u-e' => array('username', 'email'),
  );

  protected $_crypt_pass = false;

  /**
   * @see drkwolf_kohana_ORM
   */ 
  protected function before($update_ = false) # {{{
  {
    parent::before();
    if ($this->_crypt_pass)
    {
      $this->password = Auth::instance()->hash($this->password);
    }
  } # end before }}}

//   public function __construct($id = NULL)# {{{
//   {
//     parent::__construct($id);
//     if( !$this->loaded() )
//     {
//        $this->role_id = ORM::factory('Role', array('name' => 'login'))->id; #default  role
//     }
//   } # __construct }}}

  # validation {{{
  /**
   * @see Kohana_ORM::rules()
   */
  public function rules()# {{{
  {
    return array();
  }  # rules}}}}

  public function all_rules()# {{{
  {
    return array(
      'username' => array( 
        array('not_empty'), 
        array('min_length', array(':value', 5)),
        array('max_length', array(':value', 32)), 
        array(array($this, 'unique'), array('username', ':value')), 
      ),
      'email' => array(
        array('not_empty'),
        array('email'),
        array(array($this, 'unique'), array('email', ':value')),
      ),
      // there's filter that hash the pass -> length > 6
      'password' => array(
        array('not_empty'),
        array('min_length', array(':value', 6)),
      ),
      'password_confirm' => array(
        array('not_empty'),
        array('matches', array(':validation', ':field', 'password')),
      ),
      'roles' => array(
        array('not_empty'),
      )
    );
  } # all_rules }}}

  ## validation modes {{{

  /**
   * @see ORM::validation_plan 
   */
  public function validation_plan(Array $values, $plan = NULL)# {{{
  {
    $pass_not_set = empty($values['password']) AND empty($values['password_confirm']);

    if ($plan === 'admin-create')
    {
      $plan = 'u-e-p-pc-r';
      if ($this->loaded())
      { //update
        if ($pass_not_set)
        {
          $plan = 'u-e-r';
        }
      } 
    }

    if ($plan ==='create')
    {
      $plan = 'u-e-p-pc';
      if ($pass_not_set)
      {
        $plan = 'u-e';
      }
    }

    return parent::validation_plan($values, $plan);
    
  } # validation_plan }}}
  /**
   * Password validation for plain passwords.                                                                      
   *            
   * @param array $values
   * @return Validation                                                                                            
   * @see Model_Auth_User::get_password_validation                                                                 
   */       
  public static function get_password_validation($values) # {{{
  {         
    return Validation::factory($values)
      ->rule('password', 'min_length', array(':value', 6))
      ->rule('password_confirm', 'matches', array(':validation', ':field', 'password'));
  } # }}}

  ## validation modes }}}

  /**
   * See Kohana_ORM::filters()
   */
  public function filters()# {{{
  {
    return array(
      'username' => array(
        array('trim')
      ),
       'email' => array(
        array('trim')
      ),
    );
//     return parent::filters();
  } # filter }}}


  # validation$ }}}

  /**
   * check if must validate the password, rules must be overided
   * set and update the roles
   * @see Kohana_ORM::values()
   */ 
   public function values(array $values, array $expected = NULL)# {{{
  {
    # pass allwas set #precaution
    if (isset($values['password']) OR isset($values['password_confirm']))
    {
      if ( !empty($values['password']) OR !empty($values['password_confirm']))
      {
        $this->_crypt_pass = true;
      }
      else
      {
        $this->_crypt_pass = false;
        unset($values['password']);
      }
    }
    parent::values($values, $expected);
    return $this;
  }  # value }}}



  //TODO move to save
  public function update_roles($roles) {
    $this->update_records('role', $roles);
  }

  /**
   * Generates a password of given length using mt_rand.
   * 
   * @param   Integer $length: password's length 
   * @return  String hashed password
   *
   * @author   drkwolf@gmail.com
   * @created  2012-10-31
   * @since   1.0
   */
  function generate_password($length = 8) # {{{
  { 
        // start with a blank password
        $password = "";
        // define possible characters (does not include l, number relatively likely)
        $possible = "123456789abcdefghjkmnpqrstuvwxyz123456789";
        // add random characters to $password until $length is reached
        for ($i = 0; $i < $length; $i++)
        { 
            // pick a random character from the possible ones
            $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
            $password .= $char;
        }
        return $password;
    } # }}}
}

