<?php
/* This class is part of the XP framework
 *
 * $Id: CreateInstruction.class.php 10182 2007-05-02 13:56:55Z olli $ 
 */

  namespace net::xp_framework::xar::instruction;

  ::uses(
    'net.xp_framework.xar.instruction.AbstractInstruction',
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
      $this->options & ::OPTION_VERBOSE && util::cmd::Console::writeLine($urn);
      $this->archive->add(new io::File($uri), $urn);
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
          $collection= new io::collections::FileCollection($arg);
          
          // Fetch all files except 
          $iterator= new io::collections::iterate::FilteredIOCollectionIterator(
            $collection,
            new io::collections::iterate::AllOfFilter(array(
              new io::collections::iterate::NegationOfFilter(new io::collections::iterate::RegexFilter('#'.preg_quote(DIRECTORY_SEPARATOR).'(CVS|\.svn)'.preg_quote(DIRECTORY_SEPARATOR).'#')),
              new io::collections::iterate::NegationOfFilter(new io::collections::iterate::CollectionFilter())
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
