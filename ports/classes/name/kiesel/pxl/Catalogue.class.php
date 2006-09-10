<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'xml.meta.Unmarshaller',
    'xml.meta.Marshaller',
    'name.kiesel.pxl.CatalogueEntry',
    'lang.Collection'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Catalogue extends Object {
    var
      $entries=   array();
      
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function __construct() {
      $this->entries= &Collection::forClass('name.kiesel.pxl.CatalogueEntry');
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function setStorage(&$storage) {
      $this->storage= &$storage;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &create(&$storage) {
      $data= $storage->load('catalogue');
      if (!$data) return NULL;
      
      $c= &Unmarshaller::unmarshal($data, 'name.kiesel.pxl.Catalogue');
      $c && $c->setStorage($storage);
      return $c;
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function hibernate() {
      return $this->storage->save('catalogue', Marshaller::marshal($this));    
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */    
    #[@xmlmapping(element= 'entry', class= 'name.kiesel.pxl.CatalogueEntry')]
    function addEntry($entry) {
      $this->entries->add($entry);
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    #[@xmlfactory(element= 'entry', class= 'name.kiesel.pxl.CatalogueEntry')]
    function getEntries() {
      return $this->entries;
    }    
  }
?>
