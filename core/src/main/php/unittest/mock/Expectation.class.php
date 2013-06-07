<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.mock.arguments.IArgumentMatcher', 'util.Objects');

  /**
   * Expectation to a method call.
   *
   */
  class Expectation extends Object {
    private $methodName           = '';
    private $return               = NULL;
    private $repeat               = -1;
    private $exception            = NULL;
    private $isInPropertyBehavior = FALSE;
    private $actualCalls          = 0;
    private $args                 = array();

    /**
     * Constructor
     *
     * @param   string methodName
     */
    public function __construct($methodName) {
      $this->methodName= $methodName;
    }

    /**
     * Gets the method name of the expectation
     *
     * @return  string
     */
    public function getMethodName() {
      return $this->methodName;
    }
    
    /**
     * Gets the return value of the expectation
     *
     * @return  var
     */
    public function getReturn() {
      return $this->return;
    }

    /**
     * Sets the return value of the expectation.
     * 
     * @param   var value
     */
    public function setReturn($value) {
      $this->return= $value;
    }

    /**
     * Gets the number of repetions of this expectation.
     * 
     * @return int
     */
    public function getRepeat() {
      return $this->repeat;
    }

    /**
     * Sets the number of repetions of this expectation.
     * 
     * @param   int value
     */
    public function setRepeat($value) {
      $this->repeat= $value;
    }

    /**
     * Gets the exception, that is thrown on a method call.
     *
     * @return  lang.Throwable
     */
    public function getException() {
      return $this->exception;
    }

    /**
     * Sets the exception that is to be thrown on a method call.
     *
     * @param   lang.Throwable exception.
     */
    public function setException(Throwable $exception) {
      $this->exception= $exception;
    }
    
    /**
     * Changes a setter/getter (as well as the corresponding getter/setter) to 
     * be in property mode.
     *
     */
    public function setPropertyBehavior() {
      $this->isInPropertyBehavior= TRUE;
    }

    /**
     * Indicates whether the expectation is in property behaviour mode.
     *
     * @return  bool
     */
    public function isInPropertyBehavior() {
      return $this->isInPropertyBehavior;
    }

    /**
     * Gets the number of actual calls for this expectation.
     *
     * @return int
     */
    public function getActualCalls() {
      return $this->actualCalls;
    }

    /**
     * Increases the actual calls by one.
     *
     */
    public function incActualCalls() {
      $this->actualCalls+= 1;
    }

    /**
     * Indicates whether the actual calls have reached the maximum number
     * repetitions.
     *
     * @return bool
     */
    public function canRepeat() {
      return (
        -1 === $this->repeat ||                 // Unlimited repeats, or...
        $this->actualCalls < $this->repeat      // Limit reached
      );
    }

    /**
     * Gets the argument sepecifications for this expectation.
     *
     * @return  var[]
     */
    public function getArguments() {
      return $this->args;
    }

    /**
     * Sets the argument sepecifications for this expectation.
     *
     * @param   var[] args
     */
    public function setArguments($args) {
      $this->args= $args;
    }

    /**
     * Indicates whether the passed argument list matches the expectation's
     * argument list.
     * 
     * @param   var[] args
     * @return  bool
     */
    public function doesMatchArgs($args) {
      if (sizeof($this->args) != sizeof($args)) return FALSE;
      for ($i= 0; $i < sizeof($args); ++$i) {
        if (!$this->doesMatchArg($i, $args[$i])) return FALSE;
      }
      return TRUE;
    }

    /**
     * Indicates whether the argument on postion $pos machtes the specified
     * value.
     *
     * @param   int pos
     * @param   var value
     * @return  bool
     */
    private function doesMatchArg($pos, $value) {
      $argVal= $this->args[$pos];
      if ($argVal instanceof IArgumentMatcher) {
        return $argVal->matches($value);
      } else {
        return Objects::equal($argVal, $value);
      }
    }

    /**
     * Cerates a string representation
     *
     * @return string
     */
    public function toString() {
      return sprintf(
        "%s(Calling %s(%d arg(s)) %s)@{\n".
        "  [args                ] %s\n".
        "  [return              ] %s\n".
        "  [exception           ] %s\n".
        "  [isInPropertyBehavior] %s\n".
        "  [actualCalls         ] %d\n".
        "}",
        $this->getClassName(),
        $this->methodName,
        sizeof($this->args),
        $this->repeat === -1 ? '**' : '* '.$this->repeat,
        xp::stringOf($this->args, '  '),
        xp::stringOf($this->return, '  '),
        xp::stringOf($this->exception, '  '),
        xp::stringOf($this->isInPropertyBehavior),
        $this->actualCalls
      );
    }
  }
?>
