<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('lang.ElementNotFoundException');

  /**
   * Store OIDs for "remoted" objects 
   *
   * @purpose  OID store
   */
  class RemoteObjectMap extends Object {
    const
      CTX_KEY   = "RemoteObjectMap";

    protected
      $map      = array(),
      $oid      = array();
    
    /**
     * Retrieve oid for a given object. Reserves a new
     * unique oid for the object if it is not yet associated
     * with one. Returns the associated oid otherwise.
     *
     * @param   lang.Object object
     * @return  int
     */
    public function oidFor(Generic $object) {
      static $oid= 1;
      
      if (!isset($this->map[$object->hashCode()])) {
        
        // Reserve new oid
        $xoid= $oid++;
        
        // Register object
        $this->map[$object->hashCode()]= $xoid;
        $this->oid[$xoid]= $object;
      }
      
      return $this->map[$object->hashCode()];
    }
    
    /**
     * Retrieve object by oid.
     *
     * @param   int oid
     * @return  lang.Object
     * @throws  lang.ElementNotFoundException if oid is not known
     */
    public function getByOid($oid) {
      if (!isset($this->oid[$oid])) throw new ElementNotFoundException(
        'No object registered for oid '.$oid
      );
      
      return $this->oid[$oid];
    }
  }
?>
