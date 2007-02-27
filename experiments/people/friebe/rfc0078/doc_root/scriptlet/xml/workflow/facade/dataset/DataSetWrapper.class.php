<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.Wrapper', 
    'scriptlet.xml.workflow.IFormresultAggregate',
    'scriptlet.xml.workflow.facade.dataset.FieldTypeMap'
  );

  /**
   * Wrapper implementation for DataSets.
   *
   * @see      xp://rdbms.DataSet
   * @see      xp://scriptlet.xml.workflow.facade.DataSetFacade
   * @purpose  Generic Wrapper
   */
  class DataSetWrapper extends Wrapper implements IFormresultAggregate {
    protected
      $peer   = NULL;

    /**
     * Constructor
     *
     * @param   rdbms.Peer peer
     */
    public function __construct($peer) {
      $this->setPeer($peer);
    }
  
    /**
     * Set Peer.
     *
     * @param   rdbms.Peer peer
     */
    public function setPeer($peer) {
      $this->peer= $peer;
      
      // Register param info by looking at the peer instance's types
      // Omit identity fields, they should not be editable
      foreach ($peer->types as $name => $defines) {
        if ($name == $peer->identity) continue;

        $this->registerParamInfo(
          $name,
          $defines[2] ? OCCURRENCE_OPTIONAL : OCCURRENCE_UNDEFINED,
          $default= NULL,
          $this->casterFor($defines[1]),
          $precheck= NULL, 
          $postcheck= NULL,
          FieldTypeMap::typeOf($defines[1]),
          $values= array()
        );
      }
    }
    
    /**
     * Retrieve the caster for a given field type
     *
     * @param   int fieldType
     * @return  array caster or NULL if none should be used
     */
    protected function casterFor($fieldType) {
      return FieldTypeMap::casterFor($fieldType);
    }

    /**
     * Get Peer
     *
     * @return  rdbms.Peer
     */
    public function getPeer() {
      return $this->peer;
    }
  }
?>
