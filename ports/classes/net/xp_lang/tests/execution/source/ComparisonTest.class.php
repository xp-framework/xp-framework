<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests comparisons
   *
   */
  class net·xp_lang·tests·execution·source·ComparisonTest extends ExecutionTest {
    
    /**
     * Test constant == a
     *
     */
    #[@test]
    public function constantLeft() {
      foreach (array('0', 'null', '"string"', '[]', '-1') as $constant) {
        $this->assertEquals(
          TRUE, 
          $this->run('$a= '.$constant.'; return '.$constant.' == $a;'), 
          $constant
        );
      }
    }

    /**
     * Test constant === a
     *
     */
    #[@test]
    public function constantLeftIdentical() {
      foreach (array('0', 'null', '"string"', '[]', '-1') as $constant) {
        $this->assertEquals(
          TRUE, 
          $this->run('$a= '.$constant.'; return '.$constant.' === $a;'), 
          $constant
        );
      }
    }

    /**
     * Test $a == constant
     *
     */
    #[@test]
    public function constantRight() {
      foreach (array('0', 'null', '"string"', '[]', '-1') as $constant) {
        $this->assertEquals(
          TRUE, 
          $this->run('$a= '.$constant.'; return $a == '.$constant.';'), 
          $constant
        );
      }
    }

    /**
     * Test $a == constant
     *
     */
    #[@test]
    public function constantRightIdentical() {
      foreach (array('0', 'null', '"string"', '[]', '-1') as $constant) {
        $this->assertEquals(
          TRUE, 
          $this->run('$a= '.$constant.'; return $a === '.$constant.';'), 
          $constant
        );
      }
    }

    /**
     * Test <
     *
     */
    #[@test]
    public function smallerThan() {
      $this->assertTrue($this->run('return 1 < 2;'), '1 < 2');
      $this->assertFalse($this->run('return 1 < 1;'), '1 < 1');
    }

    /**
     * Test <=
     *
     */
    #[@test]
    public function smallerThanOrEqual() {
      $this->assertTrue($this->run('return 1 <= 2;'), '1 <= 2');
      $this->assertTrue($this->run('return 1 <= 1;'), '1 <= 1');
      $this->assertFalse($this->run('return 1 <= 0;'), '1 <= 0');
    }

    /**
     * Test <
     *
     */
    #[@test]
    public function greaterThan() {
      $this->assertTrue($this->run('return 2 > 1;'), '2 > 1');
      $this->assertFalse($this->run('return 1 > 1;'), '1 > 1');
    }

    /**
     * Test >=
     *
     */
    #[@test]
    public function greaterThanOrEqual() {
      $this->assertTrue($this->run('return 2 >= 1;'), '2 >= 1');
      $this->assertTrue($this->run('return 1 >= 1;'), '1 >= 1');
      $this->assertFalse($this->run('return 0 >= 1;'), '0 >= 1');
    }

    /**
     * Test !=
     *
     */
    #[@test]
    public function notEqual() {
      $this->assertTrue($this->run('return 1 != 2;'), '1 != 2');
      $this->assertFalse($this->run('return 1 != 1;'), '1 != 1');
    }

    /**
     * Test !=
     *
     */
    #[@test]
    public function isEqual() {
      $this->assertTrue($this->run('return 1 == 1;'), '1 == 1');
      $this->assertFalse($this->run('return 1 == 2;'), '1 == 2');
    }

    /**
     * Test !== with integers
     *
     */
    #[@test]
    public function integersNotIdentical() {
      $this->assertTrue($this->run('return 1 !== 2;'), '1 !== 2');
      $this->assertFalse($this->run('return 1 !== 1;'), '1 !== 1');
    }

    /**
     * Test !== with integers
     *
     */
    #[@test]
    public function integersIdentical() {
      $this->assertTrue($this->run('return 1 === 1;'), '1 === 1');
      $this->assertFalse($this->run('return 1 === 2;'), '1 === 2');
    }
  }
?>
