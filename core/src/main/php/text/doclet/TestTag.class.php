<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.doclet.Tag');

  /**
   * Represents a user-defined cross-reference to related documentation.
   *
   * @see      xp://text.doclet.Tag
   * @purpose  Tag
   */
  class TestTag extends Tag {
    public
      $scheme  = '',
      $class   = '';
    
    /**
     * Constructor
     *
     * @param   string name
     * @param   string scheme
     * @param   string class
     */
    public function __construct($name, $scheme, $class) {
      parent::__construct($name, '');
      $this->scheme= $scheme;
      $this->class= $class;
    }
  }
?>
