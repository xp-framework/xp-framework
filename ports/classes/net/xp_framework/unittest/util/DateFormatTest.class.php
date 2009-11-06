<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.DateFormat'
  );

  /**
   * TestCase
   *
   * @see      xp://util.DateFormat
   */
  class DateFormatTest extends TestCase {
  
    /**
     * Test US-format (YYYY-MM-DD)
     *
     */
    #[@test]
    public function usFormat() {
      $this->assertEquals(
        new Date('2009-12-14'),
        create(new DateFormat('%Y-%m-%d'))->parse('2009-12-14')
      );
    }

    /**
     * Test EU-format (DD.MM.YYYY HH:II:SS)
     *
     */
    #[@test]
    public function euFormat() {
      $this->assertEquals(
        new Date('2009-12-14 11:45:00'),
        create(new DateFormat('%d.%m.%Y %H:%M:%S'))->parse('14.12.2009 11:45:00')
      );
    }
 
    /**
     * Test specialized format (Mo 07-Mrz-2011)
     *
     */
    #[@test]
    public function dayAndMonthNamesInInput() {
      $this->assertEquals(
        new Date('2011-03-07'),
        create(new DateFormat('%* %d-%[month=Jan,Feb,Mrz,Apr,Mai,Jun,Jul,Aug,Sep,Okt,Nov,Dez]-%Y'))->parse('Mo 07-Mrz-2011')
      );
    }
  }
?>
