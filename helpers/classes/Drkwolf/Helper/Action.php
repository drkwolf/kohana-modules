<?php defined('SYSPATH') OR die('No Direct Script Access');
/**
 
 * bismi-llāhi r-raḥmāni r-raḥīm, was salat wa salem al Moahamed abdih wa rasoulih
 * @package Helpers
 * @author drkwolf
 * 
 * gererate link from model's action and 
 */

Class Drkwolf_Helper_Action {


  /**
   * return url for the type specified 
   * @param string $type : view, edit, delete ...
   * @param ORM $model : ORM model with id and title fileds
   */
  protected function get_url_($type, ORM $model) 
  {
    $name = array_slice(  explode('_', get_class($model)) , -1, 1);
    $name = strtolower ( $name[0]);

    return "/$name/$type/$mode->id";
  }

  /**
   * link to edit content, to access to the link we use the title and the id of 
   * the model
   * $model: ORM object
   * @param Boolean $control : use the current controller as 
   */

  /**
   * #TODO this method must handle all the following cases:
   *  - From model name : url is generated from model name 
        example : Action::edit($default, title).

    - title must translated.
   */
  public static function link($type, ORM $model, $title = null, $attributes = null, $control = false)# {{{
  {
//FIXME don't work
//     if ( !isset($mode->id) OR (empty($tilte) AND !isset($model->title)) )
//       throw new InvalidArgumentException('model should have id and title file if $title is not set');

    $name = array_slice(explode('_', get_class($model)) , -1, 1);
    $name = strtolower ($name[0]);

    $controller = strtolower(Request::initial()->controller());
    if ( $name !== $controller )
    {
      $path = explode('_', $controller);
      if ( $control OR in_array($name, array_slice($path, -1, 1)) ) $name = $controller;
    }

    if ($title === NULL AND isset($model->title)) {
      $title = $model->title;
    }

//     $hover = isset($model->title)? $model->title : NULL;
    $attributes['tilte'] = $title; // $hove;

    if ( $model->id )
    {
      return HTML::anchor('/'.$name.'/'.$type.'/'.$model->id, $title, $attributes);
    }
    else
    {
      return $title ? $title : __('Not Set');
    }
   } # title}}}}

  /**
   * edit action 
   */
  public static function edit($model, $title=null, $attributes = null, $control = false)# {{{
  {
    return self::link('edit', $model, $title, $attributes, $control);
  } # edit}}}}

  /**
   * create action 
   */
  public static function create($path, $title=null, $attri = null)# {{{
  {
    $action = array_slice(  explode('_', $path) , -1, 1);
    $action = strtolower ( $action[0]);
    if ( !$title)
    {
      $title = __('Create') .' '.ucfirst($action);
    }

//     return self::link('create', $model, $title, $attributes);
    return HTML::anchor($path.'/create', $title, $attri);
  } # edit}}}}



  /**
   * view action 
   */
   public static function view($model, $title=NULL, $attributes = NULL)# {{{
  {
    return self::link('view', $model, $title, $attributes );
  } # view}}}}

  /**
   * delete action 
   */
  public static function delete($model, $title=null)# {{{
  {
    return self::link('delete', $model, $title );
  } # Delete}}}}

  /**
   * create action from a path same as HTML::anchor
   * @param string $path : path to the action 'user/index'
   * @param string $title : title to give to the link, default is the action name
   */
  public static function anchor($path, $title = NULL, array $attri = NULL)# { {{
  {
    $action = array_slice(  explode('_', $path) , -1, 1);
    $action = strtolower ( $action[0]);
    if ( !$title)
    {
      $title = __('Create') .' '.ucfirst($action);
    }

    return HTML::anchor($path, $title, $attri);
  } # create }}}

  public static function index($model, $title = NULL, $attri = NULL)# {{{
  {
    if ($title === NULL) {
      $title =Inflector::plural( ucfirst($model))  ;
    }
    $attri['title'] = $title;
    return HTML::anchor($model.'/index', $title, $attri);
  } # index}}}}


  /**
   * link to the user profile
   */ 
  public static function profile($model)# {{{
  {
    if ( $model->id )
    {
      //TODO
    return HTML::anchor('/user/profile/'.$model->id, $model->username, 
      array('title'=> __('View :user\'s profile', array(':user'=> $model->username)))
    );
    }
    else
    {
      return __('Not Set');
    }
  } # profile }}}

  /**
   * return query string for url
   * keys has the syntax : key => value
   */
  public static function query_string($url, $text, Array $keys, $attributes =NULL)# {{{
  {
    if (empty($text)) return __('Empty');

    $url .= '?'; 
    foreach($keys as $key => $value)
    {
      $url .= $key.'='.$value.'&'; 
    }

    return HTML::anchor($url, $text, $attributes);

  } # search }}}
  
  
  /**
   * options :
   * 	- $icon string 	: icon name (bootstrap)
   * 	- $abbr string 	: abbrevation
   * 	- title bool	: show title
   * 
   * <i class="icon-$name"> </i>$title 
   * Action::link2($default->url('addusers'),__('Subscriber List'),  
	  	array('icon'  => 'user', 'title' => false, 'abbr'  => __('Subscriber List')))
   */
  public static function link2($url, $title, array $options = null, array $attributes = null ) {
  	 $show_title = Arr::get($options, 'title', true);
	 $abbr 		 = Arr::get($options, 'abbr');
	 $icon		 = Arr::get($options, 'icon');
	 
	 $rst = '';
	  if ($icon) {
	  	 $rst .= '<i';
	  	 if ($abbr AND $show_title==false ) $rst .= ' rel="tooltip" title="'.$abbr.'"';
	  	 $rst .= ' class="icon-'.$icon.'"> </i>';
	  }
	 if ($show_title) $rst .= $title;
	 
	 if (!Arr::get($attributes, 'title'))  $attributes['title'] = $title;
	 
	 return HTML::anchor($url, $rst, $attributes);
  }

}


