<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'org.cvshome.CVSInterface',
    'io.File',
    'io.TempFile'
  );

  /**
   * This class is an easy to use interface to the concurrent versioning 
   * system executables.
   *
   * @purpose  interface to CVS
   * @see      http://www.cvshome.org
   */
  class CVSFile extends CVSInterface {
    public
      $filename= NULL;
      
    /**
     * Construct a new CVS Interface object
     *
     * @param   string filename
     * @throws  io.FileNotFoundException if filename is not a file
     */
    public function __construct($filename) {
      $this->filename= realpath($filename);
      
      if (!file_exists ($this->filename) || !is_file ($this->filename)) {
        throw new FileNotFoundException('Given file must be an existing file: '.$this->filename);
      }
    }
    
    /**
     * Protected helper method that executes a CVS command, changing
     * to the desired directory before doing so and changing it back
     * after finishing.
     *
     * @param   string cvsCmd
     * @return  string[]
     */
    protected function _execute($cvsCmd) {
      $olddir= getcwd(); 
      chdir(dirname ($this->filename));
      $r= parent::_execute ($cvsCmd, basename ($this->filename));
      chdir($olddir);
      
      return $r;
    }
    
    /**
     * Update a file or directory
     *
     * @param   bool sim default FALSE whether to simulate
     * @return  stdclass[] objects
     * @see     http://www.cvshome.org/docs/manual/cvs_16.html#SEC152
     */
    public function update($sim= FALSE) {
      $results= $this->_execute (sprintf ('update %s',
        ($sim ? '-nq' : '')
      ));
      
      $stats= array();
      foreach ($results as $r) {
        if (strstr ($r, 'cvs server: warning:')) {
          // File has been removed, find out filename
          if (preg_match ('/(\S+) is not not \(any longer\) pertinent$/', $r, $match))
            $stats[$match[1]]= CVS_REMOVED;
            
          continue;
        }
        
        list ($c, $f)= explode (' ', trim ($r));
        $stats[$f]= $this->getCVSStatus ($c);
      }
      
      return $stats;
    }
    
    /**
     * Get the status from a file
     *
     * The returned object has the form:
     * <code>
     *   $result->workingrevision= '1.20';
     *   $result->tags['STABLE']= '1.15';
     * </cde>
     *
     * @return  stdclass status
     */
    public function getStatus() {
      $output= $this->_execute('status -v');
      
      if (strstr($output[0], 'cvs server: nothing known about')) {
        throw new CVSInterfaceException(
          'File '.$this->filename.' is not known to CVS'
        );
      }

      $result= new stdClass(); 
      $inTags= false;
      $result->tags= array();
      foreach ($output as $r) {
        if ($inTags) {
          if (preg_match ('/\s*(\S+)\s+\(revision: ([^\)]+)\)\s*$/', trim ($r), $match)) {
            $result->tags[$match[1]]= $match[2];
          }
        
          continue;
        }
        
        if (preg_match ('/File: (\S+)\s+Status: (.+)$/', $r, $match)) {
          $result->filename= $match[1];
          $result->status= $this->getCVSStatusFromString($match[2]);
          continue;
        }
        
        if (preg_match ('/(Working|Repository) revision:\s*(\S+)/', $r, $match)) {
          $type= strtolower ($match[1]).'revision';
          $result->$type= $match[2];
          continue;
        }
        
        if (preg_match ('/Sticky (Tag|Date|Options):\s*(\S+)/', $r, $match)) {
          if ('(none)' != $match[2]) {
            $type= 'sticky_'.strtolower ($match[1]);
            $result->$type= $match[2];
          }
          continue;
        }
        
        if (strstr ($r, 'Existing Tags:')) {
          $inTags= TRUE;
          continue;
        }
      }

      return $result;  
    }
  
    /**
     * Commit the file (needs write access to repository)
     *
     * @param   string comment
     * @see     http://www.cvshome.org/docs/manual/cvs_16.html#SEC124
     */
    public function commit($comment) {
      $f= new TempFile();
      $f->open (FILE_MODE_WRITE);
      $f->writeLine ($comment);
      $f->close();
      $return= $this->_execute(sprintf ('commit -F %s', $f->getURI()));
      
      // It seems CVS automatically removes the tmp-file after
      // reading it, so we don't need to do so (gives error).
      return $return;
    }
    
    /**
     * Removes a file from the repository, deletes (renames) it locally.
     * To complete this action, you have to call commit. Use this with
     * caution.
     *
     * @return  bool success
     */
    public function remove() {
      $f= new File ($this->filename);
      $f->move ($this->filename.'.cvsremove');
      return $this->_execute ('remove');
    }

    /**
     * Adds a file to a repository. Please note, that it is neccessary
     * that the directory also already exists in CVS, otherwise
     * an error will be thrown.
     *
     * @return  bool success
     */    
    public function add() {
      return $this->_execute ('add');
    }
    
    /**
     * Compares two versions of this file. Leave both parameters NULL to
     * let CVS compare the local file agains the one in the repository.
     * Specify only $r1 to compare local version agains revision $r1.
     * Specify both params to diff two CVS-revisions against each other.
     * You can also use CVS-Tags here.
     *
     * @param   string revision_from
     * @param   string revision_to
     * @return  array diff lines from the diff
     * @see     http://www.cvshome.org/docs/manual/cvs_16.html#SEC129
     */
    public function diff($r1= NULL, $r2= NULL) {
      $cmd= sprintf ('diff -B -b %s %s',
        (NULL !== $r1 ? '-r'.$r1 : ''),
        (NULL !== $r2 ? '-r'.$r2 : '')
      );

      return $this->_execute ($cmd);
    }

    /**
     * Tags a file in repository. 
     *
     * @param   string tag
     * @return  bool success
     */    
    public function tag($tag) {
      $result= $this->_execute(sprintf ('tag -F %s',
        $tag
      ));
      
      return substr($result[0], 0, 1) == 'T';
    }    
    
    /**
     * Get the log entries from cvs. The output format for those
     * log-entries are crap because the format is ambigous. This
     * parser will fail to correctly identify log-entries if any
     * log-line contains a line of 28 '-'.
     *
     * @return  &array logs
     * @see     http://www.cvshome.org/docs/manual/cvs_16.html#SEC142
     */
    public function getLog() {
      $output= $this->_execute ('log');
      $divider= str_repeat ('-', 28);
      $log= array();
      
      $cnt= count ($output);
      for ($i= 0; $i < $cnt-2; $i++) {
        $l= $output[$i];
        if (substr ($output[$i], 0, 28) == str_repeat ('-', 28) &&
          preg_match ('/^revision (\S+)$/', $output[$i+1], $match)) {
          
          // Set data for log
          $activeRev= $match[1];
          list ($date, $author, $state)= explode (';', $output[$i+2]);
          $activeLog= '';
          
          for ($y= $i+ 3; $y < $cnt- 1; $y++) {
            if ($divider != $output[$y]) {
              $activeLog.= $output[$y]."\n";
            } else {
              break;
            }
          }
          
          // Make that entry
          list (, $date)= explode (': ', $date, 2);
          list (, $author)= explode (': ', $author, 2);
          list (, $state)= explode (': ', $state, 2);
          
          $entry= array(
            'date'      => $date,
            'author'    => $author,
            'state'     => $state,
            'revision'  => $activeRev,
            'log'       => $activeLog
          );
          $log[$activeRev]= (object)$entry;
          
          // Move pointer to where we stopped
          $i= $y-1;
        }
      }
      
      return $log;
    }
    
    /**
     * Retrieves the data of the file at a specific revision.
     * If no revision is given, this fetches HEAD.
     *
     * @param   string revision default NULL 
     * @return  string contents
     */    
    public function getRevision($rev= NULL) {
      $data= $this->_execute (sprintf ('update -p %s', 
        NULL !== $rev ? '-r '.$rev : ''
      ));
      if (!count ($data))
        return FALSE;

      // Does this version exist?
      if (preg_match ('/^cvs server: .* is no longer in the repository/', $data[0]))
        return FALSE;
      
      return implode ("\n", $data);
    }    
  }
?>
