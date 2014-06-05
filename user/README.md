# configuration
- autologin : configure salt cookies in th bootstrap
  Cookie::$salt = 'your salt';
- enable user administration
- enable role administration

# orm api 

before
after


# permission

# Access Controlle
Controller we can set tow variables : 

- auto\_required : controller access for all actions
	public $auth\_required = array(':all' => false, 'admin', 'tester');

- secure\_actions : set role for actions
  public $secure\_actions = array(
    // actions => role
    'index' => array(':all' => true, 'login', 'admin'),
    'profile' => 'login', 
    'edit_profile' => 'login', 
    'access_denied' => 'login', # is for logged users
  ); 

Note : admin role has access to all controller by default


# many to many 
