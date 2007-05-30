<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'util.DateUtil'
  );

  /**
   * Test Date utility class
   *
   * @see      xp://util.DateUtil
   * @purpose  Unit Test
   */
  class DateUtilTest extends TestCase {

    /**
     * Test simple add operations on a usual date.
     *
     */
    #[@test]
    public function testSimpleAddition() {
      $date= new Date(Date::mktime(12, 15, 11, 1, 1, 2000));
      
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
     */
    #[@test]
    public function testSimpleSubstraction() {
      $date= new Date(Date::mktime(12, 15, 11, 1, 1, 2000));
      
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
     */
    #[@test]
    public function testLeapYear() {
      $date= new Date(Date::mktime(0, 0, 0, 2, 1, 2000));
      
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
     */
    #[@test]
    public function testNonLeapYear() {
      $date= new Date(Date::mktime(0, 0, 0, 2, 1, 1999));
      
      $this->assertEquals(
        new Date(Date::mktime(0, 0, 0, 3, 1, 1999)),
        DateUtil::addMonths($date, 1)
      );
      
      $this->assertEquals(
        new Date(Date::mktime(0, 0, 0, 3, 3, 1999)),
        DateUtil::addDays($date, 30)
      );
    }
    
    /**
     * Tests the compare method
     *
     */
    #[@test]
    public function comparison() {
      $this->assertTrue(DateUtil::compare(new Date('1977-12-14'), new Date('1980-05-28')) < 0, 'a < b') &&
      $this->assertTrue(DateUtil::compare(new Date('1980-05-28'), new Date('1977-12-14')) > 0, 'a > b') &&
      $this->assertTrue(DateUtil::compare(new Date('1980-05-28'), new Date('1980-05-28')) == 0, 'a == b');
    }

    /**
     * Tests using the compare method as a usort() callback
     *
     * @see     php://usort
     */
    #[@test]
    public function sorting() {
      $list= array(
        new Date('1977-12-14'),
        new Date('2002-02-21'),
        new Date('1980-05-28'),
      );
      
      usort($list, array('DateUtil', 'compare'));
      $this->assertEquals(new Date('1977-12-14'), $list[0], 'offset 0') &&
      $this->assertEquals(new Date('1980-05-28'), $list[1], 'offset 1') &&
      $this->assertEquals(new Date('2002-02-21'), $list[2], 'offset 2');
    }
    
    /**
     * Tests the compare method
     *
     */
    #[@test]
    public function testBeginAndEndOfWeek() {
      $this->assertEquals(
        new Date(Date::mktime(0, 0, 0, 1, 14, 2007)),
        DateUtil::getBeginningOfWeek(new Date('2007-1-18'))
      );
      $this->assertEquals(
        new Date(Date::mktime(23, 59, 59, 1, 20, 2007)),
        DateUtil::getEndOfWeek(new Date('2007-1-18'))
      );
    }
  }
?>
