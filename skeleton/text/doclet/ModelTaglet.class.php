<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.ModelTag', 'text.doclet.Taglet');

  /**
   * A taglet that represents the model tag. 
   *
   * @see      xp://text.doclet.TagletManager
   * @purpose  Taglet
   */
  class ModelTaglet extends Object implements Taglet {
     
    /**
     * Create tag from text
     *
     * @param   text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  text.doclet.Tag
     */ 
    public function tagFrom($holder, $kind, $text) {
      return new ModelTag($kind, $text);
    }

  } 
?>
