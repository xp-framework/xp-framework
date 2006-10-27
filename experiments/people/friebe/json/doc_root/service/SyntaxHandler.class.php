<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Serverside implementation
   *
   * @purpose  JSON RPC
   */
  class SyntaxHandler extends Object {
 
    /**
     * Returns highlighted html
     *
     * @access  public
     * @return  bool success
     */
    #[@webmethod]
    function highlight($entry) {
      return highlight_string($entry, TRUE);
    }
  }
?>
