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
     * @param   string code
     * @return  string
     */
    #[@webmethod]
    public function highlight($code) {
      return @highlight_string($code, TRUE);
    }
  }
?>
