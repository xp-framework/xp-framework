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
     * @param   string code
     * @return  string
     */
    #[@webmethod]
    function highlight($code) {
      return @highlight_string($code, TRUE);
    }
  }
?>
