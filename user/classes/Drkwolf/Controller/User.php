<?php defined('SYSPATH') OR die('No Direct Script Access');

/* bismi-llāhi r-raḥmāni r-raḥīm, was salat wa salem al Moahamed abdih wa rasoulih */

/**
 * User contoller, all type action need by user to login register .,
 * 
 *
 * @user      modules.drkwolf.email
 * @package   modules.user.drkwolf.controller
 * @category  User Authentication
 * @author    drkwolf@gmail.com
 * @licence   http://kohanaphp.com/license.html
 * @since     1.0
 * @created   2012-10-10
 */
Class Drkwolf_Controller_User extends Controller_App {
  public $secure_actions = array(
    // actions => role
    'index' => 'login', 
    'profile' => 'login', 
    'edit_profile' => 'login', 
    'access_denied' => 'login', # is for logged users
  ); // the others are public (forgot, login, register, reset, noaccess)
  // logout is also public to avoid confusion (e.g. easier to specify and test post-logout page)

  public function before() # before {{{
  { 
    //$baseUrl = URL::base(true);
    //# referrer and baseUrl has the same base url
    //if(substr($this->request->referrer(), 0, strlen($baseUrl)) == $baseUrl)
    //{
      //# the resource
      //$urlPath = ltrim(parse_url($this->request->referrer(),PHP_URL_PATH),'/');
      ////FIXME remove index.php from urlpath
      //$processedRef = Request::process_uri($urlPath);
      //$referrerController = Arr::path(
        //$processedRef,
        //'params.controller',
        //false
      //);
      ////TODO noReturn in never set in the application
      //if($referrerController && $referrerController != 'user') // && !Session::instance()->get('noReturn',false)){
      //{
        //Session::instance()->set('returnUrl',$this->request->referrer());
      //}
    //}

    parent::before();
	
	$register = Kohana::$config->load('drkwolf/user.register');
	
	if (!Arr::get($register, 'enable', true)) {
		$this->redirect('action_disabled');
	}
	
  } // }}} 

  public function action_index() # {{{
  {
    if( Auth::instance()->logged_in('admin') )
    {
      $this->redirect('admin_user/index');
    }
    else
    {
      $this->redirect('user/profile');
    }
  } # action_index }}}

  public function action_register() # {{{
  {
    $this->template->title = __('User Registration');

    // user already logged
    if (Auth::instance()->logged_in() != false)
    {
      $this->redirect('user/profile'); 
    }

    $view = View::factory('user/register');
    # breadcrumb {{{
    $this->template->breadcrumb = array(
      'User' => __('User'),
      __('Register'),
       );
    # }}}


    $user = ORM::factory('User');

    if( $post=$this->request->post() )
    {
      try
      {
        # register account
//         Auth::instance()->register($post);
        $user->values($post, array('username' , 'password', 'email'));
        $validation = $user->validation_plan($post, 'register');
        $user->save($validation);

        //FIXME notify user see config
        $config = Kohana::$config->load('drkwolf/user')->register;

        if ( Arr::get($config, 'confirm_email') ) 
        {
          $user->reset_token = $user->generate_password(32);
          $user->save();

          Email::from_file('user/confirm_register')
            ->to($user->email)
            ->from(Kohana::$config->load('drkwolf/user')->email_address)
            ->set(array(
              ':username' => $user->username,
              ':confirm_token_link' => URL::site('user/confirm_account/'.$user->reset_token, TRUE),
              ))
            ->send()
            ;
          Message::notice('Please check your mail to confirm registration');
          $this->redirect('user/confirm_account');
        }
        elseif ( Arr::get($config, 'login') )
        {
          $role = ORM::factory('Role', array('name' => 'login'));
          $user->add('role', $role);
          Auth::instance()->login($post['username'], $post['password']);
          $this->redirect('user/profile');
        }
        else {
          Throw new Kohana_Exception('regstration in drkwolf/user/config must be set');
        }
 
      }
      catch( ORM_Validation_Exception $e )
      {
        $errors = $e->errors('register');
        $errors = array_merge($errors, ( isset($errors['_external']) ? $errors['_external'] : array() ));
        $view->set('errors', $errors); 
        //echo debug::vars($errors);
        //isset($errors['_external']) ? print_r($errors['_external']) : array();
      }
    }

    $this->template->content = $view;
  } # action_register }}} 

  /**
   * TODO can other user see the each others profile ?
   * or do we need create a public profile
   */
  //TODO move to user/setting {2012-11-19, drkwolf}
  public function action_profile() # {{{
  {
    $id = $this->request->param('id');
    $id = is_numeric($id)? $id : Auth::instance()->get_user('id');
    $user = ORM::factory('User', $id);

    # perm and errors
    if (is_numeric($id) AND !$user->loaded()) return $this->error();
    
    # view and layout {{{
    $view = View::factory('user/profile');
    $view->user = $user;

    # breadcrumb {{{
    $this->template->breadcrumb = array(
      '#/user/dashboard' => __('User'),
      '#/user/setting' => __('Setting'),
      __('Profile')
    );
    # }}}
    #view and layout }}}
    
    $this->template->content = $view;
  } # }}}

  //TODO move to user/setting {2012-11-19, drkwolf}
    public function action_edit_profile() # {{{
    {
      $user = Auth::instance()->get_user();

      # view {{{
      $this->template->title = __('Edit Profile');
      $view = View::factory('user/edit_profile');
      $view->user = $user;
      # breadcrumb {{{
      $this->template->breadcrumb = array(
        '#/user/dashboard' => __('User'),
        '#/user/setting' => __('Setting'),
        __('Edit Profile')
      );
      # }}}

      # view$ }}}
    

      if (($post = $this->request->post()))
      {
        try
        {
          // only possible post
          $user->values($post, array('username' , 'password', 'email'));
          $validation = $user->validation_plan($post, 'create');
          $user->save($validation);
          $this->redirect('user/profile');
        }
        catch( ORM_Validation_Exception $e )
        {
          $view->errors = $e->errors('user');
        }
      }
      $this->template->content = $view;
    } # action_edit_profile }}}

  public function action_login() # {{{
  {
    // user already logged
    if (Auth::instance()->logged_in() != false)
    {
      $this->redirect('user/profile'); 
    }


    $this->template->title = __('Login');
    $view = View::factory('user/login');

    # breadcrumb {{{
       $this->template->breadcrumb = array(
         'User' => __('User'),
         __('login'),
       );
    # }}}

    $username = $this->request->post('username');
    $password = $this->request->post('password');
    $remember = (bool)$this->request->post('remember');

    #{{{ debuging 
    // Log::instance()->add(Log::DEBUG,"User login : username:".$username." password : ".$password
    //  // ."\ncrythpass: ".$password #."\ndb pass  : ".$userx->password
    // ." Remember: ".$remember
    // );
    # }}}

    if ($this->request->post())
    {// trying to loggin
      if (Auth::instance()->login($username, $password, $remember) != false)
      {
        $this->redirect(
          Session::instance()->get_once('returnUrl','user/profile')
        );
      }
      else
      {
        $view->set('username', $this->request->post('username')); 
        // Get errors for display in view 
        $validation = Validation::factory($this->request->post())
          ->rule('username', 'not_empty') 
          ->rule('password', 'not_empty'); 
        if ($validation->check()) 
        { 
          $validation->error('password', 'invalid'); 
        } 
        $view->set('errors', $validation->errors('login'));
      }
    }

    $this->template->content = $view;
  } # action_login}}}}

  public function action_logout() # {{{
  {
    Auth::instance()->logout();
    $this->redirect('user/login');
  } # action_logout}}}}

  /**
   * A basic implementation of the "Forgot password" functionality
   */
  public function action_forgot_pass() # forgot pass {{{ 
  {
    // Password reset must be enabled in config/user.php
    if (! Kohana::$config->load('drkwolf/user')->email)
    {
      Message::error('Password reset via email is not enabled. Please contact the site administrator to reset your password.');
      $this->redirect('user/register');
    }
    // set the template title (see Controller_App for implementation)
    $this->template->title = __('Forgot password');
    $view = View::factory('user/forgot_pass');
    # breadcrumb {{{
    $this->template->breadcrumb = array(
      'User' => __('User'),
      __('Request new password'),
       );
    # }}}



    if ($email=$this->request->post('email'))
    {
      $user = ORM::factory('User')
        ->where('email', '=', $email)
        ->find();
     
      if ( $user->loaded() AND ( $user->username != 'admin' ))
      {// admin passwords cannot be reset by email
        // send an email with the account reset token
        $user->reset_token = $user->generate_password(32);
        $user->save();
        # mail {{{
        //FIXME reset only for the 
        if ($user->has('roles', ORM::factory('Role', array('name' => 'login')))) 
        {
          Email::from_file('user/forget_pass')
            ->to($email)
            ->from(Kohana::$config->load('drkwolf/user')->email_address)
            ->set(array(
              ':reset_token_link' => URL::site('user/reset/'.$user->reset_token, TRUE),
              ':reset_link' => URL::site('user/reset', TRUE),
              ':reset_token' => $user->reset_token,
              ':username' => $user->username))
              ->send()
              ;
          Message::notice(__('Password reset email sent.'));
          $this->redirect('/user/reset');

        }
        else
        {
          Message::notice(__('Your Account is not Active please contact your admin to Active it'));
        }
        //TODO check if the mail has faild and send message
        # mail }}}
      }
      else
        if ($user->username == 'admin')
        {
          Message::error(__('Admin account password cannot be reset via email.'));
        }
        else
        {
          Message::error(__('User account could not be found.'));
        }
    }
    $this->template->content = $view;
  } # }}}

  /**
   * reset password action
   *
   * allow user to insert a tokken then login into its profile to change his
   * password.
   * this action allow 2 things : reset action directly through url parameter 
   * or insert it directly in the form
   * 
   * @category  action
   * @route     /reset/<token>
   * @permission anonymous
   *
   * @author    drkwolf@gmail.com
   * @created   2012-10-30
   * @since     1.0
   */
  public function action_reset()
  {
    $this->_validate_token('Reset Password', 'user/edit_profile');
  }

  /**
   * confirm account after registration 
   * 
   * @category  action 
   * @route     /user/confirm_account/<token>
   * @post      reset_token
   * @view      /user/reset
   *
   * @author    drkwolf@gmail.com
   * @created   2012-10-31
   * @since     1.0
   */
  public function action_confirm_account()
  {
    $this->_validate_token('Confirm Account', 'user/profile', true);
  }


  /**
   * Validate the reset_token used to reset password and confirm registration
   * 
   * @access  public 
   * @param   Integer $id
   * @return  Object
   *
   * @author   drkwolf@gmail.com
   * @created  2012-11-01
   * @since   1.0
   */
  protected function _validate_token($title, $redirect, $set_role=false) # {{{
  {
    if ( Auth::instance()->logged_in()) $this->redirect('/user/profile');
    $user = FALSE;

    # set content {{{
    $this->template->title = __($title);
    $view = View::factory('user/reset');
    # end contents }}}

    if ( $post=$this->request->post() )
    {//from post
      $user = ORM::factory('User')
        ->where('reset_token', '=', Arr::get($post, 'reset_token'))
        ->find();
    }
    elseif ($token = $this->request->param('reset_token'))
    {//
      $user = ORM::factory('User')
        ->where('reset_token', '=', $token)
        ->find();
    }

    if ( $user === FALSE )
    {//UGLY requesting view
    }
    elseif ( $user->loaded() )
    {//token found
      try
      {
        $user->reset_token = '';
        $user->save();

        if ( $set_role )
        {
          $roles = Kohana::$config->load('drkwolf/user')->default_roles;

          foreach( $roles as $name )
          {
            if ( $role = ORM::factory('Role', array('name' => $name)) )
            {
              if ( !$user->has('roles', $role) )  $user->add('roles', $role);
            }
            else
            {
              Throw new Kohana_Exception(
                'default Role :name must be added the database', 
                array(':name'=> $name)
              );
            }
          }
        }

        Auth::instance()->force_login($user, TRUE);
      }
      catch( ORM_Validation_Exception $e )
      {
        $view->errors = $user->validation()->errors('user');
        //echo Debug::vars($e->errors);
      }

      $this->redirect($redirect);
    }
    else
    {// token wrong
      Message::error('Invalid Token');
    }

    $this->template->content = $view;

    //$this->ajax_as_json($user); //send and exist if json 
    //$this->ajax_as_html($user); //send and exist if html
  } # _check_reset }}}

  public function action_access_denied() # {{{
  {
    $this->template->title = __('Access denied');
    $view = View::factory('errors/access_denied');

    $this->template->content = $view;
  } # action_access_denied }}}
}
