<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'io.File',
    'io.FileUtil'
  );

  /**
   * Handles /xml/overview
   *
   * @purpose  Overview state
   */
  class OverviewState extends AbstractState {

    /**
     * Process this state
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {

      try {

        // Open log xml
        $f= new File($request->getEnvValue('DOCUMENT_ROOT').'/../log/servicetests.xml');
        if (!(
          $f->exists() &&
          $tree= Tree::fromString(FileUtil::getContents($f))
        )) {
          
          $response->addFormError($this->getClassName(), 'not-available', 'servicetests');
          return;
        }
      } catch (XMLFormatException $e) {
        $response->addFormError($this->getClassName(),'not-well-formed', 'servicetests');
        return;
      }
      
      // Append result overview to the result tree
      $response->addFormResult($tree->root);
    }
  }
?>
