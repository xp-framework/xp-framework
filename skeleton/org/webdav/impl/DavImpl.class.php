<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'lang.MethodNotImplementedException',
    'org.webdav.OperationFailedException',
    'org.webdav.OperationNotAllowedException',
    'org.webdav.WebdavObject'
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
     * Move a file
     *
     * @access  abstract
     * @param   string filename
     * @param   string destination
     * @param   bool overwrite
     * @return  bool created
     * @throws  MethodNotImplementedException
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
     * @throws  MethodNotImplementedException
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
     * @throws  MethodNotImplementedException
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
     * @throws  MethodNotImplementedException
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
     * @throws  MethodNotImplementedException
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
     * @throws  MethodNotImplementedException
     */
    function &get($filename) {
      return throw(new MethodNotImplementedException($this->getName().'::get not implemented'));
    }

    /**
     * Find properties
     *
     * @access  abstract
     * @param   &org.webdav.xml.WebdavPropFindRequest request
     * @param   &org.webdav.xml.WebdavPropFindResponse response
     * @return  &org.webdav.xml.WebdavPropFindResponse response
     * @throws  MethodNotImplementedException
     */
    function &propfind(&$request, &$response) { 
      return throw(new MethodNotImplementedException($this->getName().'::propfind not implemented'));
    }
  
  }
?>
