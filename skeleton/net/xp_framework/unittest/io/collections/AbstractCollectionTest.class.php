<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.profiling.unittest.TestCase',
    'net.xp_framework.unittest.io.collections.MockCollection'
  );

  /**
   * This base class does not contain any test methods and is meant to
   * be a base class for other io.collections API tests
   *
   * @see      xp://io.collections.IOCollection
   * @purpose  Unit test
   */
  class AbstractCollectionTest extends TestCase {
    var
      $fixture = NULL,
      $sizes   = array(),
      $total   = 0;

    /**
     * Adds an element to the given collection and increases the size counter
     *
     * @access  protected
     * @return  &io.collection.IOCollection c
     * @return  &io.collection.IOElement e
     * @return  &io.collection.IOElement
     */
    function &addElement(&$c, &$e) {
      $c->addElement($e);
      $this->total++;
      with ($key= $c->getURI()); {
        isset($this->sizes[$key]) ? $this->sizes[$key]++ : $this->sizes[$key]= 1;
      }
      return $e;
    }

    /**
     * Setup method 
     *
     * @access  public
     */
    function setUp() {
      $this->fixture= &new MockCollection('.');
      $this->addElement($this->fixture, new MockElement('first.txt'));
      $this->addElement($this->fixture, new MockElement('second.txt'));
      $this->addElement($this->fixture, new MockElement('third.jpg'));

      with ($sub= &$this->addElement($this->fixture, new MockCollection('sub'))); {
        $this->addElement($sub, new MockElement('sub/IMG_6100.jpg'));
        $this->addElement($sub, new MockElement('sub/IMG_6100.txt'));

        with ($sec= &$this->addElement($this->fixture, new MockCollection('sub/sec'))); {
          $this->addElement($sec, new MockElement('sub/sec/lang.base.php'));
          $this->addElement($sec, new MockElement('sub/sec/__xp__.php'));
        }
      }
      
      // Self-check
      $this->assertEquals($this->total, array_sum($this->sizes));
    }
  }
?>
