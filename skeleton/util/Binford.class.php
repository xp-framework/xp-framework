<?php
/*
 * $Id$
 *
 * Diese Klasse ist Bestandteil des XP-Frameworks
 * (c) 2001 Timm Friebe, Schlund+Partner AG
 *
 * @see http://doku.elite.schlund.de/projekte/xp/skeleton/
 *
 */

  /**
   * Hörrhörr, diese Klasse läuft mit Benzin!
   *
   * @purpose Die Klasse für mehr Power
   * @see     http://www.binford.de/
   */
  class Binford extends Object { 
    var $poweredBy= 6100;
    
    /**
     * Constructor
     */
   function __construct($params= NULL) {
      Object::__construct($params);  
    }
    
    /**
     * Die Power setzen
     *
     * @access  public
     * @param   int p Power
     * @throws  IllegalArgumentException, wenn p einen unzulässigen Wert enthält
     */
    function setPoweredBy($p) {
      if (!($x= log10($p / 6.1)) || (floor($x) != $x)) {
        return throw(E_ILLEGAL_ARGUMENT_EXCEPTION, $p.' not allowed');
      }
      $this->poweredBy= $p;
    }
   
    /**
     * Power zurückgeben
     *
     * @access  public
     * @return  int Power
     */
    function getPoweredBy() {
      return $this->poweredBy;
    }
    
    /**
     * Passenden X-Header zurückgeben
     *
     * @access  public
     * @return  string Header
     */
    function getHeader() {
      return 'X-Binford: '.$this->poweredBy.' (more power)';
    }
  }
?>
