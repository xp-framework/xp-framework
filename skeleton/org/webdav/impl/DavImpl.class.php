<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'lang.MethodNotImplementedException',
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
     * Delete a file
     *
     * @access  abstract
     * @param   string filename
     * @param   &string data
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
