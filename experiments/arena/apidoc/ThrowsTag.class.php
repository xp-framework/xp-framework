<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('Tag');

  /**
   * Represents an @throws documentation tag
   *
   * @see      xp://Tag
   * @purpose  Tag
   */
  class ThrowsTag extends Tag {
    var
      $exception = NULL;

    /**
     * Constructor
     *
     * @access  public
     * @param   &ClassDoc exception
     * @param   string label
     */
    function __construct(&$exception, $label) {
      parent::__construct('throws', $label);
      $this->exception= &$exception;
    }
  }
?>
