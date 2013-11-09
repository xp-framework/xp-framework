<?php namespace net\xp_framework\unittest\img;

use unittest\TestCase;
use unittest\TestAction;

/**
 * Tests image type support
 *
 * @see   php://imagetypes
 */
class ImageTypeSupport extends \lang\Object implements TestAction {
  protected $type= '';

  /**
   * Constructor
   *
   * @param string $type
   */
  public function __construct($type) {
    $this->type= $type;
  }

  /**
   * This method gets invoked before a test method is invoked, and before
   * the setUp() method is called.
   *
   * @param  unittest.TestCase $t
   * @throws unittest.PrerequisitesNotMetError
   */
  public function beforeTest(TestCase $t) { 
    if (!(imagetypes() & constant('IMG_'.$this->type))) {
      throw new PrerequisitesNotMetError('Image type not supported', null, array($this->type));
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
