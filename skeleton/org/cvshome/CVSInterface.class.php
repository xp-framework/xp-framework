<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  // Constants for CVS
  define ('CVS_ADDED',    0x0001);
  define ('CVS_UNKNOWN',  0x0002);
  define ('CVS_PATCHED',  0x0003);
  define ('CVS_UPDATED',  0x0004);
  define ('CVS_REMOVED',  0x0005);
  define ('CVS_MODIFIED', 0x0006);
  define ('CVS_CONFLICT', 0x0007);
  define ('CVS_UPTODATE', 0x0008);

  uses (
    'lang.IllegalArgumentException',
    'org.cvshome.CVSInterfaceException'
  );

  /**
   * This class is an easy to use interface to the 
   * concurrent versioning system executables
   *
   * @purpose interface to CVS
   * @see http://www.cvshome.org
   */
  class CVSInterface extends Object {
    var
      $_CVS= 'cvs',
      $cvsRoot= NULL;
      
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
     * Execute a CVS command
     *
     * @access private
     * @param int cvsCmd Command to execute
     * @return array output
     * @throws CVSInterfaceException, if cvs fails
     */
    function _execute($cvsCmd) {
      $cmdLine= sprintf ("%s %s %s %s 2>&1",
        $this->_CVS,
        (NULL !== $this->cvsRoot ? '-d'.$this->cvsRoot : ''),
        $cvsCmd,
        basename ($this->filename)
      );

      $oldDir= getcwd();
      chdir (dirname ($this->filename));      
      exec ($cmdLine, $output, $returnCode);
      chdir ($oldDir);
      
      if (count ($output) && strstr ($output[0], 'Cannot access'))
        return throw (
          new CVSInterfaceException ('Cannot access CVSROOT!')
        );

      return $output;
    }

    /**
     * Set CVS-Root and Login
     * Login must be without "-d"
     * E.g: setCVSRoot ('/home/cvs/', ':ext:alex@php3.de')
     *
     * @access public
     * @param string cvsroot
     * @param string login
     */
    function setCVSRoot($cvsRoot, $login= '') {
      $this->cvsRoot= sprintf ("%s%s%s",
        !empty ($login) ? '-d' : '',
        !empty ($login) ? $login.':' : '',
        $cvsRoot
      );
    }
    
    /**
     * Returns the internal statuscode from the cvs status code
     *
     * @access public
     * @param char statusCode
     * @return int statusCode
     * @throws CVSInterfaceException
     */
    function getCVSStatus($statusCode) {
      switch ($statusCode) {
        case '?': return CVS_UNKNOWN;
        case 'P': return CVS_PATCHED;
        case 'U': return CVS_UPDATED;
        case 'M': return CVS_MODIFIED;
        case 'C': return CVS_CONFLICT;
        case 'A': return CVS_ADDED;
        default: break;
      }
      
      return throw (new CVSInterfaceException ('Unknown statuscode '.$statusCode));
    }
    
    /**
     * Returns the internal statuscode from the cvs status string
     *
     * @access public
     * @param string statusString
     * @return int statusCode
     * @throws CVSInterfaceException
     */
    function getCVSStatusFromString($statusCode) {
      switch ($statusCode) {
        case 'Up-to-date': return CVS_UPTODATE;
        case 'Added': return CVS_ADDED;
        case 'Locally modified': return CVS_MODIFIED;
        default: break;
      }
      
      return throw (new CVSInterfaceException ('Unknown statusstring '.$statusCode));
    }
    
    /**
     * Update a file or directory
     *
     * @access public
     * @param bool simulate simulate or do really
     * @return array stats
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
     */
    function commit($comment) {
      return $this->_execute (sprintf ("commit -m '%s' %s",
        addSlashes ($comment),
        $this->filename
      ));
    }
  }
?>
