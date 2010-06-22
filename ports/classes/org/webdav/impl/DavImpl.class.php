<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'lang.ElementNotFoundException',
    'lang.MethodNotImplementedException',
    'org.webdav.OperationFailedException',
    'org.webdav.OperationNotAllowedException',
    'org.webdav.version.WebdavVersionsContainer',
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
    public
      $capabilities = 0;
    
    /**
     * Retreive implementation's capabilites
     *
     * @return  int capabilities
     */
    public function getCapabilities() {
      return $this->capabilities;
    }

    /**
     * Make some magic with the path
     *
     * @param   string path The path
     * @return  string
     */
    protected function _normalizePath($path) {      
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
     * @param   string filename
     * @param   string destination
     * @param   bool overwrite
     * @return  bool created
     * @throws  lang.MethodNotImplementedException
     */
    public function move($filename, $destination, $overwrite) {
      throw new MethodNotImplementedException($this->getName().'::move not implemented');
    }

    /**
     * Copy a file
     *
     * @param   string filename
     * @param   string destination
     * @param   bool overwrite
     * @return  bool created
     * @throws  lang.MethodNotImplementedException
     */
    public function copy($filename, $destination, $overwrite) {
      throw new MethodNotImplementedException($this->getName().'::copy not implemented');
    }

    /**
     * Make a directory
     *
     * @param   string colname
     * @return  bool success
     * @throws  lang.MethodNotImplementedException
     */
    public function mkcol($colname) {
      throw new MethodNotImplementedException($this->getName().'::mkcol not implemented');
    }

    /**
     * Delete a file
     *
     * @param   string filename
     * @return  bool success
     * @throws  lang.MethodNotImplementedException
     */
    public function delete($filename) {
      throw new MethodNotImplementedException($this->getName().'::delete not implemented');
    }

    /**
     * Put a file
     *
     * @param   string filename
     * @param   string data
     * @return  bool new
     * @throws  lang.MethodNotImplementedException
     */
    public function put($filename, $data) {
      throw new MethodNotImplementedException($this->getName().'::put not implemented');
    }
    
    /**
     * Get a file
     *
     * @param   string filename
     * @return  string &org.webdav.WebdavObject
     * @throws  lang.MethodNotImplementedException
     */
    public function get($filename) {
      throw new MethodNotImplementedException($this->getName().'::get not implemented');
    }

    /**
     * Find properties
     *
     * @param   org.webdav.xml.WebdavPropFindRequest request
     * @param   org.webdav.xml.WebdavMultistatus response
     * @return  org.webdav.xml.WebdavMultistatus response
     * @throws  lang.MethodNotImplementedException
     */
    public function propfind($request, $response) {
      throw new MethodNotImplementedException($this->getName().'::propfind not implemented');
    }

    /**
     * Patch properties
     *
     * @param   org.webdav.xml.WebdavPropPatcRequest request
     * @throws  lang.MethodNotImplementedException
     */
    public function proppatch($request) {
      throw new MethodNotImplementedException($this->getName().'::proppatch not implemented');
    }
    
    /**
     * Lock a File
     *
     * @param   org.webdav.xml.WebdavLockRequest       request
     * @param   org.webdav.xml.WebdavScriptletResponse response
     * @throws  org.webdav.OperationNotAllowedException
     * @throws  org.webdav.OperationFailedException
     */
    public function lock($request, $response) {
      preg_match_all('/<[^>]*> \(<([^>]*)>\)/', $request->getHeader('If'), $ifmatches);
      $lock= $this->setLockInfo($request->getProperties(), $ifmatches[1]);
      $response->addLock($lock);
    }
    
    /**
     * Unlock a File
     *
     * @param   org.webdav.xml.WebdavLockRequest       request
     * @param   org.webdav.xml.WebdavScriptletResponse response
     * @throws  org.webdav.OperationNotAllowedException
     */
    public function unlock($request, $response) {
        
      // Remove < and > from beginning and end of the header 
      // e.g. <opaquelocktoken:88516110-6110-1851-bbde-48de5b3f07f4> => opaquelocktoken:88516110-6110-1851-bbde-48de5b3f07f4
      $reqToken= substr($request->getHeader('Lock-Token'), 1, -1);

      // return an exception if an unlock is requested on a non-locked file
      if (($lock= $this->getLockInfo($request->getPath())) == NULL) throw new OperationFailedException('No Lock for File: '.$request->getPath());

      if ($reqToken != $lock->getLockToken())
        throw new OperationNotAllowedException('Cant unlock '.$request->getPath());
      
      $this->propStorage->removeLock($request->getPath());

      if ($lock->getLockToken()) $response->setHeader('Lock-Token', $request->getHeader('Lock-Token'));
    }
    
    /**
     * Retrieve lock information
     *
     * @param   string uri  The URI
     * @return  org.webdav.WebdavLock
     */
    public function getLockInfo($uri) {
      $lock= $this->propStorage->getLock($uri);
      
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
     * @param   org.webdav.WebdavLock lock   The lock object
     * @param   string[]               tokens Optional lock-tokens to overwrite lock
     * @return  org.webdav.WebdavLock
     * @throws  org.webdav.OperationNotAllowedException
     */
    public function setLockInfo($lock, $tokens= array()) { 
      $lockinfo= $this->getLockInfo($lock->getURI());

      // There's already lock
      if ($lockinfo !== NULL) {        
        // We have s/some lock token, so check if we can overwrite the lock
        if (sizeof($tokens)) {
          if (!in_array($lockinfo->getLockToken(), $tokens)) {
            throw new OperationNotAllowedException('Can not refresh lock on '.$lock->getURI().' with owner '.$lock->getOwner());
          }
        } else {
          if ($lock->getLockToken() != $lockinfo->getLockToken()) {
            throw new OperationNotAllowedException('LOCK failed - invalid Token given '.$lock->getLockToken());
          }
        }
      }
      
      $newOwner= $lock->getOwner();      
      // We can't set a lock where owner is empty
      if (empty($newOwner)) {
        throw new OperationNotAllowedException('Can not set lock with empty owner');
      }
      
      // Check token
      if (empty($token)) $t= new OpaqueLockTocken(UUID::create());

      // Set lock
      $lock->setLockToken($t->toString());
      $this->propStorage->setLock($lock->getURI(), $lock);
      return $lock;
    }
    
    /**
     * Start Version-Control of file
     *
     * @param   string filename
     * @return  bool
     * @throws  lang.ElementNotFoundException
     */
    public function VersionControl($path, $version) {
      $props= array();

      // Set versions as properties
      with ($p= new WebdavProperty('version', new WebdavVersionsContainer($version))); {
        $p->setNameSpaceName('DAV:');
        $p->setNameSpacePrefix('D:');
        $props[$p->getNameSpacePrefix().$p->getName()]= $p;
      }

      // Set checked-in property
      with ($p= new WebdavProperty('checked-in', '1.0')); {
        $p->setNameSpaceName('DAV:');
        $p->setNameSpacePrefix('D:');
        $props[$p->getNameSpacePrefix().$p->getName()]= $p;
      }

      $this->propStorage->setProperties($path, $props); 

      // Copy file to versions collection
      $this->backup(
        $path,
        $version->getHref()
      );
      return TRUE;
    }
    
    /**
     * Report version status
     *
     * @param   org.webdav.xml.WebdavLockRequest
     * @param   org.webdav.xml.WebdavScriptletResponse
     * @return  bool success
     */
    public function report($request, $response) {
      $prop= $this->propStorage->getProperty(
        $request->getPath(),
        'D:version'
      );
      
      $response->addWebdavVersionContainer($prop->value);
      return TRUE;
    }
    
  }
?>
