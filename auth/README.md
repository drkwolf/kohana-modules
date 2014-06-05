extends Auth module by adding The auth support to  ORM,


feature of some packages has been overriten:

add has\_role (same as logged\_in) check if user has belong to the list of roles
the list of role accept an option 

':all' => bool : if true all role are required otherwise at least one is need,
default value is ture
  example : array(':all' => false, 'Admin', 'Apache') 


has\_role(Roles, admin\_?) : by default if the user has is admin the the methode
will return true to change this  behavior set the admin\_ to flase
 has\_role('XXX', false)
