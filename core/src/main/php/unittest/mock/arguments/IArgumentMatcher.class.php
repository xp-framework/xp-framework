<?php
/* This interface is part of the XP framework
 *
 * $Id$
 */

/**
 * Interface for argument matchers
 *
 * @purpose  Argument matching in expectations
 */
  interface IArgumentMatcher {
    function matches($value);
  }
