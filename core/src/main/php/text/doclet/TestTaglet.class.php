<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('text.doclet.TestTag', 'text.doclet.Taglet');

  /**
   * A taglet that represents the test tag. 
   *
   * @see      xp://text.doclet.TagletManager
   * @purpose  Taglet
   */
  class TestTaglet extends Object implements Taglet {
  
    /**
     * Create tag from text
     *
     * @param   text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  text.doclet.Tag
     */ 
    public function tagFrom($holder, $kind, $text) {
      sscanf($text, '%[^:]://%s', $scheme, $urn);
      return new TestTag($kind, $scheme, $urn);
    }
  } 
?>
