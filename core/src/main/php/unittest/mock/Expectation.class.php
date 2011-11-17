<?php

/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.mock.arguments.IArgumentMatcher');

  /**
   * Expectation to a method call.
   *
   * @see xp://unittest.mock.Expectation
   * @purpose Mocking
   */
  class Expectation extends Object {

    private $return = null;
    /**
     * Gets the return value of the expectation
     * @return mixed
     */
    public function getReturn() {
      return $this->return;
    }
    /**
     * Sets the return value of the expectation.
     * 
     * @param mixed value
     */
    public function setReturn($value) {
      $this->return = $value;
    }

    private $repeat = 1;
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
     * @param int value
     */
    public function setRepeat($value) {
      $this->repeat = $value;
    }

    private $exception= null;
    /**
     * Gets the exception, that is thrown on a method call.
     *
     * @return lang.Throwable
     */
    public function getException() {
      return $this->exception;
    }
    /**
     * Sets the exception that is to be thrown on a method call.
     *
     * @param lang.Throwable The exception.
     */
    public function setException(Throwable $exception) {
      $this->exception= $exception;
    }
    private $actualCalls = 0;
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
     */
    public function incActualCalls() {
      $this->actualCalls += 1;
    }

    /**
     * Indicates whether the actual calls have reached the maximum number
     * repetitions.
     *
     * @return boolean
     */
    public function canRepeat() {
      return $this->repeat == -1 //unlimited repeats
      || $this->actualCalls < $this->repeat; //limit not reached
    }

    private $args = array();

    /**
     * Gets the argument sepecifications for this expectation.
     *
     * @return array[]
     */
    public function getArguments() {
      return $this->args;
    }

    /**
     * Sets the argument sepecifications for this expectation.
     *
     * @param mixed[] args
     */
    public function setArguments($args) {
      $this->args = $args;
    }

    /**
     * Indicates whether the passed argument list matches the expectation's
     * argument list.
     * 
     * @param mixed[] args
     * @return boolean
     */
    public function doesMatchArgs($args) {
      if (sizeof($this->args) != sizeof($args))
        return false;

      for ($i = 0; $i < sizeof($args); ++$i)
        if (!$this->doesMatchArg($i, $args[$i]))
          return false;

      return true;
    }

    /**
     * Indicates whether the argument on postion $pos machtes the specified
     * value.     * 
     * @param int pos
     * @param mixed $value
     * @return boolean
     */
    private function doesMatchArg($pos, $value) {
      $argVal = $this->args[$pos];

      if($argVal instanceof IArgumentMatcher) {
        
        return $argVal->matches($value);
      }
      
      return $this->_compare($argVal, $value);
    }

    /**
     * Checks whether the passed arguments are equal.
     * 
     * FIXME: This is a duplication from TestCase.
     *
     * @param mixed a
     * @param mixed b
     * @return boolean
     */
    private function _compare($a, $b) { //FIXME: this is a duplication from TestCase
      if (is_array($a)) {
        if (!is_array($b) || sizeof($a) != sizeof($b))
          return FALSE;

        foreach (array_keys($a) as $key) {
          if (!$this->_compare($a[$key], $b[$key]))
            return FALSE;
        }
        return TRUE;
      }

      return $a instanceof Generic ? $a->equals($b) : $a === $b;
    }

  }

?>