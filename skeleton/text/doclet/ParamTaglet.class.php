<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.ParamTag', 'text.doclet.Taglet');

  /**
   * A taglet that represents the param tag. 
   *
   * @see      xp://text.doclet.TagletManager
   * @purpose  Taglet
   */
  class ParamTaglet extends Object implements Taglet {
     
    /**
     * Create tag from text
     *
     * @param   text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  text.doclet.Tag
     */ 
    public function tagFrom($holder, $kind, $text) {
      list($type, $name, $text)= explode(' ', $text, 3);
      return new ParamTag($type, $name, $text);
    }

  } 
?>
