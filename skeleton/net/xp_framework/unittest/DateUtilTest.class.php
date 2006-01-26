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
     * Test simple add operations on a usual date.
     *
     * @access  public
     */
    #[@test]
    function testSimpleAddition() {
      $date= &new Date(Date::mktime(12, 15, 11, 1, 1, 2000));
      
      $this->assertEquals(
        new Date(Date::mktime(12, 15, 30, 1, 1, 2000)),
        DateUtil::addSeconds($date, 19)
      );
      
      $this->assertEquals(
        new Date(Date::mktime(12, 44, 11, 1, 1, 2000)),
        DateUtil::addMinutes($date, 29)
      );
      
      $this->assertEquals(
        new Date(Date::mktime(13, 15, 11, 1, 1, 2000)),
        DateUtil::addHours($date, 1)
      );
      
      $this->assertEquals(
        new Date(Date::mktime(12, 15, 11, 1, 2, 2000)),
        DateUtil::addDays($date, 1)
      );
      
      $this->assertEquals(
        new Date(Date::mktime(12, 15, 11, 2, 1, 2000)),
        DateUtil::addMonths($date, 1)
      );
    }
    
    /**
     * Test basic add operations with negative values.
     *
     * @access  public
     */
    #[@test]
    function testSimpleSubstraction() {
      $date= &new Date(Date::mktime(12, 15, 11, 1, 1, 2000));
      
      $this->assertEquals(
        new Date(Date::mktime(12, 14, 52, 1, 1, 2000)),
        DateUtil::addSeconds($date, -19)
      );
      
      $this->assertEquals(
        new Date(Date::mktime(11, 46, 11, 1, 1, 2000)),
        DateUtil::addMinutes($date, -29)
      );
      
      $this->assertEquals(
        new Date(Date::mktime(11, 15, 11, 1, 1, 2000)),
        DateUtil::addHours($date, -1)
      );
      
      $this->assertEquals(
        new Date(Date::mktime(12, 15, 11, 12, 31, 1999)),
        DateUtil::addDays($date, -1)
      );
      
      $this->assertEquals(
        new Date(Date::mktime(12, 15, 11, 12, 1, 1999)),
        DateUtil::addMonths($date, -1)
      );
    }
    
    /**
     * Check leap year handling
     *
     * @access  public
     */
    #[@test]
    function testLeapYear() {
      $date= &new Date(Date::mktime(0, 0, 0, 2, 1, 2000));
      
      $this->assertEquals(
        new Date(Date::mktime(0, 0, 0, 3, 1, 2000)),
        DateUtil::addMonths($date, 1)
      );
      
      $this->assertEquals(
        new Date(Date::mktime(0, 0, 0, 3, 2, 2000)),
        DateUtil::addDays($date, 30)
      );
    }
    
    /**
     * Check non-leap year handling
     *
     * @access  public
     */
    #[@test]
    function testNonLeapYear() {
      $date= &new Date(Date::mktime(0, 0, 0, 2, 1, 1999));
      
      $this->assertEquals(
        new Date(Date::mktime(0, 0, 0, 3, 1, 1999)),
        DateUtil::addMonths($date, 1)
      );
      
      $this->assertEquals(
        new Date(Date::mktime(0, 0, 0, 3, 3, 1999)),
        DateUtil::addDays($date, 30)
      );
    }
  }
?>
