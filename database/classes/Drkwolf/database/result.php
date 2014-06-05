<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Database result wrapper.  See [Results](/database/results) for usage and examples.
 *
 * @package    drkwolf/Database
 * @category   Query/Result
 * @author     drkwolf
 */
abstract class Drkwolf_Database_Result extends Kohana_Database_Result {

  /**
   * Return array for From::secect 
   *
   */
  public function select($head=NULL, $prefix=NULL, $id='id', $title='title')
  {
    if ( !empty($head))
    {
      $head = $prefix . __($head) . $prefix;
      return array(NULL => $head) + $this->as_array($id, $title);
    }
    else return $this->as_array($id, $title);
  }

  public function as_json($key = NULL, $value = NULL)
  {
    $results = array();

    if ($key === NULL AND $value === NULL)
    {
      // Indexed rows

      foreach ($this as $row)
      {
        $results[] = $row;
      }
    }
    elseif ($key === NULL)
    {
      // Indexed columns

      if ($this->_as_object)
      {
        foreach ($this as $row)
        {
          $results[] = $row->$value;
        }
      }
      else
      {
        foreach ($this as $row)
        {
          $results[] = $row[$value];
        }
      }
    }
    elseif ($value === NULL)
    {
      // Associative rows

      if ($this->_as_object)
      {
        foreach ($this as $row)
        {
          $results[$row->$key] = $row;
        }
      }
      else
      {
        foreach ($this as $row)
        {
          $results[$row[$key]] = $row;
        }
      }
    }
    else
    {
      // Associative columns

      if ($this->_as_object)
      {
        foreach ($this as $row)
        {
          $results[$row->$key] = $row->$value;
        }
      }
      else
      {
        foreach ($this as $row)
        {
          $results[$row[$key]] = $row[$value];
        }
      }
    }

    $this->rewind();

    return $results;
  }

} // End Database_Result
