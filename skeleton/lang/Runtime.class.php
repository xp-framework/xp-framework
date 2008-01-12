<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'lang.Process', 
    'lang.Runnable', 
    'lang.RuntimeError', 
    'lang.ElementNotFoundException'
  );

  /**
   * Represents the runtime - that is, the PHP binary executing the
   * current process.
   *
   * @test     xp://net.xp_framework.unittest.core.RuntimeTest
   * @purpose  Access to PHP runtime
   */
  class Runtime extends Object {
    protected static 
      $instance   = NULL;
      
    protected
      $executable = NULL;
    
    static function __static() {
      self::$instance= new self();
    }
    
    /**
     * Retrieve the runtime instance
     *
     * @return  lang.Runtime
     */
    public static function getInstance() {
      return self::$instance;
    }
    
    /**
     * Loads a dynamic library.
     *
     * @see     php://dl
     * @param   string name
     * @return  bool TRUE if the library was loaded, FALSE if it was already loaded
     * @throws  lang.IllegalAccessException in case library loading is prohibited
     * @throws  lang.ElementNotFoundException in case the library does not exist
     * @throws  lang.RuntimeError in case dl() fails
     */
    public function loadLibrary($name) {
      if (extension_loaded($name)) return FALSE;
    
      // dl() will fatal if any of these are set - prevent this
      if (!(bool)ini_get('enable_dl') || (bool)ini_get('safe_mode')) {
        throw new IllegalAccessException(sprintf(
          'Loading libraries not permitted by system configuration [enable_dl= %s, safe_mode= %s]',
          ini_get('enable_dl'),
          ini_get('safe_mode')
        ));
      }
      
      // Qualify filename
      $path= rtrim(realpath(ini_get('extension_dir')), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
      $filename= $name.'.'.PHP_SHLIB_SUFFIX;
      
      // Try php_<name>.<ext>, <name>.<ext>      
      if (file_exists($lib= $path.'php_'.$filename)) {
        // E.g. php_sybase_ct.dll
      } else if (file_exists($lib= $path.$filename)) {
        // E.g. sybase_ct.so
      } else {
        throw new ElementNotFoundException('Cannot find library "'.$name.'" in "'.$path.'"');
      }
      
      // Found library, try to load it. dl() expects given argument to not contain
      // a path and will failt with "Temporary module name should contain only 
      // filename" if it does.
      if (!dl(basename($lib))) {
        throw new RuntimeError('dl() failed for '.$lib);
      }
      return TRUE;
    }

    /**
     * Check whether a given extension is available
     *
     * @see     php://extension_loaded
     * @param   string name
     * @return  bool
     */
    public function extensionAvailable($name) {
      return extension_loaded($name);
    }

    /**
     * Register a shutdown hook - a piece of code that will be run before
     * the runtime shuts down (e.g. with exit).
     *
     * @see     php://register_shutdown_function
     * @param   lang.Runnable r
     * @return  lang.Runnable the given runnable
     */
    public function addShutdownHook(Runnable $r) {
      register_shutdown_function(array($r, 'run'));
      return $r;
    }

    /**
     * Retrieve the executable associated with this runtime.
     *
     * @return  string
     */
    public function getExecutable() {
      if (NULL === $this->executable) {     // Lazy-init
        $this->executable= Process::getProcessById(getmypid());
      }
      return $this->executable;
    }
  }
?>
