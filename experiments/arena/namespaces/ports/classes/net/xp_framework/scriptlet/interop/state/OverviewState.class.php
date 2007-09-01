<?php
/* This class is part of the XP framework
 *
 * $Id: OverviewState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::scriptlet::interop::state;

  ::uses(
    'scriptlet.xml.workflow.AbstractState',
    'io.File',
    'io.FileUtil'
  );

  /**
   * Handles /xml/overview
   *
   * @purpose  Overview state
   */
  class OverviewState extends scriptlet::xml::workflow::AbstractState {

    /**
     * Process this state
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {

      try {

        // Open log xml
        $f= new io::File($request->getEnvValue('DOCUMENT_ROOT').'/../log/servicetests.xml');
        if (!(
          $f->exists() &&
          $tree= xml::Tree::fromString(io::FileUtil::getContents($f))
        )) {
          
          $response->addFormError($this->getClassName(), 'not-available', 'servicetests');
          return;
        }
      } catch ( $e) {
        $response->addFormError($this->getClassName(),'not-well-formed', 'servicetests');
        return;
      }
      
      // Append result overview to the result tree
      $response->addFormResult($tree->root);
    }
  }
?>
