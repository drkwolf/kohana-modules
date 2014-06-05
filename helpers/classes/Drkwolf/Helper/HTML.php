<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 *
 * @author drkwolf
 * @package default
 * @version 1.0
 */

/**
 * Helper Html extension
 */
class Drkwolf_Helper_HTML extends Kohana_HTML {

  /**
   * desc
   * 
   * author   drkwolf@gmail.com
   * @access  public
   * @param   name|type
   * @return  returnval
   * author   drkwolf@gmail.com
   * created  2012-03-25
   */
  public static function anchor2($uri, $title = NULL, array $attributes = NULL, $protocol = NULL, $index = TRUE)
  {
    $cont = Request::$current->controller();

    $auri = explode('/', $uri);
    
    //FIXME uri not starting by / it bugs
// echo     debug::vars($auri, $cont, $uri);

    if ( !empty($auri) AND $auri[1] === $cont)
    {
      if (empty($attributes))
      {
        $attributes= array('id' => 'active');
      }
      else
      {
        $attributes['id'] = 'active';
      }

    }

    return parent::anchor($uri, $title, $attributes, $protocol, $index);
    
  } # anchor }}}

}
