<?php defined('SYSPATH') OR die('No Direct Script Access');

/**
 * bismi-llāhi r-raḥmāni r-raḥīm, was salat wa salem al Moahamed abdih wa rasoulih
 */
/**
 * helper to format dates
 * 
 * @param   Integer $id
 * @return  Object
 *
 * @author   drkwolf@gmail.com
 * @created  2012-11-08
 * @since   1.0
 */
Class Drkwolf_Helper_Date extends Kohana_Date {

  /**
   * @var date format yyyy-mm-dd
   */
  const DATE_YMD = 'Y-m-d';

  /**
   * @var date format dd-mm-yyyy
   */
  const DATE_DMY = 'd-m-Y';

  /**
   * @var date format mm-dd-yyyy H:mm:
   */
  const DATETIME_YMD= 'Y-mm-d H:i';




  /**
   * return fuzzy time stamp from the given date
   * 
   * @param   int $timestamp
   * @param   int $locate_time : used to substructed from the time to calculate
   * the elapsed time
   * @return  string 
   *
   * @author   drkwolf@gmail.com
   * @created  2012-11-08
   * @since   1.0
   */
  public static function fuzzy_span($timestamp, $local_timestamp = NULL) # {{{
	{
    $suffix = true;
    $local_timestamp = ($local_timestamp === NULL) ? time() : (int) $local_timestamp;

    // Determine the difference in seconds
    $offset = abs($local_timestamp - $timestamp);

    if (! is_numeric($timestamp)) return $timestamp;

    if ($timestamp < 0) return __('Never');

    # get span {{{
    if ($offset < 1 * Date::MINUTE)
    {
      $span = $offset == 1 ? 'one second' : $offset . ' seconds';
    }
    elseif ($offset < 2 * Date::MINUTE)
    {
      $span = 'a minute';
    }
    elseif ($offset < 45 * Date::MINUTE)
    {
      $span = floor($offset / Date::MINUTE) . ' minutes';
    }
    elseif ($offset < 90 * Date::MINUTE)
    {
      $span = 'an hour';
    }
    elseif ($offset < 24 * Date::HOUR)
    {
      $span = floor($offset / Date::HOUR) . ' hours';
    }
    elseif ($offset < 48 * Date::HOUR)
    {
      $span = 'yesterday';
      $suffix = false;
    }
    elseif ($offset < 30 * Date::DAY)
    {
      $span = floor($offset / Date::DAY) . ' days';
    }
    elseif ($offset < 12 * Date::MONTH)
    {
      $months = floor($offset / Date::DAY / 30);
      $span = $months <= 1 ? 'one month' : $months . ' months';
    }
    else
    {
      $years = floor($offset / Date::DAY / 365);
      $span = $years <= 1 ? 'one year' : $years . ' years';
    } # span }}}

    if ($suffix)
    {
      if ($timestamp <= $local_timestamp)
      {
        // This is in the past
        $span .= ' ago';
      }
      else
      {
        // This in the future
        $span = ' in '.$span;
      } 

    }
    return $span;
  } # fuzzy_span()$ }}}

  /**
   * give the fuzzy time form a date string
   * 
   * @param   String $strTime : string time 
   * @return  String
   *
   * @author   drkwolf@gmail.com
   * @created  2012-11-08
   * @since   1.0
   */
  public static function fuzzy_sspan($strTime, $local_times_stamp = NULL)# {{{
  {
     return self::fuzzy_span(strtotime($strTime), $local_times_stamp);
  } # fuzzy_sspan}}}}


  /**
   * format date from type timestamp
   * 
   * @param   int    $timestamp : timstamp
   * @param   String $format : Time format
   * @return  String formated date
   *
   * @author   drkwolf@gmail.com
   * @created  2012-11-08
   * @since   1.0
   */
  //TODO use new DateTime('2000-01-01', new DateTimeZone('Pacific/Nauru')); {2012-11-08, drkwolf}
  public static function format($date, $format = NULL)# {{{
  {
    if ( $format === NULL )
    {
      $format = self::DATE_YMD;
    }

    if ( !is_null($date) ) 
    {
      $date = ($date == 'now') ? time() : strtotime(str_replace('/', '-', $date));

      if ( $date > 0 ) return Date($format, $date);
    }

    return null;
  } # format }}}

  /**
   * diff between tow dates "+6M 5D" 
   * 
   * @access  public static
   * @param   timestamp $start
   * @param   timestamp $end
   * @return  String
   *
   * author   drkwolf@gmail.com
   * created  2012-10-27
   * @since   1.0
   */
  public static function date_diff($start, $end) # {{{
  {
    $output;
    $date1 = new Date($end);
    $date2 = new Date($start);

    $y =  $date1->YEAR - $date2->YEAR;
    $m =  $date1->MONTH - $date2->MONTH;
    $d =  $date1->DAY - $date2->DAY;

    $y > 0 ? $output ="+$y ": $output .= "$y ";
    $m > 0 ? $output .="+$m ": $output .= "$m ";
    $d > 0 ? $output .="+$d": $output .= "$d";

    return $output;
  }

} # }}}
