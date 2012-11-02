<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Indicates an assertion failed
   *
   * @test    xp://net.xp_framework.unittest.tests.AssertionMessagesTest
   */
  class AssertionFailedError extends XPException {
    const CONTEXT_LENGTH = 20;

    public
      $actual       = NULL,
      $expect       = NULL;

    /**
     * Constructor
     *
     * @param   string message
     * @param   string errorcode
     * @param   var actual default NULL
     * @param   var expect default NULL
     */
    public function __construct($message, $actual= NULL, $expect= NULL) {
      parent::__construct((string)$message);
      $this->actual= $actual;
      $this->expect= $expect;
    }

    /**
     * Creates a string representation of a given value.
     *
     * @param   var value
     * @param   string type NULL if type name should be not included.
     * @return  string
     */
    protected function stringOf($value, $type) {
      return (NULL === $value || NULL === $type ? '' : $type.':').xp::stringOf($value);
    }

    /**
     * Compacts a string
     *
     * @param  string s
     * @param  int p common postfix offset
     * @param  int s common suffix offset
     * @param  int l length of the given string
     */
    protected function compact($str, $p, $s, $l) {
      $result= substr($str, $p, $s- $p);
      if ($p > 0) {
        $result= ($p < self::CONTEXT_LENGTH ? substr($str, 0, $p) : '...').$result; 
      }
      if ($s < $l) {
        $result= $result.($l- $s < self::CONTEXT_LENGTH ? substr($str, $s) : '...');
      }
      return '"'.$result.'"';
    }

    /**
     * Return compound message of this exception.
     *
     * @return  string
     */
    public function compoundMessage() {
      if (is_string($this->expect) && is_string($this->actual)) {
        $la= strlen($this->actual);
        $le= strlen($this->expect);
        for ($i= 0, $l= min($le, $la); $i < $l; $i++) {                     // Search from beginning
          if ($this->expect{$i} !== $this->actual{$i}) break;
        }
        for ($j= $le- 1, $k= $la- 1; $k >= $i && $j >= $i; $k--, $j--) {    // Search from end
          if ($this->expect{$j} !== $this->actual{$k}) break;
        }
        $expect= $this->compact($this->expect, $i, $j+ 1, $le);
        $actual= $this->compact($this->actual, $i, $k+ 1, $la);
      } else if ($this->expect instanceof Generic && $this->actual instanceof Generic) {
        $expect= $this->stringOf($this->expect, NULL);
        $actual= $this->stringOf($this->actual, NULL);
      } else {
        $te= xp::typeOf($this->expect);
        $ta= xp::typeOf($this->actual);
        $include= $te !== $ta;
        $expect= $this->stringOf($this->expect, $include ? $te : NULL);
        $actual= $this->stringOf($this->actual, $include ? $ta : NULL);
      }

      return sprintf(
        "%s { expected [%s] but was [%s] using: '%s' }\n",
        $this->getClassName(),
        $expect,
        $actual,
        $this->message
      );
    }
    
    /**
     * Retrieve string representation
     *
     * @return  string
     */
    public function toString() {
      $s= $this->compoundMessage();
      
      // Slice first stack trace element, this is always unittest.TestCase::fail()
      // Also don't show the arguments
      for ($i= 1, $t= sizeof($this->trace); $i < $t; $i++) {
        $this->trace[$i]->args= NULL;
        $s.= $this->trace[$i]->toString();
      }
      return $s;
    }
  }
?>
