<?php
/* This class is part of the XP framework
 *
 * $Id: ModelTaglet.class.php 9104 2007-01-03 17:13:06Z friebe $ 
 */

  namespace text::doclet;

  uses('text.doclet.ModelTag', 'text.doclet.Taglet');

  /**
   * A taglet that represents the model tag. 
   *
   * @see      xp://text.doclet.TagletManager
   * @purpose  Taglet
   */
  class ModelTaglet extends lang::Object implements Taglet {
     
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
