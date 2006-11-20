<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'scriptlet.xml.workflow.AbstractState',
    'net.xp_framework.util.markup.FormresultHelper',
    'io.FileUtil',
    'io.File',
    'text.apidoc.FileComment',
    'text.apidoc.ClassComment',
    'text.apidoc.FunctionComment'
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

        $path= '';         
        foreach (explode('.', $package) as $token) {
          $path.= $token.'.';
          $n->addChild(new Node('path', $token, array('qualified' => substr($path, 0, -1))));
        }
      }
      
      // Read cached api docs
      $stor= &new File('../build/cache/'.$collection.'/class/'.$fqcn);
      try(); {
        $apidoc= unserialize(FileUtil::getContents($stor));
      } if (catch('IOException', $e)) {
        $response->addFormError('corrupt', $e->getMessage());
        return throw($e);
        return;
      }

      // Add to formresult
      with (
        $n= &$response->addFormResult(new Node('apidoc')),
        $comments= &$n->addChild(new Node('comments'))
      ); {
      
        // File comments
        $comments->addChild(new Node(
          'file', 
          $apidoc['comments']['file']->text, 
          array('cvs' => $apidoc['comments']['file']->cvsver)
        ));
        
        // Class comments
        $class= &$comments->addChild(new Node('class', NULL, array(
          'name'            => $apidoc['comments']['class']->name,
          'extends'         => $apidoc['comments']['class']->extends,
          'model'           => $apidoc['comments']['class']->model,
          'deprecated'      => $apidoc['comments']['class']->deprecated,
          'experimental'    => $apidoc['comments']['class']->experimental
        )));
        $class->addChild(new Node('purpose', $apidoc['comments']['class']->purpose));
        $class->addChild(Node::fromArray(
          (array)$apidoc['comments']['class']->references, 
          'references'
        ));
        $class->addChild(Node::fromArray(
          (array)$apidoc['comments']['class']->extensions, 
          'extensions'
        ));
        $class->addChild(FormresultHelper::markupNodeFor(
          'text', 
          $apidoc['comments']['class']->text
        ));
        
        // Method comments
        foreach ($apidoc['comments']['function'] as $name => $comment) {
          $method= &$comments->addChild(new Node('method', NULL, array(
            'name'            => $name,
            'access'          => $comment->access,
            'model'           => $comment->model
          )));
          $method->addChild(Node::fromObject($comment->return, 'return'));
          $method->addChild(Node::fromArray((array)$comment->params, 'params'));
          $method->addChild(Node::fromArray((array)$comment->throws, 'throws'));
          $method->addChild(Node::fromArray((array)$comment->references, 'references'));
          $method->addChild(FormresultHelper::markupNodeFor(
            'text', 
            $comment->text
          ));
        }
      }
    }
  }
?>
