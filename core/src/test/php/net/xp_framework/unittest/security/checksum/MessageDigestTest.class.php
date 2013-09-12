<?php namespace net\xp_framework\unittest\security\checksum;

use unittest\TestCase;
use security\checksum\MessageDigest;


/**
 * TestCase
 *
 * @see      xp://security.checksum.MessageDigest
 */
class MessageDigestTest extends TestCase {

  /**
   * Test register() method
   *
   */
  #[@test, @expect('lang.IllegalArgumentException')]
  public function registerTestClassAsImplementation() {
    MessageDigest::register('irrelevant', $this->getClass());
  }

  /**
   * Test supportedAlgorithms() method
   *
   */
  #[@test]
  public function supportedAlgorithms() {
    $a= MessageDigest::supportedAlgorithms();
    $this->assertTrue(is_array($a), 'Expected an array but have '.\xp::typeOf($a));
  }

  /**
   * Test newInstance() method
   *
   */
  #[@test, @expect('security.NoSuchAlgorithmException')]
  public function unsupportedAlgorithm() {
    MessageDigest::newInstance('unsupported');
  }
}
