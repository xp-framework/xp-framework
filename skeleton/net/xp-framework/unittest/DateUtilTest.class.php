<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  
  uses('util.profiling.unittest.TestCase', 'util.DateUtil', 'util.Date');

  /**
   * Unittest the DateUtil class.
   *
   * @purpose  Unittest for util.DateUtil
   */
  class DateUtilTest extends TestCase {
  
    /**
     * Helper method to compare two objects with
     * their equals method.
     *
     * @access  public
     * @param   &lang.Object expect
     * @param   &lang.Object var
     * @param   string error
     */
    function assertEquivalent(&$expect, &$var, $error) {
      assert('$this->_test($expect->equals($var), $error, $expect, $var)');
    }

    /**
     * Test simple add operations on a usual date.
     *
     * @access  public
     */
    #[@test]
    function testSimpleAddition() {
      $date= &new Date(Date::mktime(12, 15, 11, 1, 1, 2000));
      
      $this->assertEquivalent(
        new Date(Date::mktime(12, 15, 30, 1, 1, 2000)),
        DateUtil::addSeconds($date, 19)
      );
      
      $this->assertEquivalent(
        new Date(Date::mktime(12, 44, 11, 1, 1, 2000)),
        DateUtil::addMinutes($date, 29)
      );
      
      $this->assertEquivalent(
        new Date(Date::mktime(13, 15, 11, 1, 1, 2000)),
        DateUtil::addHours($date, 1)
      );
      
      $this->assertEquivalent(
        new Date(Date::mktime(12, 15, 11, 1, 2, 2000)),
        DateUtil::addDays($date, 1)
      );
      
      $this->assertEquivalent(
        new Date(Date::mktime(12, 15, 11, 2, 1, 2000)),
        DateUtil::addMonths($date, 1)
      );
    }
  }
?>
