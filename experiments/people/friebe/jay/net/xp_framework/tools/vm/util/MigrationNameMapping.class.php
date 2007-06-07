<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('net.xp_framework.tools.vm.util.NameMapping');

  /**
   * Maps names and types plus supports PHP builtin classes
   *
   * @see      xp://net.xp_framework.tools.vm.util.NameMapping
   * @purpose  Utility
   */
  class MigrationNameMapping extends NameMapping {

    /**
     * Constructor. Registers all builtin classes in mapping.
     *
     */
    function __construct() {
      foreach (array_merge(get_declared_classes(), get_declared_interfaces()) as $name) {
        $r= new ReflectionClass($name);
        if ($r->isInternal()) $this->mapping[strtolower($name)]= 'php.'.$name;
      }
    }
  }
?>
