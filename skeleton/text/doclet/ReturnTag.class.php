<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.Tag');

  /**
   * Represents an @return documentation tag
   *
   * @see      xp://Tag
   * @purpose  Tag
   */
  class ReturnTag extends Tag {
    public
      $type = NULL;
   
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
