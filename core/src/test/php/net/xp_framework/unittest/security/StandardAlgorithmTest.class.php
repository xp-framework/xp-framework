<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'security.password.PasswordStrength',
    'util.log.Logger',
    'util.log.ConsoleAppender'
  );

  /**
   * TestCase
   *
   * @see      xp://security.password.PasswordStrength
   * @purpose  purpose
   */
  class StandardAlgorithmTest extends TestCase {
    protected
      $fixture= NULL;
    
    protected static
      $cat= NULL;
    
    static function __static() {
      // self::$cat= Logger::getInstance()->getCategory()->withAppender(new ConsoleAppender());
    }
  
    /**
     * Asserts a certain number is inbetween two others
     *
     * @param   int lo
     * @param   int hi
     * @param   int num
     * @param   string error default 'notbetween'
     * @throws  unittest.AssertionFailedError
     */
    protected function assertBetween($lo, $hi, $num, $error= 'notbetween') {
      if ($lo <= $num && $num <= $hi) return;
      $this->fail($error, $num, $lo.'..'.$hi);
    }
    
    /**
     * Set up testcase - initializes fixture
     *
     */
    public function setUp() {
      $this->fixture= PasswordStrength::getAlgorithm('standard');
      $this->fixture->setTrace(self::$cat);
    }
  
    /**
     * Test zero length password is very weak
     *
     */
    #[@test]
    public function zeroLength() {
      $this->assertBetween(0, 20, $this->fixture->strengthOf(''));
    }

    /**
     * Test a letters-only password is very weak
     *
     */
    #[@test]
    public function lettersOnly() {
      $this->assertBetween(0, 20, $this->fixture->strengthOf('abcdefg'));
    }

    /**
     * Test a numbers-only password is very weak
     *
     */
    #[@test]
    public function numbersOnly() {
      $this->assertBetween(0, 20, $this->fixture->strengthOf('12345678'));
    }

    /**
     * Test a short password is very weak
     *
     */
    #[@test]
    public function shortPassword() {
      $this->assertBetween(0, 20, $this->fixture->strengthOf('foo'));
    }

    /**
     * Test a mixed-upper and lowercase password with repeated characters is weak
     *
     */
    #[@test]
    public function mixedUpperAndLowerCaseWithRepitions() {
      $this->assertBetween(20, 40, $this->fixture->strengthOf('nOcHeInTe'));
    }

    /**
     * Test a mixed-upper and lowercase password is good
     *
     */
    #[@test]
    public function mixedUpperAndLowerCase() {
      $this->assertBetween(40, 60, $this->fixture->strengthOf('cOmPlExItY'));
    }

    /**
     * Test a password with numbers and letters is strong
     *
     */
    #[@test]
    public function mixedNumbersAndLetters() {
      $this->assertBetween(60, 80, $this->fixture->strengthOf('1nScor3z2'));
    }

    /**
     * Test a password with symbols and letters is strong
     *
     */
    #[@test]
    public function mixedSymbolsAndLetters() {
      $this->assertBetween(60, 80, $this->fixture->strengthOf('_nAg.Gros'));
    }

    /**
     * Test a password that meets all requirements is very strong
     *
     */
    #[@test]
    public function allRequirements() {
      $this->assertBetween(80, 100, $this->fixture->strengthOf('1,cO3kPiT'));
    }
  }
?>
