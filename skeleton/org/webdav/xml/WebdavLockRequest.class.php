<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.Tree');

  define('WEBDAV_LOCKTYPE_READ',    'read');
  define('WEBDAV_LOCKTYPE_WRITE',   'write');
  define('WEBDAV_LOCKTYPE_UNKNOWN', 0x02);

  define('WEBDAV_LOCKSCOPE_EXCL',    'exclusive');
  define('WEBDAV_LOCKSCOPE_SHARED',  'shared');
  define('WEBDAV_LOCKSCOPE_UNKNOWN', 0x02);

  /**
   * Lock Request XML
   *
   * LOCK xml
   * <pre>
   *   <?xml version="1.0" encoding="utf-8" ?>
   *   <A:lockinfo xmlns:A="DAV:">
   *     <A:locktype>
   *       <A:write/>
   *     </A:locktype>
   *     <A:lockscope>
   *       <A:exclusive/>
   *     </A:lockscope>
   *    <A:owner>
   *        <A:href>DAV Explorer</A:href>
   *    </A:owner>
   * </A:lockinfo>
   * </pre>
   *
   * ANSWER
   * <pre>
   * </pre>
   *
   * @purpose  Encapsulate LOCK  XML request
   * @see      xp://org.webdav.WebdavScriptlet#doLock
   */
  class WebdavLockRequest extends Tree {
    var
      $properties=  array(),
      $filename=    NULL,
      $namespaces=  array();
      
    /**
     * Constructor
     *
     * @access  public
     * @param   &scriptlet.HttpScriptletRequest request
     * @throws  Exception to indicate failure
     */
    function __construct(&$request) {
      parent::__construct();
    
      $this->filename= $request->uri['path_translated'];
      $this->uri= $request->uri['path'];
     
      switch ($request->getHeader('depth')) {
        case 0: $this->depth= 0x00000000; break;
        case 'infinity': 
        default: $this->depth = 'infinity'; break;
      }

      $this->ifcondition= $request->getHeader('if');
      $this->timeout= $request->getHeader('timeout');
      
      $body= $request->getData();
      if (empty($body)){
        trigger_error('LOCK without XML', E_USER_NOTICE);
        return throw(new FormatException('LOCK without XML'));
      }
      if (FALSE === $this->fromString($request->getData())) {
        return FALSE;
      }
    }
    
    /**
     * Retreive base url of request
     *
     * @access  public
     * @return  string
     */
    function getFilename() {
      return $this->filename;
    }
    
    /**
     * register an Lock-request
     *
     * @access  public
     * @param   string name
     */
    function registerLock($owner, $lktype, $lkscope, $lktoken= NULL, $filename= NULL) {
      if (!$filename) $filename= $this->filename;
      
      $this->properties= array(
        'filename'   => $filename,
        'owner'      => $owner,
        'type'       => $lktype,
        'scope'      => $lkscope,
        'token'      => $lktoken,
        'timeout'    => $this->timeout,
        'depth'      => $this->depth
      );
    }
    
    /**
     * Get all properties
     *
     * @access  public
     * @return  &string[] properties
     */
    function &getProperties($action= NULL) {
      if ($action) return $this->properties[$action]; else return $this->properties;
    }

    /**
     * Get all namespaces
     *
     * @access  public
     * @return  &string[] properties
     */
    function &getNamespaces() {
      return $this->namespaces;
    }

    /**
     * Returns an XPath expression for the current entry
     *
     * @access  private
     * @return  string path, e.g. /rdf:rdf/item/rc:summary/
     */
    function _pathname() {
      $path= '';
      for ($i= $this->_cnt; $i> 0; $i--) {
        if (FALSE !== ($p= strpos($name= strtolower($this->_objs[$i]->name), ':'))) {
          $name= substr($name, $p+ 1);
        }
        $path= $name.'/'.$path;
      }
      return '/'.$path;
    }
    
    

    function _pCallStartElement($parser, $name, $attrs) {
      parent::_pCallStartElement($parser, $name, $attrs);
      
      $path= $this->_pathname();
      if (!empty($attrs)){
        foreach ($attrs as $name => $val){
          if ('xmlns:' == substr($name,0,6)){
            $this->namespaces[substr($name,6)]=$val;
          }
        }
      }
    }
    
    /**
     * Private callback function
     *
     * @access  private
     * @param   resource parser
     * @param   string name
     * @param   array attrs
     * @throws  FormatException in case of a parse error
     */
    function _pCallEndElement($parser, $name) {
      static $trans;
      static $lktype;
      static $lkscope;
      static $lktoken;
      static $owner;
      
      if (!isset($lktype)) $lktype= NULL;
      if (!isset($lkscope)) $lkscope= NULL;
      if (!isset($lktoken)) $lktoken= NULL;
      if (!isset($owner)) $owner= NULL;
      
      $path= $this->_pathname();
      parent::_pCallEndElement($parser, $name);

      // Selective Locktype
      if (19 < strlen($path) && '/lockinfo/locktype/' == substr($path, 0, 19)) {
        if (!isset($trans)) $trans= array_flip(get_html_translation_table(HTML_ENTITIES));
        
        switch (substr($path, 19, 1)) {
          case 'r':
            $lktype= WEBDAV_LOCKTYPE_READ;
          break;
          case 'w': // set Propertie
            $lktype= WEBDAV_LOCKTYPE_WRITE;
          break;
          default:
            $lktype= WEBDAV_LOCKTYPE_UNKNOWN;
          break;
        }
      }
      
      // selective lockscope
      if (20 < strlen($path) && '/lockinfo/lockscope/' == substr($path, 0, 20)) {
        if (!isset($trans)) $trans= array_flip(get_html_translation_table(HTML_ENTITIES));

        switch (substr($path,20,1)){
          case 'e': // READ-LOCK
            $lkscope= WEBDAV_LOCKSCOPE_EXCL;
          break;
          case 's': // set Propertie
            $lkscope= WEBDAV_LOCKSCOPE_SHARED;
          break;
          default:
            $lkscope= WEBDAV_LOCKSCOPE_UNKNOWN;
          break;
        }
      }
      
      // owner
      if (20 < strlen($path) && '/lockinfo/owner/href' == substr($path, 0, 20)) {
        $owner= utf8_decode(preg_replace('/&#([0-9]+);/me', 
          'chr("\1")', 
          strtr(trim($this->_objs[$this->_cnt+ 1]->content), $trans))
        );
      }
      
      // Locktoken
      if (20 < strlen($path) && '/lockinfo/token/href' == substr($path, 0, 20)) {
        $lktoken= utf8_decode(preg_replace('/&#([0-9]+);/me', 
          'chr("\1")', 
          strtr(trim($this->_objs[$this->_cnt+ 1]->content), $trans))
        );
      }
      
      if ($path == '/lockinfo/') { // done
        $this->registerLock($owner, $lktype, $lkscope, $lktoken);
        $owner= NULL;
        $lktype= NULL;
        $lkscope= NULL;
        $lktoken= NULL;
      }
    }
  }
?>
