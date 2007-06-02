<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('text.doclet.ReturnTag', 'text.doclet.Taglet');

  /**
   * A taglet that represents the return tag. 
   *
   * @test     xp://net.xp_framework.unittest.doclet.ReturnTagletTest
   * @see      xp://text.doclet.TagletManager
   * @purpose  Taglet
   */
  class ReturnTaglet extends Object implements Taglet {
     
    /**
     * Create tag from text
     *
     * @param   text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  text.doclet.Tag
     */ 
    public function tagFrom($holder, $kind, $text) {
      preg_match('/([^<\r\n]+<[^>]+>|[^\r\n ]+) ?(.*)/', $text, $matches);
      return new ReturnTag($matches[1], $matches[2]);
    }
  } 
?>
