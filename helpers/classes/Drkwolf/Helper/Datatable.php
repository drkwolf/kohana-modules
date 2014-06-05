<?php defined('SYSPATH') or die('No direct access allowed.');

/**
 * Data table class
 *
 * Supports: storing any data, generic formatters, pagination, styling (via formatters + table class settings)
 *
 */
class Drkwolf_Helper_Datatable {

  public static function factory($columns, $config = array())
  {
    return new Drkwolf_Helper_Datatable($columns, $config);
  }

	/**
	 * Columns - array of column configs.
	 *
	 * array(
	 *    key_in_rows => array(
	 *       label => string,
	 *       class => '' OR 'CSS class for <TH> element',
	 *       sortable => false OR true,
	 *       formatter => function for formatting cell data with this key
	 *    )
	 * )
	 *
	 * @var array
	 */
	private $columns;


  /**
   * list of columns fields that must be displayed
   */
  private $__context = array();

	/**
	 * Configuration - array of general config for this datatable
	 *
	 * array(
	 *    paginator => KO3 Paginator class instance,
	 *    sortable => default value for sortable in columns,
	 *    default_sort => default column by which data is sorted,
	 *    default_dir => default direction in which data is sorted
   *    contexts =>  list of fields to be shown
   *    
	 * )
	 *
	 * @var array
	 */
	private $__config;

	/**
	 * Rows - array consisting of rows of data items.
	 * @var array
	 */
	private $rows;

	public function __construct($columns, $config = array())
	{
		$this->columns = $columns;
		$this->__config = $config;

    return $this;
	}

	/**
	 * Add a row.
	 * @param array $row
	 * @param int $index (Optional) row index.
	 */
	public function add($row, $index = null)
	{
		if (! is_numeric($index))
		{
			$index = count($this->rows) - 1;
		}
		$this->rows[$index] = $row;

    return $this;
	}

	/**
	 * Set the rows to the given value, replacing any old values.
	 * @param array $rows
	 */
	public function values($rows, $context = NULL) # {{{
	{
		$this->rows = $rows;

    if ( $context )
    {
      if ( isset($this->__config['contexts'][$context]) )
      {
        $this->__context = $this->__config['contexts'][$context];
      }
      else
      {
        throw new Exception("Context $context was't found in config");
      }
    }
    else
    {
      $this->__context = array_keys($this->columns);
    }

    return $this;
	} # }}}

	/**
	 * Get a single row, or all the rows.
	 * @param int $index (Optional) row index.
	 * @return array
	 */
	function get($index = null) # {{{
	{
		if (is_numeric($index))
		{
			return $this->rows[$index];
		}
		return $this->rows;
	} # End get }}}

	/**
	 * Delete a row.
	 * @param int $index Row index.
	 */
	public function delete($index) # {{{
	{
		if (isset($this->rows[$index]))
		{
			unset($this->rows[$index]);
		}
	} # end delete }}}

	/**
	 * Render the datatable.
	 *
	 * Configuration: defaults to $_REQUEST
	 * array(
	 *    sort => column key,
	 *    dir => 'ASC' or 'DESC',
	 *    page => int (page number)
	 * )
	 *
	 * @return string
	 */
	function render($params = null) # {{{
	{
		// create table
		$result = '<table' . HTML::attributes($this->__config) . '>';
		if (! $params)
		{
			$params = $_REQUEST;
		}
		// get row sort info
		$sort = isset($params['sort']) ? $params['sort'] : false;
		if (! $sort && ! empty($this->__config['default_sort']))
		{
			$sort = $this->__config['default_sort'];
		}
		$dir = isset($params['dir']) ? $params['dir'] : false;
		if (! $dir && ! empty($this->__config['default_dir']))
		{
			$dir = $this->__config['default_dir'];
		}
		$page = isset($params['page']) ? $params['page'] : 1;
		// create heading
		$result .= '<thead><tr>';

    foreach( $this->__context as $name )
    {
      $column = $this->columns[$name];
			if (! empty($column['sortable']) || ! empty($this->__config['sortable']) && ! isset($column['sortable']))
			{
				if (( $name == $sort && $dir == 'DESC' ) || $name != $sort)
				{
					$result .= '<th scope="col" name="'.$name.'" dir="desc">'  
					        . HTML::anchor(URL::site(Request::current()->uri(), true) . URL::query(array(
						        	'page' => $page, 
						        	'sort' => $name, 
						        	'dir' => null
						        )), 
						        ( isset($column['label']) ? $column['label'] : $name ), 
						        ( $name == $sort ? array('class' => 'desc') : null )) 
					        . '</th>';
				}
				else
				{
					$result .= '<th scope="col">' 
					        . HTML::anchor(URL::site(Request::current()->uri(), true) . URL::query(array(
						        	'page' => $page, 
						        	'sort' => $name, 
						        	'dir' => 'DESC'
					        	)), 
					        	( isset($column['label']) ? $column['label'] : $name ), 
					        	( $name == $sort ? array( 'class' => 'asc') : null )) 
				        	. '</th>';
				}
			}
			else
			{
				$result .= '<th scope="col">' 
				        . ( isset($column['label']) ? $column['label'] : $name ) 
				        . '</th>';
			}
		}
		$result .= '</tr></thead>';
		// print data
		$result .= '<tbody>';
		// array_merge renumbers the array, this is needed because unset (via deleteRow) will leave gaps in the indices.
		$this->rows = array_merge($this->rows);
		$end = count($this->rows);
		for ($i = 0; $i < $end; $i ++)
		{
			$result .= '<tr';
			if (isset($this->rows[$i]['_class']))
			{
				$result .= ' class="' . $this->rows[$i]['_class'] . '"';
			}
			else
			{
				if (( $i % 2 ) == 0)
				{
					$result .= ' class="odd"';
				}
				else
				{
					$result .= ' class="even"';
				}
			}
			$result .= '>';

			foreach ($this->__context as $column)
      {
        $settings = $this->columns[$column];
				$value = '';
				// the value does not have to even exist for formatters to work
				// since they might just use some other columns in the data.
				if (isset($settings['formatter']) && is_callable($settings['formatter']))
				{
					$value = call_user_func($settings['formatter'], 
					$this->rows[$i]);
				}
				else 
					if (isset($this->rows[$i][$column]))
					{
						$value = $this->rows[$i][$column];
					}
				$result .= '<td>' . $value . '</td>';
			}
			$result .= '</tr>';
		}
		$result .= '</tbody>';
		$result .= '</table>';
		return $result;
	} # end render }}}


  public function __toString() { return $this->render(); }


}
