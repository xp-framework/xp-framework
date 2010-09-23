<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'net.xp_lang.tests.execution.source';

  uses('net.xp_lang.tests.execution.source.ExecutionTest');

  /**
   * Tests operators
   *
   */
  class net·xp_lang·tests·execution·source·OperatorTest extends ExecutionTest {

    /**
     * Test = 
     *
     */
    #[@test]
    public function assignmentToInt() {
      $this->assertEquals(3, $this->run('$a= 3; return $a;'));
    }

    /**
     * Test = 
     *
     */
    #[@test]
    public function assignmentToString() {
      $this->assertEquals('Hello', $this->run('$a= "Hello"; return $a;'));
    }

    /**
     * Test = 
     *
     */
    #[@test]
    public function concatAssignment() {
      $this->assertEquals('HelloWorld', $this->run('$a= "Hello"; $a ~= "World"; return $a;'));
    }

    /**
     * Test += 
     *
     */
    #[@test]
    public function plusAssignment() {
      $this->assertEquals(1, $this->run('$a= 0; $a += 1; return $a;'));
    }

    /**
     * Test -= 
     *
     */
    #[@test]
    public function minusAssignment() {
      $this->assertEquals(-1, $this->run('$a= 0; $a -= 1; return $a;'));
    }

    /**
     * Test *= 
     *
     */
    #[@test]
    public function timesAssignment() {
      $this->assertEquals(4, $this->run('$a= 2; $a *= 2; return $a;'));
    }

    /**
     * Test /= 
     *
     */
    #[@test]
    public function divAssignment() {
      $this->assertEquals(2, $this->run('$a= 4; $a /= 2; return $a;'));
    }

    /**
     * Test %= 
     *
     */
    #[@test]
    public function modAssignment() {
      $this->assertEquals(2, $this->run('$a= 5; $a %= 3; return $a;'));
    }
    
    /**
     * Test |= 
     *
     */
    #[@test]
    public function bitWiseOrAssignment() {
      $this->assertEquals(1, $this->run('$a= 0; $a |= 1; return $a;'));
    }
 
    /**
     * Test ^= 
     *
     */
    #[@test]
    public function bitWiseXOrAssignment() {
      $this->assertEquals(0, $this->run('$a= 1; $a ^= 1; return $a;'));
    }

    /**
     * Test &= 
     *
     */
    #[@test]
    public function bitWiseAndAssignment() {
      $this->assertEquals(0, $this->run('$a= 2; $a &= 1; return $a;'));
    }

    /**
     * Test <<= 
     *
     */
    #[@test]
    public function shiftLeftAssignment() {
      $this->assertEquals(16, $this->run('$a= 8; $a <<= 1; return $a;'));
    }

    /**
     * Test >>= 
     *
     */
    #[@test]
    public function shiftRightAssignment() {
      $this->assertEquals(2, $this->run('$a= 8; $a >>= 2; return $a;'));
    }

    /**
     * Test !
     *
     */
    #[@test]
    public function notPrefix() {
      $this->assertEquals(FALSE, $this->run('$a= 2; return !$a;'));
    }

    /**
     * Test -
     *
     */
    #[@test]
    public function minusPrefix() {
      $this->assertEquals(-2, $this->run('$a= 2; return -$a;'));
    }

    /**
     * Test ++
     *
     */
    #[@test]
    public function incPrefix() {
      $this->assertEquals(3, $this->run('$a= 2; return ++$a;'));
    }

    /**
     * Test ++
     *
     */
    #[@test]
    public function decPrefix() {
      $this->assertEquals(1, $this->run('$a= 2; return --$a;'));
    }

    /**
     * Test ++
     *
     */
    #[@test]
    public function incPostfix() {
      $this->assertEquals(2, $this->run('$a= 2; return $a++;'));
    }

    /**
     * Test ++
     *
     */
    #[@test]
    public function decPostfix() {
      $this->assertEquals(2, $this->run('$a= 2; return $a--;'));
    }

    /**
     * Test &
     *
     */
    #[@test]
    public function binaryAnd() {
      $this->assertEquals(2, $this->run('$a= 3; return $a & 2;'));
    }

    /**
     * Test |
     *
     */
    #[@test]
    public function binaryOr() {
      $this->assertEquals(3, $this->run('$a= 2; return $a | 1;'));
    }

    /**
     * Test &&
     *
     */
    #[@test]
    public function logicalAnd() {
      $this->assertEquals(TRUE, $this->run('$a= 3; return $a && TRUE;'));
    }

    /**
     * Test ||
     *
     */
    #[@test]
    public function logicalOr() {
      $this->assertEquals(TRUE, $this->run('$a= 2; return $a || FALSE;'));
    }

    /**
     * Test |
     *
     */
    #[@test]
    public function binaryXor() {
      $this->assertEquals(1, $this->run('$a= 3; return $a ^ 2;'));
    }

    /**
     * Test +
     *
     */
    #[@test]
    public function plus() {
      $this->assertEquals(5, $this->run('$a= 3; return $a + 2;'));
    }

    /**
     * Test -
     *
     */
    #[@test]
    public function minus() {
      $this->assertEquals(1, $this->run('$a= 3; return $a - 2;'));
    }

    /**
     * Test *
     *
     */
    #[@test]
    public function times() {
      $this->assertEquals(6, $this->run('$a= 3; return $a * 2;'));
    }

    /**
     * Test /
     *
     */
    #[@test]
    public function div() {
      $this->assertEquals(2, $this->run('$a= 4; return $a / 2;'));
    }

    /**
     * Test %
     *
     */
    #[@test]
    public function mod() {
      $this->assertEquals(1, $this->run('$a= 5; return $a % 2;'));
    }

    /**
     * Test ~
     *
     */
    #[@test]
    public function concat() {
      $this->assertEquals('HelloWorld', $this->run('$a= "Hello"; return $a ~ "World";'));
    }

    /**
     * Test ~
     *
     */
    #[@test]
    public function bitWiseComplement() {
      $this->assertEquals(0x00000111, $this->run('$a= 0xFFFFFEEE; return ~$a;'));
    }
 
    /**
     * Test <<
     *
     */
    #[@test]
    public function shiftLeft() {
      $this->assertEquals(16, $this->run('$a= 8; return $a << 1;'));
    }

    /**
     * Test >>
     *
     */
    #[@test]
    public function shiftRight() {
      $this->assertEquals(2, $this->run('$a= 8; return $a >> 2;'));
    }

    /**
     * Test the following code:
     *
     * <code>
     *   $a= 2; 
     *   $b= 0; 
     *   $a && $b+= 1;
     * </code>
     */
    #[@test]
    public function conditionalAssignmentResult() {
      $this->assertEquals(1, $this->run('$a= 2; $b= 0; $a && $b+= 1; return $b;'));
    }
  }
?>
