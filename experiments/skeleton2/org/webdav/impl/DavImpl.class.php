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
    'org.webdav.util.WebdavBool'
  );

  define('WEBDAV_IMPL_PROPFIND',    0x0001);
  define('WEBDAV_IMPL_PROPPATCH',   0x0002);

  /**
   * Base class of DAV implementation
   *
   * @purpose  Provide an abstract base class for DAV implementations
   * @see      org.webdav.WebdavScriptlet#__construct
   */ 
  class DavImpl extends Object {
    public
      $capabilities = 0;
      
    /**
     * Retrieve implementation's capabilites
     *
     * @access  public
     * @return  int capabilities
     */
    public function getCapabilities() {
      return $this->capabilities;
    }

    /**
     * Move a file
     *
     * @access  abstract
     * @param   string filename
     * @param   string destination
     * @param   bool overwrite
     * @return  bool created
     * @throws  MethodNotImplementedException
     */
    public function move($filename, $destination, $overwrite) {
      throw (new MethodNotImplementedException(self::getName().'::move not implemented'));
    }

    /**
     * Copy a file
     *
     * @access  abstract
     * @param   string filename
     * @param   string destination
     * @param   bool overwrite
     * @return  bool created
     * @throws  MethodNotImplementedException
     */
    public function copy($filename, $destination, $overwrite) {
      throw (new MethodNotImplementedException(self::getName().'::copy not implemented'));
    }

    /**
     * Make a directory
     *
     * @access  abstract
     * @param   string colname
     * @return  bool success
     * @throws  MethodNotImplementedException
     */
    public function mkcol($colname) {
      throw (new MethodNotImplementedException(self::getName().'::mkcol not implemented'));
    }

    /**
     * Delete a file
     *
     * @access  abstract
     * @param   string filename
     * @return  bool success
     * @throws  MethodNotImplementedException
     */
    public function delete($filename) {
      throw (new MethodNotImplementedException(self::getName().'::delete not implemented'));
    }

    /**
     * Put a file
     *
     * @access  abstract
     * @param   string filename
     * @param   &string data
     * @return  bool new
     * @throws  MethodNotImplementedException
     */
    public function put($filename, $data) {
      throw (new MethodNotImplementedException(self::getName().'::put not implemented'));
    }
    
    /**
     * Get a file
     *
     * @access  abstract
     * @param   string filename
     * @return  string &org.webdav.WebdavObject
     * @throws  MethodNotImplementedException
     */
    public function get($filename) {
      throw (new MethodNotImplementedException(self::getName().'::get not implemented'));
    }

    /**
     * Find properties
     *
     * @access  abstract
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.xml.WebdavMultistatus response
     * @return  &org.webdav.xml.WebdavMultistatus response
     * @throws  MethodNotImplementedException
     */
    public function propfind(WebdavPropFindRequest $request, WebdavMultistatus $response) { 
      throw (new MethodNotImplementedException(self::getName().'::propfind not implemented'));
    }

    /**
     * Patch properties
     *
     * @access  abstract
     * @param   &org.webdav.xml.WebdavPropPatcRequest request
     * @throws  MethodNotImplementedException
     */
    public function proppatch(WebdavPropPatcRequest $request) { 
      throw (new MethodNotImplementedException(self::getName().'::proppatch not implemented'));
    }
  
  }
?>
