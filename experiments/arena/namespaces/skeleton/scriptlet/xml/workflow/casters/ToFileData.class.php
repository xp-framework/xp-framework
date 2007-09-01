<?php
/* This class is part of the XP framework
 *
 * $Id: ToFileData.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace scriptlet::xml::workflow::casters;

  uses(
    'scriptlet.xml.workflow.casters.ParamCaster',
    'scriptlet.xml.workflow.FileData'
  );
  
  /**
   * Casts given values to a FileData object
   *
   * @purpose  Caster
   */
  class ToFileData extends ParamCaster {
  
    /**
     * Cast a given value
     *
     * @see     xp://scriptlet.xml.workflow.casters.ParamCaster
     * @param   array value
     * @return  array value
     */
    public function castValue($value) {
      return array(new scriptlet::xml::workflow::FileData(
        $value['name'],
        $value['type'],
        $value['size'],
        $value['tmp_name']
      ));
    }
  }
?>
