<?php namespace xp\xar\instruction;

use xp\xar\Options;
use io\collections\FileCollection;
use io\collections\iterate\FilteredIOCollectionIterator;
use io\collections\iterate\NegationOfFilter;
use io\collections\iterate\AllOfFilter;
use io\collections\iterate\UriMatchesFilter;
use io\collections\iterate\CollectionFilter;

/**
 * Create Instruction
 */
class CreateInstruction extends AbstractInstruction {

  /**
   * Add a single URI
   *
   * @param   string uri Absolute path to file
   * @param   string cwd Absolute path to current working directory
   * @param   string urn The name under which this file should be added in the archive
   */
  protected function add($uri, $cwd, $urn= null) {
    $urn || $urn= strtr(preg_replace('#^('.preg_quote($cwd, '#').'|/)#', '', $uri), DIRECTORY_SEPARATOR, '/');
    $this->options & Options::VERBOSE && $this->out->writeLine($urn);
    $this->archive->add(new \io\File($uri), $urn);
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
      if (false !== ($p= strrpos($arg, '='))) {
        $urn= substr($arg, $p+ 1);
        $arg= substr($arg, 0, $p);
      } else {
        $urn= null;
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
            new NegationOfFilter(new UriMatchesFilter('#'.$qs.'(CVS|\.svn|\.git|\.arch|\.hg|_darcs|\.bzr)'.$qs.'#')),
            new NegationOfFilter(new CollectionFilter())
          )),
          true
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
