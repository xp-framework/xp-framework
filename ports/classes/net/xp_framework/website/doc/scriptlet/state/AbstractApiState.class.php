<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.xml.workflow.AbstractState', 'util.PropertyManager', 'xml.Tree', 'io.File', 'io.FileUtil');

  /**
   * Abstract base class for all classes reading 
   *
   * @purpose  State
   */
  class AbstractApiState extends AbstractState {

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      sscanf($request->getQueryString(), '%[a-zA-Z_.]', $classname);
      $f= new File(PropertyManager::getInstance()->getProperties('storage')->readString('storage', 'base').DIRECTORY_SEPARATOR.$classname.'.dat');
      $response->addFormResult(cast(unserialize(FileUtil::getContents($f)), 'xml.Tree')->root);
    }
  }
?>
