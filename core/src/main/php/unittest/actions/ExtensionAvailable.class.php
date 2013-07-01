<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestAction', 'lang.Runtime');

  /**
   * Only runs this testcase if a given PHP extension is available
   *
   * @see   xp://lang.Runtime#extensionAvailable
   */
  class ExtensionAvailable extends Object implements TestAction {
    protected $extension= '';

    /**
     * Create a new ExtensionAvailable instance
     *
     * @param string extension The name of a PHP extension
     */
    public function __construct($extension) {
      $this->extension= $extension;
    }

    /**
     * This method gets invoked before a test method is invoked, and before
     * the setUp() method is called.
     *
     * @param  unittest.TestCase $t
     * @throws unittest.PrerequisitesNotMetError
     */
    public function beforeTest(TestCase $t) { 
      if (!Runtime::getInstance()->extensionAvailable($this->extension)) {
        throw new PrerequisitesNotMetError('PHP Extension not available', NULL, array($this->extension));
      }
    }

    /**
     * This method gets invoked after the test method is invoked and regard-
     * less of its outcome, after the tearDown() call has run.
     *
     * @param  unittest.TestCase $t
     */
    public function afterTest(TestCase $t) {
      // Empty
    }
  }
?>