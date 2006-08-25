<?php
/*
 *
 * $Id:$
 */

  uses(
    'lang.IllegalArgumentException',
    'org.dia.DiaCompound',
    'org.dia.DiaConnection'
  );

  /**
   * Represents a 'dia:connections' node
   */
  class DiaConnections extends DiaCompound {

    var
      $node_name= 'dia:connections';

    /**
     *
     * <code>
     *   $connection= array(
     *     0 => array('id' => $id, 'conn' => $conn), // start of connection
     *     1 => array('id' => $id, 'conn' => $conn)  // end of connection
     *   )
     * </code>
     * $id= object id attribute
     * $conn= object connectionpoint number, starting: top-left
     *
     * @param   array connection
     * @throws  lang.IllegalArgumentException
     */
    function __construct($connection= NULL) {
      if (!isset($connection) or empty($connection)) return;

      // $connection must be an array with 2 arrays in it
      if (
        !is_array($connection) or
        (!empty($connection) and sizeof($connection) !== 2) or
        (!is_array($connection[0]) or !is_array($connection[1]))
      ) return throw(new IllegalArgumentException(
        'Given argument does not match specification: '.
        xp::stringOf($connection)
      ));

      // new DiaConnection($handle, $to, $conn)
      // handle=0: start, handle=1: end of line
      // to='01' -> object ID
      // connection -> connection point of object (start: topleft)
      // i.e.: from obj='00' topleft to obj='00' next connection point:

      // add child objects
      for ($i= 0; $i<1; $i++) {
        $this->add(new DiaConnection($i, $connection[$i]['id'], $connection[$i]['conn']));
      }
    }
  }
?>
