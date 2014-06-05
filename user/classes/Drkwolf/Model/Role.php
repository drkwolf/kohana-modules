<?php defined('SYSPATH') OR die('No Direct Script Access');

/**
 * 
 * Permission : Admin
 */
Class Drkwolf_Model_Role extends  ORM
{
    protected $_has_many = array(
        'users' => array('model' => 'user', 'through' => 'roles_users')
    );

    #roles
    public static $types = array(
      'users' => 'Users',
      'tasks' => 'Tasks'
    );

    /**
     * add list of users to the current role.
     *
     * @Query :
     * @parms usernames : array of user names to be added to this role
     * @return array of invalid users
     * @author  drkwolf
     */
    public function add_users($usernames = array() ) # {{{ add_users
    {
        $invalid_users = array();
        foreach ($usernames as $username) 
        {
            $user = ORM::factory('User');
            $user->where('username', '=', $username)
                ->find();

            if ($user->loaded())
            {
                if( ! $this->has('users', $user))
                {
                    $this->add('users', $user); 
                }
            }
            else 
            {
                $invalid_users[] = $username;
            }

        }
        return $invalid_users;
    } #  }}} End add_users

    /**
     *  remove list of users from the current role
     *
     * @Query : 
     * @params usernames : array of users names 
     * @return array of invalid users
     * @author drkwolf
     **/
    public function remove_users ($users_id = array()) # {{{ remove users
    {
        $invalid_users = array();
        foreach ($users_id as $user_id) 
        {
            //FIXME don't nned to load all users
//             $this->users->where('user_id', '=', $user_id);
            $user = ORM::factory('User', $user_id);


            if ($user->loaded()) {
                if($this->has('users', $user))
                {
                    $this->remove('users', $user);
                }
            }
            else
            {
                $invalid_users[] = $user_id;
            }
        }

        return $invalid_users;
    } # }}}

    /**
     * Validate rules for role model.
     */
    public function rules() # {{{ rules
    {
        return array(
            'name' => array(
                array('not_empty'),
                array(array($this, 'unique'), array('name', ':value')),
            )
        );
    } # }}} End rules

}

