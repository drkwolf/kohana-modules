<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Message is a class that lets you easily send messages
 * in your application (aka Flash Messages)
 *
 * @package	Message
 * @author	Dave Widmer, drkwolf
 * @see	http://github.com/daveWid/message
 * @see	http://www.davewidmer.net
 * @copyright	2010 © Dave Widmer
 */
class Message_Core
{
	/**
	 * Constants to use for the types of messages that can be set.
	 */
	const ERROR = 'error';
	const NOTICE = 'notice';
	const SUCCESS = 'success';
	const WARN = 'warnnig';
	const INFO = 'info';
  const SESSION_VAR = 'flash_message';
  const DEFAULT_VIEW = 'flash_messages/basic2'; 

	/**
	 * @var	mixed	The message to display.
	 */
	public $messages;

	/**
	 * @var	string	The type of message.
	 */
	public $type;

  /**
   * 
   */
  public $template;

  /**
   * @var string default template messsage/basic
   */

  /**
   * @var session variable
   */

	/**
	 * Creates a new Falcon_Message instance.
	 *
	 * @param	string	Type of message
	 * @param	mixed	Message to display, either string or array
	 */
	public function __construct($type, $message)
	{
		$this->type = $type;
		$this->messages[$type][] = $message;

 	}

	/**
	 * Clears the message from the session
	 *
	 * @return	void
	 */
	public static function clear()
	{
		Session::instance()->delete(Message::SESSION_VAR);
	}

	/**
	 * Displays the message
	 *
	 * @return	string	Message to string
	 */
	public static function display($view = null)
	{
 		$msg = self::get();
    $config = Kohana::$config->load('flash');
    //cache ite
    $template = Message_Core::DEFAULT_VIEW;
    if(isset($config['template'])) $template = $config['template'];

 		if( $msg ){
        self::clear();
       return View::factory( (is_null($view))? $template : $view)
           ->set('message', $msg)->render();
 		} else	{
 			return NULL; 
 		}
	}

	/**
	 * The same as display - used to mold to Kohana standards
	 *
	 * @return	string	HTML for message
	 */
	public static function render()
	{
		return self::display();
	}

	/**
	 * Gets the current message.
	 *
	 * @return	mixed	The message or FALSE
	 */
	public static function get()
	{
		return Session::instance()->get(Message_Core::SESSION_VAR, FALSE);
	}

	/**
	 * Sets a message.
	 *
	 * @param	string	Type of message
	 * @param	mixed	Array/String for the message
	 * @return	void
	 */
	public static function set($type, $message, Array $values = NULL, $lang = NULL)
	{
    //Log::instance()->add(Log::DEBUG, "type,message:".$type.','.$message.','.Message::SESSION_VAR);
    Session::instance()->set(Message_Core::SESSION_VAR, 
      new Message($type,__($message, $values, $lang)
    ));
	}

	/**
	 * Sets an error message.
	 *
	 * @param	mixed	String/Array for the Message(s)
	 * @return	void
	 */
	public static function error($message, Array $values = NULL, $lang = NULL)
	{
		self::set(Message::ERROR, $message, $values, $lang);
	}

	/**
	 * Sets a notice.
	 *
	 * @param	mixed	String/Array for the Message(s)
	 * @return	void
	 */
	public static function notice($message, Array $values = NULL, $lang = NULL)
	{
		self::set(Message_Core::NOTICE, $message, $values, $lang);
	}

	/**
	 * Sets a success message.
	 *
	 * @param	mixed	String/Array for the Message(s)
	 * @return	void
	 */
	public static function success($message, Array $values = NULL, $lang = NULL)
	{
		self::set(Message_Core::SUCCESS, $message, $values, $lang);
	}

	/**
	 * Sets a warning message.
	 *
	 * @param	mixed	String/Array for the Message(s)
	 * @return	void
	 */
	public static function warn($message, Array $values = NULL, $lang = NULL)
	{
		self::set(Message_Core::WARN, $message, $values, $lang);
	}


  //make this module compatible with user module
  /**
   *
   * @depricated
   */
  public static function output()
  {
    return self::display(Message_Core::DEFAULT_VIEW); 
  }

  public static function add($type, $message)
  {
    self::set($type, $message);
  }

  /**
   * @depricated
   */
  public static function count() #{{{ count
  {
      $msg = Session::instance()->get(Message::SESSION_VAR);
      if (is_object($msg)) {
          return count($msg->messages);
      }

      return 0;
  } # }}} End count
}
