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
     * @param   string uri
     * @param   string cwd
     */
    protected function add($uri, $cwd) {
      $urn= strtr(preg_replace('#^('.preg_quote($cwd, '#').'|/)#', '', $uri), DIRECTORY_SEPARATOR, '/');
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

      foreach ($this->getArguments() as $arg) {
        if (is_file($arg)) {
          $this->add(realpath($arg), $cwd);
          continue;
        }
        
        // Recursively retrieve all files from directory
        if (is_dir($arg)) {
          $collection= new FileCollection($arg);
          
          // Fetch all files except 
          $iterator= new FilteredIOCollectionIterator(
            $collection,
            new AllOfFilter(array(
              new NegationOfFilter(new RegexFilter('#'.preg_quote(DIRECTORY_SEPARATOR).'(CVS|\.svn)'.preg_quote(DIRECTORY_SEPARATOR).'#')),
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
