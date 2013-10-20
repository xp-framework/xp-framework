<?php namespace net\xp_framework\unittest\util;



/**
 * Testcase for util.Properties class.
 *
 * @see   xp://net.xp_framework.unittest.util.AbstractPropertiesTest
 * @see   xp://util.Properties#fromString
 */
class StringBasedPropertiesTest extends AbstractPropertiesTest {

  /**
   * Create a new properties object from a string source
   *
   * @param   string source
   * @return  util.Properties
   */
  protected function newPropertiesFrom($source) {
    return \util\Properties::fromString($source);
  }
}
