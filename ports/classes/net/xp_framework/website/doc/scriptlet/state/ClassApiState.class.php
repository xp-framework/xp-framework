<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('scriptlet.xml.workflow.AbstractState', 'xml.Tree', 'io.File', 'io.FileUtil');

  /**
   * Handles /xml/api/class
   *
   * @purpose  State
   */
  class ClassApiState extends AbstractState {

    /**
     * Process this state.
     *
     * @param   scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      sscanf($request->getQueryString(), '%[a-zA-Z_.]', $classname);
      $f= new File($request->getEnvValue('DOCUMENT_ROOT').'/../build/'.$classname.'.dat');
      $response->addFormResult(unserialize(FileUtil::getContents($f))->root);
    }
  }
?>
