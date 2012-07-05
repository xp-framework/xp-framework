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
      return (NULL === $type ? '' : $type.':').xp::stringOf($value);
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
        if ($j < $i && $k < $i) {
          $expect= '"'.$this->expect.'"';
          $actual= '"'.$this->actual.'"';
        } else {
          $expect= substr($this->expect, $i, $j+ 1- $i);
          $actual= substr($this->actual, $i, $k+ 1- $i);
          $c= $i <= $l && $i > 0;
          $expect= $c ? '"...'.$expect : '"'.$expect;                       // Common beginning
          $actual= $c ? '"...'.$actual : '"'.$actual;
          $expect.= ($j < $le- 1) ? '..."' : '"';                           // Common ending
          $actual.= ($k < $la- 1) ? '..."' : '"';
        }
      } else {
        $te= xp::typeOf($this->expect);
        $ta= xp::typeOf($this->actual);
        if ($this->expect instanceof Generic && $this->actual instanceof Generic) {
          $include= FALSE;
        } else {
          $include= $te !== $ta;
        }
        $expect= $this->stringOf($this->expect, NULL !== $this->expect && $include ? $te : NULL);
        $actual= $this->stringOf($this->actual, NULL !== $this->actual && $include ? $ta : NULL);
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
