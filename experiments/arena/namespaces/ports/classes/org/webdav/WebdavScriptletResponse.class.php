<?php
/* This class is part of the XP framework
 *
 * $Id: WebdavScriptletResponse.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace org::webdav;
 
  ::uses(
    'xml.Tree',
    'scriptlet.HttpScriptletResponse'
  );

  /**
   * Webdav scriptlet request
   *
   * @purpose  Request object
   */
  class WebdavScriptletResponse extends scriptlet::HttpScriptletResponse {
  
    public
      $tree=   NULL;
    
    /**
     * Sets the root node for the response
     *
     * @param  xml.Node node The node
     */
    public function setRootNode($node) {
      if ($this->tree === NULL) {
        $this->tree= new xml::Tree();
        $this->tree->setEncoding('UTF-8');
      }
      $this->tree->root= $node;
    }

    /**
     * Encode string in the right encoding (currently UTF-8 is used)
     *
     * @param  string string The string which should be encoded
     * @return string
     */    
    public function encode($string) {
      return utf8_encode($string);
    }
    
    /**
     * Adds a child to the root node of the tree
     *
     * @param  &xml.Node node The node
     * @return &xml.Node
     */
    public function addChild($node) {
      return $this->tree->addChild($node);
    }
    
    /**
     * Process the response (setting status code, adding XML data to
     * response body, ...)
     *
     */
    public function process() {
      parent::process();
      
      if ($this->tree !== NULL) {
        $this->setHeader('Content-Type', 'text/xml; charset="'.$this->tree->getEncoding().'"');
        $this->setContent($body= $this->tree->getDeclaration()."\n".$this->tree->getSource(0));
        $this->setHeader('Content-length', strlen($body));
      }
    }
  
    /**
     * Encode the parts of a path 
     *
     * Example:
     * <pre>
     *   "/Test Folder/file.txt" -> "/Test%20Folder/file.txt"
     * </pre>
     *
     * @param   string path The path
     * @return  string
     * @see org.webdav.WebdavScriptletRequest#decodePath
     */
    public function encodePath($path) {
      $parts = explode('/', $path);
      for ($i = 0; $i < sizeof($parts); $i++) $parts[$i]= rawurlencode($parts[$i]);
      return implode('/', $parts);
    }
    
  }
?>
