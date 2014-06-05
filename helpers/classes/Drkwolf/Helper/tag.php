<?php
/**
 * usage 
 Tag::factory('div')
 ->content($content, $array_of_attr)
 ->insert( // tag('name', insert_or_append_bool)
   Tag::factory('p')
   ->content($content, attributes)
 )->append(
   Tag::factory('p')
  $content, $attributes
 )->append(
  ...
 )->render()
 
 *
 */
class Tag {

  # static {{{

  public static function factory($name)
  {
    return new Tag($name);
  }
  # end static }}}

  private $__name;
  private $__attributes;

  // content of the tag either string or tag
  private $__contents  = array();
  private $__next_tags = array();


  public function __construct($name, $content = NULL, $attributes = NULL)
  {
    $this->__name = $name;
    return $this;
  }


  /**
   * insert a content to the tags
   *
   */
  public function insert($element, $content = NULL , $attributes = NULL) 
  {
    if ( $element instanceof Tag )
    {
      $this->__contents[] = $element; 
    }
    else
    {
      $this->__contents = new Tag($element, $contents, $attributes);
    }
      return $this;
  }

  /**
   * Append new elelment/Tag to the function
   *
   */
  public function append($element, $content = NULL , $attributes = NULL) 
  {
    if ( $element instanceof Tag )
    {
      $this->__next_tags[] = $element;
    }
    else
    {
      $this->__contents[] = new Tag($element, $content, $attributes);
    }

    return $this;
  }

//TODO  cash object


  /**
   * renteder the tag
   * @return string
   * //TODO add caching
   */
  public  function render() 
  {
    return "<$this->__name $this->__attr()> $this->__nested() </$this->__name>" . $this->__next_tags();
  }




  # private {{{
  // render attribute
  private function __render_attr()
  {
    $out = '';
    foreach( $this->__attributes as $key => $value )
    {
      $out .= " $key=\"$value\" ";
    }  
    return $out;
  }

  // render nested contents
  private function __nested()
  {

  }
  # end private }}}
}
?>
