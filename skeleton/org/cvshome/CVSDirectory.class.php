<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  class CVSDirectory extends CVSInterface {
    var
      $path= NULL,
      $_folder= NULL;
    
    function __construct($path) {
      $this->path= $path;
      $this->_folder= &new Folder ($path);
      
      try(); {
        // $this->_folder->open();
      } if (catch ('IOException', $e)) {
        return throw ($e);
      }
    }
    
    function _execute($cvsCmd) {
      $olddir= getcwd(); chdir ($this->path);
      return parent::_execute ($cvsCmd);
      chdor ($olddir);
    }
    
    function update($recursive= FALSE, $sim= TRUE) {
      try(); {
        $results= $this->_execute (sprintf ('%s update %s',
          ($sim ? '-nq' : ''),
          ($recursive ? '' : '-l')
        ));
      } if (catch ('CVSInterfaceException', $e)) {
        return throw ($e);
      }
      
      $stats= array();
      foreach ($results as $r) {
        if (strstr ($r, 'cvs server: warning:')) {
          // File has been removed, find out filename
          if (preg_match ('/(\S+) is not not \(any longer\) pertinent$/', $r, $match))
            $stats[$match[1]]= CVS_REMOVED;
            
          continue;
        }
    
        list ($state, $filename)= explode (' ', $r);
        if (FALSE !== ($s= $this->getCVSStatus ($state))) {
          $f= new StdClass();
          $f->status= $s;
          $f->filename= $filename;
          $f->uri= $this->path.'/'.$filename;
          $stats[]= $f;
        } else {
          // Remove after debugging
          var_dump ('Unknown status: '.$state);
        }
      }
      
      return $stats;
    }
    
  }
  
?>
