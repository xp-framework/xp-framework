<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'math.Aggregation'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class AggregationTest extends TestCase {
  
    /**
     * Test
     *
     */
    #[@test]
    public function average() {
      $this->assertEquals(
        2,
        Aggregation::$AVERAGE->calculate(array(1,3))
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function medianOdd() {
      $this->assertEquals(
        2,
        Aggregation::$MEDIAN->calculate(array(1,2,5))
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function medianEven() {
      $this->assertEquals(
        1.5,
        Aggregation::$MEDIAN->calculate(array(1,2))
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function maximum() {
      $this->assertEquals(
        10,
        Aggregation::$MAXIMUM->calculate(array(1, 10, 5, 2, 6))
      );
    }
    /**
     * Test
     *
     */
    #[@test]
    public function minimum() {
      $this->assertEquals(
        1,
        Aggregation::$MINIMUM->calculate(array(1, 10, 5, 2, 6))
      );
    }



  }
?>
