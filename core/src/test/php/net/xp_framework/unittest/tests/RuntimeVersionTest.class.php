<?php namespace net\xp_framework\unittest\tests;

use unittest\actions\RuntimeVersion;

/**
 * Test test action "Runtime Version"
 */
class RuntimeVersionTest extends \unittest\TestCase {

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

  #[@test, @values(array('5.3.0', '5.4.0', '5.4.99'))]
  public function range($value) {
    $this->assertTrue(create(new RuntimeVersion('>=5.3.0,<5.5.0'))->verify($value));
  }

  #[@test, @values(array('5.2.99', '5.5.0'))]
  public function not_range($value) {
    $this->assertFalse(create(new RuntimeVersion('>=5.3.0,<5.5.0'))->verify($value));
  }

  #[@test, @values(array('1.2.0', '1.2.1', '1.2.99', '1.3.0', '1.99.99'))]
  public function next_significant_release_with_minor($value) {
    $this->assertTrue(create(new RuntimeVersion('~1.2'))->verify($value));
  }

  #[@test, @values(array('2.0.0', '2.2.0'))]
  public function not_next_significant_release_with_minor($value) {
    $this->assertFalse(create(new RuntimeVersion('~1.2'))->verify($value));
  }

  #[@test, @values(array('1.2.3', '1.2.99'))]
  public function next_significant_release($value) {
    $this->assertTrue(create(new RuntimeVersion('~1.2.3'))->verify($value));
  }

  #[@test, @values(array('1.1.0', '1.2.0', '1.2.2', '1.3.0'))]
  public function not_next_significant_release($value) {
    $this->assertFalse(create(new RuntimeVersion('~1.2.3'))->verify($value));
  }

  #[@test, @expect(class= 'unittest.PrerequisitesNotMetError', withMessage= '/Test not intended for this version/')]
  public function beforeTest_throws_exception() {
    create(new RuntimeVersion('1.0.0'))->beforeTest($this);
  }
}
