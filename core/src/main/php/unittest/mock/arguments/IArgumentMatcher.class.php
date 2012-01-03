<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Interface for argument matchers
   *
   */
  interface IArgumentMatcher {

    /**
     * Checks whether the provided parameter does match a certain criteria.
     * 
     * @param   var value
     * @return  bool
     */
    public function matches($value);
  }
?>
