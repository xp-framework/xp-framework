<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.io.collections.MockCollection',
    'io.collections.CollectionComposite'
  );

  /**
   * Unit tests for CollectionComposite class
   *
   * @see      xp://io.collections.CollectionComposite
   * @purpose  Unit test
   */
  class CollectionCompositeTest extends TestCase {
  
    /**
     * Helper method that asserts a given element is an IOElement
     * and that its URI equals the expected URI.
     *
     * @param   string uri
     * @param   io.collections.IOElement element
     * @throws  unittest.AssertionFailedError
     */
    protected function assertElement($uri, $element) {
      $this->assertSubClass($element, 'io.collections.IOElement');
      $this->assertEquals($uri, $element->getURI());
    }
    
    /**
     * Returns a collection 
     *
     * @param   string name
     * @param   io.collections.IOElement[] elements
     * @return  io.collections.IOCollection
     */
    protected function newCollection($name, $elements) {
      $c= new MockCollection($name);
      foreach ($elements as $element) {
        $c->addElement($element);
      }
      return $c;
    }
    
    /**
     * Returns an empty collection.
     *
     * @param   string name
     * @return  io.collections.IOCollection
     */
    protected function emptyCollection($name) {
      return $this->newCollection($name, array());
    }

    /**
     * Test CollectionComposite's constructor throws an exception when 
     * passed an empty list
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function constructorThrowsExceptionForEmptyList() {
      new CollectionComposite(array());
    }
    
    /**
     * Test next() returns NULL when no elements are left
     *
     */
    #[@test]
    public function nextReturnsNullForOneEmptyCollection() {
      $empty= new CollectionComposite(array($this->emptyCollection('empty-dir')));
      $empty->open();
      $this->assertNull($empty->next());
      $empty->close();
    }

    /**
     * Test next() returns NULL when no elements are left
     *
     */
    #[@test]
    public function nextReturnsNullForTwoEmptyCollections() {
      $empty= new CollectionComposite(array(
        $this->emptyCollection('empty-dir'),
        $this->emptyCollection('lost+found')
      ));
      $empty->open();
      $this->assertNull($empty->next());
      $empty->close();
    }

    /**
     * Test next() returns elements from all collections
     *
     */
    #[@test]
    public function elementsFromAllCollections() {
      $composite= new CollectionComposite(array(
        $this->newCollection('/home', array(new MockElement('.nedit'))),
        $this->newCollection('/usr/local/etc', array(new MockElement('php.ini'))),
      ));
      $composite->open();
      $this->assertElement('.nedit', $composite->next()) &&
      $this->assertElement('php.ini', $composite->next()) &&
      $this->assertNull($composite->next());
      $composite->close();
    }
  }
?>
