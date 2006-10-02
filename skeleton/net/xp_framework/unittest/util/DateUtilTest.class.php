<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'util.profiling.unittest.TestCase',
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
     * Tests the compare method
     *
     * @access  public
     */
    #[@test]
    function comparison() {
      $this->assertTrue(DateUtil::compare(new Date('1977-12-14'), new Date('1980-05-28')) < 0, 'a < b') &&
      $this->assertTrue(DateUtil::compare(new Date('1980-05-28'), new Date('1977-12-14')) > 0, 'a > b') &&
      $this->assertTrue(DateUtil::compare(new Date('1980-05-28'), new Date('1980-05-28')) == 0, 'a == b');
    }

    /**
     * Tests using the compare method as a usort() callback
     *
     * @access  public
     * @see     php://usort
     */
    #[@test]
    function sorting() {
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
  }
?>
