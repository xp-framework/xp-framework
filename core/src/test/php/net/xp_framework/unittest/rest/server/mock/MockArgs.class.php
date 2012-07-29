<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Mock args class
   *
   */
  class MockArgs extends Object {
    
    /**
     * Method without functionality to be used by tests.
     *
     * @param   string id
     */
    public function simpleMethod($id) { }

    /**
     * Method without functionality to be used by tests.
     *
     * @param   string id
     * @param   string title
     * @param   string name
     */
    private function methodWithMultipleArguments($id, $title, $name) { }
    
    /**
     * Method without functionality to be used by tests.
     *
     * @param   string arg1
     * @param   string arg2 default NULL
     */
    private function methodWithOptionalArguments($arg1, $arg2= 'default') { }
    
    /**
     * Method without functionality to be used by tests.
     *
     * @param   lang.Object another
     */
    private function methodWithAnotherArgument($another) { }
  }
?>
