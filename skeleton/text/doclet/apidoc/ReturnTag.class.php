<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Tag');

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
     * @access  public
     * @param   string type
     * @param   string label
     */
    function __construct($type, $label) {
      parent::__construct('return', $label);
      $this->type= $type;
    }
  }
?>
