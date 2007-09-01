<?php
/* This class is part of the XP framework
 *
 * $Id: ReturnTag.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace text::doclet;

  uses('text.doclet.Tag');

  /**
   * Represents an @return documentation tag
   *
   * @see      xp://Tag
   * @purpose  Tag
   */
  class ReturnTag extends Tag {
   
    /**
     * Constructor
     *
     * @param   string type
     * @param   string label
     */
    public function __construct($type, $label) {
      parent::__construct('return', $label);
      $this->type= $type;
    }
  }
?>
