<?php
/* This class is part of the XP framework
 *
 * $Id: ThrowsTaglet.class.php 10509 2007-06-02 21:31:30Z friebe $ 
 */

  namespace text::doclet;

  uses('text.doclet.ThrowsTag', 'text.doclet.Taglet');

  /**
   * A taglet that represents the throws tag. 
   *
   * @test     xp://net.xp_framework.unittest.doclet.ThrowsTagletTest
   * @see      xp://text.doclet.TagletManager
   * @purpose  Taglet
   */
  class ThrowsTaglet extends lang::Object implements Taglet {
     
    /**
     * Create tag from text
     *
     * @param   text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  text.doclet.Tag
     */ 
    public function tagFrom($holder, $kind, $text) {
      sscanf($text, '%s %[^$]', $class, $condition);
      return new ThrowsTag($holder->root->classNamed($class), (string)$condition);
    }
  } 
?>
