<?php
defined('SYSPATH') or die('No direct access allowed.');

/**
 * generate application-specific markup for forms.
 * 
 * @author drkwolf
 * @package Helpers
 * @version 1.0
 *
 */
class Drkwolf_Form_Bootstrap extends Drkwolf_Form_Widget { 

  public $error_class = 'help-line error';

  public function control_group($name, $forum, $label = NULL,  array $attributes = NULL)
  {
    $eclass= 'control-group';
    $espan = '';
    if ( isset($this->errors[$name]) ) {
      $eclass .= ' error';
      $espan = $this->addAlertSpan($this->errors[$name]);
    }

    if ( $label )
    {
      $label = '<label class="control-label" for="'.$name.'">'.$label.'</label>';
    }

    $attributes = $this->add_class($attributes, $eclass);
    return '<div '.HTML::attributes($attributes).'>'
      . $label
      .   '<div class="controls">'
      .   $forum
      .   $espan
      .   '</div>'
      . '</div>';
  }

  public function controls($input, $attributes = array())
  {
  }


  /**
   * $See parent::checkbox
   *
   * if the attibute has the checkbox set the it will be added
   */
  public function checkbox($name, $value = NULL, $checked = FALSE, array $attributes = NULL)
  {
    $label = Arr::get($attributes, 'label');
    $this->load_values($name, $value, $attributes);
    if ( $label !== Null)
    {
      return "<label class=\"checkbox\" name=\"$name\">"
        . Form::hidden($name, 0)
        . Kohana_Form::checkbox($name, $value, $checked, $attributes)
        . $label
        . $this->addAlertSpan((isset($this->errors[$name])?$this->errors[$name]:NULL), $attributes)
        .'</label>'; 
    }
    else
    {
      return $this->checkbox($name, $value, $checked, $attributes);
    }
  }

}
