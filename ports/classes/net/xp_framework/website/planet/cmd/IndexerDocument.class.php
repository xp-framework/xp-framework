<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class IndexerDocument extends Object {
    public
      $document = NULL;
    
    protected
      $operation  = 0;
    
    const
      UPDATE      = '.',
      DELETE      = 'd';
    
    public function __construct($document, $operation= self::UPDATE) {
      $this->document= $document;
      $this->operation= $operation;
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function getOperation() {
      return $this->operation;
    }    
    
    /**
     * (Insert method's description here)
     *
     * @param   
     * @return  
     */
    public function setOperation($operation) {
      $this->operation= $operation;
    }
  
  }
?>
