<?php
/* This class is part of the XP framework
 *
 * $Id: TimeZoneTest.class.php 11031 2007-09-02 20:09:38Z kiesel $ 
 */

  uses(
    'unittest.TestCase',
    'util.TimeZone'
  );

  /**
   * TestCase
   *
   * @see      xp://util.TimeZone
   * @purpose  Testcase
   */
  class TimeZoneTest extends TestCase {
    protected
      $fixture = NULL;  

    /**
     * Setup fixture
     *
     */
    public function setUp() {
      $this->fixture= new TimeZone('Europe/Berlin');
    }    
  
    /**
     * Test
     *
     */
    #[@test]
    public function name() {
      $this->assertEquals('Europe/Berlin', $this->fixture->getName());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function offsetDST() {
      $this->assertEquals('+0200', $this->fixture->getOffset(new Date('2007-08-21')));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function offsetNoDST() {
      $this->assertEquals('+0100', $this->fixture->getOffset(new Date('2007-01-21')));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function offsetWithHalfHourDST() {
      // Australia/Adelaide is +10:30 in DST
      $this->fixture= new TimeZone('Australia/Adelaide');
      $this->assertEquals('+1030', $this->fixture->getOffset(new Date('2007-01-21')));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function offsetWithHalfHourNoDST() {
      // Australia/Adelaide is +09:30 in non-DST
      $this->fixture= new TimeZone('Australia/Adelaide');
      $this->assertEquals('+0930', $this->fixture->getOffset(new Date('2007-08-21')));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function offsetInSecondsDST() {
      $this->assertEquals(7200, $this->fixture->getOffsetInSeconds(new Date('2007-08-21')));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function offsetInSecondsNoDST() {
      $this->assertEquals(3600, $this->fixture->getOffsetInSeconds(new Date('2007-01-21')));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function convert() {
      $date= new Date('2007-01-01 00:00 Australia/Sydney');
      $this->assertEquals(
        new Date('2006-12-31 14:00:00 Europe/Berlin'),
        $this->fixture->translate($date)
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function previousTransition() {
      $transition= $this->fixture->previousTransition(new Date('2007-08-23'));
      $this->assertEquals(TRUE, $transition->isDst());
      $this->assertEquals('CEST', $transition->abbr());
      $this->assertEquals(new Date('2007-03-25 02:00:00 Europe/Berlin'), $transition->getDate());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function previousPreviousTransition() {
      $transition= $this->fixture->previousTransition(new Date('2007-08-23'));
      $transition->previous();
      $this->assertFalse($transition->isDst());
      $this->assertEquals('CET', $transition->abbr());
      $this->assertEquals(new Date('2006-10-29 02:00:00 Europe/Berlin'), $transition->getDate());
    }

    /**
     * Test
     *
     */
    #[@test]
    public function nextTransition() {
      $transition= $this->fixture->nextTransition(new Date('2007-08-23'));
      $this->assertEquals(FALSE, $transition->isDst());
      $this->assertEquals('CET', $transition->abbr());
      $this->assertEquals(new Date('2007-10-28 02:00:00 Europe/Berlin'), $transition->getDate());
    }

    /**
     * Test an unknown timezone name
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function unknownTimeZone() {
      new TimeZone('UNKNOWN');
    }
  }
?>
