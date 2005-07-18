<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'xml.Tree',
    'xml.Node',
    'io.File', 
    'io.Folder', 
    'text.apidoc.parser.ClassParser',
    'text.PHPSyntaxHighlighter',
    'org.cvshome.CVSFile'
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
  
  function &recurseFolders($uri, $pattern, $base, &$parser, &$nodes) {
    // Don't try to parse CVS directories
    if ('CVS' == basename ($uri))
      return NULL;
      
    $subTree= &new Node('collection');
    $subTree->attribute['prefix']= getXPClassName ($uri, $base);
    $subTree->attribute['shortName']= getXPClassName (basename ($uri), $base);
    
    // Copy this
    $cTree= $subTree;
    
    $folder= &new Folder($uri);
    while ($entry= $folder->getEntry()) {
      // Console::writef("%s ~? %s\n", $entry, $pattern);
      if (
        ('.class.php' == substr($entry, -10)) &&
        (preg_match($pattern, $entry))
      ) {
        Console::writef('===> Parse %s: ', getXPClassName ($folder->uri.$entry, $base));
        try(); {
          $parser->setFile(new File($folder->uri.$entry));
          $result= &$parser->parse();
          
          if (defined ('USE_CVS')) {
            $cvs= &new CVSFile ($folder->uri.$entry);
            $status= $cvs->getStatus();
            $result['comments']['file']->cvstags= $status->tags;
          }
          
          // Add new class to classtree
          $subNode= &Node::fromArray (array (
            'cvsver'  => $result['comments']['file']->cvsver,
            'purpose' => $result['comments']['class']->purpose
          ), 'class');
          $subNode->attribute['className']= getXPClassName ($folder->uri.$entry, $base);
          $subNode->attribute['shortName']= getXPClassName ($entry, $base);
          
          $subTree->addChild ($subNode);
          
          // Create a class node
          $idx= &$subNode->attribute['shortName'];
          $nodes[$idx]= &new Node('class');
          $nodes[$idx]->attribute['idx']= $subNode->attribute['shortName'];
          $nodes[$idx]->attribute['name']= $subNode->attribute['className'];
          $nodes[$idx]->attribute['parent-idx']= (isset($result['comments']['class']->extends)
            ? $result['comments']['class']->extends
            : NULL
          );
          
          // Assume this is a regular class
          $classtype= 'default';
          
          // Check to see if this is an interface
          if (
            (0 == strcasecmp($nodes[$idx]->attribute['idx'], 'Interface')) ||
            (0 == strcasecmp($nodes[$idx]->attribute['parent-idx'], 'Interface'))
          ) {
            $classtype= 'interface';
          } else {
          
            // Let's see if this is an exception or error
            foreach (array(
              'Exception' => 'exception', 
              'Error'     => 'error'
            ) as $portion => $type) {
              if (strstr($subNode->attribute['shortName'], $portion)) {
                $classtype= $type;
                break;
              }
            }
          }
          
          $nodes[$idx]->attribute['type']= $classtype;
          $subNode->attribute['type']= $classtype;
        } if (catch('Exception', $e)) {
          $e->printStackTrace();
          continue;
        }
        
        $uses= str_replace('/', '.', substr(
          $folder->uri.$entry, 
          strlen($base), 
          -10
        ));
        
        $node= &Node::fromArray($result, 'classdoc');
        $node->attribute['filename']= $entry;
        $node->attribute['classname']= $uses;
        $node->attribute['generated_at']= date ('l, F d, Y');  
        $node->attribute['collection']= $subTree->attribute['prefix'];      
        $node->attribute['type']= $classtype;
        
        $cvsId= @$result['comments']['file']->cvsver;
        if (!empty ($cvsId)) {
          @list (,,$version,$date,$time,$user)= explode (' ', $cvsId);
          $node->attribute['version']= $version;
          $node->attribute['checkinUser']= $user;
          $node->attribute['checkinDate']= $date;
          $node->attribute['checkinTime']= $time;
        }
        
        $out= &new File('xml/'.$uses.'.xpdoc.xml');
        try(); {
          $out->open(FILE_MODE_WRITE);
          $out->writeLine('<?xml version="1.0" encoding="iso-8859-1"?>');
          $out->writeLine(preg_replace(
            array(
              '#&lt;pre&gt;(.*)&lt;/pre&gt;#sU',
              '#&lt;xmp&gt;(.*)&lt;/xmp&gt;#sU',
              '#&lt;tt&gt;(.*)&lt;/tt&gt;#sU',
              '#&lt;quote&gt;(.*)&lt;/quote&gt;#sU',
              '#&lt;code&gt;(.*)&lt;/code&gt;#sUe',
              '#&lt;ul&gt;(.*)&lt;/ul&gt;#sU',
              '#&lt;ol&gt;(.*)&lt;/ol&gt;#sU',
              '#&lt;(/?li ?)&gt;#sU',
              '#&lt;(br ?/?)&gt;#sU'
            ), array(
              '<pre>$1</pre>',
              '<pre>$1</pre>',
              '<tt>$1</tt>',
              '<quote>$1</quote>',
              'highlightPHPSource(stripslashes(\'$1\'))',
              '<ul>$1</ul>',
              '<ol>$1</ol>',
              '<$1>',
              '<br/>'
            ),
            $node->getSource(0)
          ));
          $out->close();
        } if (catch('Exception', $e)) {
          $e->printStackTrace(STDERR);
          continue;
        }
        Console::writeLinef('OK, type %s', $classtype);
      } else if (is_dir($folder->uri.$entry) && 'CVS' != $entry) {
        if ($child= &recurseFolders($folder->uri.$entry, $pattern, $base, $parser, $nodes)) {
          // Only add children if they contain children itself (are not empty)
          if (isset ($child->children) && count ($child->children))
            $subTree->addChild ($child);
        }
      }
    }
    $folder->close();
    
    return $subTree;
  }
  
  // Set limits appropriate
  ini_set('memory_limit', -1);
  ini_set('max_execution_time', -1);
  
  $p= &new ParamString($_SERVER['argv']);
  $pattern= ($p->exists('file')
    ? '/'.str_replace('/', '\/', $p->value('file')).'/i'
    : '/.*/'
  );
  
  // This is a global option
  if ($p->exists ('with-cvs')) {
    define ('USE_CVS', 1);
  }
      
  // Open properties file for generator
  $packages= array ();
  
  $prop= &new Properties ('packages.ini');
  if ($prop->exists ()) {
    $sect= $prop->getFirstSection();
    do {
      if (empty ($sect)) continue;
      
      $packages[$sect]= $prop->readSection($sect);
      
      // Register additional schemes
      foreach ($prop->readArray($sect, 'reference.additionalschemes', array()) as $add) {
        Reference::registerScheme($add);
      }
      
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

  foreach ($packages as $type=> $info) {
    $classNodes= &recurseFolders(
      $info['path'], 
      $pattern,
      $info['base'],
      new ClassParser(),
      $nodes= array()
    );

    // Node for package
    $core= &new Node('package', NULL, array(
      'type' => $type,
      'name' => $info['name']
    ));
    $core->addChild ($classNodes);
    $classTree->addChild ($core);
  }
  
  $out= &new File('xml/classTree.xpdoc.xml');
  $out->open (FILE_MODE_WRITE);
  $out->writeLine('<?xml version="1.0" encoding="iso-8859-1"?>');
  $out->writeLine($classTree->getSource());
  $out->close();
  
  // All xml files have been written, now build the inheritance tree
  Console::writef("===> Building inheritance tree [%d classes]...\n", sizeof($nodes));

  // Save it to file, structure must be /document/main/...
  $main= &new Node('document', NULL, array(
    'title'           => 'XP::The Classtree',
    'generated_at'    => date('l, F d, Y')
  ));
  $nodes[NULL]= &$main->addChild(new Node('main'));
  
  foreach (array_keys($nodes) as $class) {
    if (NULL == $class) continue;
    $parent= $nodes[$class]->attribute['parent-idx'];
    
    Console::writef("---> Adding %s to parent %s\n", $class, var_export($parent, 1));
    if (!is_a($nodes[$parent], 'Node')) {
      throw(new FormatException('Cannot add '.$class.' to non-existant parent '.$parent));
      break;
    }
    $nodes[$class]= &$nodes[$parent]->addChild($nodes[$class]);
    if (NULL !== $parent) {
      $nodes[$class]->attribute['parent']= $nodes[$parent]->attribute['name'];
    }
  }
  
  $out= &new File ('xml/inheritanceTree.xml');
  try(); {
    $out->open (FILE_MODE_WRITE);
    $out->writeLine('<?xml version="1.0" encoding="iso-8859-1"?>');
    $out->writeLine($main->getSource());
    $out->close();
  } if (catch ('IOException', $e)) {
    $e->printStackTrace(STDERR);
    exit(-1);
  }
 
  $m= function_exists('memory_get_usage') ? memory_get_usage() : -1;
  $r= getrusage();
  Console::writef(
    "===> Done\n".
    "+++  Memory used: %d bytes, %.2f KB, %.4f MB\n".
    "+++  Time:        %d.%d seconds\n",
    $m,
    $m / 1024,
    $m / 1048576,
    $r['ru_utime.tv_sec'],
    $r['ru_utime.tv_usec']
  );
?>
