<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.actions.IsPlatform');

  /**
   * Test test action "Is Platform"
   */
  class IsPlatformTest extends TestCase {

    #[@test]
    public function can_create() {
      new IsPlatform('Windows');
    }

    #[@test]
    public function verify_current_platform() {
      $this->assertTrue(create(new IsPlatform('*'))->verify());
    }

    #[@test]
    public function verify_platform_with_same_name_as_os() {
      $this->assertTrue(create(new IsPlatform('Windows'))->verify('Windows'));
    }

    #[@test]
    public function verify_platform_case_insensitively() {
      $this->assertTrue(create(new IsPlatform('WINDOWS'))->verify('Windows'));
    }

    #[@test]
    public function verify_platform_matching_leading_segment() {
      $this->assertTrue(create(new IsPlatform('WIN'))->verify('Windows'));
    }

    #[@test]
    public function verify_platform_substring() {
      $this->assertFalse(create(new IsPlatform('DOW'))->verify('Windows'));
    }

    #[@test]
    public function verify_platform_with_different_name() {
      $this->assertFalse(create(new IsPlatform('Windows'))->verify('MacOS'));
    }

    #[@test]
    public function negative_verify_platform_with_different_name() {
      $this->assertTrue(create(new IsPlatform('!Windows'))->verify('MacOS'));
    }

    #[@test]
    public function negative_verify_platform_with_same_name() {
      $this->assertFalse(create(new IsPlatform('!Windows'))->verify('Windows'));
    }

    #[@test, @values(array('Windows', 'MacOS', 'Un*x'))]
    public function verify_platform_selection_negatively($value) {
      $this->assertTrue(create(new IsPlatform('!*BSD'))->verify($value));
    }

    #[@test, @values(array('FreeBSD', 'OpenBSD'))]
    public function verify_platform_selection($value) {
      $this->assertTrue(create(new IsPlatform('*BSD'))->verify($value));
    }

    #[@test, @values(array('FreeBSD', 'OpenBSD'))]
    public function verify_platform_alternatively($value) {
      $this->assertTrue(create(new IsPlatform('FreeBSD|OpenBSD'))->verify($value));
    }
  }
?>
