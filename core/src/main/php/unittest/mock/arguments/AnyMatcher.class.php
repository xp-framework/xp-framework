<?php

/* This class is part of the XP framework
 *
 */
  uses('unittest.mock.arguments.IArgumentMatcher');

 /**
  * Trivial argument matcher, that just returns true.
  *
  * @purpose Argument Matching
  */
  class AnyMatcher extends Object implements IArgumentMatcher {
    /**
     * Trivial matches implementations.
     * 
     * @param value mixed
     */
    public function matches($value) {
      return TRUE;
    }
  }
?>