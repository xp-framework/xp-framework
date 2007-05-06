<?php
/* This class is part of the XP framework
 *
 * $Id: xp5.php.xsl 56677 2007-03-21 14:10:03Z rdoebele $
 */
 
  uses('rdbms.DataSet');
 
  /**
   * Class wrapper for table progress, database main
   * (Auto-generated on Sun, 06 May 2007 20:53:05 +0200 by Alex)
   *
   * @purpose  Datasource accessor
   */
  class PxlProgress extends DataSet {
    public
      $bz_id              = NULL,
      $description        = NULL;

    static function __static() { 
      with ($peer= self::getPeer()); {
        $peer->setTable('progress');
        $peer->setConnection('pxl');
        $peer->setIdentity('bz_id');
        $peer->setPrimary(array(''));
        $peer->setTypes(array(
          'bz_id'               => array('%d', FieldType::INT, TRUE),
          'description'         => array('%s', FieldType::VARCHAR, TRUE)
        ));
      }
    }  
  
    /**
     * Retrieve associated peer
     *
     * @return  rdbms.Peer
     */
    public static function getPeer() {
      return Peer::forName(__CLASS__);
    }
  
    /**
     * Retrieves bz_id
     *
     * @return  int
     */
    public function getBz_id() {
      return $this->bz_id;
    }
      
    /**
     * Sets bz_id
     *
     * @param   int bz_id
     * @return  int the previous value
     */
    public function setBz_id($bz_id) {
      return $this->_change('bz_id', $bz_id);
    }

    /**
     * Retrieves description
     *
     * @return  string
     */
    public function getDescription() {
      return $this->description;
    }
      
    /**
     * Sets description
     *
     * @param   string description
     * @return  string the previous value
     */
    public function setDescription($description) {
      return $this->_change('description', $description);
    }
  }
?>