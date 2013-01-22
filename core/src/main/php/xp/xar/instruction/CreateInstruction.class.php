<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xp.xar.instruction.AbstractInstruction',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.NegationOfFilter',
    'io.collections.iterate.AllOfFilter',
    'io.collections.iterate.RegexFilter',
    'io.collections.iterate.CollectionFilter'
  );

  /**
   * Create Instruction
   *
   * @purpose  Create
   */
  class CreateInstruction extends AbstractInstruction {

    /**
     * Add a single URI
     *
     * @param   string uri Absolute path to file
     * @param   string cwd Absolute path to current working directory
     * @param   string urn The name under which this file should be added in the archive
     */
    protected function add($uri, $cwd, $urn= NULL) {
      $urn || $urn= strtr(preg_replace('#^('.preg_quote($cwd, '#').'|/)#', '', $uri), DIRECTORY_SEPARATOR, '/');
      $this->options & Options::VERBOSE && $this->out->writeLine($urn);
      $this->archive->add(new File($uri), $urn);
    }

    /**
     * Retrieve files from filesystem
     *
     * @param   string cwd
     * @return  string[]
     */
    public function addAll($cwd) {
      $list= array();
      $qs= preg_quote(DIRECTORY_SEPARATOR);

      foreach ($this->getArguments() as $arg) {
        if (FALSE !== ($p= strrpos($arg, '='))) {
          $urn= substr($arg, $p+ 1);
          $arg= substr($arg, 0, $p);
        } else {
          $urn= NULL;
        }
      
        if (is_file($arg)) {
          $this->add(realpath($arg), $cwd, $urn);
          continue;
        }
        
        // Recursively retrieve all files from directory, ignoring well-known
        // VCS control files.
        if (is_dir($arg)) {
          $collection= new FileCollection($arg);
          $iterator= new FilteredIOCollectionIterator(
            $collection,
            new AllOfFilter(array(
              new NegationOfFilter(new RegexFilter('#'.$qs.'(CVS|\.svn|\.git|\.arch|\.hg|_darcs|\.bzr)'.$qs.'#')),
              new NegationOfFilter(new CollectionFilter())
            )),
            TRUE
          );
          
          while ($iterator->hasNext()) {
            $this->add($iterator->next()->getURI(), $cwd);
          }
          continue;
        }
      }
      
      return $list;
    }
    
    /**
     * Execute action
     *
     * @return  int
     */
    public function perform() {
      $this->archive->open(ARCHIVE_CREATE);
      $this->addAll(rtrim(realpath(getcwd()), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR);
      $this->archive->create();
    }
  }
?>
