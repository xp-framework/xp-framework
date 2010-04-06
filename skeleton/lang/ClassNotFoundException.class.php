<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'lang.ClassLoadingException',
    'lang.ChainedException'
  );

  /**
   * Indicates a class specified by a name cannot be found - that is,
   * no classloader provides such a class.
   *
   * @see   xp://lang.IClassLoader#loadClass
   * @see   xp://lang.XPClass#forName
   */
  class ClassNotFoundException extends ChainedException implements ClassLoadingException {
    protected
      $loaders= array();

    /**
     * Constructor
     *
     * @param  string message
     * @param  lang.IClassLoader[] loaders default array()
     */
    public function __construct($message, $loaders= array(), $cause= NULL) {
      parent::__construct($message, $cause);
      $this->loaders= $loaders;
    }
    
    /**
     * Returns the classloaders that were asked
     *
     * @return  lang.IClassLoader[]
     */
    public function getLoaders() {
      return $this->loaders;
    }

    /**
     * Returns the exception's message - override this in
     * subclasses to provide exact error hints.
     *
     * @return  string
     */
    protected function message() {
      return 'Exception %s (Class "%s" could not be found)';
    }

    /**
     * Retrieve compound representation
     *
     * @return string
     */
    public function compoundMessage() {
      return
        sprintf($this->message()." {\n  ",
          $this->getClassName(),
          $this->getMessage()
        ).
        implode("\n    ", array_map(array('xp', 'stringOf'), $this->loaders))."\n  }"
      ;
    }
  }
?>
