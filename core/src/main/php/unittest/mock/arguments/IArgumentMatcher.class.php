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
    /**
     * Checks whether the provided parameter does match a certain criteria.
     * 
     * @param value mixed
     * @return bool
     */
    function matches($value);
  }
