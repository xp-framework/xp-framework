<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.compiler.Syntax',
    'xp.compiler.emit.Emitter',
    'xp.compiler.io.Source',
    'xp.compiler.io.FileSource',
    'io.streams.FileOutputStream',
    'io.Folder',
    'io.File'
  );

  /**
   * File manager takes care of locating and parsing sourcecode and writing
   * compiled types.
   *
   * @test    xp://tests.FileManagerTest
   */
  class FileManager extends Object {
    protected $output= NULL;
    protected $sourcePaths= array();
    
    /**
     * Sets output folder
     *
     * @param   io.Folder
     */
    public function setOutput(Folder $output) {
      $this->output= $output;
    }

    /**
     * Sets output folder and returns this file manager instance
     *
     * @param   io.Folder
     * @return  xp.compiler.io.FileManager
     */
    public function withOutput(Folder $output) {
      $this->output= $output;
      return $this;
    }
    
    /**
     * Set source paths
     *
     * @param   string[] paths
     */
    public function setSourcePaths(array $paths) {
      $this->sourcePaths= $paths;
    }

    /**
     * Add source path
     *
     * @param   string path
     */
    public function addSourcePath($path) {
      $this->sourcePaths[]= $path;
    }

    /**
     * Get source paths
     *
     * @param   string[] path
     */
    public function getSourcePaths() {
      return $this->sourcePaths;
    }

    /**
     * Get parse tree for a given qualified class name by looking it
     * up in the source path.
     *
     * @param   string qualified
     * @return  xp.compiler.io.Source
     */
    public function findClass($qualified) {
      $name= DIRECTORY_SEPARATOR.strtr($qualified, '.', DIRECTORY_SEPARATOR);
      foreach ($this->sourcePaths as $path) {
        foreach (Syntax::available() as $ext => $syntax) {
          if (!file_exists($uri= $path.$name.'.'.$ext)) continue;
          return new FileSource(new File($uri), $syntax);   // FIXME: Use class loader / resources
        }
      }
      return NULL;
    }

    /**
     * Find a package
     *
     * @param   string qualifieds
     * @param   string
     */
    public function findPackage($qualified) {
      $name= DIRECTORY_SEPARATOR.strtr($qualified, '.', DIRECTORY_SEPARATOR);
      foreach ($this->sourcePaths as $path) {
        if (!is_dir($uri= $path.$name)) continue;
        return $qualified;
      }
      return NULL;
    }
  
    /**
     * Get parse tree for a given file
     *
     * @param   xp.compiler.io.Source in
     * @param   xp.compiler.Syntax s Syntax to use, determined via source file's syntax otherwise
     * @return  xp.compiler.ast.ParseTree
     */
    public function parseFile(xp·compiler·io·Source $in, Syntax $s= NULL) {
      if (NULL === $s) {
        $s= $in->getSyntax();
      }
      return $s->parse($in->getInputStream(), $in->getURI());
    }

    /**
     * Write compilation result to a given target
     *
     * @param   xp.compiler.emit.Result r
     * @param   io.File target
     * @throws  io.IOException
     */
    public function write($r, File $target) {
      $folder= new Folder($target->getPath());
      $folder->exists() || $folder->create();
      
      $r->writeTo(new FileOutputStream($target));
    }

    /**
     * Returns a file the compiled type should be written to.
     *
     * The file name will be calculated by replacing dots (".") in the 
     * type's fully qualified name by the operating system's directory 
     * separator and by append xp::CLASS_FILE_EXT. For example:
     * <pre>
     *   de.thekid.demo.Value => de/thekid/demo/Value.class.php
     * </pre>
     *
     * This is how this method behaves.
     * <ul>
     *   <li>If an output folder is set on this instance, returns a file
     *       located in the output folder. This can be changed by the 
     *       compiler's "-o" option.
     *   </li>
     *   <li>If a source is given, this method will return a file in the
     *       folder the source file resides in.
     *   </li>
     *   <li>If neither a source nor an output folder is given, the 
     *       current directory is used as a base.
     *    </li>
     * <ul>
     *
     * @param   xp.compiler.types.Types type
     * @param   xp.compiler.io.Source source
     * @return  io.File target
     */
    public function getTarget(Types $type, xp·compiler·io·Source $source= NULL) {
      $mapped= strtr($type->name(), '.', DIRECTORY_SEPARATOR);
      if ($this->output) {
        $base= $this->output;
      } else if ($source) {
        $name= str_replace('/', DIRECTORY_SEPARATOR, $source->getURI());
        return new File(str_replace(strstr(basename($name), '.'), xp::CLASS_FILE_EXT, $name));
      } else {
        $base= new Folder('.');
      }
      
      return new File($base, $mapped.xp::CLASS_FILE_EXT);
    }
  }
?>
