<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.beans.Bean', 'util.profiling.unittest.TestSuite');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  #[@lookupName('xp/test/UnittestRunner'),
  # @homeInterface('beans.unittest.UnittestRunnerHome')
  #]
  class UnittestRunnerBean extends Bean {
 
    /**
     * (Insert method's description here)
     *
     * @access  public
     * @param   string classname
     * @return  mixed results
     */ 
    #[@remote]
    function runTestsFrom($classname) {
      $suite= &new TestSuite();
      try(); {
        $suite->addTestClass(XPClass::forName($classname));
      } if (catch('Exception', $e)) {
        return throw($e);
      }

      return $suite->run();
    }
  
  } implements(__FILE__, 'beans.unittest.UnittestRunner');
?>
