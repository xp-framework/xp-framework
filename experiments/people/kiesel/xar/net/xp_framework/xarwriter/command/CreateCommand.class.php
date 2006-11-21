<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'net.xp_framework.xarwriter.command.AbstractCommand',
    'lang.archive.Archive',
    'io.File',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.NegationOfFilter',
    'io.collections.iterate.AllOfFilter',
    'io.collections.iterate.RegexFilter',
    'io.collections.iterate.CollectionFilter'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class CreateCommand extends AbstractCommand {

    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function retrieveFilelist() {
      $list= array();

      for ($i= 3; $i < $this->args->count; $i++) {
        if (is_file($this->args->value($i))) {
          $list[]= ltrim($this->args->value($i), './');
          continue;
        }
        
        // Recursively retrieve all files from directory
        if (is_dir($base= $this->args->value($i))) {
          $collection= &new FileCollection($base);
          
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
            $list[]= ltrim(substr($element->getURI(), strlen(realpath(dirname($base)))), './');
          }
          
          continue;
        }
      }
      
      return $list;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
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
