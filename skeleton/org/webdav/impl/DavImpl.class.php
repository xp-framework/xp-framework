<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'lang.MethodNotImplementedException',
    'org.webdav.OperationFailedException',
    'org.webdav.OperationNotAllowedException',
    'org.webdav.WebdavObject',
    'org.webdav.util.WebdavBool',
    'org.webdav.util.OpaqueLockTocken'
  );
  
  define('WEBDAV_IMPL_PROPFIND',    0x0001);
  define('WEBDAV_IMPL_PROPPATCH',   0x0002);

  /**
   * Base class of DAV implementation
   *
   * @purpose  Provide an  base class for DAV implementations
   * @see      org.webdav.WebdavScriptlet#__construct
   */ 
  class DavImpl extends Object {
    var
      $capabilities = 0;
    
    /**
     * Retreive implementation's capabilites
     *
     * @access  public
     * @return  int capabilities
     */
    function getCapabilities() {
      return $this->capabilities;
    }

    /**
     * Make some magic with the path
     *
     * @access  private
     * @param   string path The path
     * @return  string
     */
    function _normalizePath($path) {      
      $p= preg_replace('#//#', '/', $path);
      $p= preg_replace('#/\./#', '/', $p);
      $p= preg_replace('#[^/]+/\.\./#', '', $p);
      $p= preg_replace('#//#', '/', $p);
      $p= preg_replace('#/$#', '', $p);
      return $p;
    }

    /**
     * Move a file
     *
     * @access  abstract
     * @param   string filename
     * @param   string destination
     * @param   bool overwrite
     * @return  bool created
     * @throws  lang.MethodNotImplementedException
     */
    function &move($filename, $destination, $overwrite) {
      return throw(new MethodNotImplementedException($this->getName().'::move not implemented'));
    }

    /**
     * Copy a file
     *
     * @access  abstract
     * @param   string filename
     * @param   string destination
     * @param   bool overwrite
     * @return  bool created
     * @throws  lang.MethodNotImplementedException
     */
    function &copy($filename, $destination, $overwrite) {
      return throw(new MethodNotImplementedException($this->getName().'::copy not implemented'));
    }

    /**
     * Make a directory
     *
     * @access  abstract
     * @param   string colname
     * @return  bool success
     * @throws  lang.MethodNotImplementedException
     */
    function &mkcol($colname) {
      return throw(new MethodNotImplementedException($this->getName().'::mkcol not implemented'));
    }

    /**
     * Delete a file
     *
     * @access  abstract
     * @param   string filename
     * @return  bool success
     * @throws  lang.MethodNotImplementedException
     */
    function &delete($filename) {
      return throw(new MethodNotImplementedException($this->getName().'::delete not implemented'));
    }

    /**
     * Put a file
     *
     * @access  abstract
     * @param   string filename
     * @param   &string data
     * @return  bool new
     * @throws  lang.MethodNotImplementedException
     */
    function &put($filename, &$data) {
      return throw(new MethodNotImplementedException($this->getName().'::put not implemented'));
    }
    
    /**
     * Get a file
     *
     * @access  abstract
     * @param   string filename
     * @return  string &org.webdav.WebdavObject
     * @throws  lang.MethodNotImplementedException
     */
    function &get($filename) {
      return throw(new MethodNotImplementedException($this->getName().'::get not implemented'));
    }

    /**
     * Find properties
     *
     * @access  abstract
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.xml.WebdavMultistatus response
     * @return  &org.webdav.xml.WebdavMultistatus response
     * @throws  lang.MethodNotImplementedException
     */
    function &propfind(&$request, &$response) { 
      return throw(new MethodNotImplementedException($this->getName().'::propfind not implemented'));
    }

    /**
     * Patch properties
     *
     * @access  abstract
     * @param   &org.webdav.xml.WebdavPropPatcRequest request
     * @throws  lang.MethodNotImplementedException
     */
    function &proppatch(&$request) { 
      return throw(new MethodNotImplementedException($this->getName().'::proppatch not implemented'));
    }
    
    /**
     * Retrieve lock information
     *
     * @access  public
     * @param   string uri  The URI
     * @return  org.webdav.WebdavLock
     */
    function &getLockInfo($uri) {
      $lock= &$this->propStorage->getLock($uri);
      
      // There's not current lock
      if ($lock === NULL) return NULL;

      // Check if the lock is expired
      $cdate= $lock->getCreationDate();
      if ($cdate->_utime + $lock->getTimeout() < time()) {
        $this->propStorage->removeLock($uri);
        return NULL;
      }

      // Otherwise return valid lock object
      return $lock;
    }
    
    /**
     * Set lock for URI
     *
     * @access  public
     * @param   &org.webdav.WebdavLock lock   The lock object
     * @param   string[]               tokens Optional lock-tokens to overwrite lock
     * @return  &org.webdav.WebdavLock
     * @throws  OperationNotAllowedException
     */
    function &setLockInfo(&$lock, $tokens= array()) { 
      $lockinfo= $this->getLockInfo($lock->getURI());        

      // There's already lock
      if ($lockinfo !== NULL) {        
        // We have s/some lock token, so check if we can overwrite the lock
        if (sizeof($tokens)) {
          if (!in_array($lockinfo->getLockToken(), $tokens)) {
            return throw(new OperationNotAllowedException('Can not refresh lock on '.$uri.' with owner '.$lock->getOwner()));
          }
        
        // Create a new lock
        } else {
          if ($lock->getLockToken() != $lockinfo->getLockToken()) {
            return throw(new OperationNotAllowedException('Can not lock '.$uri.' with owner '.$lock->getOwner()));
          }
        }
      }
      
      $newOwner= $lock->getOwner();      
      // We can't set a lock where owner is empty
      if (empty($newOwner)) {
        return throw(new OperationNotAllowedException('Can not set lock with empty owner'));
      }
      
      // Check token
      if (empty($token)) $t= &new OpaqueLockTocken(UUID::create());

      // Set lock
      $lock->setLockToken($t->toString());
      $this->propStorage->setLock($lock->getURI(), $lock);
      return $lock;
    }
    
  }
?>
