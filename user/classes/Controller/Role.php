<?php defined('SYSPATH') OR die('No Direct Script Access');

/*
 * bismi-llāhi r-raḥmāni r-raḥīm, was salat wa salem al Moahamed abdih wa rasoulih
 */

/**
 *
 * @author drkwolf@gmail.com
 * @package modules.drkwolf.user.controller
 */
Class Controller_Role extends Controller_APP {
 
 # actions {{{

    public function action_view() # {{{
    {
      $id = $this->request->param('id');
      $role = ORM::factory('Role', $id);
  
      # perm and errors {{{ 
      if( !is_numeric($id) OR !$role->loaded() ) return $this->error();
      //if( !$role->has_perm('view') ) $this->access_denied();
      # }}}
  
      # set content {{{
      $this->template->title = $role->name;
      $view = View::factory('role/admin/view');
      $view->role = $role;
      $view->users = $role->users->find_all();
  
      #set layout
      //    $this->_layout(array(
      //      'right' => array(
      //         View::factory('obr/list'),
      //      ),
      // left' => array(
      //        View::factory('project_box'),
      //      )
      //    ));
      # }}}
  
      $this->template->content = $view;
    } # end action_view }}}
 
 #}}}
} // END Controller Name
