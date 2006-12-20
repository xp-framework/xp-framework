<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.xarwriter.command.AbstractCommand',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.NegationOfFilter',
    'io.collections.iterate.AllOfFilter',
    'io.collections.iterate.RegexFilter',
    'io.collections.iterate.CollectionFilter'
  );

  /**
   * Create command
   *
   * @purpose  Create
   */
  class CreateCommand extends AbstractCommand {
      
    /**
     * Retrieve files from filesystem
     *
     * @return  string[]
     */
    public function retrieveFilelist() {
      $list= array();

      foreach ($this->getArguments() as $arg) {
        if (is_file($arg)) {
          $list[]= realpath($arg);
          continue;
        }
        
        // Recursively retrieve all files from directory
        if (is_dir($arg)) {
          $collection= &new FileCollection($arg);
          
          // Fetch all files except 
          $iterator= &new FilteredIOCollectionIterator(
            $collection,
            new AllOfFilter(array(
              new NegationOfFilter(new RegexFilter('#(CVS|\.svn)/#')),
              new NegationOfFilter(new CollectionFilter())
            )),
            TRUE
          );
          
          while ($iterator->hasNext()) {
            $list[]= $iterator->next()->getURI();
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
      
      $cwd= dirname($this->archive->file->getURI());
      foreach ($this->retrieveFilelist() as $file) {
        $urn= substr($file, strlen($cwd)+ 1);
        $this->options & Xar::OPTION_VERBOSE && Console::writeLine($urn);
        $this->archive->add(new File($file), $urn);
      }
      
      $this->archive->create();
    }
  }
?>
