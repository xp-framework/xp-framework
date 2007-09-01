<?php
/* This class is part of the XP framework
 *
 * $Id: DavImpl.class.php 8975 2006-12-27 18:06:40Z friebe $
 */

  namespace org::webdav::impl;
 
  ::uses(
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
  class DavImpl extends lang::Object {
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
      throw(new lang::MethodNotImplementedException($this->getName().'::move not implemented'));
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
      throw(new lang::MethodNotImplementedException($this->getName().'::copy not implemented'));
    }

    /**
     * Make a directory
     *
     * @param   string colname
     * @return  bool success
     * @throws  lang.MethodNotImplementedException
     */
    public function mkcol($colname) {
      throw(new lang::MethodNotImplementedException($this->getName().'::mkcol not implemented'));
    }

    /**
     * Delete a file
     *
     * @param   string filename
     * @return  bool success
     * @throws  lang.MethodNotImplementedException
     */
    public function delete($filename) {
      throw(new lang::MethodNotImplementedException($this->getName().'::delete not implemented'));
    }

    /**
     * Put a file
     *
     * @param   string filename
     * @param   &string data
     * @return  bool new
     * @throws  lang.MethodNotImplementedException
     */
    public function put($filename, $data) {
      throw(new lang::MethodNotImplementedException($this->getName().'::put not implemented'));
    }
    
    /**
     * Get a file
     *
     * @param   string filename
     * @return  string &org.webdav.WebdavObject
     * @throws  lang.MethodNotImplementedException
     */
    public function get($filename) {
      throw(new lang::MethodNotImplementedException($this->getName().'::get not implemented'));
    }

    /**
     * Find properties
     *
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.xml.WebdavMultistatus response
     * @return  &org.webdav.xml.WebdavMultistatus response
     * @throws  lang.MethodNotImplementedException
     */
    public function propfind($request, $response) {
      throw(new lang::MethodNotImplementedException($this->getName().'::propfind not implemented'));
    }

    /**
     * Patch properties
     *
     * @param   &org.webdav.xml.WebdavPropPatcRequest request
     * @throws  lang.MethodNotImplementedException
     */
    public function proppatch($request) {
      throw(new lang::MethodNotImplementedException($this->getName().'::proppatch not implemented'));
    }
    
    /**
     * Lock a File
     *
     * @param   &org.webdav.xml.WebdavLockRequest       request
     * @param   &org.webdav.xml.WebdavScriptletResponse response
     * @throws  org.webdav.OperationNotAllowedException
     * @throws  org.webdav.OperationFailedException
     */
    public function lock($request, $response) {
      preg_match_all('/<[^>]*> \(<([^>]*)>\)/', $request->getHeader('If'), $ifmatches);
      try {
        $lock= $this->setLockInfo($request->getProperties(), $ifmatches[1]);
      } catch (::Exception $e) {
        throw($e);
      }
      $response->addLock($lock);
    }
    
    /**
     * Unlock a File
     *
     * @param   &org.webdav.xml.WebdavLockRequest       request
     * @param   &org.webdav.xml.WebdavScriptletResponse response
     * @throws  org.webdav.OperationNotAllowedException
     */
    public function unlock($request, $response) {
      try {
        
        // Remove < and > from beginning and end of the header 
        // e.g. <opaquelocktoken:88516110-6110-1851-bbde-48de5b3f07f4> => opaquelocktoken:88516110-6110-1851-bbde-48de5b3f07f4
        $reqToken= substr($request->getHeader('Lock-Token'), 1, -1);
        
        // return an exception if an unlock is requested on a non-locked file
        if (($lock= $this->getLockInfo($request->getPath())) == NULL) throw(new org::webdav::OperationFailedException('No Lock for File: '.$request->getPath()));
        
        if ($reqToken != $lock->getLockToken())
          throw(new org::webdav::OperationNotAllowedException('Cant unlock '.$request->getPath()));
       
      } catch (::Exception $e) {
        throw($e);
      } 
      
      $this->propStorage->removeLock($request->getPath());

      if ($lock->getLockToken()) $response->setHeader('Lock-Token', $request->getHeader('Lock-Token'));
    }
    
    /**
     * Retrieve lock information
     *
     * @param   string uri  The URI
     * @return  &org.webdav.WebdavLock
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
     * @param   &org.webdav.WebdavLock lock   The lock object
     * @param   string[]               tokens Optional lock-tokens to overwrite lock
     * @return  &org.webdav.WebdavLock
     * @throws  org.webdav.OperationNotAllowedException
     */
    public function setLockInfo($lock, $tokens= array()) { 
      $lockinfo= $this->getLockInfo($lock->getURI());

      // There's already lock
      if ($lockinfo !== NULL) {        
        // We have s/some lock token, so check if we can overwrite the lock
        if (sizeof($tokens)) {
          if (!in_array($lockinfo->getLockToken(), $tokens)) {
            throw(new org::webdav::OperationNotAllowedException('Can not refresh lock on '.$lock->getURI().' with owner '.$lock->getOwner()));
          }
        } else {
          if ($lock->getLockToken() != $lockinfo->getLockToken()) {
            throw(new org::webdav::OperationNotAllowedException('LOCK failed - invalid Token given '.$lock->getLockToken()));
          }
        }
      }
      
      $newOwner= $lock->getOwner();      
      // We can't set a lock where owner is empty
      if (empty($newOwner)) {
        throw(new org::webdav::OperationNotAllowedException('Can not set lock with empty owner'));
      }
      
      // Check token
      if (empty($token)) $t= new org::webdav::util::OpaqueLockTocken(::create());

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

      try { 
        $props= array();
 
        // Set versions as properties
        with ($p= new ('version', new org::webdav::version::WebdavVersionsContainer($version))); {
          $p->setNameSpaceName('DAV:');
          $p->setNameSpacePrefix('D:');
          $props[$p->getNameSpacePrefix().$p->getName()]= $p;
        }
 
        // Set checked-in property
        with ($p= new ('checked-in', '1.0')); {
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
        
      } catch (::Exception $e) {
        throw($e);
      }

      return TRUE;
    }
    
    /**
     * Report version status
     *
     * @param   &org.webdav.xml.WebdavLockRequest
     * @param   &org.webdav.xml.WebdavScriptletResponse
     * @return  bool success
     */
    public function report($request, $response) {
    
      try {
        $prop= $this->propStorage->getProperty(
          $request->getPath(),
          'D:version'
        );
        
      } catch (io::IOException $e) {
        throw($e);
      }
      
      $response->addWebdavVersionContainer($prop->value);
      return TRUE;
    }
    
  }
?>
