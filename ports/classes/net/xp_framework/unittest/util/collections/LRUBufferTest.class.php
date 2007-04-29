<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'util.collections.LRUBuffer',
    'lang.types.String'
  );

  define('LRUTEST_BUFFER_DEAULT_SIZE',  3);

  /**
   * Test LRUBuffer class
   *
   * @see      xp://util.collections.LRUBuffer
   * @purpose  Unit Test
   */
  class LRUBufferTest extends TestCase {
    public
      $buffer= NULL;
    
    /**
     * Setup method. Creates the buffer member
     *
     */
    public function setUp() {
      $this->buffer= new LRUBuffer(LRUTEST_BUFFER_DEAULT_SIZE);
    }
    
    /**
     * Tests the buffer is initially empty
     *
     */
    #[@test]
    public function initiallyEmpty() {
      $this->assertEquals(0, $this->buffer->numElements());
    }
    
    /**
     * Tests the getSize() method
     *
     */
    #[@test]
    public function getSize() {
      $this->assertEquals(LRUTEST_BUFFER_DEAULT_SIZE, $this->buffer->getSize());
    }

    /**
     * Tests the add() method
     *
     */
    #[@test]
    public function add() {
      $this->buffer->add(new String('one'));
      $this->assertEquals(1, $this->buffer->numElements());
    }

    /**
     * Tests the add() method returns the victim
     *
     */
    #[@test]
    public function addReturnsVictim() {

      // We should be able to add at least as many as the buffer's size
      // elements to the LRUBuffer. Nothing should be deleted from it
      // during this loop.
      for ($i= 0, $s= $this->buffer->getSize(); $i < $s; $i++) {
        if (NULL === ($victim= $this->buffer->add(new String('item #'.$i)))) continue;
        
        return $this->fail(
          'Victim '.xp::stringOf($victim).' when inserting item #'.($i + 1).'/'.$s, 
          $victim, 
          NULL
        );
      }
      
      // The LRUBuffer is now "full". Next time we add something, the
      // element last recently used should be returned.
      $this->assertEquals(
        new String('item #0'), 
        $this->buffer->add(new String('last item'))
      );
    }
    
    /**
     * Add a specified number of strings to the buffer.
     *
     * @param   int num
     */
    protected function addElements($num) {
      for ($i= 0; $i < $num; $i++) {
        $this->buffer->add(new String('item #'.$i));
      }
    }
    
    /**
     * Tests the buffer does not grow beyond the set limit
     *
     */
    #[@test]
    public function bufferDoesNotGrowBeyondSize() {
      $this->addElements($this->buffer->getSize()+ 1);
      $this->assertEquals($this->buffer->getSize(), $this->buffer->numElements());
    }
 
    /**
     * Tests the update() method
     *
     */
    #[@test]
    public function update() {
    
      // Fill the LRUBuffer until its size is reached
      $this->addElements($this->buffer->getSize());
      
      // Update the first item
      $this->buffer->update(new String('item #0'));
      
      // Now the second item should be chosen the victim when adding 
      // another element
      $this->assertEquals(
        new String('item #1'), 
        $this->buffer->add(new String('last item'))
      );
    }

    /**
     * Tests the setSize() method
     *
     */
    #[@test]
    public function setSize() {
      $this->buffer->setSize(10);
      $this->assertEquals(10, $this->buffer->getSize());
    }

    /**
     * Tests the setSize() method when passed an argument <= zero
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function illegalSize() {
      $this->buffer->setSize(0);
    }
  }
?>
