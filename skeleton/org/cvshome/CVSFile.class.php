<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses (
    'org.cvshome.CVSInterface',
    'lang.IllegalArgumentException',
    'io.File'
  );

  /**
   * This class is an easy to use interface to the 
   * concurrent versioning system executables
   *
   * @purpose interface to CVS
   * @see http://www.cvshome.org
   */
  class CVSFile extends CVSInterface {
    var
      $filename= NULL;  
      
    /**
     * Construct a new CVS Interface object
     *
     * @access public
     * @param string filename
     * @throws IllegalArgumentException, if filename is not a file
     */
    function __construct($filename) {
      $this->filename= $filename;
      
      if (!file_exists ($this->filename) || !is_file ($this->filename)) {
        return throw (new IllegalArgumentException ('Given file must be an existing file'));
      }
      
      parent::__construct();
    }
    
    /**
     * Update a file or directory
     *
     * @access public
     * @param bool simulate simulation mode
     * @return array stats
     * @see http://www.cvshome.org/docs/manual/cvs_16.html#SEC152
     */
    function update($sim= false) {
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
     * @access  public
     * @param 
     * @return object status
     *         The returned object has the form:
     *         result->workingrevision= '1.20';
     *         result->tags['STABLE']= '1.15'; etc.
     * 
     */
    function &getStatus() {
      try(); {
        $output= $this->_execute ('status -v');
      } if (catch ('CVSInterfaceException', $e)) {
        $e->printStackTrace();
        return $e;
      }
      
      if (strstr ($output[0], 'cvs server: nothing known about'))
        return throw (new CVSInterfaceException ('File '.$this->filename.
          ' is not known to CVS'));

      $result= new StdClass(); $inTags= false;
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
          $inTags= true;
          continue;
        }
      }

      return $result;  
    }
  
    /**
     * Commit the file (needs write access to repository)
     *
     * @access public
     * @param string comment
     * @see http://www.cvshome.org/docs/manual/cvs_16.html#SEC124
     */
    function commit($comment) {
      $tmpFilename= '/tmp/cvscommitmsg.'.rand(1, 10000);
      $f= &new File ($tmpFilename);
      try(); {
        $f->open (FILE_MODE_WRITE);
        $f->writeLine ($comment);
        $f->close();
      } if (catch ('IOException', $e)) {
        echo $e->printStackTrace();
        return $e;
      }

      $return= &$this->_execute (sprintf ("commit -F %s",
        $tmpFilename
      ));
      
      // It seems CVS automatically removes the tmp-file after
      // reading it, so we don't need to do so (gives error).
      return $return;
    }
    
    /**
     * Removes a file from the repository, deletes (renames) it locally.
     * To complete this action, you have to call commit. Use this with
     * caution.
     *
     * @access public
     * @return bool success
     */
    function remove() {
      $f= &new File ($this->filename);
      try(); {
        $f->move ($this->filename.'.cvsremove');
      } if (catch ('IOException', $e)) {
        $e->printStackTrace();
        return false;
      }
      
      return $this->_execute ('remove');
    }
    
    /**
     * Compares two versions of this file. Leave both parameters NULL to
     * let CVS compare the local file agains the one in the repository.
     * Specify only $r1 to compare local version agains revision $r1.
     * Specify both params to diff two CVS-revisions against each other.
     * You can also use CVS-Tags here.
     *
     * @access public
     * @param string revision_from
     * @param string revision_to
     * @return array diff lines from the diff
     * @see http://www.cvshome.org/docs/manual/cvs_16.html#SEC129
     */
    function diff($r1= NULL, $r2= NULL) {
      $cmd= sprintf ('diff %s %s',
        (NULL !== $r1 ? '-r'.$r1 : ''),
        (NULL !== $r2 ? '-r'.$r2 : '')
      );

      return $this->_execute ($cmd);
    }
    
    /**
     * Get the log entries from cvs. The output format for those
     * log-entries are crap because the format is ambigous. This
     * parser will fail to correctly identify log-entries if any
     * log-line contains a line of 28 '-'.
     *
     * @access public
     * @return array logs
     * @see http://www.cvshome.org/docs/manual/cvs_16.html#SEC142
     */
    function &getLog() {
      $output= $this->_execute ('log');
      $log= array();
      
      $cnt= count ($output);
      for ($i= 0; $i < $cnt-2; $i++) {
        $l= $output[$i];
        // printf ("* Checking: %s\n", $output[$i]);
        if (substr ($output[$i], 0, 28) == str_repeat ('-', 28) &&
          preg_match ('/^revision (\S+)$/', $output[$i+1], $match)) {
          
          // Set data for log
          $activeRev= $match[1];
          list ($date, $author, $state)= explode (';', $output[$i+2]);
          $activeLog= '';
          
          for ($y= $i+3; $y < $cnt-1; $y++) {
            
            if (str_repeat ('-', 28) != $output[$y]) {
              // printf ("+ Add [%s]: %s\n", $activeRev, $output[$y]);
              $activeLog.= $output[$y]."\n";
            } else {
              // printf ("-Skip [%s]: %s\n", $activeRev, $output[$y]);
              break;
            }
          }
          
          // Make that entry
          list (,$date)= explode (': ', $date);
          list (,$author)= explode (': ', $author);
          list (,$state)= explode (': ', $state);
          
          $entry= array (
            'date' => $date,
            'author' => $author,
            'state' => $state,
            'revision' => $activeRev,
            'log' => $activeLog
          );
          $log[$activeRev]= (Object)$entry;
          // var_dump ($log);
          
          // Move pointer to where we stopped
          $i= $y-1;
        }
      }
      
      return $log;
    }
  }
?>
