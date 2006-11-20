<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'xml.Tree',
    'scriptlet.HttpScriptletResponse'
  );

  /**
   * Webdav scriptlet request
   *
   * @purpose  Request object
   */
  class WebdavScriptletResponse extends HttpScriptletResponse {
  
    var
      $tree=   NULL;
    
    /**
     * Sets the root node for the response
     *
     * @access public
     * @param  xml.Node node The node
     */
    function setRootNode(&$node) {
      if ($this->tree === NULL) {
        $this->tree= new Tree();
        $this->tree->setEncoding('UTF-8');
      }
      $this->tree->root= &$node;
    }

    /**
     * Encode string in the right encoding (currently UTF-8 is used)
     *
     * @access public
     * @param  string string The string which should be encoded
     * @return string
     */    
    function encode($string) {
      return utf8_encode($string);
    }
    
    /**
     * Adds a child to the root node of the tree
     *
     * @access protected
     * @param  &xml.Node node The node
     * @return &xml.Node
     */
    function &addChild(&$node) {
      return $this->tree->addChild($node);
    }
    
    /**
     * Process the response (setting status code, adding XML data to
     * response body, ...)
     *
     * @access public
     */
    function process() {
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
     * @access  private
     * @param   string path The path
     * @return  string
     * @see org.webdav.WebdavScriptletRequest#decodePath
     */
    function encodePath($path) {
      $parts = explode('/', $path);
      for ($i = 0; $i < sizeof($parts); $i++) $parts[$i]= rawurlencode($parts[$i]);
      return implode('/', $parts);
    }
    
  }
?>
