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
    function _normalizePath($path){      
      $p= preg_replace('#//#','/', $path);
      $p= preg_replace('#/\./#','/', $p);
      $p= preg_replace('#[^/]+/\.\./#','', $p);
      $p= preg_replace('#//#','/', $p);
      $p= preg_replace('#/$#','', $p);
      return $p;
    }

    /**
     * Encode the parts of a path 
     *
     * Example:
     * <pre>
     *   "/Test Folder/file.txt" -> "/Text%20File/file.txt"
     * </pre>
     *
     * The optional parameter davDisplaynameStyle indicates that the path 
     * should be transformed for display.
     *
     * @access  private
     * @param   string path The path
     * @param   bool davDisplaynameStyle default FALSE
     * @return  string
     */
    function _urlencodePath($path, $davDisplaynameStyle= FALSE){
      $p= explode('\/', $this->_normalizePath($path));
      $ret= '';
      for ($t= 0; $t < sizeof($p); $t++){
        if (!empty($p[$t])) $ret.= '/'.rawurlencode($p[$t]);
      }

      return substr($ret, (int)$davDisplaynameStyle);
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
  }
?>
