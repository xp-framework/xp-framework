<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

    uses(
      'lang.IClassLoader',
      'xp.compiler.emit.source.Emitter',
      'xp.compiler.types.TaskScope',
      'xp.compiler.diagnostic.NullDiagnosticListener',
      'xp.compiler.io.FileManager',
      'xp.compiler.task.CompilationTask'
    );

  /**
   * JIT compiling classloader
   *
   */
  class JitClassLoader extends Object implements IClassLoader {
    protected $filemanager= NULL;
    protected $emitter= NULL;
    protected $sources= array();
    
    /**
     * Initializes file manager and emitter
     *
     */
    public function __construct() {
      $this->filemanager= new FileManager();
      $this->filemanager->setSourcePaths(xp::$registry['classpath']);
      $this->emitter= new xp·compiler·emit·source·Emitter();
    }
    
    /**
     * Checks whether this loader can provide the requested class
     *
     * @param   string class
     * @return  bool
     */
    public function providesClass($class) {
      if ('' == $class || NULL === ($source= $this->filemanager->findClass($class))) return FALSE;
      
      // Cache information for use in loadClass0()
      $this->sources[$class]= $source;
      return TRUE;
    }
    
    /**
     * Checks whether this loader can provide the requested resource
     *
     * @param   string filename
     * @return  bool
     */
    public function providesResource($filename) {
      return FALSE;
    }

    /**
     * Checks whether this loader can provide the requested package
     *
     * @param   string package
     * @return  bool
     */
    public function providesPackage($package) {
      return FALSE;
    }

    /**
     * Get package contents
     *
     * @param   string package
     * @return  string[] filenames
     */
    public function packageContents($package) {
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
      return new XPClass($this->loadClass0($class));
    }

    /**
     * Load the class by the specified name
     *
     * @param   string class fully qualified class name io.File
     * @return  string class name
     * @throws  lang.ClassNotFoundException in case the class can not be found
     */
    public function loadClass0($class) {
      if (isset(xp::$registry['classloader.'.$class])) return xp::reflect($class);
      
      // Read cached information provided by providesClass()
      if (!($source= @$this->sources[$class])) {
        throw new ClassNotFoundException('Cannot find class '.$class);
      }
      unset($this->sources[$class]);

      try {
        $r= $this->emitter->emit($this->filemanager->parseFile($source), new TaskScope(new CompilationTask(
          $source,
          new NullDiagnosticListener(),
          $this->filemanager,
          $this->emitter
        )));
        $r->executeWith(array());
        return xp::reflect($r->type()->name());
      } catch (ParseException $e) {
        raise('lang.ClassFormatException', $e->getCause()->getMessage());
      } catch (FormatException $e) {
        raise('lang.ClassFormatException', $e->getMessage());
      }
    }
    
    /**
     * Loads a resource.
     *
     * @param   string string name of resource
     * @return  string
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResource($string) {
      raise('lang.lang.ElementNotFoundException', 'Cannot find resource '.$string);
    }
    
    /**
     * Retrieve a stream to the resource
     *
     * @param   string string name of resource
     * @return  io.Stream
     * @throws  lang.ElementNotFoundException in case the resource cannot be found
     */
    public function getResourceAsStream($string) {
      raise('lang.lang.ElementNotFoundException', 'Cannot find resource '.$string);
    }
    
    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'<@classpath>';
    }
  }
?>
