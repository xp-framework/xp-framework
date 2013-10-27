<?php namespace net\xp_framework\unittest\io\collections;

use io\collections\IOElement;
use io\collections\IOCollection;
use io\collections\RandomCollectionAccess;

/**
 * IOCollection implementation
 *
 * @see    xp://io.collections.IOCollection
 */
class MockCollection extends \lang\Object implements IOCollection {
  protected
    $uri       = '',
    $_elements = array(),
    $_offset   = -1,
    $origin    = null;
    
  /**
   * Constructor
   *
   * @param   string uri
   */
  public function __construct($uri) {
    $this->uri= rtrim($uri, '/').'/';
  }

  /**
   * Add an element to the collection. Returns the added element.
   *
   * @param   io.collection.IOElement e
   * @return  io.collection.IOElement
   */
  public function addElement($e) {
    $e->setOrigin($this);
    $this->_elements[]= $e;
    return $e;
  }

  /**
   * Returns this element's name
   *
   * @return  string
   */
  public function getName() {
    return basename($this->uri);
  }
    
  /**
   * Returns this element's URI
   *
   * @return  string
   */
  public function getURI() {
    return $this->uri;
  }
  
  /**
   * Open this collection
   *
   */
  public function open() { 
    $this->_offset= 0;
  }

  /**
   * Rewind this collection (reset internal pointer to beginning of list)
   *
   */
  public function rewind() { 
    $this->_offset= 0;
  }
  
  /**
   * Retrieve next element in collection. Return NULL if no more entries
   * are available
   *
   * @return  io.collection.IOElement
   */
  public function next() {
    if (-1 == $this->_offset) throw new \lang\IllegalStateException('Not open');
    if ($this->_offset >= sizeof($this->_elements)) return null;

    return $this->_elements[$this->_offset++];
  }

  /**
   * Close this collection
   *
   */
  public function close() { 
    $this->_offset= -1;
  }

  /**
   * Retrieve this element's size in bytes
   *
   * @return  int
   */
  public function getSize() { 
    return 512;
  }

  /**
   * Retrieve this element's created date and time
   *
   * @return  util.Date
   */
  public function createdAt() {
    return null;
  }

  /**
   * Retrieve this element's last-accessed date and time
   *
   * @return  util.Date
   */
  public function lastAccessed() {
    return null;
  }

  /**
   * Retrieve this element's last-modified date and time
   *
   * @return  util.Date
   */
  public function lastModified() {
    return null;
  }

  /**
   * Creates a string representation of this object
   *
   * @return  string
   */
  public function toString() { 
    return $this->getClassName().'('.$this->uri.')';
  }

  /**
   * Gets origin of this element
   *
   * @return  io.collections.IOCollection
   */
  public function getOrigin() {
    return $this->origin;
  }

  /**
   * Sets origin of this element
   *
   * @param   io.collections.IOCollection
   */
  public function setOrigin(IOCollection $origin) {
    $this->origin= $origin;
  }

  /**
   * Gets input stream to read from this element
   *
   * @return  io.streams.InputStream
   * @throws  io.IOException
   */
  public function getInputStream() {
    throw new \io\IOException('Cannot read from a directory');
  }

  /**
   * Gets output stream to read from this element
   *
   * @return  io.streams.OutputStream
   * @throws  io.IOException
   */
  public function getOutputStream() {
    throw new \io\IOException('Cannot write to a directory');
  }

  /**
   * Creates a qualified name
   *
   * @param   string
   * @return  string
   */
  protected function qualifiedName($name) {
    return $this->uri.basename($name);
  }

  /**
   * Creates a new element in this collection
   *
   * @param   string name
   * @return  io.collections.IOElement
   */
  public function newElement($name) {
    $qualified= $this->qualifiedName($name);
    foreach ($this->_elements as $element) {
      if ($element instanceof IOElement && $qualified === $element->getURI()) {
        throw new \lang\IllegalArgumentException('Element already exists');
      }
    }
    $created= new MockElement($qualified);
    $created->setOrigin($this);
    $this->_elements[]= $created;
    return $created;
  }

  /**
   * Creates a new collection inside this collection
   *
   * @param   string name
   * @return  io.collections.IOCollection
   */
  public function newCollection($name) {
    $qualified= rtrim($this->qualifiedName($name), '/').'/';
    foreach ($this->_elements as $element) {
      if ($element instanceof IOCollection && $qualified === $element->getURI()) {
        throw new \lang\IllegalArgumentException('Collection already exists');
      }
    }

    return $this->addElement(new MockCollection($qualified));
  }

  /**
   * Finds an element inside this collection
   *
   * @param   string name
   * @return  io.collections.IOElement
   */
  public function findElement($name) {
    $qualified= $this->qualifiedName($name);
    foreach ($this->_elements as $element) {
      if ($element instanceof IOElement && $qualified === $element->getURI()) return $element;
    }
    return null;
  }
  
  /**
   * Finds a collection inside this collection
   *
   * @param   string name
   * @return  io.collections.IOCollection
   */
  public function findCollection($name) {
    $qualified= rtrim($this->qualifiedName($name), '/').'/';
    foreach ($this->_elements as $element) {
      if ($element instanceof IOCollection && $qualified === $element->getURI()) return $element;
    }
    return null;
  }

  /**
   * Gets an element inside this collection
   *
   * @param   string name
   * @return  io.collections.IOElement
   * @throws  util.NoSuchElementException
   */
  public function getElement($name) {
    if (!($found= $this->findElement($name))) {
      throw new \util\NoSuchElementException('Cannot find '.$name.' in '.$this->uri);
    }
    return $found;
  }
  
  /**
   * Get a collection inside this collection
   *
   * @param   string name
   * @return  io.collections.IOCollection
   * @throws  util.NoSuchElementException
   */
  public function getCollection($name) {
    if (!($found= $this->findCollection($name))) {
      throw new \util\NoSuchElementException('Cannot find '.$name.' in '.$this->uri);
    }
    return $found;
  }

  /**
   * Removes an element from this collection
   *
   * @param   string name
   */
  public function removeElement($name) {
    $qualified= $this->qualifiedName($name);
    foreach ($this->_elements as $i => $element) {
      if ($element instanceof IOElement && $qualified === $element->getURI()) {
        unset($this->_elements[$i]);
        break;
      }
    }
  }

  /**
   * Removes a collection from this collection
   *
   * @param   string name
   */
  public function removeCollection($name) {
    $qualified= rtrim($this->qualifiedName($name), '/').'/';
    foreach ($this->_elements as $i => $element) {
      if ($element instanceof IOCollection && $qualified === $element->getURI()) {
        unset($this->_elements[$i]);
        break;
      }
    }
  }

  /**
   * Returns whether another object is equal to this element
   *
   * @param   lang.Generic cmp
   * @return  bool
   */
  public function equals($cmp) {
    return $cmp instanceof self && $cmp->getURI() === $this->getURI();
  }
} 
