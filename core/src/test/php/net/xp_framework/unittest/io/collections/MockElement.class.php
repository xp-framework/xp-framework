<?php namespace net\xp_framework\unittest\io\collections;

use io\collections\IOElement;
use io\collections\IOCollection;
use io\streams\MemoryInputStream;
use io\streams\MemoryOutputStream;

/**
 * Represents a Mock element
 *
 * @see    xp://net.xp_framework.unittest.io.collections.MockCollection
 */
class MockElement extends \lang\Object implements IOElement {
  protected
    $uri    = '',
    $size   = 0,
    $adate  = null,
    $mdate  = null,
    $cdate  = null,
    $origin = null;

  /**
   * Constructor
   *
   * @param   string uri
   * @param   int size default 0
   * @param   util.Date adate default NULL
   * @param   util.Date adate default NULL
   * @param   util.Date cdate default NULL
   */
  public function __construct($uri, $size= 0, $adate= null, $mdate= null, $cdate= null) {
    $this->uri= $uri;
    $this->size= $size;
    $this->adate= $adate;
    $this->mdate= $mdate;
    $this->cdate= $cdate;
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
   * Retrieve this element's size in bytes
   *
   * @return  int
   */
  public function getSize() { 
    return $this->size;
  }

  /**
   * Retrieve this element's created date and time
   *
   * @return  util.Date
   */
  public function createdAt() {
    return $this->cdate;
  }

  /**
   * Retrieve this element's last-accessed date and time
   *
   * @return  util.Date
   */
  public function lastAccessed() {
    return $this->adate;
  }

  /**
   * Retrieve this element's last-modified date and time
   *
   * @return  util.Date
   */
  public function lastModified() {
    return $this->mdate;
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
    return new MemoryInputStream('File contents of {'.$this->uri.'}');
  }

  /**
   * Gets output stream to read from this element
   *
   * @return  io.streams.OutputStream
   * @throws  io.IOException
   */
  public function getOutputStream() {
    return new MemoryOutputStream();
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
