<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.actions.ExtensionAvailable');

  /**
   * Test test action "Extension Available"
   */
  class ExtensionAvailableTest extends TestCase {

    #[@test]
    public function can_create() {
      new ExtensionAvailable('core');
    }

    #[@test]
    public function verify_core_extension() {
      $this->assertTrue(create(new ExtensionAvailable('core'))->verify());
    }

    #[@test]
    public function verify_non_existant_extension() {
      $this->assertFalse(create(new ExtensionAvailable('@@non-existant@@'))->verify());
    }
  }
?>
