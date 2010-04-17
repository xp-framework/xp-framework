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
   * @test  xp://net.xp_framework.unittest.reflection.ClassLoaderTest
   * @test  xp://net.xp_framework.unittest.reflection.ReflectionTest
   * @test  xp://net.xp_framework.unittest.reflection.RuntimeClassDefinitionTest
   */
  class ClassNotFoundException extends ChainedException implements ClassLoadingException {
    protected
      $failedClass  = NULL,
      $loaders      = array();

    /**
     * Constructor
     *
     * @param   string failedClass
     * @param   lang.IClassLoader[] loaders default array()
     * @param   lang.Throwable cause default NULL
     */
    public function __construct($failedClass, $loaders= array(), $cause= NULL) {
      parent::__construct(sprintf($this->message(), $failedClass), $cause);
      $this->failedClass= $failedClass;
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
      return 'Class "%s" could not be found';
    }

    /**
     * Retrieve name of class which could not be loaded
     *
     * @return  string
     */
    public function getFailedClassName() {
      return $this->failedClass;
    }

    /**
     * Retrieve compound representation
     *
     * @return string
     */
    public function compoundMessage() {
      return
        'Exception '.$this->getClassName().' ('.$this->message.") {\n  ".
        implode("\n    ", array_map(array('xp', 'stringOf'), $this->loaders))."\n  }"
      ;
    }
  }
?>
