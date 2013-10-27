<?php namespace net\xp_framework\unittest\io\collections;

use io\collections\CollectionComposite;

/**
 * Unit tests for CollectionComposite class
 *
 * @see  xp://io.collections.CollectionComposite
 */
class CollectionCompositeTest extends AbstractCollectionTest {

  /**
   * Helper method that asserts a given element is an IOElement
   * and that its URI equals the expected URI.
   *
   * @param   string uri
   * @param   io.collections.IOElement element
   * @throws  unittest.AssertionFailedError
   */
  protected function assertElement($uri, $element) {
    $this->assertInstanceOf('io.collections.IOElement', $element);
    $this->assertEquals($uri, $element->getURI());
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

  #[@test, @expect('lang.IllegalArgumentException')]
  public function constructorThrowsExceptionForEmptyList() {
    new CollectionComposite(array());
  }

  #[@test]
  public function nextReturnsNullForOneEmptyCollection() {
    $empty= new CollectionComposite(array($this->emptyCollection('empty-dir')));
    $empty->open();
    $this->assertNull($empty->next());
    $empty->close();
  }

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

  #[@test]
  public function elementsFromAllCollections() {
    $composite= new CollectionComposite(array(
      $this->newCollection('/home', array(new MockElement('.nedit'))),
      $this->newCollection('/usr/local/etc', array(new MockElement('php.ini'))),
    ));
    $composite->open();
    $this->assertElement('.nedit', $composite->next());
    $this->assertElement('php.ini', $composite->next());
    $this->assertNull($composite->next());
    $composite->close();
  }
}
