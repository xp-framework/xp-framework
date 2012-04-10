<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'util.log.LogContext'
  );

  /**
   * Tests LogContext class
   *
   */
  class LogContextTest extends TestCase {

    /**
     * LogContext::format() should return empty string
     *
     */
    #[@test]
    public function defaultFormatIsEmpty() {
      $this->assertEquals('', create(new LogContext())->format());
    }
  }
?>