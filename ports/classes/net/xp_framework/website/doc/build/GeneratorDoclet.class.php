<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'io.Folder',
    'xml.Tree',
    'text.doclet.Doclet',
    'text.doclet.markup.MarkupBuilder',
    'util.collections.HashSet',
    'io.collections.CollectionComposite', 
    'io.collections.FileCollection', 
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.ExtensionEqualsFilter',
    'net.xp_framework.website.doc.build.AllClassesIterator',
    'net.xp_framework.website.doc.build.storage.FileSystemDocStorage'
  );

  /**
   * Generates the framework's api documentation
   *
   * @purpose  Doclet
   */
  class GeneratorDoclet extends Doclet {
    protected
      $build    = NULL,
      $markup   = NULL,
      $impl     = array(),
      $packages = array();

    /**
     * Returns a tag attribute
     *
     * @param   array<string, text.doclet.Tag> tags
     * @param   string which
     * @param   string attribute
     * @return  any
     */
    protected function tagAttribute($tags, $which, $attribute= 'text') {
      return isset($tags[$which]) ? $tags[$which]->{$attribute} : NULL;
    }
    
    /**
     * Returns a node for a list of annotations
     *
     * @param   text.doclet.AnnotationDoc[] list
     * @return  xml.Node
     */
    protected function annotationNode($list) {
      $n= new Node('annotations');
      foreach ($list as $annotation) {
        $a= $n->addChild(new Node('annotation', NULL, array('name' => $annotation->name())));
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
    
    /**
     * Returns a node referencing a class for a given class doc
     *
     * @param   text.doclet.ClassDoc classdoc
     * @return  xml.Node
     */
    protected function classReferenceNode($classdoc) {
      $n= new Node('link', NULL, array(
        'rel'     => 'class',
        'href'    => $classdoc->qualifiedName(),
      ));
      return $n;
    }
    
    /**
     * Returns a node for a list of methods
     *
     * @param   text.doclet.ClassDoc classdoc
     * @param   bool inherited
     * @return  xml.Node
     */
    protected function methodsNode($classdoc, $inherited= FALSE) {
      $n= new Node('methods');
      $inherited && $n->setAttribute('from', $classdoc->qualifiedName());

      foreach ($classdoc->methods as $method) {
        $m= $n->addChild(new Node('method', NULL, array(
          'name'   => $method->name(),
          'access' => $method->getAccess(),
          'return' => $this->tagAttribute($method->tags('return'), 0, 'type')
        )));
        
        $m->addChild(Node::fromArray($method->getModifiers(), 'modifiers'));

        // Apidoc
        $m->addChild(new Node('comment', $this->markup($method->commentText())));
        foreach ($method->tags('see') as $ref) {
          $m->addChild(new Node('see', $ref->text, array(
            'scheme' => $ref->scheme,
            'href'   => $ref->urn
          )));
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
          $param['$'.$tag->name]= $tag;
        }
        foreach ($method->arguments as $name => $default) {
          $a= $m->addChild(new Node('argument', NULL, array('name' => $name)));
          $a->addChild(new Node('default', $default));
          if (isset($param[$name])) {
            $a->setAttribute('type', $param[$name]->type);
            $a->addChild(new Node('comment', $param[$name]->text));
          } else {
            $a->setAttribute('type', 'any');
            // DEBUG Console::writeLine('Unknown ', $name, ' in  ', xp::stringOf($method->tags('param')));
          }
        }
      }
      return $n;
    }

    /**
     * Returns a node for a list of fields
     *
     * @param   text.doclet.ClassDoc classdoc
     * @param   bool inherited
     * @return  xml.Node
     */
    protected function fieldsNode($classdoc, $inherited= FALSE) {
      $n= new Node('fields');
      $inherited && $n->setAttribute('from', $classdoc->qualifiedName());

      foreach ($classdoc->fields as $field) {
        $f= $n->addChild(new Node('field', NULL, array(
          'name'   => $field->name(),
          'access' => $field->getAccess(),
        )));
        $f->addChild(new Node('constant', $field->constantValue()));
        $f->addChild(Node::fromArray($field->getModifiers(), 'modifiers'));
      }
      
      return $n;
    }

    /**
     * Returns a node for a given class
     *
     * @param   text.doclet.ClassDoc classdoc
     * @return  xml.Node
     */
    protected function classNode($classdoc) {
      $n= new Node('class', NULL, array(
        'name'    => $classdoc->qualifiedName(),
        'package' => $classdoc->containingPackage()->name(),
        'type'    => $classdoc->classType()
      ));

      $n->addChild(Node::fromArray($classdoc->getModifiers(), 'modifiers'));
      
      // Apidoc
      $n->addChild(new Node('comment', $this->markup($classdoc->commentText())));
      $n->addChild(new Node('purpose', $this->tagAttribute($classdoc->tags('purpose'), 0, 'text')));
      foreach ($classdoc->tags('see') as $ref) {
        $n->addChild(new Node('see', $ref->text, array(
          'scheme' => $ref->scheme,
          'href'   => $ref->urn
        )));
      }
      foreach ($classdoc->tags('test') as $ref) {
        $n->addChild(new Node('test', NULL, array('href' => $ref->text)));
      }
      if ($classdoc->tags('deprecated')) {
        $n->addChild(new Node('deprecated', $this->tagAttribute($classdoc->tags('deprecated'), 0, 'text')));
      }

      // Annotations
      $n->addChild($this->annotationNode($classdoc->annotations()));
      
      // Constants
      foreach ($classdoc->constants as $name => $value) {
        $n->addChild(new Node('constant', $value, array('name' => $name)));
      }

      // Superclasses
      $extends= $n->addChild(new Node('extends'));
      $doc= $classdoc;
      while ($doc= $doc->superclass) {
        $extends->addChild($this->classReferenceNode($doc));
      }
      
      // Interfaces
      $interfaces= $n->addChild(new Node('implements'));
      for ($classdoc->interfaces->rewind(); $classdoc->interfaces->hasNext(); ) {
        $interface= $classdoc->interfaces->next();
        $interfaces->addChild($this->classReferenceNode($interface));
        
        // Add implementations
        $qname= $interface->qualifiedName();
        if (!isset($this->impl[$qname])) $this->impl[$qname]= array();
        $this->impl[$qname][]= $classdoc;
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
    
    /**
     * Marshal class doc
     *
     * @param   text.doclet.ClassDoc classdoc
     */
    protected function marshalClassDoc($classdoc) {

      // Add contained package
      $package= $classdoc->containingPackage();
      $hash= $package->name();

      if (!isset($this->packages[$hash])) {
        $this->packages[$hash]= array('info' => $package);
      }
      $this->packages[$hash]['classes'][$classdoc->name()]= $classdoc->classType();

      // Create XML tree
      $tree= new Tree('doc');
      $tree->addChild($this->classNode($classdoc));

      // Write to file
      $this->storage->store($classdoc->qualifiedName(), $tree);
    }

    /**
     * Marshal package doc
     *
     * @param   array<string, any> package
     */
    protected function marshalPackageDoc($package) {
      $tree= new Tree('doc');
      $p= $tree->addChild(new Node('package', NULL, array('name' => $package['info']->name())));
      $p->addChild(new Node('comment', $this->markup($package['info']->commentText())));

      $p->addChild(new Node('purpose', $this->tagAttribute($package['info']->tags('purpose'), 0, 'text')));
      foreach ($package['info']->tags('see') as $ref) {
        $p->addChild(new Node('see', $ref->text, array(
          'scheme' => $ref->scheme,
          'href'   => $ref->urn
        )));
      }
      foreach ($package['info']->tags('test') as $ref) {
        $p->addChild(new Node('test', NULL, array('href' => $ref->text)));
      }
      if ($package['info']->tags('deprecated')) {
        $p->addChild(new Node('deprecated', $this->tagAttribute($package['info']->tags('deprecated'), 0, 'text')));
      }

      // Add classes
      foreach ($package['classes'] as $name => $type) {
        $p->addChild(new Node('class', NULL, array(
          'name' => $name,
          'type' => $type
        )));
      }

      // Add subpackages
      $name= $package['info']->name().'.';
      foreach ($this->packages as $cmp) {
        if (0 !== strncmp($name, $cmp['info']->name(), strlen($name))) continue;
        $p->addChild(new Node('package', NULL, array('name' => $cmp['info']->name())));
      }
      
      // Check for parent packages.
      $parent= $package['info'];
      while ($parent= $parent->containingPackage()) {
        if (isset($this->packages[$parent->name()])) continue;
        $this->marshalPackageDoc(array('info' => $parent, 'classes' => array()));
      }

      // Store it
      $this->storage->store($package['info']->name(), $tree);
    }
    
    /**
     * Returns a node with markup for a given apidoc comment
     *
     * @param   string comment
     * @return  xml.Node
     */
    protected function markup($comment) {
      return new PCData('<p>'.$this->markup->markupFor($comment).'</p>');
    }

    /**
     * This doclet's entry point
     *
     * @param   text.doclet.RootDoc root
     */
    public function start($root) {
      $build= new Folder($root->option('build', 'build'));
      $build->exists() || $build->create();
      $this->storage= new FileSystemDocStorage($build);
      
      $this->markup= new MarkupBuilder();
      
      // Marshal classes
      $this->packages= array();
      while ($root->classes->hasNext()) {
        $classdoc= $root->classes->next();
        Console::writeLine('- ', $classdoc->toString());
        $this->marshalClassDoc($classdoc);
      }
      
      // Marshal packages
      foreach ($this->packages as $package) {
        Console::writeLine('- ', $package['info']->toString());
        $this->marshalPackageDoc($package);
      }

      // Add implementations
      foreach ($this->impl as $interface => $classes) {
        Console::writeLine('- ', $interface, ' implementations');
        $tree= $this->storage->get($interface);
        
        // Merge into class/doc
        with ($n= $tree->root->children[0]); {
          $i= NULL;
          foreach ($n->children as $child) {
            if ('implementations' !== $child->name) continue;
            $i= $child;
            break;
          }
          if (!$i) $i= $n->addChild(new Node('implementations'));
          foreach ($classes as $classdoc) {
            foreach ($i->children as $child) {
              if (
                'link' === $child->name && 
                'class' === $child->attribute['rel'] &&
                $classdoc->qualifiedName() === $child->attribute['href']
              ) continue 2;   // Already exists
            }
            $i->addChild($this->classReferenceNode($classdoc));
          }
        }

        $this->storage->store($interface, $tree);
      }
    }

    /**
     * Retrieve Iterator
     *
     * @param   text.doclet.RootDoc root
     * @param   string[] classes
     * @return  util.XPIterator
     */
    public function iteratorFor($root, $classes) {
      $collections= array();
      foreach (explode(PATH_SEPARATOR, $root->option('scan')) as $path) {
        $scan= new Folder($path);
        if (!$scan->exists()) {
          throw new IllegalArgumentException($scan->getURI().' does not exist!');
        }
     
        $collections[]= new FileCollection($scan->getURI());
      }

      $iterator= new AllClassesIterator(
        new FilteredIOCollectionIterator(
          new CollectionComposite($collections), 
          new ExtensionEqualsFilter('class.php'),
          TRUE
        ),
        xp::registry('classpath')
      );
      $iterator->root= $root;
      return $iterator;
    }
    
    /**
     * Returns valid options
     *
     * @return  array<string, int>
     */
    public function validOptions() {
      return array(
        'scan'  => HAS_VALUE,
        'build' => HAS_VALUE
      );
    }
  }
?>
