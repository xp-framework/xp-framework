<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'util.PropertyManager',
    'name.kiesel.pxl.Catalogue',
    'name.kiesel.pxl.Page',
    'name.kiesel.pxl.Picture',
    'name.kiesel.pxl.storage.FilesystemContainer'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class AbstractPxlState extends AbstractState {
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _getDataPath($path) {
      $path= $_SERVER['DOCUMENT_ROOT'].'/pages/'.$path;
      return realpath($path);
    }    
  
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _getCatalogue() {
      if (NULL === $this->catalogue) {
        $this->catalogue= &Catalogue::create(new FilesystemContainer($this->_getDataPath()));
      }
      
      return $this->catalogue;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function _getPage($path) {
      $page= &Page::create(new FilesystemContainer($this->_getDataPath($path)));
      return $page;
    }
  }
?>
