<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'io.FileUtil',
    'io.File',
    'lang.apidoc.FileComment',
    'lang.apidoc.ClassComment',
    'lang.apidoc.FunctionComment'
  );

  /**
   * Handles /xml/documentation/class
   *
   * @purpose  State
   */
  class ClassDocumentationState extends AbstractState {

    /**
     * Process this state.
     *
     * @access  public
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    function process(&$request, &$response) {
      if (2 != sscanf($request->getData(), '%[^/]/%s', $collection, $fqcn)) {
        $response->addFormError('illegalaccess');
        return;
      }
      
      // Split fully qualified name into package and class name
      $p= strrpos($fqcn, '.');
      $package= substr($fqcn, 0, $p);
      $classname= substr($fqcn, $p+ 1);
      
      // Add "breadcrumb" navigation to formresult
      with ($n= &$response->addFormResult(new Node('breadcrumb'))); {
        $n->addChild(new Node('current', NULL, array(
          'collection' => $collection,
          'package'    => $package,
          'class'      => $classname
        )));
        $path= ''; $t= strtok($package, '.'); do {
          $path.= $t.'.';
          $n->addChild(new Node('path', $t, array('qualified' => substr($path, 0, -1))));
        } while ($t= strtok('.'));
      }
      
      // Read cached api docs
      $stor= &new File('../build/cache/class.php/'.$fqcn);
      try(); {
        $apidoc= unserialize(FileUtil::getContents($stor));
      } if (catch('IOException', $e)) {
        $response->addFormError('corrupt', $e->getMessage());
        return;
      }

      $response->addFormResult(Node::fromArray($apidoc, 'apidoc'));
    }
  }
?>
