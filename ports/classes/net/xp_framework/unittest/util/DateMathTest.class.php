<?php
/* This class is part of the XP framework
 *
 * $Id: DateMathTest.class.php 10962 2007-08-27 08:21:21Z kiesel $ 
 */

  uses(
    'unittest.TestCase',
    'util.DateMath'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class DateMathTest extends TestCase {
  
    /**
     * Test simple diff
     *
     */
    #[@test]
    public function diffSimple() {
      $this->assertEquals(
        0,
        DateMath::diff(TimeInterval::$DAY, new Date('2007-08-24'), new Date('2007-08-24'))
      );
    }
    
    /**
     * Diff against day before
     *
     */
    #[@test]
    public function diffYesterday() {
      $this->assertEquals(
        -1,
        DateMath::diff(TimeInterval::$DAY, new Date('2007-08-24'), new Date('2007-08-23'))
      );
    }
    
    /**
     * Diff against day after
     *
     */
    #[@test]
    public function diffTomorrow() {
      $this->assertEquals(
        1,
        DateMath::diff(TimeInterval::$DAY, new Date('2007-08-23'), new Date('2007-08-24'))
      );
    }
    
    /**
     * Diff bordercase from midnight to almost next midnight
     *
     */
    #[@test]
    public function diffMidnightToMidnight() {
      $this->assertEquals(
        0,
        DateMath::diff(TimeInterval::$DAY, new Date('2007-08-24 00:00:00'), new Date('2007-08-24 23:59:59'))
      );
    }
    
    /**
     * Diff bordercase where one second is the trigger
     *
     */
    #[@test]
    public function diffOneSecond() {
      $this->assertEquals(
        1,
        DateMath::diff(TimeInterval::$DAY, new Date('2007-08-23 23:59:59'), new Date('2007-08-24 00:00:00'))
      );
    }
    
    /**
     * Check leap year support
     *
     */
    #[@test]
    public function diffleapYear() {
      $this->assertEquals(
        2,
        DateMath::diff(TimeInterval::$DAY, new Date('2004-02-28 23:59:59'), new Date('2004-03-01 00:00:00'))
      );
    }
    
    /**
     * Check timezone independence
     *
     */
    #[@test]
    public function diffTimezoneIndependence() {
      $this->assertEquals(
        0,
        DateMath::diff(TimeInterval::$DAY, new Date('2000-01-01 00:00:00 Europe/Berlin'), new Date('1999-12-31 23:59:59 Europe/London'))
      );
    }
    
    /**
     * Test day difference in a foreign timezone
     *
     */
    #[@test]
    public function diffDayInForeignTimezone() {
      $this->assertEquals(
        1,
        DateMath::diff(TimeInterval::$DAY, new Date('2007-08-27 23:59:59 Australia/Sydney'), new Date('2007-08-28 00:00:00 Australia/Sydney'))
      );
    }

    /**
     * Test month difference in a foreign timezone
     *
     */
    #[@test]
    public function diffMonthInForeignTimezone() {
      $this->assertEquals(
        1,
        DateMath::diff(TimeInterval::$MONTH, new Date('2008-11-30 23:59:59 Australia/Sydney'), new Date('2008-12-01 00:00:00 Australia/Sydney'))
      );
    }

    /**
     * Test year difference in a foreign timezone
     *
     */
    #[@test]
    public function diffYearInForeignTimezone() {
      $this->assertEquals(
        1,
        DateMath::diff(TimeInterval::$YEAR, new Date('2008-12-31 23:59:59 Australia/Sydney'), new Date('2009-01-01 00:00:00 Australia/Sydney'))
      );
    }
    
    /**
     * diff over one year
     *
     */
    #[@test]
    public function diffOneYear() {
      $this->assertEquals(
        365,
        DateMath::diff(TimeInterval::$DAY, new Date('2006-08-24'), new Date('2007-08-24'))
      );
    }
    
    /**
     * diff over one leap year
     *
     */
    #[@test]
    public function diffOneLeapYear() {
      $this->assertEquals(
        366,
        DateMath::diff(TimeInterval::$DAY, new Date('2004-02-24'), new Date('2005-02-24'))
      );
    }
    
    /**
     * Test year diffing
     *
     */
    #[@test]
    public function yearDiff() {
      $this->assertEquals(0, DateMath::diff(TimeInterval::$YEAR, new Date('2007-01-01'), new Date('2007-12-31')));
      $this->assertEquals(1, DateMath::diff(TimeInterval::$YEAR, new Date('2007-01-01'), new Date('2008-01-01')));
      $this->assertEquals(-1, DateMath::diff(TimeInterval::$YEAR, new Date('2007-01-01'), new Date('2006-12-31')));
    }

    /**
     * Test month diffing
     *
     */
    #[@test]
    public function monthDiff() {
      $this->assertEquals(0, DateMath::diff(TimeInterval::$MONTH, new Date('2004-01-01'), new Date('2004-01-31')));
      $this->assertEquals(1, DateMath::diff(TimeInterval::$MONTH, new Date('2004-02-29'), new Date('2004-03-01')));
      $this->assertEquals(0, DateMath::diff(TimeInterval::$MONTH, new Date('2005-02-29'), new Date('2005-03-01')));
      $this->assertEquals(-1, DateMath::diff(TimeInterval::$MONTH, new Date('2007-01-01'), new Date('2006-12-31')));
    }
    
    /**
     * Test hour diffing
     *
     */
    #[@test]
    public function hourDiff() {
      $this->assertEquals(0, DateMath::diff(TimeInterval::$HOURS, new Date('2007-08-12 12:00:00'), new Date('2007-08-12 12:59:59')));
      $this->assertEquals(1, DateMath::diff(TimeInterval::$HOURS, new Date('2007-08-12 12:00:00'), new Date('2007-08-12 13:00:00')));
      $this->assertEquals(-1, DateMath::diff(TimeInterval::$HOURS, new Date('2007-08-12 12:00:00'), new Date('2007-08-12 11:59:59')));
    }
  }
?>
