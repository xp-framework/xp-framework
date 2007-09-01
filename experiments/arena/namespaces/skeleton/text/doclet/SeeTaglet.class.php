<?php
/* This class is part of the XP framework
 *
 * $Id: SeeTaglet.class.php 10509 2007-06-02 21:31:30Z friebe $ 
 */

  namespace text::doclet;

  uses('text.doclet.SeeTag', 'text.doclet.Taglet');

  /**
   * A taglet that represents the see tag. 
   *
   * @test     xp://net.xp_framework.unittest.doclet.SeeTagletTest
   * @see      xp://text.doclet.TagletManager
   * @purpose  Taglet
   */
  class SeeTaglet extends lang::Object implements Taglet {
  
    /**
     * Create tag from text
     *
     * @param   text.doclet.Doc holder
     * @param   string kind
     * @param   string text
     * @return  text.doclet.Tag
     */ 
    public function tagFrom($holder, $kind, $text) {
      sscanf($text, '%[^:]://%s %[^$]', $scheme, $urn, $comment);
      return new SeeTag($kind, (string)$comment, $scheme, $urn);
    }
  } 
?>
