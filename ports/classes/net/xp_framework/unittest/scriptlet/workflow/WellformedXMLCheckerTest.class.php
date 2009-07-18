<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'scriptlet.xml.workflow.checkers.WellformedXMLChecker'
  );

  /**
   * TestCase
   *
   * @see      reference
   * @purpose  purpose
   */
  class WellformedXMLCheckerTest extends TestCase {
    protected
      $fixture  = NULL;
  
    /**
     * Sets up test case
     *
     */
    public function setUp() {
      $this->fixture= new WellformedXMLChecker();
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function emptyInput() {
      $this->assertEquals(array(''), $this->fixture->check(array('')));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function validXml() {
      $this->assertEquals(
        array('<document/>'),
        $this->fixture->check(array('<document/>'))
      );
    }
    
    /**
     * Test that passing a fragment of an XML document
     * is accepted; eg. no passing a single node as root.
     *
     */
    #[@test]
    public function noRootNode() {
      $this->assertEquals(
        array('<node1/><node2/>'),
        $this->fixture->check(array('<node1/><node2/>'))
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
        $this->fixture->check(array('<outer><inner></outer>'))
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
        $this->fixture->check(array("\0"))
      );
    }
  }
?>
