<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState'
  );

  /**
   * Handles /xml/documentation/package
   *
   * @purpose  State
   */
  class PackageDocumentationState extends AbstractState {

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    public function process(&$request, &$response) {
      if (2 != sscanf($request->getData(), '%[^/]/%s', $collection, $package)) {
        $response->addFormError('illegalaccess');
        return;
      }
      
      // Add "breadcrumb" navigation to formresult
      with ($n= &$response->addFormResult(new Node('breadcrumb'))); {
        $n->addChild(new Node('current', NULL, array(
          'collection' => $collection,
          'package'    => $package
        )));

        $path= '';         
        foreach (explode('.', $package) as $token) {
          $path.= $token.'.';
          $n->addChild(new Node('path', $token, array('qualified' => substr($path, 0, -1))));
        }
      }
    }
  }
?>
