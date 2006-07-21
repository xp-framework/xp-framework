<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
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
  class DetailsState extends AbstractState {

    /**
     * Process this state
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    public function process(&$request, &$response) {

      $service= $request->getParam('service', NULL);
      $method=  $request->getParam('method',  NULL);
      $type=    $request->getParam('type',    NULL);
      
      if (!$service || !$method || !$type) {
        $this->addFormError($this->getClassName(), 'missing-parameter');
        return;
      }

      try {

        // Open logfile
        $f= &new File(sprintf('%s/%s.%s',
          $request->getEnvValue('DOCUMENT_ROOT').'/../log/'.basename($service),
          basename(strtolower($method)),
          basename(strtolower($type))
        ));
        
        if (!$f->exists()) {
          $response->addFormError($this->getClassName(), 'not-available', 'details');
          return;
        }
        
        $contents= &new CData(FileUtil::getContents($f));
      } catch (XMLFormatException $e) {
        $response->addFormError($this->getClassName(),'not-well-formed', 'details');
        return;
      }
      
      // Append result overview to the result tree
      $n= &$response->addFormResult(new Node(
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
