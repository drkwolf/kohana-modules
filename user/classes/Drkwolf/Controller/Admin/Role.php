<?php defined('SYSPATH') OR die('No Direct Script Access');

/**
 * manage roles, create read update and delete
 * Permission : Role Admin
 *
 */
Class Drkwolf_Controller_Admin_Role extends Controller_App {
  public $auth_required = array(':all' => false, 'admin', 'user manager');
  public $secure_actions = array(
    'view' => 'login',
  );

  //     public $secure_actions = array('delete' => 'admin'); // array( action => role)
  
  
  public function before()
  {
  	parent::before();
  	$managed = Kohana::$config->load('drkwolf/user.roles.managed');
	if ( !$managed and $this->request->action() != 'config')
		$this->redirect('action_disabled');
  }


    /**
     * @action : list all aviablable roles, with pagination
     */
    public function action_index() # {{{ action_index
    {
    	//hidden except from  supper user
        $roles = ORM::factory('Role');

		//
		$hidden_l = Kohana::$config->load('drkwolf/user.roles.hidden');
		$white_l = Kohana::$config->load('drkwolf/user.roles.unhide');
		if (Auth::instance()->has_role($white_l)) {
			$roles->where('name', 'NOT IN', $hidden_l);
		}

        // export roles view

        // {{{ pagination
        // This is an example of how to use Kohana pagination
        // Get the total count for the pagination
        $total = $roles->count_all();
        // Create a paginator
        $pagination = new Pagination(array(
            'total_items' => $total,
            'items_per_page' => 10,  // set this to 30 or 15 for the real thing, now just for testing purposes...
            'auto_hide' => true,
            'view' => 'pagination/role'

        ));
        // Get the items for the query
        $sort = 'id'; //TODO getit from post set default sorting direction here
        $dir = isset($_GET['dir']) ? 'DESC' : 'ASC';
        $result = $roles
        	// ->where('name', 'NOT IN', $ingore_list)
        	->limit($pagination->items_per_page)
            ->offset($pagination->offset)
            ->order_by($sort, $dir)
            ->find_all();
			
        // render view
        // pass the paginator, result and default sorting direction
        # {{{ breadcrumb
        $this->template->breadcrumb = array(
        '#' => __('Admin'),
        __('Roles')
        );
        # }}}
        $this->template->title = __('Role List');
        $this->template->content = View::factory('role/admin/index')
            ->set('roles', $result)
            ->set('paging', $pagination)
            ->set('default_sort', $sort);
        // }}} */

       
//         $this->template->content = $view;
    } # }}} End action_index

    /**
     * @action : create a new role
     */
    public function action_update() //{{{ action create
    { 
        $view = View::factory('role/admin/update');
        
        if ($this->request->post()) 
        {// save role
             try
             {
               $role = ORM::factory('Role'); 
               $role->name = $this->request->post('name');
               $role->description = $this->request->post('description');

               $role->save();
               // redirect to index
               $this->redirect('admin_role/index');   
             
             } 
             catch (ORM_Validation_Exception $e)
             {
                 //FIXME is this the right way
                $view->errors = $role->validation()->errors('role');
             }
        }

        $this->template->content = $view;

    } /// }}} create action

    /**
     * @action : show role 
     */
    public function action_view() # {{{ action_view
    {
        $this->template->title = __('Role details');
        $view = View::factory('role/admin/view');

        $id = $this->request->param('id');
        $role = ORM::factory('Role', $id);
        // KO3 ORM lazy loading ...
        if (is_numeric($role->id)) {
            $view->role = $role;
            $view->users = $role->users->find_all();
        } else {
            //User not found 
            $view->message = __('Role not found');
            Kohana::$log->add(Kohana::Warning, 'Role id='.$id.' not found, user='.Auth::instance()->get_user()->username);
            $this->redirect('errors/nofound');
        }

        # {{{ breadcrumb
        $this->template->breadcrumb = array(
          'admin_role' => __('Roles'),
          $role->name,
        );
        # }}}

        $this->template->content = $view;
    } # }}} End action_view

    /**
     * @action : edit update role.
     * @args numeric:  id
     */
    public function action_edit() # {{{ action_edit
    {
      $id = $this->request->param('id');
      $role = ORM::factory('Role', $id);

      # perm and errors
      if (is_numeric($id) AND !$role->loaded()) return $this->error();

      #set contents {{{
      $this->template->title = is_numeric($id)? __('Update Role') : __('Create Role');
      $view = View::factory('role/admin/edit')
      ->set('default', $role);
	  
      # {{{ breadcrumb
      $this->template->breadcrumb = array(
        'admin/index' => __('Admin'),
        'admin_role' => __('Roles'),
      ($role->id)? __('Edit')." ($role->name)": __('Edit'),
      );
      # }}}

      #layout

      # }}}
      //prevent user from edit required roles used in other classes
      $def_role = Kohana::$config->load('drkwolf/user.roles.required');
	  if (in_array($role->name, $def_role)) {
		Message::error('Role `:role` are required and cannot be edited', array(':role'=> $role->name));
	  	$this->redirect('admin_role/index');
	  }

      # save post (update and create)
      if ($this->request->post())
      {
        try
        { 
          $role->values($this->request->post());
          $role->save();
          Message::notice(__('Content Updated'));
          $this->redirect('admin_role/view/'.$role->id);
        }
        catch( ORM_Validation_Exception $e )
        {
          $view->errors = $role->validation()->errors('role');
        }
      }

      $this->template->content = $view;
    } # }}} End action_edit

    /**
     * @action : delete role
     */
    public function action_delete() # {{{ action_delete
    {
      $this->template->title = __('Delete Role');
      $view = View::factory('role/admin/delete');

      $id = $this->request->param('id');
      $role = ORM::factory('Role', $id);
	  
	  // prevent user form deleting required users
	  $def_role = Kohana::$config->load('drkwolf/user.roles.required');
	  if (in_array($role->name, $def_role)) {
	  	Message::error('Role `:role` are required', array(':role'=> $role->name));
	  	$this->redirect('admin_role/index');
	  }
	  
      if ($role->loaded()) {
        if ($this->request->post('confirmation') == 'Y') {
          $role->delete($id);
          $this->redirect('admin_role');
        }

        $this->template->content = $view
          ->set('id', $id)
          ->set('name', $role->name);
      } else {
        $this->redirect('role/index');
      }

    } # }}} End action_delete

    /**
     * edit user group
     * @arg numeric: id
     */
    public function action_edit_users() # {{{ action_add_user
    {
      $this->template->title = __('Edit Group Permission');
      $view = View::factory('role/admin/edit_user');

      $id = $this->request->param('id');
      $role = ORM::factory('Role', $id);

      # perm and perm
      if (!is_numeric($id) OR !$role->loaded()) return $this->error();
      # end perm

      # contents {{{
      # breadcrumb {{{
         $this->template->breadcrumb = array(
         //action path => name 
          'admin/index' => __('Admin'),
          'admin_role/index' => __('Roles'),
          $role->name
         );
      # }}}

      # contents$ }}}

      if ($role->loaded()) {
        $role->users->find_all();
        if($this->request->post('adduser'))
        {
          $users = array_map('trim', explode(',', $this->request->post('usernames')));
          $invalid = $role->add_users($users);
          if( ! empty($invalid))
          {  //TODO throw exception
            $view->message = __('add user to role : invalid users');
            $view->invalid = $invalid;
            Message::error(__('Invalid users'));
          }
        } elseif ($this->request->post('removeuser')) {
          $users_id = $this->request->post('users');
          $invalid = $role->remove_users($users_id);
        }
        $view->role = $role;
      } else {
        //TODO throw new HTTP_404 exception
      }
      $this->template->content = $view;
    } # }}} End action_add_user

    public function action_config() {
    	
		$view = View::factory('role/admin/config');
		
		$view->default = Kohana::$config->load('drkwolf/user.roles');
		
		$this->template->title = __('Role Configuration');
		$this->template->content = $view; 	
    }
 }
