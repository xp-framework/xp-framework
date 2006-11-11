<?php
/* This class is part of the XP framework
 *
 * $Id: AnnotationDoc.class.php 8361 2006-11-09 12:45:47Z friebe $ 
 */

  uses('text.doclet.Doc');

  /**
   * Represents an annotation.
   *
   * @purpose  Documents an annotation
   */
  class AnnotationDoc extends Doc {
    public
      $value= NULL;
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string name
     * @param   mixed value
     */
    public function __construct($name, $value) {
      $this->name= $name;
      $this->value= $value;
    }
  }
?>
