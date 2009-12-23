<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  /**
   * Indicates a class specified by a name cannot be found - that is,
   * no classloader provides such a class.
   *
   * @see   xp://lang.IClassLoader#loadClass
   * @see   xp://lang.XPClass#forName
   */
  class ClassNotFoundException extends XPException {
    protected
      $loaders= array();

    /**
     * Constructor
     *
     * @param  string message
     * @param  lang.IClassLoader[] loaders default array()
     */
    public function __construct($message, $loaders= array()) {
      parent::__construct($message);
      $this->loaders= $loaders;
    }

    /**
     * Retrieve compound representation
     *
     * @return string
     */
    public function compoundMessage() {
      return parent::compoundMessage()." {\n  ".implode("\n  ", array_map(array('xp', 'stringOf'), $this->loaders))."\n}";
    }
  }
?>
