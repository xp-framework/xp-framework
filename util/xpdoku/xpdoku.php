<?php
  require('lang.base.php');
  uses(
    'xml.Tree',
    'xml.Node',
    'io.File', 
    'io.Folder', 
    'lang.apidoc.parser.ClassParser',
    'util.cmd.ParamString',
    'util.text.PHPSyntaxHighlighter'
  );
  
  function highlightPHPSource($str) {
    static $p;
    static $t;
    
    if (!isset($p)) $p= &new PHPSyntaxHighlighter();
    if (!isset($t)) $t= array_flip(get_html_translation_table(HTML_ENTITIES));
    
    $p->setSource('<?php '.strtr($str, $t).' ?>');
    return $p->getHighlight();
  }
  
  function getXPClassName($uri, $base) {
    $uri= preg_replace ('/^'.str_replace ('/', '\\/', $base).'/', '', $uri);
    $uri= preg_replace ('/\.class\.php$/', '', $uri);
    $uri= str_replace ('/', '.', $uri);
    return $uri;    
  }
  
  function &recurseFolders($uri, $pattern, $base, &$parser, &$depTree) {
    // Don't try to parse CVS directories
    if ('CVS' == basename ($uri))
      return NULL;
      
    $subTree= &new Node();
    $subTree->name= 'collection';
    $subTree->attribute['prefix']= getXPClassName ($uri, $base);
    $subTree->attribute['shortName']= getXPClassName (basename ($uri), $base);
    
    // Copy this
    $cTree= $subTree;
    
    $folder= &new Folder($uri);
    while ($entry= $folder->getEntry()) {
      // printf("%s ~? %s\n", $entry, $pattern);
      if (
        ('.class.php' == substr($entry, -10)) &&
        (preg_match($pattern, $entry))
      ) {
        printf("===> Parse %s\n", getXPClassName ($folder->uri.$entry, $base));
        try(); {
          $parser->setFile(new File($folder->uri.$entry));
          $result= &$parser->parse();
          
          // Add new class to classtree
          $subNode= &new Node();
          $subNode->name= 'class';
          $subNode->attribute['className']= getXPClassName ($folder->uri.$entry, $base);
          $subNode->attribute['shortName']= getXPClassName ($entry, $base);
          $subNode->fromArray (array (
            'cvsver'  => $result['comments']['file']->cvsver,
            'purpose' => $result['comments']['class']->purpose
          ), 'class');
          
          $subTree->addChild ($subNode);
          
          if (!empty ($result['comments']['class']->extends))
            $depTree[$subNode->attribute['shortName']]= $result['comments']['class']->extends;
          else
            $depTree[$subNode->attribute['shortName']]= 'StdClass';
          
        } if (catch('Exception', $e)) {
          $e->printStackTrace();
          continue;
        }
        
        $uses= str_replace('/', '.', substr(
          $folder->uri.$entry, 
          strlen($base), 
          -10
        ));
        
        $node= &new Node();
        $node->attribute['filename']= $entry;
        $node->attribute['classname']= $uses;
        $node->attribute['generated_at']= date ('l, F d, Y');  
        $node->attribute['collection']= $subTree->attribute['prefix'];      
        $node->fromArray($result, 'classdoc');

        $cvsId= @$result['comments']['file']->cvsver;
        if (!empty ($cvsId)) {
          list (,,$version,$date,$time,$user)= explode (' ', $cvsId);
          $node->attribute['version']= $version;
          $node->attribute['checkinUser']= $user;
          $node->attribute['checkinDate']= $date;
          $node->attribute['checkinTime']= $time;
        }
        
        $out= &new File('xml/'.$uses.'.xpdoc.xml');
        try(); {
          $out->open(FILE_MODE_WRITE);
          $out->writeLine($node->getDeclaration());
          $out->writeLine(preg_replace(
            array(
              '#&lt;pre&gt;(.*)&lt;/pre&gt;#sU',
              '#&lt;xmp&gt;(.*)&lt;/xmp&gt;#sU',
              '#&lt;code&gt;(.*)&lt;/code&gt;#sUe',
            ), array(
              '<pre>$1</pre>',
              '<xmp>$1</xmp>',
              'highlightPHPSource(stripslashes(\'$1\'))'
            ),
            $node->getSource(0)
          ));
          $out->close();
        } if (catch('Exception', $e)) {
          $e->printStackTrace();
          continue;
        }
      } else if (is_dir($folder->uri.$entry) && 'CVS' != $entry) {
        if ($child= &recurseFolders($folder->uri.$entry, $pattern, $base, $parser, $depTree)) {
          // Only add children if they contain children itself (are not empty)
          if (isset ($child->children) && count ($child->children))
            $subTree->addChild ($child);
        }
      }
    }
    $folder->close();
    
    return $subTree;
  }
  
  $p= &new ParamString($_SERVER['argv']);
  $pattern= ($p->exists('file')
    ? '/'.str_replace('/', '\/', $p->value('file')).'/i'
    : '/.*/'
  );

  // Open properties file for generator
  $packages= array ();
  
  $prop= &new Properties ('packages.ini');
  if ($prop->exists ()) {
    $sect= $prop->getFirstSection();
    do {
      if (!empty ($sect))
        $packages[$sect]= $prop->readSection($sect);
    } while (false !== ($sect= $prop->getNextSection()));
  }
  
  if (!isset ($packages['core'])) {
    $packages['core']= array (
      'path' => SKELETON_PATH,
      'name' => 'Core Packages',
      'base' => SKELETON_PATH
    );
  }

  // Main Node for all packages
  $classTree= &new Node();
  $classTree->name= 'packages';
  $classTree->attribute['generated_at']= date ('l, F d, Y');

  // Extend-array: Which class inherits from which
  $depTree= array (
  );
  
  foreach ($packages as $type=> $info) {
    $classNodes= &recurseFolders(
      $info['path'], 
      $pattern,
      $info['base'],
      new ClassParser(),
      $depTree
    );

    // Node for package
    $core= &new Node();
    $core->name= 'package';
    $core->attribute['type']= $type;
    $core->attribute['name']= $info['name'];
    $core->addChild ($classNodes);
    $classTree->addChild ($core);
  }
  
  $out= &new File('xml/classTree.xpdoc.xml');
  $out->open (FILE_MODE_WRITE);
  $out->writeLine ($classTree->getDeclaration());
  $out->writeLine ($classTree->getSource());
  $out->close();
  
  /* All xml files have been written, now
   * build the inheritance tree
   */
  echo "===> Building inheritance tree...\n";

  // Find "unresolved" dependencies, so we won't end up with endless loop
  foreach ($depTree as $class=> $parent) {
    if ('StdClass' != $parent && !isset ($depTree[$parent])) {
      echo sprintf ("     - Unresolved dependency: %s extends unknown class %s\n",
        $class,
        $parent
      );
      unset ($depTree[$class]);
    }
  }
  
  
  // Inheritance tree
  $iTree= array (
    'StdClass' => array ()
  );
  
  /**
   * traverse through the result tree, find
   * the class named $className and return
   * a reference to it.
   *
   * @param &array tree the tree
   * @param $className the class to search
   * @return &$class reference to the class entry or false if not found
   */
  function &findClass (&$tree, $className) {
    if (isset ($tree[$className]))
      return $tree[$className];
    
    // Not found? Walk through all nodes  
    foreach ($tree as $name=> $idx) {
      if (false !== ($found= &findClass ($tree[$name], $className)))
        return $found;
    }
    
    return false;
  }
  
  // Populate inheritance tree
  while (count ($depTree)) {
    foreach ($depTree as $class=> $parent) {
      if (false !== ($parentClass= &findClass ($iTree, $parent))) {
        $parentClass[$class]= array();
        unset ($depTree[$class]);
      }
    }
  }

  // Save it to file, structure must be /document/main/...
  $main= new Node();
  $main->name= 'document';
  $main->attribute['title']= 'XP::The Classtree';
  $main->attribute['generated_at']= date ('l, F d, Y');

  $tree= new Node();
  $tree->fromArray ($iTree, 'main');
  $main->addChild ($tree);
  
  $out= &new File ('xml/inheritanceTree.xml');
  try(); {
    $out->open (FILE_MODE_WRITE);
    $out->writeLine ($main->getDeclaration());
    $out->writeLine ($main->getSource());
    $out->close();
  } if (catch ('IOException', $e)) {
    $e->printStackTrace();
  }
 
?>
