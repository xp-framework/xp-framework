<?php
/* This class is part of the XP framework
 *
 * $Id: PackageDocumentationState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::scriptlet::state;

  ::uses(
    'scriptlet.xml.workflow.AbstractState'
  );

  /**
   * Handles /xml/documentation/package
   *
   * @purpose  State
   */
  class PackageDocumentationState extends scriptlet::xml::workflow::AbstractState {

    /**
     * Process this state.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      if (2 != sscanf($request->getData(), '%[^/]/%s', $collection, $package)) {
        $response->addFormError('illegalaccess');
        return;
      }
      
      // Add "breadcrumb" navigation to formresult
      with ($n= $response->addFormResult(new ('breadcrumb'))); {
        $n->addChild(new ('current', NULL, array(
          'collection' => $collection,
          'package'    => $package
        )));

        $path= '';         
        foreach (explode('.', $package) as $token) {
          $path.= $token.'.';
          $n->addChild(new ('path', $token, array('qualified' => substr($path, 0, -1))));
        }
      }
    }
  }
?>
