<?php
/* This class is part of the XP framework
 *
 * $Id: SeeTag.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace text::doclet;

  uses('text.doclet.Tag');

  /**
   * Represents a user-defined cross-reference to related documentation.
   *
   * @see      xp://Tag
   * @purpose  Tag
   */
  class SeeTag extends Tag {
    public
      $scheme  = '',
      $urn     = '';
    
    /**
     * Constructor
     *
     * @param   string name
     * @param   string text
     * @param   string scheme
     * @param   string urn
     */
    public function __construct($name, $text, $scheme, $urn) {
      parent::__construct($name, $text);
      $this->scheme= $scheme;
      $this->urn= $urn;
    }
  }
?>
