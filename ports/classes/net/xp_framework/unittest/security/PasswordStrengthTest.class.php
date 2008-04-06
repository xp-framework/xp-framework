<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'security.password.PasswordStrength'
  );

  /**
   * TestCase for PasswordStrength entry point class
   *
   * @see      xp://security.password.PasswordStrength
   * @purpose  Unittest
   */
  class PasswordStrengthTest extends TestCase {
  
    /**
     * Test standard algortihm is always available
     *
     */
    #[@test]
    public function standardAlgorithm() {
      $this->assertClass(
        PasswordStrength::getAlgorithm('standard'), 
        'security.password.StandardAlgorithm'
      );
    }

    /**
     * Test setAlgorithm() / getAlgorithm() roundtrip
     *
     */
    #[@test]
    public function registerAlgorithm() {
      with ($class= newinstance('security.password.Algorithm', array(), '{
        public function strengthOf($password) { return 0; }
      }')->getClass()); {
        PasswordStrength::setAlgorithm('null', $class);
        $this->assertEquals($class, PasswordStrength::getAlgorithm('null')->getClass());
      }
    }

    /**
     * Test getAlgorithm() throws an exception when no algorithm is
     * registered by the given name
     *
     */
    #[@test, @expect('util.NoSuchElementException')]
    public function noSuchAlgorithm() {
      PasswordStrength::getAlgorithm('@@NON_EXISTANT@@');
    }

    /**
     * Test setAlgorithm() throws an exception when the given algorithm
     * is not a security.password.Algorithm subclass
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function registerNonAlgorithm() {
      PasswordStrength::setAlgorithm('object', $this->getClass());
    }
  }
?>
