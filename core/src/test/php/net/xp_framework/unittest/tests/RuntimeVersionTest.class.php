<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.actions.RuntimeVersion');

  /**
   * Test test action "Runtime Version"
   */
  class RuntimeVersionTest extends TestCase {

    #[@test]
    public function can_create() {
      new RuntimeVersion('5.3.0');
    }

    #[@test]
    public function exact_version_match() {
      $this->assertTrue(create(new RuntimeVersion('5.3.0'))->verify('5.3.0'));
    }

    #[@test, @values(array('4.3.0', '5.2.0', '5.2.99', '5.3.0RC1', '5.3.0alpha', '5.3.0beta', '5.3.1', '6.3.0'))]
    public function negation($value) {
      $this->assertTrue(create(new RuntimeVersion('!=5.3.0'))->verify($value));
    }

    #[@test]
    public function not_negation() {
      $this->assertFalse(create(new RuntimeVersion('!=5.3.0'))->verify('5.3.0'));
    }

    #[@test, @values(array('5.3.0', '5.3.6', '5.3.26'))]
    public function wildcard_match($value) {
      $this->assertTrue(create(new RuntimeVersion('5.3.*'))->verify($value));
    }

    #[@test, @values(array('4.3.0', '5.2.0', '5.2.99', '5.3.0RC1', '5.3.0alpha', '5.3.0beta'))]
    public function smaller_than($value) {
      $this->assertTrue(create(new RuntimeVersion('<5.3.0'))->verify($value));
    }

    #[@test, @values(array('4.3.0', '5.2.0', '5.2.99'))]
    public function smaller_than_minor($value) {
      $this->assertTrue(create(new RuntimeVersion('<5.3'))->verify($value));
    }

    #[@test, @values(array('4.3.0', '5.2.0', '5.2.99', '5.3.0RC1', '5.3.0alpha', '5.3.0beta', '5.3.0'))]
    public function smaller_than_or_equal_to($value) {
      $this->assertTrue(create(new RuntimeVersion('<=5.3.0'))->verify($value));
    }

    #[@test, @values(array('5.3.0', '5.3.1', '6.3.0'))]
    public function not_smaller_than($value) {
      $this->assertFalse(create(new RuntimeVersion('<5.3.0'))->verify($value));
    }

    #[@test, @values(array('5.3.0RC1', '5.3.0alpha', '5.3.0beta', '5.3.0', '5.3.1', '6.3.0'))]
    public function not_smaller_than_minor($value) {
      $this->assertFalse(create(new RuntimeVersion('<5.3'))->verify($value));
    }

    #[@test, @values(array('5.3.1', '6.3.0'))]
    public function not_smaller_than_or_equal_to($value) {
      $this->assertFalse(create(new RuntimeVersion('<=5.3.0'))->verify($value));
    }

    #[@test, @values(array('5.3.1', '5.3.99', '6.3.0'))]
    public function larger_than($value) {
      $this->assertTrue(create(new RuntimeVersion('>5.3.0'))->verify($value));
    }

    #[@test, @values(array('5.3.0RC1', '5.3.0alpha', '5.3.0beta', '5.3.0', '5.3.1', '5.3.99', '6.3.0'))]
    public function larger_than_minor($value) {
      $this->assertTrue(create(new RuntimeVersion('>5.3'))->verify($value));
    }

    #[@test, @values(array('5.3.0', '5.3.1', '5.3.99', '6.3.0'))]
    public function larger_than_or_equal_to($value) {
      $this->assertTrue(create(new RuntimeVersion('>=5.3.0'))->verify($value));
    }

    #[@test, @values(array('5.3.0', '5.3.0RC1', '5.3.0alpha', '5.3.0beta', '5.2.99', '4.3.0'))]
    public function not_larger_than($value) {
      $this->assertFalse(create(new RuntimeVersion('>5.3.0'))->verify($value));
    }

    #[@test, @values(array('5.2.99', '4.3.0'))]
    public function not_larger_than_minor($value) {
      $this->assertFalse(create(new RuntimeVersion('>5.3'))->verify($value));
    }

    #[@test, @values(array('5.3.0RC1', '5.3.0alpha', '5.3.0beta', '4.3.0'))]
    public function not_larger_than_or_equal_to($value) {
      $this->assertFalse(create(new RuntimeVersion('>=5.3.0'))->verify($value));
    }

    #[@test, @values('5.3.0', '5.4.0', '5.4.99')]
    public function range($value) {
      $this->assertTrue(create(new RuntimeVersion('>=5.3.0,<5.5.0'))->verify($value));
    }

    #[@test, @values('5.2.99', '5.5.0')]
    public function not_range($value) {
      $this->assertFalse(create(new RuntimeVersion('>=5.3.0,<5.5.0'))->verify($value));
    }
  }
?>
