<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.AssertException');

  /**
   * Assertion
   *
   * <quote>
   * Assertions should be used as a debugging feature only. You may use them 
   * for sanity-checks that test for conditions that should always be TRUE 
   * and that indicate some programming errors if not or to check for the 
   * presence of certain features like extension functions or certain system 
   * limits and features.
   * Assertions should not be used for normal runtime operations like input 
   * parameter checks. As a rule of thumb your code should always be able 
   * to work correctly if assertion checking is not activated.
   * </quote>
   * 
   * Example:
   * <code>
   *   function divide($y, $x) {
   *     Assert::that('${1} != 0', $x) or die(Assert::printException());
   *     return $y / $x;
   *   }
   * </code>
   *
   * @model    static
   * @purpose  Make assertian
   */
  class Assert extends Object {
    protected static $instance= NULL;
    public
      $exception= NULL;
      
    /**
     * Callback method for when an assertion failed
     *
     * @access  private
     * @param   string file
     * @param   int line
     * @param   string code
     */
    private function onAssertionFailed($file, $line, $code) {
      $this->exception= new AssertException(sprintf(
        'Assertion {%s} failed at line %d of %s',
        preg_replace('/\$_\[([0-9]+)\]/', '\${$1}', $code),
        $line,
        $file
      ));
    }
  
    /**
     * Get an instance
     *
     * @access  private
     * @return  &util.Assert
     */
    private function getInstance() {
      if (!isset(self::$instance)) {
        self::$instance= new Assert();
        assert_options(ASSERT_CALLBACK, array(&self::$instance, 'onAssertionFailed'));
        assert_options(ASSERT_WARNING, 0);
      }
      return $instance;
    }
  
    /**
     * Make an assertion
     *
     * @access  public
     * @param   string code
     * @param   mixed* args
     * @return  bool FALSE if assertion failed, TRUE otherwise
     */
    public function that() {
      static $instance;
      if (!isset($instance)) $instance= Assert::getInstance();
      
      $instance->exception= NULL;
      $_= func_get_args();
      return assert(preg_replace('/\$\{([0-9]+)\}/', '\$_[$1]', $_[0]));
    }
    
    /**
     * Retrieve the exception thrown by the assertion
     *
     * @access  public
     * @return  &util.AssertException
     */
    public function getException() {
      static $instance;
      if (!isset($instance)) $instance= Assert::getInstance();
      
      return $instance->exception;
    }
    
    /**
     * Print the exception thrown by the assertion
     *
     * @access  public
     */
    public function printException() {
      static $instance;
      if (!isset($instance)) $instance= Assert::getInstance();
      
      if (NULL !== $instance->exception) {
        $instance->exception->printStackTrace();
      }
    }
    
    /**
     * Activate assertions
     *
     * @access  public
     */
    public function activate() {
      assert_options(ASSERT_ACTIVE, 1);
    }
    
    /**
     * Deactivate assertions
     *
     * @access  public
     */
    public function deactivate() {
      assert_options(ASSERT_ACTIVE, 0);
    }
  }
?>
