<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.Tag');

  /**
   * Represents an @throws documentation tag
   *
   * @see      xp://Tag
   * @purpose  Tag
   */
  class ThrowsTag extends Tag {
    public
      $exception = NULL;

    /**
     * Constructor
     *
     * @param   &ClassDoc exception
     * @param   string label
     */
    public function __construct($exception, $label) {
      parent::__construct('throws', $label);
      $this->exception= $exception;
    }
  }
?>
