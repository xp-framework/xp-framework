<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'scriptlet.xml.workflow.casters.ToValidXMLString'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class ToValidXMLStringTest extends TestCase {
    protected
      $fixture  = NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new ToValidXMLString();
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function emptyInput() {
      $this->assertEquals(array(''), $this->fixture->castValue(array('')));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function validXml() {
      $this->assertEquals(
        array('<document/>'),
        $this->fixture->castValue(array('<document/>'))
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function notWellFormedXml() {
      $this->assertEquals(
        'not_well_formed',
        $this->fixture->castValue(array('<outer><inner></outer>'))
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function invalidCharacters() {
      $this->assertEquals(
        'invalid_chars',
        $this->fixture->castValue(array("\0"))
      );
    }
    
  }
?>
