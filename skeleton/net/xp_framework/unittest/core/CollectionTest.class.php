<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.profiling.unittest.TestCase',
    'lang.Collection',
    'util.Binford'
  );
  
  define('COLLECTION_CLASS_NAME',   'util.Binford');

  /**
   * Test the XP lang.Collection class
   *
   * @see      xp://lang.Collection
   * @purpose  Testcase
   */
  class CollectionTest extends TestCase {
    var
      $child= NULL;
  
    /**
     * Setup method
     *
     * @access  
     */
    function setUp() {
      $cl= &ClassLoader::getDefault();
      $this->child= &$cl->defineClass(
        'net.xp_framework.unittest.core.MorePower', 
        'class MorePower extends Binford { }'
      );
    }

    /**
     * Tests that a collection cannot be created for non-existant classes
     *
     * @access  public
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    function nonExistantClass() {
      Collection::forClass('@@NON-EXISTANT@@');
    }

    /**
     * Tests that a newly created list is initially empty
     *
     * @access  public
     */
    #[@test]
    function initiallyEmpty() {
      $collection= &Collection::forClass(COLLECTION_CLASS_NAME);
      $this->assertTrue($collection->isEmpty());
      $this->assertEquals(0, $collection->size());
    }

    /**
     * Tests getElementClass() amd getElementClassName() methods
     *
     * @access  public
     */
    #[@test]
    function elementClass() {
      $collection= &Collection::forClass(COLLECTION_CLASS_NAME);
      $this->assertEquals(COLLECTION_CLASS_NAME, $collection->getElementClassName());
      $this->assertEquals(XPClass::forName(COLLECTION_CLASS_NAME), $collection->getElementClass());
    }
    
    /**
     * Tests adding an element
     *
     * @access  public
     */
    #[@test]
    function addElement() {
      $collection= &Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->add(new Binford());
      $collection->add($this->child->newInstance());
      $this->assertFalse($collection->isEmpty());
      $this->assertEquals(2, $collection->size());
    }

    /**
     * Tests adding an element that is not of the correct type
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function addElementOfWrongClass() {
      $collection= &Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->add(new Object());
    }

    /**
     * Tests prepending an element
     *
     * @access  public
     */
    #[@test]
    function prependElement() {
      $collection= &Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->prepend(new Binford());
      $collection->prepend($this->child->newInstance());
      $this->assertFalse($collection->isEmpty());
      $this->assertEquals(2, $collection->size());
    }

    /**
     * Tests prepending an element that is not of the correct type
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function prependElementOfWrongClass() {
      $collection= &Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->prepend(new Object());
    }

    /**
     * Tests getting an element
     *
     * @access  public
     */
    #[@test]
    function getElement() {
      $collection= &Collection::forClass(COLLECTION_CLASS_NAME);
      $element= &$collection->add(new Binford());
      $this->assertEquals($element, $collection->get(0));
      $this->assertNull($collection->get(1));
    }

    /**
     * Tests removing an element
     *
     * @access  public
     */
    #[@test]
    function removeElement() {
      $collection= &Collection::forClass(COLLECTION_CLASS_NAME);
      $element= &$collection->add(new Binford());
      $this->assertEquals($element, $collection->remove(0));
      $this->assertTrue($collection->isEmpty());
    }

    /**
     * Tests clearing the collection
     *
     * @access  public
     */
    #[@test]
    function clearCollection() {
      $collection= &Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->add(new Binford());
      $collection->clear();
      $this->assertTrue($collection->isEmpty());
    }

    /**
     * Tests finding element via contains()
     *
     * @access  public
     */
    #[@test]
    function elementContained() {
      $collection= &Collection::forClass(COLLECTION_CLASS_NAME);
      $e1= &$collection->add(new Binford());
      $e2= &$collection->add(new Binford());
      $this->assertTrue($collection->contains($e1));
      $this->assertTrue($collection->contains($e2));
      $this->assertFalse($collection->contains(new Binford()));
    }

    /**
     * Tests finding an element's position via indexOf()
     *
     * @access  public
     */
    #[@test]
    function elementIndex() {
      $collection= &Collection::forClass(COLLECTION_CLASS_NAME);
      $e1= &$collection->add(new Binford());
      $e2= &$collection->add(new Binford());
      $this->assertEquals(0, $collection->indexOf($e1));
      $this->assertEquals(1, $collection->indexOf($e2));
      $this->assertFalse($collection->indexOf(new Binford()));
    }
  }
?>
