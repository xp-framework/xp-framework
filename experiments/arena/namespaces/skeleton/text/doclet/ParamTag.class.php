<?php
/* This class is part of the XP framework
 *
 * $Id: ParamTag.class.php 10502 2007-06-02 21:01:37Z friebe $ 
 */

  namespace text::doclet;

  uses('text.doclet.Tag');

  /**
   * Represents an @param documentation tag
   *
   * @see      xp://text.doclet.TagTag
   * @purpose  Tag
   */
  class ParamTag extends Tag {
    public
      $type= '',
      $name= '';

    /**
     * Constructor
     *
     * @param   string type
     * @param   string name
     * @param   string label
     */
    public function __construct($type, $name, $label) {
      parent::__construct('type', $label);
      $this->type= $type;
      $this->name= $name;
    }  
  }
?>
