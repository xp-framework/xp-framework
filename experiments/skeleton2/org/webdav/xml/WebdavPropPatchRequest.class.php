<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('xml.Tree');

  /**
   * PropPatch request XML
   *
   * PROPPATCH xml
   * <pre>
   *   <?xml version="1.0" encoding="utf-8" ?>
   *   <D:propertyupdate xmlns:D="DAV:">
   *     <D:set>
   *       <D:prop>
   *         <key xmlns="http://webdav.org/cadaver/custom-properties/">value</key>
   *       </D:prop>
   *     </D:set>
   *   </D:propertyupdate>
   * </pre>
   *
   *
   * @purpose  Encapsulate PROPPATCH XML request
   * @see      xp://org.webdav.WebdavScriptlet#doPropPatch
   */
  class WebdavPropPatchRequest extends Tree {
    public
      $properties= array(),
      $baseurl=    '';
    
    /**
     * Constructor
     *
     * @access  public
     * @param   &org.apache.HttpScriptletRequest request
     * @throws  Exception to indicate failure
     */
    public function __construct(&$request) {
      if (FALSE === self::fromString($request->getData())) {
        return FALSE;
      }
      $this->filename= $request->uri['path_translated'];
      self::setEncoding('utf-8');
      parent::__construct();
    }
    
    /**
     * Retrieve base url of request
     *
     * @access  public
     * @return  string
     */
    public function getFilename() {
      return $this->filename;
    }
    
    /**
     * Add a property
     *
     * @access  public
     * @param   string name
     */
    public function addProperty($name, $value) {
      $this->properties[$name]= $value;
    }
    
    /**
     * Get all properties
     *
     * @access  public
     * @return  &string[] properties
     */
    public function getProperties() {
      return $this->properties;
    }

    /**
     * Returns an XPath expression for the current entry
     *
     * @access  private
     * @return  string path, e.g. /rdf:rdf/item/rc:summary/
     */
    private function _pathname() {
      $path= '';
      for ($i= $this->_cnt; $i> 0; $i--) {
        if (FALSE !== ($p= strpos($name= strtolower($this->_objs[$i]->name), ':'))) {
          $name= substr($name, $p+ 1);
        }
        $path= $name.'/'.$path;
      }
      return '/'.$path;
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
    private function _pCallEndElement($parser, $name) {
      static $trans;
      
      $path= self::_pathname();
      parent::_pCallEndElement($parser, $name);
      if ($this->_cnt <= 0) return;

      // Selective properties
      if (25 < strlen($path) && '/propertyupdate/set/prop/' == substr($path, 0, 25)) {
        if (!isset($trans)) $trans= array_flip(get_html_translation_table(HTML_ENTITIES));
        
        self::addProperty($name, utf8_decode(preg_replace(
          '/&#([0-9]+);/me', 
          'chr("\1")', 
          strtr(trim($this->_objs[$this->_cnt+ 1]->content), $trans)
        )));
        return;
      }
    }
  }
?>
