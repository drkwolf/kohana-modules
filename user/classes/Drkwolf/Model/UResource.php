<?php defined('SYSPATH') OR die('No Direct Script Access');

/**
 * bismi-llāhi r-raḥmāni r-raḥīm, was salat wa salem al Moahamed abdih wa rasoulih
 * User resource
 */
Class Model_UResource extends ORM {


  protected $_owner_model = 'user';
  protected $_owner_id = 'user_id';
  
  # associations {{{
//  protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
//  protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');  

  protected $_belongs_to = array(
      'user' => array(),
  );
  
  # }}}


  public function before($update_= false)
  {
    parent::before();

    if ( $update )
    {
      
    }
    else
    {
      $this->set_owner();
    }
  }


  /**
   * @return ORM : owner of this model
   */
  public function owner()
  {
    if ( $this->loaded() )
      return $this->{$this->_owner_model};
    else
      throw new Exception('Model Note loaded');
  }

  public function owner_id() 
  {
     if ( $this->loaded() )
      return $this->{$this->_owner_id};
     else
      throw new Exception('Model Note loaded');
  }

  /**
   * set the owner of the content as the authenticated user,
   * no need to do it in the controller or to set a hidden user id in the form
   * @param object $user : assign resource to this user, if set the logged 
   * user must have belong to admin or User Manager role to be able to change the owner
   */
  public function set_owner($user = NULL)
  {
    if ( Auth::instance()->logged_in() )
    {
      if ( $user )
      {
        if ( Auth::instance()->has_role(array('admin', 'user manager')) )
        {
          $this->{$this->_owner_id} = $user->id;
        } 
      }
      else
      {
        $this->{$this->_owner_id} = Auth::instance()->get_user()->id;
      }
    }
    else
    {
      throw new Exception('User must be logged in, ensure that in the controller
        that the is logged');
    }
  }


  //TODO
} // End Model UResource

