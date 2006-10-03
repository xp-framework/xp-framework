<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.io.collections.AbstractCollectionTest',
    'net.xp_framework.unittest.io.collections.NullFilter',
    'io.collections.iterate.IOCollectionIterator',
    'io.collections.iterate.FilteredIOCollectionIterator'
  );

  /**
   * Unit tests for IOCollectionIterator class
   *
   * @see      xp://io.collections.IOCollectionIterator
   * @purpose  Unit test
   */
  class IOCollectionIteratorTest extends AbstractCollectionTest {
    
    /**
     * Test IOCollectionIterator
     *
     * @access  public
     */
    #[@test]
    function iteration() {
      for ($it= &new IOCollectionIterator($this->fixture), $i= 0; $it->hasNext(); $i++) {
        $e= &$it->next();
        $this->assertTrue(is('io.collections.IOElement', $e));
      }
      $this->assertEquals($this->sizes[$this->fixture->getURI()], $i);
    }

    /**
     * Test IOCollectionIterator
     *
     * @access  public
     */
    #[@test]
    function recursiveIteration() {
      for ($it= &new IOCollectionIterator($this->fixture, TRUE), $i= 0; $it->hasNext(); $i++) {
        $e= &$it->next();
        $this->assertTrue(is('io.collections.IOElement', $e));
      }
      $this->assertEquals($this->total, $i);
    }    

    /**
     * Test FilteredIOCollectionIterator
     *
     * @access  public
     */
    #[@test]
    function filteredIteration() {
      for ($it= &new FilteredIOCollectionIterator($this->fixture, new NullFilter()), $i= 0; $it->hasNext(); $i++) {
        $e= &$it->next();
        $this->assertTrue(is('io.collections.IOElement', $e));
      }
      $this->assertEquals($this->sizes[$this->fixture->getURI()], $i);
    }

    /**
     * Test FilteredIOCollectionIterator
     *
     * @access  public
     */
    #[@test]
    function filteredRecursiveIteration() {
      for ($it= &new FilteredIOCollectionIterator($this->fixture, new NullFilter(), TRUE), $i= 0; $it->hasNext(); $i++) {
        $e= &$it->next();
        $this->assertTrue(is('io.collections.IOElement', $e));
      }
      $this->assertEquals($this->total, $i);
    }    
  }
?>
