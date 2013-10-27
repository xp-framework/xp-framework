<?php namespace net\xp_framework\unittest\io\collections;

use io\collections\iterate\IterationFilter;

/**
 * Accept-all filter
 *
 * @purpose  Iteration Filter
 */
class NullFilter extends \lang\Object implements IterationFilter {

  /**
   * Accepts an element
   *
   * @param   io.collections.IOElement $element
   * @return  bool
   */
  public function accept($element) {
    return true;
  }
} 
