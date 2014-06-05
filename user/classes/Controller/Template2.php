<?php defined('SYSPATH') OR die('No Direct Script Access');

/**
 * my applicaltion template
 * static content are in PATH.public foldet
 * 
 * @author drkwolf@gmail.com
 * @package User
 */
 //TODO rename it : authTemplate
Class Controller_Template2 extends Kohana_Controller_Template {

  /** 
   * @var string $template : default template file /view/site.php
   */
  public $template = 'site';  
  /**
   * @var string $media : path to media files default : /public/
   */
  protected $media = 'media';

  /**
   * @var obj $session : reference to session object
   */
  protected $session;

  /**
   * set the content's of the page layout
   * @layout: an array with :
   *  key1 => array(content1, content2, ...)
   */
  protected function _layout($comps) # _layout {{{
  {
    foreach( $comps as $key => $values )
    {
      foreach( $values as $value )
      {
        //echo Debug::vars($this->template->{$key});
        $this->template->{$key}[] = $value; // add new content
      } 
    }

  } # layout }}}
 
  /**
   * redirect to the last url, used after user login
   */
  public static function redirect2() 
  {
    $ref = $this->request->referrer();
    $base = URL::base(true);
    $action_name = substr($ref, strlen($base), strlen($ref));
	
    if ( $action_name === 'user/access_denied') $ref = 'user/profile';

    $this->redirect($ref);
  }



  /**
   * Controls access for the whole controller, if not set to FALSE we will only 
   * allow user roles specified
   *
   * Can be set to a string or an array, for example array('login', 'admin') or 'login'
   */
  public $auth_required = FALSE;

  /** Controls access for separate actions
   * 
   * Examples:
   * 'adminpanel' => 'admin' will only allow users with the role 
   * admin to access action_adminpanel
   * 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
   */
  public $secure_actions = FALSE;

  public function __construct(Request $request, Response $response)
  {
    parent::__construct($request, $response);
  }

  public function before()# {{{
    {

        // This codeblock is very useful in development sites:
        // What it does is get rid of invalid sessions which cause exceptions, which may happen
        // 1) when you make errors in your code.
        // 2) when the session expires!
//         try
//         { 
//             $this->session = Session::instance();
//         }
//         catch (ErrorException $e)
//         { 
//             session_destroy();
//         }
        // Execute parent::before first
        parent::before();
        // Open session
        $this->session = Session::instance();

        // set browser language

        # debug {{{
//         echo debug::vars($this->session);
        # }}}

        //if we're not logged in, but auth type is orm. gives us chance to auto login
        $supports_auto_login = new ReflectionClass(get_class(Auth::instance()));
        $supports_auto_login = $supports_auto_login->hasMethod('auto_login');
        if(!Auth::instance()->logged_in() && $supports_auto_login)
        {
          Auth::instance()->auto_login();
        }

        // Check user auth and role
        $action_name = Request::current()->action();

        $has_perm_ = TRUE; //default access is public
        if ( is_array($this->secure_actions) && array_key_exists($action_name, $this->secure_actions) )
        {
          $has_perm_ = Auth::instance()->logged_in($this->secure_actions[$action_name]);
        }
        elseif ($this->auth_required !== FALSE)
        {
          $has_perm_ = Auth::instance()->logged_in($this->auth_required);
        }

        if ( $has_perm_ === FALSE)
        {
          if (Auth::instance()->logged_in())
          {
            // user is logged in but not on the secure_actions list
            $this->access_denied();
          }
          else
          {
            $this->login_required();
          }
        }                                          


        ## set media and layout
        if( $this->request->action() === 'public' ) # Media : contents
        {
          $this->auto_render = false;
        }
        else
        {
          // validation errors
          View::set_global('errors', array());
          // default form value
          View::set_global('default', array());
          $this->template->styles = array();
          $this->template->scripts = array();

          $this->template->title = '';
          $this->template->menu = array();
          $this->template->breadcrumb = array();

          $this->template->right = array();
          $this->template->left = array();
          $this->template->content = '';

          // Load Markdown support
          // define('MARKDOWN_PARSER_CLASS', 'Blog_Markdown');
          //         if ( ! class_exists('Markdown', FALSE))
          //         { 
          //             // Load Markdown support
          //             require Kohana::find_file('vendor', 'markdown/markdown');
          //         }
          // Namespace the markdown parser
          //         Blog_Markdown::$base_url  = URL::site($this->guide->uri()).'/'.$module.'/';
          //         Blog_Markdown::$image_url = URL::site($this->media->uri()).'/'.$module.'/';
      }
    } # action_before}}}}

  public function after()# {{{
  {
    if( $this->auto_render )
    {
      //$styles = array(
      //'public'    
      //);
      //$scripts = array();

      //$this->template->styles = array_merge( $this->template->styles, self::$styles );
      //$this->template->scripts = array_merge( $this->template->scripts, self::$scripts );

   }

    return parent::after();
  } # afer}}}}

  # actions {{{
  public function action_create() # {{{
  {
      $this->action_edit();
  } # action_edit }}}
  

  #//FIXME remove
  /**
   * used by Markdown editor to preview.
   * @category action json
   * @depricated (Move to Markdown Controller)
   */
  public function action_parse() # {{{
  {
    $this->auto_render = false;
    $view = View::factory('preview');
    $view->title  = 'Preview';

    $parser = new MarkdownExtra_Parser;
    $view->content = $parser->transform($this->request->post('data'));
    echo $view->render();
  } # action_parse }}}

   /**
   * ajax action setter
   * must be ovverriden by action
   * 
   * @category action json
   *
   * @redirect content/index
   *
   * @author   drkwolf@gmail.com
   * @created  2012-11-14
   * @since   1.0
   */
  public function action_setdata() # {{{
  {
    $key = $this->request->post('key');
    $key = 'mod1.opt1';
    switch($this->request->param('name'))
    {
    case 'profile':
      echo debug::vars( Arr::path(Profile::import(), $key) );
      break;
    case 'profile2':
      Profile::put($key, 'ddd');
      break;
    }
  
    # perm & errors {{{
    //if (is_numeric($id))
    //{
    //  if ( !$->loaded()) return $this->error();
    //  if ( !$->has_perm('action')) $this->access_denied();
    //} 
    //else
    //{
    //  if ( !$->has_perm('create')) $this->access_denied(__('Need create permission'));
    //  return $this->error();
    //}
    # }}}
//  
    # set content {{{
  
    # end contents }}}
  
    //$this->ajax_as_json($); //send and exist if json 
    //$this->ajax_as_html($); //send and exist if html
    $this->template->content = debug::vars($this->request->param());
  } # action_setdata }}}
  
  
  /**
    * Set the user language
    * @author drkwolf
    * @since 1
    * @api
    */
  public function action_set_lang()
  {
    $this->lang =  Lang::set_lang($this->request->post('lang'));

    // Log::instance()->add(Log::INFO, "file : user/class/controller/template2::action_set_lang");
    // Log::DEBUG("language set at: ".$this->lang);

    $ref = $this->request->referrer();
    $this->redirect($ref);
  }
   # End actions }}}


  /**
   * simple filter, 
   * @param ORM $model: orm model
   * @param array $def_filters : default filter
   * @param string $order_by : 
   * @param string $dir : direction default 'ASC' 
   * @return  ORM 
   * 
   * TODO remove 
   *
   */
  public function search($model, $filter_by = null, $order_by = NULL, $dir = 'ASC') # {{{
  {
    $filters = $this->request->query();

    if( empty($filters)) {
      $filters = $filter_by;
    }

   //if ( $model instanceof String) $model = ORM::factory($model);
    $model = ORM::factory($model);

    $t_cols = $model->table_columns();

    foreach( $filters as $filter => $value )
    {
    	// add filter only if it exist in db
      if ( array_key_exists($filter, $t_cols) ) $model->where($filter, '=', $value);
    }

    if ( !empty($order_by) )
    {
      $model->order_by($order_by, $dir);
    }

    return $model;

  } # action_search }}}

  /**
   * redirect to  access denied view
   * @param String $path : redirect path, default : 'user/access_denied'
   * @redirect user/access_denied
   * @api
   * 
   * TODO for example:
   * - handle json requests by returning a http error code and a json object
   * - redirect to a different failure page from one part of the application
   */
  public function access_denied($path = 'user/access_denied')
  {
    // Log::instance()->add(Log::NOTICE, 'access_denied');
    $this->redirect($path);
  }

  /**
   * redirect to login action and put the the current action's url in session
   * 
   * @param String $path : login action path
   * @redirect user/login 
   * @api
   */
  public function login_required($path = 'user/login') # {{{
  {
    // Log::instance()->add(Log::NOTICE, 'login required');
    Session::instance()->set('returnUrl', $this->request->url());
    HTTP::redirect($path);
  } # }}}

  /**
   * send error message to the client
   * @message : message to display default is `Content not found`
   * TODO delete and replace by error module throw new HTTP_Exception_404($message);
   */
  public function error($message= 'Content not found', $status = 404)# {{{
  {
    # todo client ip or username
    // Log::instance()->add(Log::INFO, "file : user/class/controller/Template2::error");
    // Log::instance()->add(Log::NOTICE, '404 Error client: url:');

    $this->response->status($status);
    $this->template->title = 'Site - Error'; //FIXME put something relevant
    $this->template->content = View::factory('errors/error')
    ->set('message', $message)
	->set('status', $status);

    # send error for ajax
    $this->ajax_as_json(array('error'=> $status), false, $message);

  } # error}}}}


  # @section json {{{
  
  /**
   * set contents:
   * * tow type fo view :
   * - html : the content is set 
   * - json : init form is_ajax and can be rest by the action
   * @param array $options : options for contents
   *    - formated Boolean : html view
   *    - asJson   Boolean: for javascript
   *    - view     VIew : view object
   *    - errors   Array : validation errors.
   *    - model    ORM : orm/db_result object
   *    - filter   Array : key filter @see Helper_JSON
   */
  protected function _set_content(Array $options)
  {
      $defaults = array_merge( 
          array(
          'formated' => null,
          'asJson'  => $this->request->is_ajax(),
          'view'     => null, //View need when formated:true
          'model'    => null, // needed when formted:false,and asJson:true
          'filters'  => array(), // required attributes to be sent as json.
          'errors' => array(),
          ), $options
      );
      
      if($defaults['formated']) 
      {
          // view is required !
          if( !$defaults['view']) 
              throw new Kohana_Exception("Valid view is required for content to generated");
            
          if ($defaults['asJson']) echo $defaults['view']->__toString();
          else $this->template->content = $defaults['view'];
        }
        else {
            $this->auto_render = False;
            // model is required 
          if( !$defaults['model'])
              throw new Kohana_Exception("Valid model is required for content to generated");
          
          $data = Helper_JSON::encode_model($defaults['model'], $defaults['filters']);
          $this->__set_JSON_content($data, $defaults['errors']);
        }
  }
  
  /**
   * format reponse and sent it to client as json,
   *
   * @param $data Array : data to be sent
   * @param $errors Array: validation errors
   * @return String (json)
   */
  private function __set_JSON_content($data, $errors = NULL)
  {
    $msg = array(
      'state' => empty($errors),
      'data'  => $data,
      'errors'=> $errors
    );
    echo json_encode($msg);
  }
  
  /**
   * if ajax is set send the content as ajax otherwise set the template content
   * @param $string $content : the view's content
   * @param bool $json : send put the the content in json form.
   * 
   * @api
   * @depricated use : set_content.
   */
  //TODO change the name
  //FIXME remove $json param and use as_json
  public function ajax_as_html($content, $json= false)# {{{
  {
    if( $this->request->is_ajax() )
    {
      $this->auto_render = false;

      if ($json)
      {
        $data['status'] = true;
        $data['data'] = $content->__toString();
        $data['message'] = '';
        echo json_encode($data);
      }
      elseif ($content instanceof View)
      {
        echo $content->__toString();
      }
      else
      {
        echo $content;
      }
      exit;
    }
    else
    {
      $this->template->content = $content;
    }
  } # ajax_as_html }}}

  /**
   * return json data to client 
   * @data : ORM object or array, when status is false the data sould contain the errors
   * @success : 0 and 1 of ture and false and can containe http status
   * @msg: simple message
   * TODO : change name to  
   * @api
   * @return : is_ajax
   * @depricated use : set_content.
   */
  public function ajax_as_json($data, $status = true, $msg = '')# {{{
  {
      if( $this->request->is_ajax() )
      {
          $this->auto_render = false;

          //$json['success'] = $success;
          $json['status'] = $status;
          $json['message'] = $msg;
          $json['data'] = ($data instanceof ORM)? $data->as_array(): $data;

          echo json_encode($json);
          exit;
      }
      return false;
  } # ajax_as_json }}}

 
  # @end json }}}
  
  ///////////////////////////////////////////////////////////////////
  // Css, js, html
  // TODO https://github.com/OpenBuildings/asset-merger
  //////////////////////////////////////////////////////////////////

  # @section javascript {{{
  /**
   * init all template variables 
   * - scripts : javascripts file
   * - styles : css
   * - contents :
   */

  public static $js_body = array();
  /**
  * @vars array of scripts to load on the template
  */
  public static $scripts = array();
  /**
   * collect javascript code from views and insert it in the main view
   * 
   * @param String $code : javascript code
   * @param String $name : $name of the script default NULL, usefull 
   * for debugging (name displayed in the body)
   * @since 1.0
   * 
   *  
   * The main body should contain this code to 
   * <code> echo Controller_App::js_body() </code>
   * @api
   
   */
   //TODO remame it add_block
  public static function js_add_onload($code, $name=NULL, $top=False)
  {
    if ( $name != NULL )
    {
      self::$js_body[$name] = $code;
    }
    else {
      self::$js_body[] = $code;
    }
  }
  
   /**
   * Add a script.
   * 
   * @param   string $path : script path
   *
   * @author   drkwolf@gmail.com
   * @created  2012-11-10
   * @since   1.0
   */
  public static function add_script($path) 
  {
    if ( !in_array($path, self::$scripts))
    { 
      self::$scripts[] = $path;
    }
  }
  
 /**
  * dump the js code
  */
  public static function js_body($js_view = Null) 
  {
    $out = '';

    //add scripts
    foreach( self::$scripts as $path )
    {
      $out .= HTML::script($path);
    }

    $out .= '<script id="onload" type="text/javascript">$(document).ready(function() {';

      foreach( self::$js_body as $name => $block)
      {
        if(!is_numeric($name))  $out .= "\n".'//file:'.$name."\n";
        $out .= $block;
      }

      if ( $js_view )
      {
        $out .= View::factory($js_view);
      }

    return $out."});</script>";
  }

 
 

  /**
   * @vars array of styles to load on the template
   */
  protected static $styles = array();
  /**
   * Add a style.
   * 
   * @param   string $path : style path
   *
   * @author   drkwolf@gmail.com
   * @created  2012-11-10
   * @since   1.0
   */
  public static function add_style($path) 
  {
    if ( !in_array($path, self::$styles))
    { 
      self::$styles[] = $path;
    }
  }

  # javacript$ }}}

  ///////////////////////////////////////////////////////////////////
  // Testing function
  ///////////////////////////////////////////////////////////////////
  
  /**
   * route static/view
   */
  public function action_static()
  {
  	$view_path = 'templates/'.$this->request->query('view');
	//todo check file exist
	$view = View::factory($view_path);
//	$this->ajax_as_html($view);
	$this->auto_render = false;
	echo $view;
  }
  // end test

}
