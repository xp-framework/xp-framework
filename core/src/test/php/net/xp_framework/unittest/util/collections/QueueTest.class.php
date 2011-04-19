<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'util.collections.Queue',
    'lang.types.String'
  );

  /**
   * Test Queue class
   *
   * @see      xp://util.collections.Queue
   * @purpose  Unit Test
   */
  class QueueTest extends TestCase {
    public
      $queue= NULL;
    
    /**
     * Setup method. Creates the queue member
     *
     */
    public function setUp() {
      $this->queue= new Queue();
    }
        
    /**
     * Tests the queue is initially empty
     *
     */
    #[@test]
    public function initiallyEmpty() {
      $this->assertTrue($this->queue->isEmpty());
    }

    /**
     * Tests queue equals its clone
     *
     */
    #[@test]
    public function equalsClone() {
      $this->queue->put(new String('green'));
      $this->assertTrue($this->queue->equals(clone($this->queue)));
    }

    /**
     * Tests put()
     *
     */
    #[@test]
    public function put() {
      $this->queue->put(new String('green'));
      $this->assertFalse($this->queue->isEmpty());
      $this->assertEquals(1, $this->queue->size());
    }

    /**
     * Tests get()
     *
     */
    #[@test]
    public function get() {
      $color= new String('red');
      $this->queue->put($color);
      $this->assertEquals($color, $this->queue->get());
      $this->assertTrue($this->queue->isEmpty());
    }

    /**
     * Tests get() throws an exception when there are no more elements
     * in the queue,
     *
     */
    #[@test, @expect('util.NoSuchElementException')]
    public function exceptionOnNoMoreElements() {
      $this->queue->get();
    }

    /**
     * Tests peek()
     *
     */
    #[@test]
    public function peek() {
      $color= new String('blue');
      $this->queue->put($color);
      $this->assertEquals($color, $this->queue->peek());
      $this->assertFalse($this->queue->isEmpty());
    }

    /**
     * Tests peek() returns NULL when there are no more elements
     * in the queue.
     *
     */
    #[@test]
    public function peekReturnsNullOnNoMoreElements() {
      $this->assertNull($this->queue->peek());
    }

    /**
     * Tests remove()
     *
     */
    #[@test]
    public function remove() {
      $color= new String('blue');
      $this->queue->put($color);
      $this->queue->remove($color);
      $this->assertTrue($this->queue->isEmpty());
    }

    /**
     * Tests remove() returns TRUE when the element was deleted, FALSE otherwise
     *
     */
    #[@test]
    public function removeReturnsWhetherDeleted() {
      $color= new String('pink');
      $this->queue->put($color);
      $this->assertTrue($this->queue->remove($color));
      $this->assertFalse($this->queue->remove(new String('purple')));
      $this->assertTrue($this->queue->isEmpty());
      $this->assertFalse($this->queue->remove($color));
      $this->assertFalse($this->queue->remove(new String('purple')));
    }

    /**
     * Tests elementAt()
     *
     */
    #[@test]
    public function elementAt() {
      $this->queue->put(new String('red'));
      $this->queue->put(new String('green'));
      $this->queue->put(new String('blue'));
      $this->assertEquals(new String('red'), $this->queue->elementAt(0));
      $this->assertEquals(new String('green'), $this->queue->elementAt(1));
      $this->assertEquals(new String('blue'), $this->queue->elementAt(2));
    }

    /**
     * Tests iterative use
     *
     * Example:
     * <code>
     *   
     *   // Fill queue
     *   with ($q= new Queue()); {
     *     $q->put(new String('One'));
     *     $q->put(new String('Two'));
     *     $q->put(new String('Three'));
     *     $q->put(new String('Four'));
     *   }
     *   
     *   // Empty queue
     *   while (!$q->isEmpty()) {
     *     var_dump($q->get());
     *   }
     * </code>
     *
     */
    #[@test]
    public function iterativeUse() {
      $input= array(new String('red'), new String('green'), new String('blue'));
      
      // Add
      for ($i= 0, $s= sizeof($input); $i < sizeof($input); $i++) {
        $this->queue->put($input[$i]);
      }
      
      // Retrieve
      $i= 0;
      while (!$this->queue->isEmpty()) {
        $element= $this->queue->get();

        if (!$input[$i]->equals($element)) {
          $this->fail('Not equal at offset #'.$i, $element, $input[$i]);
          break;
        }
        $i++;
      }
    }

    /**
     * Tests elementAt() throws an exception in case an illegal offset
     * is specified.
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function elementAtIllegalOffset() {
      $this->queue->elementAt(-1);
    }

    /**
     * Tests elementAt() throws an exception in case an out-of-bound
     * offset is specified.
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function elementAtOffsetOutOfBounds() {
      $this->queue->put(new String('one'));
      $this->queue->elementAt($this->queue->size() + 1);
    }

    /**
     * Tests elementAt() throws an exception in case the list is
     * empty.
     *
     */
    #[@test, @expect('lang.IndexOutOfBoundsException')]
    public function elementAtEmptyList() {
      $this->queue->elementAt(0);
    }
  }
?>
