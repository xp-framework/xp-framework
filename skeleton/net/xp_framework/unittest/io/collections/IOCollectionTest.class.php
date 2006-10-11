<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.unittest.io.collections.AbstractCollectionTest');

  /**
   * Unit tests for IOCollection class (basic functionality)
   *
   * @see      xp://io.collections.IOCollection
   * @purpose  Unit test
   */
  class IOCollectionTest extends AbstractCollectionTest {
    
    /**
     * Test next() returns NULL when no elements are left
     *
     * @access  public
     */
    #[@test]
    function nextReturnsNull() {
      $empty= &new MockCollection('empty-dir');
      $empty->open();
      $this->assertNull($empty->next());
      $empty->close();
    }
  
    /**
     * Test next() returns IOElements
     *
     * @access  public
     */
    #[@test]
    function nextReturnsIOElements() {
      $this->fixture->open();
      for ($i= 0; $e= &$this->fixture->next(); $i++) {
        $this->assertTrue(is('io.collections.IOElement', $e));
      }
      $this->assertEquals($this->sizes[$this->fixture->getURI()], $i);
      $this->fixture->close();
    }

    /**
     * Test next() returns NULL after iterating over all elements
     *
     * @access  public
     */
    #[@test]
    function nextReturnsNullAfterIteration() {
      $this->fixture->open();
      while ($this->fixture->next()) { 
        // Intentionally empty
      }
      $this->assertNull($this->fixture->next());
      $this->fixture->close();
    }

    /**
     * Test consecutive iteration works
     *
     * @access  public
     */
    #[@test]
    function consecutiveIteration() {
      for ($i= 0; $i < 2; $i++) {
        $elements= 0;
        $this->fixture->open();
        while ($this->fixture->next()) { 
          $elements++;
        }
        $this->assertNull($this->fixture->next());
        $this->assertEquals($this->sizes[$this->fixture->getURI()], $elements, 'Iteration #'.$i);
        $this->fixture->close();
      }
    }

    /**
     * Test consecutive iteration works
     *
     * @access  public
     */
    #[@test]
    function consecutiveIterationWithRewind() {
      $this->fixture->open();
      for ($i= 0; $i < 2; $i++) {
        $elements= 0;
        $this->fixture->rewind();
        while ($this->fixture->next()) { 
          $elements++;
        }
        $this->assertNull($this->fixture->next());
        $this->assertEquals($this->sizes[$this->fixture->getURI()], $elements, 'Iteration #'.$i);
      }
      $this->fixture->close();
    }
  }
?>
