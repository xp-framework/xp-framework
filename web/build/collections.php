<?php
/* This class is part of the XP framework website
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.Properties', 
    'xml.Tree', 
    'io.Folder',
    'lang.apidoc.parser.ClassParser'
  );
  
  // {{{ void recurse(&xml.Tree tree, string base, string uri= '') throws lang.IllegalArgumentException
  //     Recurse a folder an build a sub-tree
  function recurse(&$tree, $base, $uri= '') {
    static $except= array('META-INF', 'CVS');
    static $parser= NULL;
    
    $f= &new Folder($base.DIRECTORY_SEPARATOR.$uri);
    if (!$f->exists()) {
      return throw(new IllegalArgumentException('Folder "'.$f->getURI().'" does not exist'));
    }

    // Set up parser (reference operator & missing intentionally)
    if (!$parser) $parser= new ClassParser();

    // Initialize
    $classes= $packages= 0;
    if (empty($uri)) {
      $node= &$tree;
    } else {
      $node= &new Node('package', NULL, array('name' => str_replace(DIRECTORY_SEPARATOR, '.', $uri)));
    }

    // Go through folder entries
    while (FALSE !== ($entry= $f->getEntry())) {
      $fn= str_replace($base, '', $f->getURI().$entry);

      // Recurse into subdirectories, ignoring well-known directories 
      // defined in static variable "except"
      if (is_dir($f->getURI().$entry) && !in_array($entry, $except)) {
        $packages++;
        recurse($node, $base, $fn);
        continue;
      }

      if ('.class.php' == substr($entry, -10)) {

        // Add class node to package node
        $node->addChild(new Node('class', NULL, array(
          'name' => str_replace(DIRECTORY_SEPARATOR, '.', substr($fn, 0, -10))
        )));
        
        // Parse class API docs
        // try(); {
        //   $parser->setFile(new File($f->getURI().$entry));
        //   $result= &$parser->parse();
        // } if (catch('Exception', $e)) {
        //   $e->printStackTrace();
        //   continue;
        // }
        // var_dump($result);
        // exit;
      } elseif ('.sapi.php' == substr($entry, -9)) {

        // Add sapi node to package node
        $node->addChild(new Node('sapi', NULL, array(
          'name' => str_replace(DIRECTORY_SEPARATOR, '.', substr($fn, 0, -9))
        )));        
      } else {
        continue;
      }
      $classes++;
    }
    $f->close();

    // Ignore folders without classes or subpackages in them
    if (empty($uri) || (empty($classes) && empty($packages))) return;

    // Add package node to tree
    $node->setAttribute('classes', $classes);
    $node->setAttribute('packages', $packages);
    $tree->addChild($node);
  }
  // }}}
 
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLinef('Usage: %s %s <collection_property_file>', $p->value(-1), $p->value(0));
    exit(-2);
  }

  $tree= &new Tree('collections');  
  $prop= &new Properties($p->value(1));
  if ($sect= $prop->getFirstSection()) do {

    // Process a single collection
    $base= realpath($prop->readString($sect, 'base')).DIRECTORY_SEPARATOR;
    // Console::writeLinef('---> Processing collection %s based in %s', $sect, $base);
    
    recurse($tree->addChild(new Node('collection', NULL, array(
      'name' => $sect,
      'base' => $base
    ))), $base);

  } while ($sect= $prop->getNextSection());
  
  echo $tree->getSource();
  // }}}
?>
