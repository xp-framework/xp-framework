<?php
/* This class is part of the XP framework
 *
 * $Id: ClassDocumentationState.class.php 8971 2006-12-27 15:27:10Z friebe $ 
 */

  namespace net::xp_framework::scriptlet::state;

  ::uses(
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
  class ClassDocumentationState extends scriptlet::xml::workflow::AbstractState {

    /**
     * Process this state.
     *
     * @param   &scriptlet.xml.workflow.WorkflowScriptletRequest request
     * @param   &scriptlet.xml.XMLScriptletResponse response
     */
    public function process($request, $response) {
      if (2 != sscanf($request->getData(), '%[^/]/%s', $collection, $fqcn)) {
        $response->addFormError('illegalaccess');
        return;
      }
      
      // Split fully qualified name into package and class name
      $p= strrpos($fqcn, '.');
      $package= substr($fqcn, 0, $p);
      $classname= substr($fqcn, $p+ 1);
      
      // Add "breadcrumb" navigation to formresult
      with ($n= $response->addFormResult(new ('breadcrumb'))); {
        $n->addChild(new ('current', NULL, array(
          'collection' => $collection,
          'package'    => $package,
          'class'      => $classname
        )));

        $path= '';         
        foreach (explode('.', $package) as $token) {
          $path.= $token.'.';
          $n->addChild(new ('path', $token, array('qualified' => substr($path, 0, -1))));
        }
      }
      
      // Read cached api docs
      $stor= new io::File('../build/cache/'.$collection.'/class/'.$fqcn);
      try {
        $apidoc= unserialize(io::FileUtil::getContents($stor));
      } catch (io::IOException $e) {
        $response->addFormError('corrupt', $e->getMessage());
        throw($e);
        return;
      }

      // Add to formresult
      with (
        $n= $response->addFormResult(new ('apidoc')),
        $comments= $n->addChild(new ('comments'))
      ); {
      
        // File comments
        $comments->addChild(new (
          'file', 
          $apidoc['comments']['file']->text, 
          array('cvs' => $apidoc['comments']['file']->cvsver)
        ));
        
        // Class comments
        $class= $comments->addChild(new ('class', NULL, array(
          'name'            => $apidoc['comments']['class']->name,
          'extends'         => $apidoc['comments']['class']->extends,
          'model'           => $apidoc['comments']['class']->model,
          'deprecated'      => $apidoc['comments']['class']->deprecated,
          'experimental'    => $apidoc['comments']['class']->experimental
        )));
        $class->addChild(new ('purpose', $apidoc['comments']['class']->purpose));
        $class->addChild(::fromArray(
          (array)$apidoc['comments']['class']->references, 
          'references'
        ));
        $class->addChild(::fromArray(
          (array)$apidoc['comments']['class']->extensions, 
          'extensions'
        ));
        $class->addChild(net::xp_framework::util::markup::FormresultHelper::markupNodeFor(
          'text', 
          $apidoc['comments']['class']->text
        ));
        
        // Method comments
        foreach ($apidoc['comments']['function'] as $name => $comment) {
          $method= $comments->addChild(new ('method', NULL, array(
            'name'            => $name,
            'access'          => $comment->access,
            'model'           => $comment->model
          )));
          $method->addChild(::fromObject($comment->return, 'return'));
          $method->addChild(::fromArray((array)$comment->params, 'params'));
          $method->addChild(::fromArray((array)$comment->throws, 'throws'));
          $method->addChild(::fromArray((array)$comment->references, 'references'));
          $method->addChild(net::xp_framework::util::markup::FormresultHelper::markupNodeFor(
            'text', 
            $comment->text
          ));
        }
      }
    }
  }
?>
