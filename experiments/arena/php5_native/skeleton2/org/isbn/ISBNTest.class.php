<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.profiling.unittest.TestCase', 'org.isbn.ISBN');

  /**
   * ISBN class unit test
   *
   * @see      xp://org.isbn.ISBN
   * @purpose  Unit test case
   */
  class ISBNTest extends TestCase {
  
    /**
     * Test correct ISBN
     *
     * @access  public
     * @see     http://isbntools.com/default.html#ij
     */
    #[@test]
    public function verifyISBNToolsExampleISBN() {
      $this->assertTrue(ISBN::isValid('0-8436-1072-7'));
    }

    /**
     * Test ISBN of "Softwaretest mit JUnit" book
     *
     * @access  public
     * @see     http://www.amazon.de/exec/obidos/ISBN=3898643255
     */
    #[@test]
    public function verifyJUnitTestBookISBN() {
      $this->assertTrue(ISBN::isValid('3-89864-325-5'));
    }

    /**
     * Test 13-digit ISBN
     *
     * @access  public
     * @see     http://www.isbn.org/standards/home/isbn/transition.asp
     */
    #[@test]
    public function verify13DigitISBN() {
      $this->assertTrue(ISBN::isValid('978-0-393-04002-9'));
    }

    /**
     * Test ISBN with invalid checksum (is: 3, should be: 7)
     *
     * @access  public
     */
    #[@test]
    public function verifyInvalidChecksum() {
      $this->assertFalse(ISBN::isValid('0-8436-1072-3'));
    }
    
    /**
     * Test ISBN::toString()
     *
     * @access  public
     */
    #[@test]
    public function testStringRepresentation() {
      $this->assertEquals(
        'org.isbn.ISBN(3-89864-325-5)', 
        xp::stringOf(new ISBN('3-89864-325-5'))
      );
    }
  }
?>
