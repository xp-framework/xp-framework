<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.ParamTag');

  /**
   * A taglet that represents the @param tag. 
   *
   * @see      xp://TagletManager
   * @purpose  Taglet
   */
  class ParamTaglet extends Object {
     
    /**
     * Create tag from text
     *
     * @access  public
     * @param   &Doc holder
     * @param   string kind
     * @param   string text
     * @return  &Tag
     */ 
    function &tagFrom(&$holder, $kind, $text) {
      list($type, $name, $text)= explode(' ', $text, 2);
      return new ParamTag($type, $name, $text);
    }

  } implements(__FILE__, 'text.doclet.Taglet');
?>
