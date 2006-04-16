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
    
    /**
     * Non-web-invokeable method.
     *
     * @access  public
     * @return  bool
     */    
    function methodExistsButIsNotAWebmethod() {
      return TRUE;
    }

    /**
     * Dummy method
     *
     * @access  public
     * @return  string
     */
    #[@webmethod]
    function giveMeFault() {
      return throw(new ServiceException(403, 'This is a intentionally caused exception.'));
    }
  }
?>
