<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.Doc');

  /**
   * Represents an annotation.
   *
   * @purpose  Documents an annotation
   */
  class AnnotationDoc extends Doc {
    var
      $value= NULL;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   mixed value
     */
    function __construct($name, $value) {
      $this->name= $name;
      $this->value= $value;
    }
  }
?>
