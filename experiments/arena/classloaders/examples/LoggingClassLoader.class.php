<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Logs classloader requests
   *
   * @purpose  ClassLoader
   */
  class LoggingClassLoader extends Object implements IClassLoader {
    protected 
      $cat= NULL;
  
    /**
     * Constructor
     *
     * @param   util.log.LogCategory cat
     */
    public function __construct(LogCategory $cat) {
      $this->cat= $cat;
    }
  
    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName(). '<'.$this->cat->toString().'>';
    }
    
    /**
     * Checks whether this loader can provide the requested class
     *
     * @param   string class
     * @return  bool
     */
    public function providesClass($class) {
      $this->cat->debug('Provides class:', $class);
      return FALSE;
    }
    
    /**
     * Checks whether this loader can provide the requested resource
     *
     * @param   string filename
     * @return  bool
     */
    public function providesResource($filename) {
      $this->cat->debug('Provides filename:', $filename);
      return FALSE;
    }

    /**
     * Checks whether this loader can provide the requested package
     *
     * @param   string package
     * @return  bool
     */
    public function providesPackage($package) {
      $this->cat->debug('Provides package:', $package);
      return FALSE;
    }

    /**
     * Get package contents
     *
     * @param   string package
     * @return  string[] filenames
     */
    public function packageContents($package) {
      $this->cat->debug('Package contents:', $package);
      return array();
    }

    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass($class) {
      throw new IllegalStateException('Should not be reached');
    }

    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  string class name
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass0($class) {
      throw new IllegalStateException('Should not be reached');
    }

    /**
     * Loads a resource.
     *
     * @param   string string name of resource
     * @return  string
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResource($string) {
      throw new IllegalStateException('Should not be reached');
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @param   string string name of resource
     * @return  io.Stream
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($string) {
      throw new IllegalStateException('Should not be reached');
    }
  }
?>
