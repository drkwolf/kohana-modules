<?php defined('SYSPATH') OR die('No Direct Script Access');

/**
 * Extension of the Kohana URL helper class.
 */
class Drkwolf_Helper_URL extends Kohana_URL 
{
    /**
     * Fetches the URL to the current request uri.
     *
     * example :
     * echo URL::current();            //  controller/action
     * echo URL::current(TRUE);        //  /base_url/controller/action
     * echo URL::current(TRUE, TRUE);  //  http://domain/base_url/controller/action
     *
     * @param   bool  make absolute url
     * @param   bool  add protocol and domain (ignored if relative url)
     * @return  string
     */
    public static function current($absolute = FALSE, $protocol = FALSE)
    {
        $url = Request::current()->uri();

        if($absolute === TRUE)
            $url = self::site($url, $protocol);

        return $url;
    }
}


