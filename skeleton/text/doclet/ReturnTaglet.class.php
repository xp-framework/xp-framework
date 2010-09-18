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
      for ($parse= $text.' ', $i= 0, $s= strlen($parse), $brackets= 0; $i < $s; $i++) {
        if (' ' === $parse{$i} && 0 === $brackets) {
          return new ReturnTag(substr($parse, 0, $i), (string)substr($parse, $i+ 1, -1));
        } else if ('<' === $parse{$i}) {
          $brackets++;
        } else if ('>' === $parse{$i}) {
          $brackets--;
        }
      }
      return new ReturnTag('void', $text);
    }
  } 
?>
