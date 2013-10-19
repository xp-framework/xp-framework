<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Test action
   *
   * @see   xp://unittest.TestCase
   */
  interface TestAction {

    /**
     * This method gets invoked before a test method is invoked, and before
     * the setUp() method is called.
     *
     * @param  unittest.TestCase $t
     * @return void
     * @throws unittest.PrerequisitesNotMetError
     */
    public function beforeTest(TestCase $t);

    /**
     * This method gets invoked after the test method is invoked and regard-
     * less of its outcome, after the tearDown() call has run.
     *
     * @param  unittest.TestCase $t
     * @return void
     */
    public function afterTest(TestCase $t);
  }
?>