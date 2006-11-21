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
     * @access  public
     * @return  string[]
     */
    function retrieveFilelist() {
      $list= array();

      foreach ($this->getArguments() as $arg) {
        if (is_file($arg)) {
          $list[]= ltrim($arg, './');
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
            $element= $iterator->next();
            $list[]= ltrim(substr($element->getURI(), strlen(realpath(dirname($arg)))), './');
          }
          
          continue;
        }
      }
      
      return $list;
    }
    
    /**
     * Execute action
     *
     * @access  public
     * @return  int
     */
    function perform() {
      $archive= &new Archive(new File($this->filename));
      $archive->open(ARCHIVE_CREATE);
      
      $cwd= getcwd();
      if ($this->args->exists('C', 'C', FALSE)) {
        chdir($this->args->value('C', 'C'));
      }
      
      foreach ($this->retrieveFilelist() as $file) {
        if (($this->options & OPTION_VERBOSE)) {
          Console::writeLine($file);
        }
        
        $archive->add(new File($file), $file);
      }
      
      $archive->create();
      chdir($cwd);
      
      return 0;
    }
  }
?>
