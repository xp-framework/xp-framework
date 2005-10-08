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
    'text.apidoc.parser.ClassParser'
  );
  
  // {{{ void recurse(&xml.Tree tree, string base, string prefix= '', string uri= '') throws lang.IllegalArgumentException
  //     Recurse a folder an build a sub-tree
  function recurse(&$tree, $base, $prefix= '', $uri= '') {
    static $except= array('META-INF', 'CVS', '.svn');
    static $include= array('class', 'sapi');
    
    $f= &new Folder($base.DIRECTORY_SEPARATOR.$uri);
    if (!$f->exists()) {
      return throw(new IllegalArgumentException('Folder "'.$f->getURI().'" does not exist'));
    }

    // Initialize
    $classes= $packages= 0;
    if (empty($uri)) {
      $node= &$tree;
    } else {
      $node= &new Node('package', NULL, array(
        'name' => ltrim($prefix.str_replace(DIRECTORY_SEPARATOR, '.', $uri), '.')
      ));
    }

    // Go through folder entries
    while (FALSE !== ($entry= $f->getEntry())) {

      // Ignore well-known files and directories
      if (in_array($entry, $except)) continue;

      // Recurse into subdirectories
      if (is_dir($f->getURI().$entry)) {
        $packages++;
        recurse($node, $base, $prefix, str_replace($base, '', $f->getURI().$entry));
        continue;
      }

      // Only take documentable files into account
      sscanf($entry, '%[^\.].%s', $filename, $indicator);
      $indicator= substr($indicator, 0, -4);
      if (!in_array($indicator, $include)) continue;

      // Calculate package name
      $package= ltrim($prefix.str_replace(
        DIRECTORY_SEPARATOR, 
        '.', 
        $uri
      ), '.');

      $node->addChild(new Node($indicator, NULL, array(
        'name' => $package.'.'.$filename
      )));

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
    $prefix= rtrim($prop->readString($sect, 'prefix'), '.').'.';
    
    recurse($tree->addChild(new Node('collection', NULL, array(
      'name' => $sect,
      'base' => $base
    ))), $base, $prefix);

  } while ($sect= $prop->getNextSection());
  
  echo $tree->getSource();
  // }}}
?>
