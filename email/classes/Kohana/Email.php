<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * Email message building and sending.
 *
 * @uses       Swiftmailer (v4.1)
 * @package    Kohana
 * @category   Email
 * @author     drkwolf
 * @version    1.0.0
 *
 * @copyright  (c) 2012
 * @license    http://kohanaphp.com/license.html
 */
class Kohana_Email {

  /**
   * @const MAIL_PATH path to the mails direcotry
   */
  const MAIL_PATH = 'emails';

  /**
   * @var  Swiftmailer  Holds Swiftmailer instance
   */
  protected static $_mailer;

  /**
   * Creates a SwiftMailer instance.
	 *
	 * @return  object  Swift object
	 */
	public static function mailer() # mailer {{{
	{
		if ( ! Email::$_mailer)
		{
			// Load email configuration, make sure minimum defaults are set
			$config = Kohana::$config->load('email')->as_array() + array(
				'driver'  => 'native',
				'options' => array(),
			);

			// Extract configured options
			extract($config, EXTR_SKIP);

			if ($driver === 'smtp')
			{
				// Create SMTP transport
				$transport = Swift_SmtpTransport::newInstance($options['hostname']);

				if (isset($options['port']))
				{
					// Set custom port number
					$transport->setPort($options['port']);
				}

				if (isset($options['encryption']))
				{
					// Set encryption
					$transport->setEncryption($options['encryption']);
				}

				if (isset($options['username']))
				{
					// Require authentication, username
					$transport->setUsername($options['username']);
				}

				if (isset($options['password']))
				{
					// Require authentication, password
					$transport->setPassword($options['password']);
				}

				if (isset($options['timeout']))
				{
					// Use custom timeout setting
					$transport->setTimeout($options['timeout']);
				}
			}
			elseif ($driver === 'sendmail')
			{
				// Create sendmail transport
				$transport = Swift_SendmailTransport::newInstance();

				if (isset($options['command']))
				{
					// Use custom sendmail command
					$transport->setCommand($options['command']);
				}
			}
			else
			{
				// Create native transport
				$transport = Swift_MailTransport::newInstance();

				if (isset($options['params']))
				{
					// Set extra parameters for mail()
					$transport->setExtraParams($options['params']);
				}
			}

			// Create the SwiftMailer instance
			Email::$_mailer = Swift_Mailer::newInstance($transport);
		}

		return Email::$_mailer;
	} # }}}


  /**
   * generate email from file, the file contain the subject and the message to
   * be sent
   * 
   * @param   String $file : file name, the path search is application/emails/
   * @param   String $type : text/html, text/plain
   * @param   String $lang : language of the file, directory of the language
   * must be created and mail in the corresponding language put there
   * @return  Object
   *
   * @author   drkwolf@gmail.com
   * @created  2012-10-31
   * @since   1.0
   */
  public static function from_file($file, $type='text/html', $lang = NULL)
  {
    $mail = new Kohana_Email(NULL, NULL, $type);
    $mail
      ->set_lang($lang)
      //TODO add type setter {2012-11-07, drkwolf}
      ->set_type($type) 
      ->set_filename($file);
      ;

    return $mail;
  }

  /**
   * return instance of the mail
   *
   * @param   String $file : file name, the path search is application/emails/
   * @param   String $type : text/html, text/plain
   * @param   String $lang : language of the file, directory of the language
   * 
   *
   * @author   drkwolf@gmail.com
   * @created  2012-11-07
   * @since   1.0
   */
  public static function factory($subject, $message, $type ='text/html') {
    return new Kohana_Email($subject, $message, $type);
  }


  #
  /**
   * @var $_file : list of variables in subject and message that should be set
   */
  protected $_data = array();

  /**
   * @var String $_file : message and subject file
   */
  protected $_file;

  /**
   * @var String $_type : message Minetype
   */
  protected $_type = 'text/html';



  /**
   * @var Swift_Message $_message : swift mail message
   */
  protected $_message;


	/**
	 * Initialize a new Swift_Message, set the subject and body.
	 *
	 * @param   string  message subject
	 * @param   string  message body
	 * @param   string  body mime type
	 * @return  void
	 */
	public function __construct($subject = NULL, $message = NULL, $type = NULL) # constructor {{{
	{
    Email::mailer();
    // Create a new message, match internal character set
    $this->_message = Swift_Message::newInstance()
      ->setCharset(Kohana::$charset)
      ;

		if ($subject)
		{
			$this->subject($subject);
		}

		if ($message)
		{
			$this->message($message, $type);
		}

    return $this;
	} # }}}


  # Swift api {{{

	/**
	 * Set the message subject.
	 *
	 * @param   string  new subject
	 * @return  Email
	 */
	public function subject($subject)
	{
		// Change the subject
		$this->_message->setSubject($subject);

		return $this;
	}

	/**
	 * Set the message body. Multiple bodies with different types can be added
	 * by calling this method multiple times. Every email is required to have
	 * a "text/plain" message body.
	 *
	 * @param   string  new message body
	 * @param   string  mime type: text/html, etc
	 * @return  Email
	 */
	public function Message($body, $type = NULL)
	{
		if ( ! $type OR $type === 'text/plain')
		{
			// Set the main text/plain body
			$this->_message->setBody($body);
		}
		else
		{
			// Add a custom mime type
			$this->_message->addPart($body, $type);
		}

		return $this;
	}

	/**
	 * Add one or more email recipients..
	 *
	 *     // A single recipient
	 *     $email->to('john.doe@domain.com', 'John Doe');
	 *
	 *     // Multiple entries
	 *     $email->to(array(
	 *         'frank.doe@domain.com',
	 *         'jane.doe@domain.com' => 'Jane Doe',
	 *     ));
	 *
	 * @param   mixed    single email address or an array of addresses
	 * @param   string   full name
	 * @param   string   recipient type: to, cc, bcc
	 * @return  Email
	 */
	public function to($email, $name = NULL, $type = 'to')
	{
		if (is_array($email))
		{
			foreach ($email as $key => $value)
			{
				if (ctype_digit((string) $key))
				{
					// Only an email address, no name
					$this->to($value, NULL, $type);
				}
				else
				{
					// Email address and name
					$this->to($key, $value, $type);
				}
			}
		}
		else
		{
			// Call $this->_message->{add$Type}($email, $name)
			call_user_func(array($this->_message, 'add'.ucfirst($type)), $email, $name);
		}

		return $this;
	}

	/**
	 * Add a "carbon copy" email recipient.
	 *
	 * @param   string   email address
	 * @param   string   full name
	 * @return  Email
	 */
	public function cc($email, $name = NULL)
	{
		return $this->to($email, $name, 'cc');
	}

	/**
	 * Add a "blind carbon copy" email recipient.
	 *
	 * @param   string   email address
	 * @param   string   full name
	 * @return  Email
	 */
	public function bcc($email, $name = NULL)
	{
		return $this->to($email, $name, 'bcc');
	}

	/**
	 * Add email senders.
	 *
	 * @param   string   email address
	 * @param   string   full name
	 * @param   string   sender type: from, replyto
	 * @return  Email
	 */
	public function from($email, $name = NULL, $type = 'from')
	{
		// Call $this->_message->{add$Type}($email, $name)
		call_user_func(array($this->_message, 'add'.ucfirst($type)), $email, $name);

		return $this;
	}

	/**
	 * Add "reply to" email sender.
	 *
	 * @param   string   email address
	 * @param   string   full name
	 * @return  Email
	 */
	public function reply_to($email, $name = NULL)
	{
		return $this->from($email, $name, 'replyto');
	}

	/**
	 * Add actual email sender.
	 *
	 * [!!] This must be set when defining multiple "from" addresses!
	 *
	 * @param   string   email address
	 * @param   string   full name
	 * @return  Email
	 */
	public function sender($email, $name = NULL)
	{
		$this->_message->setSender($email, $name);
	}

	/**
	 * Set the return path for bounce messages.
	 *
	 * @param   string  email address
	 * @return  Email
	 */
	public function return_path($email)
	{
		$this->_message->setReplyPath($email);

		return $this;
	}

	/**
	 * Access the raw [Swiftmailer message](http://swiftmailer.org/docs/messages).
	 *
	 * @return  Swift_Message
	 */
	public function raw_message()
	{
		return $this->_message;
	}

	/**
	 * Attach a file.
	 *
	 * @param   string  file path
	 * @return  Email
	 */
	public function attach_file($path)
	{
		$this->_message->attach(Swift_Attachment::fromPath($path));

		return $this;
	}

	/**
	 * Attach content to be sent as a file.
	 *
	 * @param   binary  file contents
	 * @param   string  file name
	 * @param   string  mime type
	 * @return  Email
	 */
	public function attach_content($data, $file, $mime = NULL)
	{
		if ( ! $mime)
		{
			// Get the mime type from the filename
			$mime = File::mime_by_ext(pathinfo($file, PATHINFO_EXTENSION));
		}

		$this->_message->attach(Swift_Attachment::newInstance($data, $file, $mime));

		return $this;
	}

  /**
   * set the message and the subject from a file
   * 
   * @access  public 
   * @return  Object
   *
   * @author   drkwolf@gmail.com
   * @created  2012-10-31
   * @since   1.0
   */
  protected function set_sub_msg() 
  {
    $sub = NULL;
    $msg = '';
    if (($handle = fopen($this->_file, 'r')) !== FALSE) {

        $next_line = trim(fgets($handle));
        if ($next_line === '-- subject')
        {
          $sub = trim(fgets($handle));
        }
        else
        {
          Throw new Kohana_Exception('invalid mail file format :file', array(':file' => $path));
        }

        $next_line = trim(fgets($handle));
        if ($next_line !== '-- message')
        {
          Throw new Exception('invalid mail file format :file', array(':file',
            $path));
        }

      while (!feof($handle) ) {

        $msg .= fgets($handle);
      }

      fclose($handle);
    }

    if ( !empty($this->_data) )
    {
      $sub = strtr($sub, $this->_data);
      $msg = strtr($msg, $this->_data);
    }
    return $this
      ->subject($sub)
      ->message($msg, $this->_type);
  }

	/**
	 * Send the email. Failed recipients can be collected by passing an array.
	 *
	 * @param   array   failed recipient list, by reference
	 * @return  boolean
	 */
	public function send(array & $failed = NULL)
  {
    if ( !empty($this->_file))
      $this->set_sub_msg();

    //FIXME check of all the fields are set {2012-11-07, drkwolf}

    return Email::mailer()->send($this->_message, $failed);
	}


  # end swift api }}}

/*
  # copied from view render mail {{{



  /**
   * set the filename
   * 
   * @param   String $file : file name
   * @return  Object
   *
   * @author   drkwolf@gmail.com
   * @created  2012-10-31
   * @since   1.0
   */
  public function set_filename($file)
  {
    if (($path = Kohana::find_file(self::MAIL_PATH, $file)) === FALSE)
    {
      throw new Kohana_Exception('The requested message file :file could not be found', 
        array(':file' => $file)
      );
    }

		// Store the file path locally
    $this->_file = $path;
    return $this;
  }


  public function set_type($type) {
    //TODO check if the type is valid {2012-11-15, drkwolf}
    $this->_type = $type;
    return $this;
  }

  /**
   * set the language of the message file
   */
  public function set_lang($lang = NULL)
  {
    if ( $lang )
    {
      $this->_lang = $lang;
    }
    else
    {
      $this->_lang = I18n::lang();
    }
    return $this;
  }

  /**
   * Assigns a variable by name. Assigned values will be available as a
   * variable within the view file:
   *
   *     // This value can be accessed as $foo within the view
   *     $view->set('foo', 'my value');
   *
   * You can also use an array to set several values at once:
   *
   *     // Create the values $food and $beverage in the view
   *     $view->set(array('food' => 'bread', 'beverage' => 'water'));
   *
   * @param   string   variable name or an array of variables
   * @param   mixed    value
   * @return  $this
   */
  public function set($key, $value = NULL)
  {
    if (is_array($key))
    {
      foreach ($key as $name => $value)
      {
        $this->_data[$name] = $value;
      }
    }
    else
    {
      $this->_data[$key] = $value;
    }

    return $this;
  }

} // End email
