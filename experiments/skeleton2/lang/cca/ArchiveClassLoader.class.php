<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses(
    'lang.ClassLoader',
    'lang.cca.Archive'
  );

  /** 
   * Loads XP classes from a CCA (Class Collection Archive)
   * 
   * Note: The classes *must* be stored in the CCA with their fully 
   * qualified class names as key!
   *
   * Usage:
   * <code>
   *   $l= new ArchiveClassLoader(new Archive(new File('soap.cca')));
   *   try(); {
   *     $name= $l->loadClass($_SERVER['argv'][1]);
   *   } if (catch('ClassNotFoundException', $e)) {
   *     die($e->printStackTrace());
   *   }
   * 
   *   $obj= new $name();
   *   var_dump($obj, $obj->getClassName(), $obj->toString());
   * </code>
   *
   * @purpose  Load classes from an archive
   * @see      xp://lang.ClassLoader
   * @see      xp://lang.cca.Archive
   */
  class ArchiveClassLoader extends ClassLoader {
    public
      $archive  = NULL;
    
    /**
     * Constructor
     * 
     * @access  public
     * @param   &lang.cca.Archive archive
     */
    public function __construct(&$archive) {
      $this->archive= $archive;
      parent::__construct();
    }
    
    /**
     * Destructor
     * 
     * @access  public
     */
    public function __destruct() {
      $this->archive->close();
      parent::__destruct();
    }
    
    /**
     * Load a class from the CCA
     *
     * @access  public
     * @param   string className fully qualified class name io.File
     * @return  string class' name for instantiation
     * @throws  ClassNotFoundException in case the class can not be found
     */
    public function loadClass($className) {
      try {
        if (!$this->archive->isOpen()) {
          $this->archive->open(ARCHIVE_READ);
        }
        $data= $this->archive->extract($className);
      } catch (XPException $e) {
        throw (new ClassNotFoundException(sprintf(
          'class "%s" not found: %s',
          $className,
          $e->message
        )));
      }
      
      // This is damn ugly and will also be fatal on parse errors
      // include_once doesn't do this... but else, $data would have
      // to be written to disk first, which is also quite stupid.
      eval('?>'.$data);
      
      $parts= array_reverse(explode('.', $className));
      $GLOBALS['php_class_names'][strtolower($parts[0])]= $className;
      return $parts[0];
    }
  }
?>
