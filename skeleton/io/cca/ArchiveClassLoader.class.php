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
   * @deprecated
   * @test     xp://net.xp_framework.unittest.io.ArchiveClassLoaderTest
   * @purpose  Load classes from an archive
   * @see      xp://lang.ClassLoader
   * @see      xp://lang.cca.Archive
   * @ext      tokenize
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
     * Load class bytes
     *
     * @access  public
     * @param   string name fully qualified class name
     * @return  string
     */
    function loadClassBytes($name) {
      $src= '';
      $line= 0;
      $tokens= token_get_all($this->archive->extract($name));
      for ($i= 0, $s= sizeof($tokens); $i < $s; $i++) {
        switch ($tokens[$i][0]) {
          case T_FILE: 
            $tokens[$i][1]= "'".strtr($name, '.', '/').'.class.php\''; 
            break;
            
          case T_LINE:
            $tokens[$i][1]= $line;
            break;

          case T_STRING:
            if ('uses' == $tokens[$i][1] || 'implements' == $tokens[$i][1]) {
              $o= $i+ 1;
              while (')' != $tokens[$o][0]) {
                if (T_CONSTANT_ENCAPSED_STRING == $tokens[$o][0]) {
                  $used= trim($tokens[$o][1], '"\'');
                  $this->archive->contains($used) && $this->loadClass($used);
                }
                $o++;
              }
            }
            break;
        }

        if (is_array($tokens[$i])) {
          $src.= $tokens[$i][1];
          $line+= substr_count($tokens[$i][1], "\n");
        } else {
          $src.= $tokens[$i];
          $line+= substr_count($tokens[$i], "\n");
        }
      }
      
      return $src;
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
          $src= $this->loadClassBytes($class);
        } if (catch('Exception', $e)) {
          return throw(new ClassNotFoundException(sprintf(
            'Class "%s" not found: %s',
            $class,
            $e->getMessage()
          )));
        }

        if (FALSE === eval('?>'.$src)) {
          return throw(new FormatException('Cannot define class "'.$class.'"'));
        }

        xp::registry('class.'.$name, $class);
        xp::registry('classloader.'.$class, $this);
        is_callable(array($name, '__static')) && call_user_func(array($name, '__static'));
      }

      $c= &new XPClass($name);
      return $c;
    }
  }
?>
