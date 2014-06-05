<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * ORM Auth driver.
 *
 * @package    modules.drkwolf.auth
 * @author     drkwolf@gmail.com
 * 
 * 
 */
// Note Kohana_Auth_ORM in modules/orm/classes/kohana/auth/orm.php
class Drkwolf_Auth_ORM extends Kohana_Auth_ORM {


  /**
   * 
   * @param mixed $roles :
   * - string : role name
   * - array : list of roles, the array accept option to set if all roles role are 
   * required or at least one
   *   options : :all => true/false
   * - model (role)
   * @param bool $admin : the admin  henirte all other roles, set it to false to 
   * force only the user role
   * @return boolean
   * @author : drkwolf
   */

  public function has_role($role = NULL, $admin = true)
  {
		$user = $this->get_user();

		if ( ! $user) return FALSE;

    if ( $admin AND $user->has('roles', ORM::factory('Role', array('name' => 'admin'))))
    {
      return true;
    }

    if ($user instanceof Model_User AND $user->loaded())
    {
      // all roles are needed Or at least one(false) ?
      $all = TRUE;
      // If we don't have a roll no further checking is needed
      if ( ! $role) return TRUE; // user has login role !

      if (is_array($role)) # role  as array {{{
      {
        if ( isset($role[':all'] ))
        {
          $all = $role[':all'];
          unset($role[':all']);
        }

        // Get all the roles
        $roles = ORM::factory('Role')
          ->where('name', 'IN', $role)
          ->find_all()
          ->as_array(NULL, 'id');

        if ( $all )
        {
          // Make sure all the roles are valid ones
          if (count($roles) !== count($role))
            return FALSE;
        }
        else
          if (count($roles) === 0) 
            # at least one is selected,
            return FALSE;
      } # }}}
      else # role as object/string {{{
      {
        if ( ! is_object($role))
        {
          // Load the role
          $roles = ORM::factory('Role', array('name' => $role));

          if ( ! $roles->loaded())
            return FALSE;
        }
      } # }}}

      if ( $all )
      {
        return $user->has('roles', $roles);
      }
      else
      {
        foreach( $roles as $role )
          if ( $user->has('roles', $role) )
            return TRUE;

        return FALSE;
      }

      return FALSE;
    }

  }

	/**
   * @see Kohana_Auth_ORM
	 */
	public function logged_in($role = NULL)
	{
    return $this->has_role($role);
  } 

} // End Auth ORM
