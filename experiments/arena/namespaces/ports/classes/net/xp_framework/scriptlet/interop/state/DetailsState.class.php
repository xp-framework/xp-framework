<?php
/* This class is part of the XP framework
 *
 * $Id: DetailsState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::scriptlet::interop::state;

  ::uses(
    'scriptlet.xml.workflow.AbstractState',
    'io.File',
    'io.FileUtil',
    'xml.CData'
  );

  /**
   * Handles /xml/overview
   *
   * @purpose  Overview state
   */
  class DetailsState extends scriptlet::xml::workflow::AbstractState {

    /**
     * Process this state
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {

      $service= $request->getParam('service', NULL);
      $method=  $request->getParam('method',  NULL);
      $type=    $request->getParam('type',    NULL);
      
      if (!$service || !$method || !$type) {
        $this->addFormError($this->getClassName(), 'missing-parameter');
        return;
      }

      try {

        // Open logfile
        $f= new io::File(sprintf('%s/%s.%s',
          $request->getEnvValue('DOCUMENT_ROOT').'/../log/'.basename($service),
          basename(strtolower($method)),
          basename(strtolower($type))
        ));
        
        if (!$f->exists()) {
          $response->addFormError($this->getClassName(), 'not-available', 'details');
          return;
        }
        
        $contents= new xml::CData(io::FileUtil::getContents($f));
      } catch ( $e) {
        $response->addFormError($this->getClassName(),'not-well-formed', 'details');
        return;
      }
      
      // Append result overview to the result tree
      $n= $response->addFormResult(new (
        'detail', 
        $contents, 
        array(
        'service' => $service,
        'method'  => $method,
        'type'    => $type
      )));
    }
  }
?>
