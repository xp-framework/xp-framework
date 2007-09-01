<?php
/* This class is part of the XP framework
 *
 * $Id: AnnotationDoc.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace text::doclet;

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
     * @param   string name
     * @param   mixed value
     */
    public function __construct($name, $value) {
      $this->name= $name;
      $this->value= $value;
    }
  }
?>
