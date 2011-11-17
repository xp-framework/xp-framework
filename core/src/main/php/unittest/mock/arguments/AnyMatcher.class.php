<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.mock.arguments.IArgumentMatcher');

 /**
  * Trivial argument matcher, that just returns true.
  *
  * @purpose Argument Matching
  */
  class AnyMatcher extends Object implements IArgumentMatcher {
    public function matches($value) {
      return true;
    }
  }
?>