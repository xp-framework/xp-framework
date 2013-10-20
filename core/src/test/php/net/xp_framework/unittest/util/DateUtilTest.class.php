<?php namespace net\xp_framework\unittest\util;
 
use unittest\TestCase;
use util\DateUtil;
use util\TimeZone;


/**
 * Test Date utility class
 *
 * @see      xp://util.DateUtil
 * @purpose  Unit Test
 */
class DateUtilTest extends TestCase {
  protected
    $fixture= null;

  /**
   * Sets up this test
   *
   */
  public function setUp() {
    $this->fixture= \util\Date::create(2000, 1, 1, 12, 15, 11, new TimeZone('Europe/Berlin'));
  }

  /**
   * Test simple add operations on a usual date.
   *
   */
  #[@test]
  public function addSeconds() {
    $this->assertEquals(
      \util\Date::create(2000, 1, 1, 12, 15, 30, new TimeZone('Europe/Berlin')),
      DateUtil::addSeconds($this->fixture, 19)
    );
  }
    
  /**
   * Test simple add operations on a usual date.
   *
   */
  #[@test]
  public function addMinutes() {
    $this->assertEquals(
      \util\Date::create(2000, 1, 1, 12, 44, 11, new TimeZone('Europe/Berlin')),
      DateUtil::addMinutes($this->fixture, 29)
    );
  }
    
  /**
   * Test simple add operations on a usual date.
   *
   */
  #[@test]
  public function addHours() {
    $this->assertEquals(
      \util\Date::create(2000, 1, 1, 13, 15, 11, new TimeZone('Europe/Berlin')),
      DateUtil::addHours($this->fixture, 1)
    );
  }
    
  /**
   * Test simple add operations on a usual date.
   *
   */
  #[@test]
  public function addDays() {
    $this->assertEquals(
      \util\Date::create(2000, 1, 2, 12, 15, 11, new TimeZone('Europe/Berlin')),
      DateUtil::addDays($this->fixture, 1)
    );
  }
    
  /**
   * Test simple add operations on a usual date.
   *
   */
  #[@test]
  public function addMonths() {
    $this->assertEquals(
      \util\Date::create(2000, 2, 1, 12, 15, 11, new TimeZone('Europe/Berlin')),
      DateUtil::addMonths($this->fixture, 1)
    );
  }
  
  /**
   * Test basic add operations with negative values.
   *
   */
  #[@test]
  public function addNegativeSeconds() {
    $this->assertEquals(
      \util\Date::create(2000, 1, 1, 12, 14, 52, new TimeZone('Europe/Berlin')),
      DateUtil::addSeconds($this->fixture, -19)
    );
  }
    
  /**
   * Test basic add operations with negative values.
   *
   */
  #[@test]
  public function addNegativeMinutes() {
    $this->assertEquals(
      \util\Date::create(2000, 1, 1, 11, 46, 11, new TimeZone('Europe/Berlin')),
      DateUtil::addMinutes($this->fixture, -29)
    );
  }
    
  /**
   * Test basic add operations with negative values.
   *
   */
  #[@test]
  public function addNegativeHours() {
    $this->assertEquals(
      \util\Date::create(2000, 1, 1, 11, 15, 11, new TimeZone('Europe/Berlin')),
      DateUtil::addHours($this->fixture, -1)
    );
  }
    
  /**
   * Test basic add operations with negative values.
   *
   */
  #[@test]
  public function addNegativeDays() {
    $this->assertEquals(
      \util\Date::create(1999, 12, 31, 12, 15, 11, new TimeZone('Europe/Berlin')),
      DateUtil::addDays($this->fixture, -1)
    );
  }
    
  /**
   * Test basic add operations with negative values.
   *
   */
  #[@test]
  public function addNegativeMonths() {
    $this->assertEquals(
      \util\Date::create(1999, 12, 1, 12, 15, 11, new TimeZone('Europe/Berlin')),
      DateUtil::addMonths($this->fixture, -1)
    );
  }
  
  /**
   * Check leap year handling
   *
   */
  #[@test]
  public function testLeapYear() {
    $date= \util\Date::create(2000, 2, 1, 0, 0, 0);
    
    $this->assertEquals(
      \util\Date::create(2000, 3, 1, 0, 0, 0),
      DateUtil::addMonths($date, 1)
    );
    
    $this->assertEquals(
      \util\Date::create(2000, 3, 2, 0, 0, 0),
      DateUtil::addDays($date, 30)
    );
  }
  
  /**
   * Check non-leap year handling
   *
   */
  #[@test]
  public function testNonLeapYear() {
    $date= \util\Date::create(1999, 2, 1, 0, 0, 0, new TimeZone('Europe/Berlin'));
    
    $this->assertEquals(
      \util\Date::create(1999, 3, 1, 0, 0, 0, new TimeZone('Europe/Berlin')),
      DateUtil::addMonths($date, 1)
    );
    
    $this->assertEquals(
      \util\Date::create(1999, 3, 3, 0, 0, 0, new TimeZone('Europe/Berlin')),
      DateUtil::addDays($date, 30)
    );
  }
  
  /**
   * Tests the compare method
   *
   */
  #[@test]
  public function comparison() {
    $this->assertTrue(DateUtil::compare(new \util\Date('1977-12-14'), new \util\Date('1980-05-28')) < 0, 'a < b') &&
    $this->assertTrue(DateUtil::compare(new \util\Date('1980-05-28'), new \util\Date('1977-12-14')) > 0, 'a > b') &&
    $this->assertTrue(DateUtil::compare(new \util\Date('1980-05-28'), new \util\Date('1980-05-28')) == 0, 'a == b');
  }

  /**
   * Tests using the compare method as a usort() callback
   *
   * @see     php://usort
   */
  #[@test]
  public function sorting() {
    $list= array(
      new \util\Date('1977-12-14'),
      new \util\Date('2002-02-21'),
      new \util\Date('1980-05-28'),
    );
    
    usort($list, array(\xp::reflect('util.DateUtil'), 'compare'));
    $this->assertEquals(new \util\Date('1977-12-14'), $list[0], 'offset 0') &&
    $this->assertEquals(new \util\Date('1980-05-28'), $list[1], 'offset 1') &&
    $this->assertEquals(new \util\Date('2002-02-21'), $list[2], 'offset 2');
  }
  
  /**
   * Tests the getBeginningOfWeek and
   * getEndOfWeek methods.
   *
   */
  #[@test]
  public function testBeginAndEndOfWeek() {
    $this->assertEquals(
      \util\Date::create(2007, 1, 14, 0, 0, 0),
      DateUtil::getBeginningOfWeek(new \util\Date('2007-1-18'))
    );
    $this->assertEquals(
      \util\Date::create(2007, 1, 20, 23, 59, 59),
      DateUtil::getEndOfWeek(new \util\Date('2007-1-18'))
    );
  }
  
  /**
   * Test that a date's value is preserved when moving the
   * date to another timezone.
   *
   */
  #[@test]
  public function testMoveToTimezone() {
    $copy= clone $this->fixture;
    $tz= new TimeZone('Australia/Sydney');
    
    $this->assertEquals($this->fixture, DateUtil::moveToTimeZone($copy, $tz));
  }
  
  /**
   * Test that a date's single "digit" values remain unaltered when
   * forcibly setting the timezone to another.
   *
   */
  #[@test]
  public function testSetTimezone() {
    $this->assertEquals(
      \util\Date::create(2000, 1, 1, 17, 15, 11, new TimeZone('GMT')),
      DateUtil::setTimeZone($this->fixture, new TimeZone('America/New_York'))
    );
  }
}
