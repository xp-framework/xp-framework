<?php
/* This class is part of the XP framework
 *
 * $Id: ThrowsTag.class.php 9104 2007-01-03 17:13:06Z friebe $ 
 */

  namespace text::doclet;

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
     * @param   text.doclet.ClassDoc exception
     * @param   string label
     */
    public function __construct($exception, $label) {
      parent::__construct('throws', $label);
      $this->exception= $exception;
    }
  }
?>
