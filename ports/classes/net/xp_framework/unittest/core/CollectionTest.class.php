<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
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

    /**
     * Tests that a collection cannot be created for non-existant classes
     *
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function nonExistantClass() {
      Collection::forClass('@@NON-EXISTANT@@');
    }

    /**
     * Tests that a newly created list is initially empty
     *
     */
    #[@test]
    public function initiallyEmpty() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $this->assertTrue($collection->isEmpty());
      $this->assertEquals(0, $collection->size());
    }

    /**
     * Tests getElementClass() amd getElementClassName() methods
     *
     */
    #[@test]
    public function elementClass() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $this->assertEquals(COLLECTION_CLASS_NAME, $collection->getElementClassName());
      $this->assertEquals(XPClass::forName(COLLECTION_CLASS_NAME), $collection->getElementClass());
    }
    
    /**
     * Tests adding an element
     *
     */
    #[@test]
    public function addElement() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->add(new Binford());
      $collection->add(newinstance(COLLECTION_CLASS_NAME, array(), '{}'));
      $this->assertFalse($collection->isEmpty());
      $this->assertEquals(2, $collection->size());
    }

    /**
     * Tests adding an element that is not of the correct type
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addElementOfWrongClass() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->add(new Object());
    }

    /**
     * Tests addAll() method
     *
     */
    #[@test]
    public function addAll() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->addAll(array(new Binford(), new Binford()));
      $this->assertEquals(2, $collection->size());
    }

    /**
     * Tests addAll() method when the array given contains an element of
     * incorrect type.
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addAllWithElementOfWrongClass() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->addAll(array(new Binford(), new Object()));
    }

    /**
     * Tests addAll() method
     *
     */
    #[@test]
    public function addAllDoesNotModifyCollectionOnException() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      try {
        $collection->addAll(array(new Binford(), new Object())) &&
        $this->fail('IllegalArgumentException expected');
      } catch (IllegalArgumentException $e) {
        // Expected behaviour
      }
      $this->assertEquals(0, $collection->size());
    }

    /**
     * Tests prepending an element
     *
     */
    #[@test]
    public function prependElement() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->prepend(new Binford());
      $collection->prepend(newinstance(COLLECTION_CLASS_NAME, array(), '{}'));
      $this->assertFalse($collection->isEmpty());
      $this->assertEquals(2, $collection->size());
    }

    /**
     * Tests prepending an element that is not of the correct type
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function prependElementOfWrongClass() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->prepend(new Object());
    }

    /**
     * Tests getting an element
     *
     */
    #[@test]
    public function getElement() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $element= $collection->add(new Binford());
      $this->assertEquals($element, $collection->get(0));
      $this->assertNull($collection->get(1));
    }

    /**
     * Tests removing an element
     *
     */
    #[@test]
    public function removeElement() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $element= $collection->add(new Binford());
      $this->assertEquals($element, $collection->remove(0));
      $this->assertTrue($collection->isEmpty());
    }

    /**
     * Tests clearing the collection
     *
     */
    #[@test]
    public function clearCollection() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->add(new Binford());
      $collection->clear();
      $this->assertTrue($collection->isEmpty());
    }

    /**
     * Tests finding element via contains()
     *
     */
    #[@test]
    public function elementContained() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $e1= $collection->add(new Binford(61));
      $e2= $collection->add(new Binford(610));
      $this->assertTrue($collection->contains($e1));
      $this->assertTrue($collection->contains($e2));
      $this->assertFalse($collection->contains(new Binford()));
    }

    /**
     * Tests finding an element's position via indexOf()
     *
     */
    #[@test]
    public function elementIndex() {
      $collection= Collection::forClass(COLLECTION_CLASS_NAME);
      $e1= $collection->add(new Binford(61));
      $e2= $collection->add(new Binford(610));
      $this->assertEquals(0, $collection->indexOf($e1));
      $this->assertEquals(1, $collection->indexOf($e2));
      $this->assertFalse($collection->indexOf(new Binford()));
    }

    /**
     * Tests the equals() method
     *
     */
    #[@test]
    public function listEquality() {
      $element= new Binford();
      $c1= Collection::forClass(COLLECTION_CLASS_NAME);
      $c1->add($element);

      $c2= Collection::forClass(COLLECTION_CLASS_NAME);
      $c2->add($element);
      
      $c3= Collection::forClass(COLLECTION_CLASS_NAME);

      $this->assertTrue($c1->equals($c2));
      $this->assertFalse($c1->equals($c3));
    }
  }
?>
