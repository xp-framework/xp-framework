<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Blah
   *
   * @purpose  Dummy implementation
   */
  class DummyRpcImplementationHandler extends Object {
  
    /**
     * Dummy method
     *
     * @access  public
     * @return  string
     */
    #[@webmethod]
    function getImplementationName() {
      return $this->getClassName();
    }
  }
?>
