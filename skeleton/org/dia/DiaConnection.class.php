<?php
/*
 *
 * $Id:$
 */
  uses(
    'org.dia.DiaElement'
  );

  /**
   * Represents a 'dia:connection' node
   */
  class DiaConnection extends DiaElement {

    var
      $node_name= 'dia:connection',
      $value= array();

    /**
     * Set the value of this 'dia:connection' node
     *
     * <code>
     *  $connection= "$handle, $to, $connpoint"
     * </code>
     * where:
     * - $handle=0|1 : start|end of connection
     * - $to=<OBJECT_ID> : '@id' attribute of an 'dia:object' node 
     * - $connpoint=<INT> : connectionpoint number of the referenced object
     * The connectionpoint number starts at 0 in the top-left corner
     *
     * @access  protected
     * @param   string connection
     * @return  bool
     */
    function setValue($connpoint) {
      // must be comma separated string with 3 elements
      $vals= explode(',', $connpoint);
      if (sizeof($vals) !== 3) return FALSE;

      $this->value= $vals;
      return TRUE;
    }

    /**
     *
     *
     * @return  &xml.Node
     */
    function &getNode() {
      $Node= &parent::getNode();
      if (isset($this->value)) {
        $Node->setAttribute('handle', trim($this->value[0]));
        $Node->setAttribute('to', trim($this->value[1]));
        $Node->setAttribute('connection', trim($this->value[2]));
      }
      return $Node;
    }
  }
?>
