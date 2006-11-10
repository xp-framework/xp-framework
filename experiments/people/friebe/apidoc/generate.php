<?php
/* This file is part of the XP framework's experiments
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'AllClassesIterator',
    'io.Folder',
    'io.File',
    'io.FileUtil',
    'xml.Tree',
    'text.doclet.Doclet',
    'io.collections.CollectionComposite', 
    'io.collections.FileCollection', 
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.ExtensionEqualsFilter'
  );
  
  // {{{ GeneratorDoclet
  //     Specialized doclet
  class GeneratorDoclet extends Doclet {
    var
      $build= NULL;

    function tagAttribute($tags, $which, $attribute= 'text') {
      return isset($tags[$which]) ? $tags[$which]->{$attribute} : NULL;
    }
    
    function annotationNode($list) {
      $n= &new Node('annotations');
      foreach ($list as $annotation) {
        $a= &$n->addChild(new Node('annotation', NULL, array('name' => $annotation->name())));
        if (is_array($annotation->value)) {
          $a->addChild(Node::fromArray($annotation->value, 'value'));
        } else if (is_scalar($annotation->value)) {
          $a->addChild(new Node('value', $annotation->value, array('type' => gettype($annotation->value))));
        } else if (NULL === $annotation->value) {
          $a->addChild(new Node('value', NULL, array('type' => '')));
        } else {
          $a->addChild(new Node('value', xp::stringOf($annotation->value), array('type' => xp::typeOf($annotation->value))));
        }
      }
      return $n;
    }
    
    function classReferenceNode(&$classdoc) {
      $n= &new Node('link', NULL, array(
        'rel'     => 'class',
        'href'    => $classdoc->qualifiedName(),
      ));
      $this->marshalClassDoc($classdoc);
      return $n;
    }
    
    function methodsNode(&$classdoc, $inherited= FALSE) {
      $n= &new Node('methods');
      $inherited && $n->setAttribute('from', $classdoc->qualifiedName());

      foreach ($classdoc->methods as $method) {
        $m= &$n->addChild(new Node('method', NULL, array(
          'name'   => $method->name(),
          'access' => $this->tagAttribute($method->tags('access'), 0, 'text'),
          'return' => $this->tagAttribute($method->tags('return'), 0, 'type')
        )));
        
        // Apidoc
        $m->addChild(new Node('comment', $method->commentText()));
        foreach ($method->tags('see') as $ref) {
          $m->addChild(new Node('see', NULL, array('href' => $ref->text)));
        }

        // Annotations
        $m->addChild($this->annotationNode($method->annotations()));
        
        // Thrown exceptions
        foreach ($method->tags('throws') as $thrown) {
          $m->addChild(new Node('exception', $thrown->text, array(
            'class' => $thrown->exception->qualifiedName()
          )));
        }
        
        // Arguments
        $param= array();
        foreach ($method->tags('param') as $tag) {
          $param['$'.$tag->name]= &$tag;
        }
        foreach ($method->arguments as $name => $default) {
          $a= &$m->addChild(new Node('argument', NULL, array('name' => $name)));
          $a->addChild(new Node('default', $default));
          if (isset($param[$name])) {
            $a->setAttribute('type', $param[$name]->type);
            $a->addChild(new Node('comment', $param[$name]->text));
          } else {
            $a->setAttribute('type', 'mixed');
            // DEBUG Console::writeLine('Unknown ', $name, ' in  ', xp::stringOf($method->tags('param')));
          }
        }
      }
      return $n;
    }

    function fieldsNode(&$classdoc, $inherited= FALSE) {
      $n= &new Node('fields');
      $inherited && $n->setAttribute('from', $classdoc->qualifiedName());

      foreach ($classdoc->fields as $name => $value) {
        $n->addChild(new Node('field', $value, array(
          'name'  => $name
        )));
      }
      
      return $n;
    }

    function classNode(&$classdoc) {
      $n= &new Node('class', NULL, array(
        'name'    => $classdoc->qualifiedName(),
        'type'    => $classdoc->classType()
      ));
      
      // Apidoc
      $n->addChild(new Node('comment', $classdoc->commentText()));
      $n->addChild(new Node('purpose', $this->tagAttribute($classdoc->tags('purpose'), 0, 'text')));
      foreach ($classdoc->tags('see') as $ref) {
        $n->addChild(new Node('see', NULL, array('href' => $ref->text)));
      }
      foreach ($classdoc->tags('test') as $ref) {
        $n->addChild(new Node('test', NULL, array('href' => $ref->text)));
      }
      if ($classdoc->tags('deprecated')) {
        $n->addChild(new Node('deprecated', $this->tagAttribute($classdoc->tags('purpose'), 0, 'text')));
      }

      // Annotations
      $n->addChild($this->annotationNode($classdoc->annotations()));

      // Superclasses
      $extends= &$n->addChild(new Node('extends'));
      $doc= $classdoc;
      while ($doc= $doc->superclass) {
        $extends->addChild($this->classReferenceNode($doc));
      }
      
      // Interfaces
      $interfaces= &$n->addChild(new Node('implements'));
      for ($classdoc->interfaces->rewind(); $classdoc->interfaces->hasNext(); ) {
        $interfaces->addChild($this->classReferenceNode($classdoc->interfaces->next()));
      }

      // Members
      $doc= $classdoc;
      $inherited= FALSE;
      do {
        $n->addChild($this->fieldsNode($doc, $inherited));
        $n->addChild($this->methodsNode($doc, $inherited));
        $inherited= TRUE;
      } while ($doc= $doc->superclass);
      
      return $n;
    }
    
    function marshalClassDoc(&$classdoc) {
      static $done= array();
      
      if (isset($done[$classdoc->hashCode()])) return;    // Already been there

      $out= &new File($this->build->getURI().$classdoc->qualifiedName().'.xml');
      Console::writeLine('- ', $classdoc->toString());

      // Create XML tree
      $tree= &new Tree('doc');
      $tree->addChild($this->classNode($classdoc));

      // Write to file
      FileUtil::setContents(
        $out,
        $tree->getDeclaration()."\n".$tree->getSource(INDENT_DEFAULT)
      );
      
      $done[$classdoc->hashCode()]= TRUE;
      delete($out);
      delete($tree);
    }

    function start(&$root) {
      $this->build= &new Folder($root->option('build', 'build'));
      $this->build->exists() || $this->build->create();
      
      while ($root->classes->hasNext()) {
        $doc= &$root->classes->next();
        $doc && $this->marshalClassDoc($doc);
        xp::gc();
      }
    }

    function &iteratorFor(&$root, $classes) {
      $collections= array();
      foreach (explode(PATH_SEPARATOR, $root->option('scan')) as $path) {
        $scan= &new Folder($path);
        if (!$scan->exists()) {
          return throw(new IllegalArgumentException($scan->getURI().' does not exist!'));
        }
     
        $collections[]= &new FileCollection($scan->getURI());
      }

      $iterator= &new AllClassesIterator(
        new FilteredIOCollectionIterator(
          new CollectionComposite($collections), 
          new ExtensionEqualsFilter('class.php'),
          TRUE
        ),
        ini_get('include_path')
      );
      $iterator->root= &$root;
      return $iterator;
    }
    
    
    function validOptions() {
      return array(
        'scan'  => HAS_VALUE,
        'build' => HAS_VALUE
      );
    }
  }
  // }}}
  
  // {{{ main
  RootDoc::start(new GeneratorDoclet(), new ParamString());
  // }}}
?>
