<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * User controller: user administration, also user accounts/profiles.
 *
 * @author Mikito Takada
 * @author drkwolf
 * @version 1.0
 */
class Drkwolf_Controller_Admin_User extends Controller_App {

	/**
	 * @var string Filename of the template file.
	 */

  public $auth_required = array(':all' => false, 'admin', 'user manager');

  public $secure_actions = array(
    'switch_user' => 'tester',
//     'index' => 'tester'
  );

	// USER ADMINISTRATION
	/**
	 * Administator view of users.
	 */
	public function action_index() # index {{{
	{
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('User administration');
		// create a user
		$user = ORM::factory('User');
		// This is an example of how to use Kohana pagination
		// Get the total count for the pagination
		$total = $user->count_all();
		// Create a paginator
		$pagination = new Pagination(array(
			'total_items' => $total, 
			'items_per_page' => 10,  // set this to 30 or 15 for the real thing, now just for testing purposes...
			'auto_hide' => true, 
			// 'view' => 'pagination/role'
		));
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'username'; // set default sorting direction here
		$dir = isset($_GET['dir']) ? 'DESC' : 'ASC';
		$result = $user->limit($pagination->items_per_page)
			->offset($pagination->offset)
			->order_by($sort, $dir)
			->find_all();
		// render view
    # {{{ breadcrumb
    $this->template->breadcrumb = array(
      'admin/index' => __('Admin'),
      __('Users')
    );
    # }}}

		// pass the paginator, result and default sorting direction
		$this->template->content = View::factory('user/admin/index')
			->set('users', $result)
			->set('paging', $pagination)
			->set('default_sort', $sort);
	} # index }}}

	/**
	 * Administrator edit user.
	 * @param string $id
	 * @return void
	 */
  public function action_edit() # {{{
  {
    $id = $this->request->param('id');
    $user = ORM::factory('User', $id);

    # perm and errors
    if (is_numeric($id) AND !$user->loaded()) return $this->error();

    $this->template->title = is_numeric($id)? 
      __('Edit User :user',  array(':user' => $user->username) )
      : __('Create New User');
    $view = View::factory('user/admin/edit');
    $view->user = $user;

    # {{{ breadrumb
    $this->template->breadcrumb = array(
      'admin/index' => __('Admin'),
      'admin_user/index/' => __('Users'),
      ($user->id)? __('Edit')." ($user->username)": __('Create User'),
    );
    # }}}

    if (($post = $this->request->post()))
    {// save user
      try
      {
        # value overrided 
        $user->values($post);
        
        $plan = empty($post['password'])? 'u-e':'u-e-p-pc';
        $valid = $user->validation_plan($post, $plan);
        $user->save($valid);
        //FIXME should be done in user->save
        $roles = isset($post['roles'])? $post['roles']:array();
        $user->update_roles($roles);
        # send notifcation mail {{{
        if ( $this->request->post('mail') AND $user->username != 'admin' )
        {
          # mail {{{
          $user->reset_token = $user->generate_password(32);
          $user->save();
 
 		 #//TODO move to mail directory
          $message = "You have requested a password reset. You can reset password to your account by visiting the page at:\n\n" .
            ":reset_token_link\n\n" .
            "If the above link is not clickable, please visit the following page:\n" .
            ":reset_link\n\n" .
            "and copy/paste the following Reset Token: :reset_token\nYour user account name is: :username\n";

          // Create complex Swift_Message object stored in $message
          // MUST PASS ALL PARAMS AS REFS
          $subject = __('Account password reset');
          $to = $user->email;
          $from = Kohana::$config->load('drkwolf/user')->email_address;
          $body = __($message, array(
            ':reset_token_link' => URL::site('user/reset?reset_token='.$user->reset_token.'&reset_email='.$to, TRUE),
            ':reset_link' => URL::site('user/reset', TRUE),
            ':reset_token' => $user->reset_token,
            ':username' => $user->username
          ));
 
          $body = __($message, array(
            ':url' => URL::site('user/reset?reset_token='.$user->reset_token.'&reset_email='.$to, TRUE),
            ':username' => $user->username
          ));
          // FIXME: Test if Swift_Message has been found.
          $mailer = Email::connect(); // swift object
          $message_swift = Swift_Message::newInstance($subject, $body)->setFrom($from)->setTo($to);
          if ($mailer->send($message_swift))
          {
            Message::notice(__('Password reset email sent.'));
            $this->redirect('admin_user/index');
          }
          else
          {
            Message::error( __('Could not send email.'));
          }
          # mail }}}

        }
        # }}}
        $this->redirect('admin_user/index');
      }
      catch( ORM_Validation_Exception $e )
      {
        //FIXME merge with _external {2012-11-29, drkwolf}
        $errors = $e->errors('user');
        $errors = array_merge($errors, (isset($errors['_external']) ? $errors['_external'] : array()));
        $view->errors = $errors;
      }
    }

    $this->template->content = $view;
  } # action_edit}}}}


	/**
	 * Administrator delete user
	 * @param string $id
	 * @return void
	 */
	public function action_delete() # delete {{{
	{
		// set the template title (see Controller_App for implementation)
		$this->template->title = __('Delete user');
    $id = $this->request->param('id');
		$user = ORM::factory('User', $id);

    if ( is_numeric($id) AND !$user->loaded() ) return $this->error();

    # contents {{{
    $view = View::factory('user/admin/delete')
        ->set('default', $user);

    # {{{ breadrumb
    $this->template->breadcrumb = array(
      '#admin/dashboard' => __('Admin'),
      'admin_user/index/' => __('Users'),
      __('Delete').' '.__('User')." ($user->username)",
    );
    # }}}


    # }}}

		// check for confirmation
		if ($this->request->post('confirmation') === 'Y')
		{
      // Delete the user
      $user->remove('roles');
      $user->delete($id);
      //FIXME Delete any associated identities
      //       DB::delete('user_identity')->where('user_id', '=', $id)
      //         ->execute();
      // message: save success
      Message::add('success', __('User deleted.'));

      $this->redirect('admin_user/index');
		}
		// display confirmation
    $this->template->content = $view;
} # delete }}}
 
}
