<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.Tag');

  /**
   * Represents an @param documentation tag
   *
   * @see      xp://Tag
   * @purpose  Tag
   */
  class ParamTag extends Tag {
    var
      $type= '',
      $name= '';

    /**
     * Constructor
     *
     * @access  public
     * @param   string type
     * @param   string name
     * @param   string label
     */
    function __construct($type, $name, $label) {
      parent::__construct('type', $label);
      $this->type= $type;
      $this->name= $name;
    }  
  }
?>
