<?php
/* This class is part of the XP framework
 *
 * $Id: CVSInterface.class.php 8975 2006-12-27 18:06:40Z friebe $
 */

  namespace org::cvshome;

  // Constants for CVS
  define('CVS_ADDED',    0x0001);
  define('CVS_UNKNOWN',  0x0002);
  define('CVS_PATCHED',  0x0003);
  define('CVS_UPDATED',  0x0004);
  define('CVS_REMOVED',  0x0005);
  define('CVS_MODIFIED', 0x0006);
  define('CVS_CONFLICT', 0x0007);
  define('CVS_UPTODATE', 0x0008);

  ::uses(
    'lang.System',
    'org.cvshome.CVSInterfaceException'
  );
  
  /**
   * Wraps CVS commands
   *
   * @purpose  Base class
   */
  class CVSInterface extends lang::Object {
    public
      $cvsRoot= NULL;
    
    public
      $_CVS= 'cvs';
  
    /**
     * Execute a CVS command
     *
     * @param   int cvsCmd Command to execute
     * @return  array output
     * @throws  org.cvshome.CVSInterfaceException if cvs fails
     * @see     http://www.cvshome.org/docs/manual/cvs_16.html#SEC115
     */
    protected function _execute($cvsCmd, $object= '') {
      $cmdLine= sprintf ("%s %s %s %s",
        $this->_CVS,
        (NULL !== $this->cvsRoot ? '-d'.$this->cvsRoot : ''),
        $cvsCmd,
        $object
      );
      
      try {
        $output= lang::System::exec ($cmdLine, '2>&1', FALSE);
      } catch ( $e) {
      
        // Only return error if command was not "diff"
        if ('diff' != substr ($cvsCmd, 0, 4))
          throw (new CVSInterfaceException ('CVS returned failure ['.$cmdLine.']'));
      }
      
      if (count ($output) && strstr ($output[0], 'Cannot access'))
        throw (
          new CVSInterfaceException ('Cannot access CVSROOT! ['.$cmdLine.']')
        );

      return $output;
    }

    /**
     * Set CVS-Root and Login
     * Login must be without "-d"
     * E.g: setCVSRoot ('/home/cvs/', ':ext:alex@php3.de')
     *
     * @param   string cvsroot
     * @param   string login
     */
    public function setCVSRoot($cvsRoot, $login= '') {
      $this->cvsRoot= sprintf ("%s%s%s",
        !empty ($login) ? '-d' : '',
        !empty ($login) ? $login.':' : '',
        $cvsRoot
      );
    }

    /**
     * Returns the internal statuscode from the cvs status string
     *
     * @param   string statusString
     * @return  int statusCode
     * @throws  org.cvshome.CVSInterfaceException
     */
    public function getCVSStatusFromString($statusCode) {
      switch ($statusCode) {
        case 'Up-to-date': return CVS_UPTODATE;
        case 'Added': return CVS_ADDED;
        case 'Locally Modified': return CVS_MODIFIED;
        case 'Unknown': return CVS_UNKNOWN;
        case 'Needs Checkout': return CVS_UPDATED;
        case 'Conflict': return CVS_CONFLICT;
        default: break;
      }
      
      throw (new CVSInterfaceException ('Unknown statusstring '.$statusCode));
    }

    /**
     * Returns the internal statuscode from the cvs status code
     *
     * @param   char statusCode
     * @return  int statusCode
     * @throws  org.cvshome.CVSInterfaceException
     */
    public function getCVSStatus($statusCode) {
      switch ($statusCode) {
        case '?': return CVS_UNKNOWN;
        case 'P': return CVS_PATCHED;
        case 'U': return CVS_UPDATED;
        case 'M': return CVS_MODIFIED;
        case 'C': return CVS_CONFLICT;
        case 'A': return CVS_ADDED;
        default: break;
      }
      
      throw (new CVSInterfaceException ('Unknown statuscode '.$statusCode));
    }
    
  }

?>
