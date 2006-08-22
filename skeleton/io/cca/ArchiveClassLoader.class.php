<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */
 
  uses('lang.ClassLoader', 'io.cca.Archive');
  
  /** 
   * Loads XP classes from a CCA (Class Collection Archive)
   * 
   * Note: The classes *must* be stored in the CCA with their fully 
   * qualified class names as key!
   *
   * Usage:
   * <code>
   *   $l= &new ArchiveClassLoader(new Archive(new File('soap.cca')));
   *   try(); {
   *     $class= &$l->loadClass($argv[1]);
   *   } if (catch('ClassNotFoundException', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   * 
   *   $obj= &$class->newInstance();
   * </code>
   *
   * @purpose  Load classes from an archive
   * @see      xp://lang.ClassLoader
   * @see      xp://lang.cca.Archive
   */
  class ArchiveClassLoader extends ClassLoader {
    var
      $archive  = NULL;
    
    /**
     * Constructor
     * 
     * @access  public
     * @param   &lang.cca.Archive archive
     */
    function __construct(&$archive) {
      parent::__construct();
      $this->archive= &$archive;
      $this->archive->isOpen() || $this->archive->open(ARCHIVE_READ);
    }
    
    /**
     * Load the class by the specified name
     *
     * @access  public
     * @param   string class fully qualified class name io.File
     * @return  &lang.XPClass
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    function &loadClass($class) {
      $name= xp::reflect($class);

      if (!class_exists($name)) {
        try(); {
          $data= &$this->archive->extract($class);
        } if (catch('Exception', $e)) {
          return throw(new ClassNotFoundException(sprintf(
            'Class "%s" not found: %s',
            $className,
            $e->getMessage()
          )));
        }

        $src= str_replace('__FILE__', "'".strtr($class, '.', '/').'.class.php\'', $data);
        if (FALSE === eval('?>'.$src)) {
          return throw(new FormatException('Cannot define class "'.$class.'"'));
        }

        xp::registry('class.'.$name, $class);
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }

      return new XPClass($name);
    }
  }
?>
