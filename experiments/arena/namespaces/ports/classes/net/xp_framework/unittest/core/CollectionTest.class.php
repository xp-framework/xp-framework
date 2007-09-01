<?php
/* This class is part of the XP framework
 *
 * $Id: CollectionTest.class.php 10155 2007-04-29 16:33:09Z friebe $
 */

  namespace net::xp_framework::unittest::core;

  ::uses(
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
  class CollectionTest extends unittest::TestCase {

    /**
     * Tests that a collection cannot be created for non-existant classes
     *
     */
    #[@test, @expect('lang.ClassNotFoundException')]
    public function nonExistantClass() {
      lang::Collection::forClass('@@NON-EXISTANT@@');
    }

    /**
     * Tests that a newly created list is initially empty
     *
     */
    #[@test]
    public function initiallyEmpty() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $this->assertTrue($collection->isEmpty());
      $this->assertEquals(0, $collection->size());
    }

    /**
     * Tests getElementClass() amd getElementClassName() methods
     *
     */
    #[@test]
    public function elementClass() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $this->assertEquals(COLLECTION_CLASS_NAME, $collection->getElementClassName());
      $this->assertEquals(lang::XPClass::forName(COLLECTION_CLASS_NAME), $collection->getElementClass());
    }
    
    /**
     * Tests adding an element
     *
     */
    #[@test]
    public function addElement() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->add(new util::Binford());
      $collection->add(::newinstance(COLLECTION_CLASS_NAME, array(), '{}'));
      $this->assertFalse($collection->isEmpty());
      $this->assertEquals(2, $collection->size());
    }

    /**
     * Tests adding an element that is not of the correct type
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addElementOfWrongClass() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->add(new lang::Object());
    }

    /**
     * Tests addAll() method
     *
     */
    #[@test]
    public function addAll() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->addAll(array(new util::Binford(), new util::Binford()));
      $this->assertEquals(2, $collection->size());
    }

    /**
     * Tests addAll() method when the array given contains an element of
     * incorrect type.
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function addAllWithElementOfWrongClass() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->addAll(array(new util::Binford(), new lang::Object()));
    }

    /**
     * Tests addAll() method
     *
     */
    #[@test]
    public function addAllDoesNotModifyCollectionOnException() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      try {
        $collection->addAll(array(new util::Binford(), new lang::Object())) &&
        $this->fail('IllegalArgumentException expected');
      } catch (lang::IllegalArgumentException $e) {
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
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->prepend(new util::Binford());
      $collection->prepend(::newinstance(COLLECTION_CLASS_NAME, array(), '{}'));
      $this->assertFalse($collection->isEmpty());
      $this->assertEquals(2, $collection->size());
    }

    /**
     * Tests prepending an element that is not of the correct type
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function prependElementOfWrongClass() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->prepend(new lang::Object());
    }

    /**
     * Tests getting an element
     *
     */
    #[@test]
    public function getElement() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $element= $collection->add(new util::Binford());
      $this->assertEquals($element, $collection->get(0));
      $this->assertNull($collection->get(1));
    }

    /**
     * Tests removing an element
     *
     */
    #[@test]
    public function removeElement() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $element= $collection->add(new util::Binford());
      $this->assertEquals($element, $collection->remove(0));
      $this->assertTrue($collection->isEmpty());
    }

    /**
     * Tests clearing the collection
     *
     */
    #[@test]
    public function clearCollection() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $collection->add(new util::Binford());
      $collection->clear();
      $this->assertTrue($collection->isEmpty());
    }

    /**
     * Tests finding element via contains()
     *
     */
    #[@test]
    public function elementContained() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $e1= $collection->add(new util::Binford(61));
      $e2= $collection->add(new util::Binford(610));
      $this->assertTrue($collection->contains($e1));
      $this->assertTrue($collection->contains($e2));
      $this->assertFalse($collection->contains(new util::Binford()));
    }

    /**
     * Tests finding an element's position via indexOf()
     *
     */
    #[@test]
    public function elementIndex() {
      $collection= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $e1= $collection->add(new util::Binford(61));
      $e2= $collection->add(new util::Binford(610));
      $this->assertEquals(0, $collection->indexOf($e1));
      $this->assertEquals(1, $collection->indexOf($e2));
      $this->assertFalse($collection->indexOf(new util::Binford()));
    }

    /**
     * Tests the equals() method
     *
     */
    #[@test]
    public function listEquality() {
      $element= new util::Binford();
      $c1= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $c1->add($element);

      $c2= lang::Collection::forClass(COLLECTION_CLASS_NAME);
      $c2->add($element);
      
      $c3= lang::Collection::forClass(COLLECTION_CLASS_NAME);

      $this->assertTrue($c1->equals($c2));
      $this->assertFalse($c1->equals($c3));
    }
  }
?>
