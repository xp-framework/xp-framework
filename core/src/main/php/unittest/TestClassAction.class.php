<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Test class action
   *
   * @see   xp://unittest.TestCase
   */
  interface TestClassAction {

    /**
     * This method gets invoked before any test method of the given class is
     * invoked, and before any methods annotated with beforeTest.
     *
     * @param  lang.XPClass $c
     * @return void
     * @throws unittest.PrerequisitesNotMetError
     */
    public function beforeTestClass(XPClass $c);

    /**
     * This method gets invoked after all test methods of a given class have
     * executed, and after any methods annotated with afterTest
     *
     * @param  lang.XPClass $c
     * @return void
     */
    public function afterTestClass(XPClass $c);
  }
?>