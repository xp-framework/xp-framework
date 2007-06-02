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
      preg_match('/([^<\r\n]+<[^>]+>|[^\r\n ]+) ?([^\r\n ]+)? ?(.*)/', $text, $matches);
      return new ParamTag($matches[1], $matches[2], $matches[3]);
    }
  } 
?>
