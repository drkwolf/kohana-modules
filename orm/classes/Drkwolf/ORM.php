<?php defined('SYSPATH') OR die('No Direct Script Access');

/* bismi-llāhi r-raḥmāni r-raḥīm, was salat wa salem al Moahamed abdih wa rasoulih */

/**
 * ORM classe with persmissions, 
 *
 * the table should have an user_id
 * 
 * @uses drkwolf/auth
 * @package drkwolf/orm
 * @author   drkwolf@gmail.com
 * @created  2012-09-01
 * @since   1.0
 */
Class Drkwolf_ORM extends Kohana_ORM {
    
  # stat protected {{{
  // child class inherit association
  //FIXME don't work yet
//   protected $_inherit = FALSE; // and _merge_ass_

  /**
   * @var bool set owner automatically at creating
   */
  protected $_set_owner_ = false;

  /**
   * @var String label id of the owner 
   * @deprecated to remove (why t?)
   */ 
  protected $_owner_id = 'user_id';


  /**
   * validation plans
   * @author drkwolf
   */
  protected $_validation_plans = array();


  /**
   * run before creating updating data
   *
   * @param bool $update_ : update or create, default false => create
   * @return void
   * @author drkwolf
   */
  protected function before($update_ = false)# {{{
  {
    if ( !$update_ )
    {
      if ( $this->_set_owner_ ) $this->set_owner();
    }
  } # before }}}

  /**
   * run after saveing data
   * @param bool $update_ : update or create, default false => create
   * @return void
   * @author drkwolf
   */
  protected function after($update_ = false)# {{{ 
  {
  } # after }}}

  /**
   * initialize a fresh model
   * @return void
   * @author drkwolf@gmail.com
   * //FIXME should it be static
   */ 
  protected function initialize() 
  {
  }

  /**
   * return thre perms list ('perm_name' => 'callback_function'), if the 
   * callback function is not set _has_NAME_perm will be used as function with 
   * NAME = perm_name
   * NOTE : overrite as follow :
   * return array( 'new perm list' ) + parent::get_perms();
   * 
   * author   drkwolf@gmail.com
   * created  2012-06-27
   */
  protected function get_perms()
  {
    return array('owner', 'view', 'edit', 'create', 'delete');
    // 'perm_name' => 'callback_name',
  }

  # permssions {{{
  # NB: don't use $this->loaded()
  protected function _has_owner_perm()# {{{
  {
    if ( Auth::instance()->logged_in() )
      return $this->owner_id() == Auth::instance()->get_user()->id;
    else
      return false;
  } # name }}} 

  protected function _has_view_perm()# {{{
  {
    return true;
  } # _has_view_perm }}}

  protected function _has_edit_perm()# {{{
  {
    return $this->owner_id() === Auth::instance()->get_user()->id;
  } # _has_edit_perm }}}

  protected function _has_create_perm()# {{{
  {
    return true;
  } # _has_view_perm }}}
  
  protected function _has_delete_perm()# {{{
  {
    return $this->owner_id() === Auth::instance()->get_user()->id;
  } # _has_delete_perm }}}

 # end permission }}} 

# end protected }}}

  public function __construct($id = NULL) # {{{
  {
    parent::__construct($id);

//     if ( $this->_inherit )
//     {
//       $this->_merge_parent_asso();
//     }

    // class initialize
    if ( !$this->loaded() ) $this->initialize();
  } # }}}

  /**
   * @see Kohana_ORM::update
   * @author drkwolf
   */
  public function update(Validation $validation = null)  # update {{{
  {
    $this->before(true);
    parent::update($validation);
    $this->after(true);

    return $this;
  } # update }}}

  /**
   * @see Kohana_ORM::create
   */
  public function create(Validation $validation = null) # create {{{
  {
    $this->before();
    parent::create($validation);
    $this->after();

    return $this;
  } # }}}

  /**
   * set the validatio plan, in some cases validation fileds depend on the 
   * action (register, login, create)
   *
   * @param array $values 
   * @param $plan name of the validation plan
   * @return Validation object.
   *
   * @TODO feature : for the same fields have multiple choice of validation, 
   *  rules= 'filed' => array( 0 => array(rules ....), 1 => array(rules) and in 
   *  validation_plan = array( filed => 0:choice, ...)
   */
  public function validation_plan(Array $values, $plan = NULL)# {{{
  {
    $_rules = $this->all_rules();
    $fields = ($plan === NULL)? key($_rules) : $this->_validation_plans[$plan];
    $validation = Validation::factory($values);
    foreach( $fields as $key => $field)
    {
      $frules = (is_numeric($key))? 
        $validation->rules($field, $_rules[$field])
        : $validation->rules($key, $_rules[$key][$field]);
    }
    return $validation;
  } # validation_plan}}}}




  /**
   * differential update of table
   * @param $old : old array id => value
   * @param $updated: uptated arrary id => value
   * @return : array : add => elements
   *                   removed => elements 
   */
  public static function diff_update(array &$old, array &$updated)# {{{
  {
    $diff = array( 
      'add' => array(), // new item
      'removed' => array(), // removed item
    );

    foreach ( $old as $key => $value )
    {
      if (!isset($updated[$key]))
      {
        $diff['removed'][$key] = $value; 
      }
      else
      {
        unset($updated[$key]); # remove unchanged
      }
    }
    $diff['add'] = $updated;

    return $diff;
  } # end func diff_update }}}

  /**
   * update the records for many to many association
   *
   * @param String $model : string model name
   * @param array $record : array (key => value)
   * @param String  $key : key in the record
   * @param String $name : title of the record
   *
   * @author drkwolf 
   * @since  1.0 
   * @date   08/03/2012
   */
  public function update_records($model, $records, $key ='id', $name='name')# {{{
  {
    // for many
    $models = Inflector::plural($model);
    if ( !$this->loaded()) return;

    $old = $this->$models->find_all()->as_array($key, $name); 
    $diff = $this->diff_update($old, $records);
    # add new assoss
    foreach( $diff['add'] as $id => $value )
    {
      $this->add($models,
        ORM::factory($model)
        ->where($name, '=', $value)
        ->find()
      );
    }
    # rem roles
    foreach( $diff['removed'] as $key => $value)
    {
      $this->remove($models, 
        ORM::factory($model)
        ->where($name, '=', $value)
        ->find()
      );
    }
  } # update_records }}}



  /**
   * set the values of the records. ( 
   * @value see parent::values
   * @unset : value to unset form the values.
   */
  public function uvalues(array $values, $protect = NULL)# {{{
  {
    if ($protect) 
    {
      if (is_array($protect))
      {
        foreach ($protect as $key)
        {
          unset($values[$key]);
        }
      } 
      else
      {
        unset($values[$protect]);
      }
    }

    parent::values($values);
    return $this;
  } # values }}}

  /**
   * set the owner of the content as the authenticated user,
   * no need to do it in the controller or to set a hidden user id in the form
   * @depricated to remove
   */
  public function set_owner($user = NULL)
  {
      $this->{$this->_owner_id} = Auth::instance()->get_user()->id;
  }

  /**
   * owner of the current model.
   *
   * @:return the owner (object)
   */
  public function owner()
  {
    if(isset($this->user_id)) return $this->user;
    else return false;
  }

  /**
   * return id of the owner of the record, 
   * FIXME it's just an alias
   * @return integer : owner_id
   *
   * @author drkwolf@gmail.com
   */
  public function owner_id()# {{{
  {
    if(isset($this->user_id)) return $this->user_id;
    else return NULL;
  } # owner_id }}}

  public function is_owner_()
  {
    return $this->owner_id() === Auth::instance()->get_user()->id;
  }

 /**
   * check if the user has the permission to access the contents
   *
   * NB : permission function must check if the model is loaded.
   * 
   * @param   string $type : permission type, default is 'owner'
   * @param   bool $admin_ : by default admin has access to all, if set to flase 
   *          the admin will be exculded from the permission,
   *
   * @return  bool 
   *
   * @author   drkwolf@gmail.com
   * @created  2012-06-26
   * @since   1.0
   */
  public function has_perm($type = 'owner', $admin_ = TRUE )# {{{
  {
    //TODO cache permission {2012-11-04, drkwolf}
    // table string:perm_name int:user_id bool:has_it

    if ($admin_ AND Auth::instance()->has_role('admin')) return true;

    foreach( $this->get_perms() as $key => $value)
    {
      list($perm, $fun) = is_string($key) ? array($key, $value) : array($value, '_has_'.$value.'_perm');

      if ( $perm === $type ) 
      {
        if ( method_exists($this, $fun) )
        {
          return $this->$fun();
        } 
        else
        {
          Throw new Exception('class '.get_class($this).'doesn\'t have methode name '.$fun);
        }
      }
    } 

    # default
    return false;
  
  } # has_perm }}}
  
  
  /**
   * give select choise from hash arrary.
   * 
   * requirement  field static in upper case is initialized to array (hash => value)
   * 
   * @param String $field filed name
   * @return String
   */
  public function get_choice($field) {
      $class = get_class($this) ;
      $hash = strtoupper($field);
      return __(Arr::get($class::$$hash, $this->$field));
  }
  
  public function url($action='index', $id = null, $controller = null) {
  	if (is_null($id)) 		  $id = $this->id;
	if (is_null($controller)) $controller = $this->object_name();
	return URL::site("/$controller/$action/$id");
  }
  
  /**
   * return list of the enumarion for the field name
   * 
   * @param String $filed : field name 
   * @param bool/String  $first when not equal to false insert it as the first row
   * @return Array 
   */
  public function get_enums($field, $first = NULL) {
      $columns = $this->list_columns();
      $options = $columns[$field]['options'];
      
      $ret = array();
      foreach($options as $option ) {
          $ret[$option] = __($option);
       }
     
      if ($first !== FALSE) {//FIXME is this good idea ?
          return array( NULL => $first) + $ret;
      }
      
      return  $ret;
  }

}

