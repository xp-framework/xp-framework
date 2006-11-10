<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.SeeTag');

  /**
   * A taglet that represents the see tag. 
   *
   * @see      xp://text.doclet.TagletManager
   * @purpose  Taglet
   */
  class SeeTaglet extends Object {
  
    /**
     * Create tag from text
     *
     * @access  public
     * @param   &text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  &text.doclet.Tag
     */ 
    function &tagFrom(&$holder, $kind, $text) {
      sscanf($text, '%[^:]://%s %[^$]', $scheme, $urn, $comment);
      return new SeeTag($kind, $comment, $scheme, $urn);
    }

  } implements(__FILE__, 'text.doclet.Taglet');
?>
