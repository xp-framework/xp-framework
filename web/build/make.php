<?php
/* This class is part of the XP framework website
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'util.Properties', 
    'util.Hashmap', 
    'io.Folder',
    'io.File',
    'io.FileUtil',
    'text.apidoc.parser.ClassParser',
    'text.apidoc.parser.SapiParser'
  );
  

  // {{{ void parse(string filename, string indicator) throws lang.FormatException
  //     Parses API doc 
  function parse($filename, $indicator) {
    static $parser= NULL;

    // Set up parser
    if (!$parser) $parser= array(
      'class' => new ClassParser(),
      'sapi'  => new SapiParser()
    );
    
    // Sanity check
    if (!isset($parser[$indicator])) {
      return throw(new FormatException('Indicator "'.$indicator.'" unknown'));
    }

    // Parse
    try(); {
      $parser[$indicator]->setFile(new File($filename));
      $result= $parser[$indicator]->parse();
    } if (catch('FormatException', $e)) {
      return throw($e);
    }
    
    return $result;
  }
  // }}}

  // {{{ void recurse(&util.Hashmap list, string name, string base, string prefix= '', string uri= '') throws lang.IllegalArgumentException
  //     Recurse a folder an build a sub-tree
  function recurse(&$list, $name, $base, $prefix= '', $uri= '') {
    static $except= array('META-INF', 'CVS', '.svn');
    static $include= array('class', 'sapi');
    static $output= array();
    
    $f= &new Folder($base.DIRECTORY_SEPARATOR.$uri);
    if (!$f->exists()) {
      return throw(new IllegalArgumentException('Folder "'.$f->getURI().'" does not exist'));
    }

    // Go through folder entries
    while (FALSE !== ($entry= $f->getEntry())) {
      $fn= $f->getURI().$entry;
      
      // Ignore well-known files and directories
      if (in_array($entry, $except)) continue;

      // Recurse into subdirectories
      if (is_dir($fn)) {
        recurse($list, $name, $base, $prefix, str_replace($base, '', $fn));
        continue;
      }

      // Only take documentable files into account
      sscanf($entry, '%[^\.].%s', $filename, $indicator);
      $indicator= substr($indicator, 0, -4);
      if (!in_array($indicator, $include)) continue;

      // Check if a file needs updating
      $mtime= filemtime($fn);
      while (1) {
        if (!$list->containsKey($fn)) {
          Console::writeLine('---> Updating added ', $fn);
          $result= array();
          break;
        }

        $result= $list->get($fn);
        if ($mtime != $result['mtime']) {
          Console::writeLine('---> Updating modified ', $fn);
          break;
        }

        continue 2;
      }

      // Invoke parser
      try(); {
        $apidoc= parse($fn, $indicator);
      } if (catch('FormatException', $e)) {
        $e->printStackTrace();
        continue;
      }
      
      // Calculate package name
      $package= ltrim($prefix.str_replace(
        DIRECTORY_SEPARATOR, 
        '.', 
        $uri
      ), '.');
      
      // Store parsed information
      $stor= &new File(sprintf(
        'cache%s%s%s%s%s%s.%s',
        DIRECTORY_SEPARATOR,
        $name,
        DIRECTORY_SEPARATOR,
        $indicator,
        DIRECTORY_SEPARATOR,
        $package,
        $filename
      ));
      
      // Check if output directory exists. Create, if not
      if (!isset($output[$stor->getPath()])) {
        $path= &new Folder($stor->getPath());
        $path->exists() || $path->create(0755);
        $output[$stor->getPath()]= &$path;
        Console::writeLine('---> Created output directory ', $path->getURI());
      }
      
      Console::writeLine('---> Writing api documentation to ', $stor->getURI());
      FileUtil::setContents($stor, serialize($apidoc));
      delete($stor);

      // Store modification timestamp in list
      $result['mtime']= $mtime;
      $list->put($fn, $result);
    }
    $f->close();
    delete($f);
  }
  // }}}
 
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLinef('Usage: %s %s <collection_property_file>', $p->value(-1), $p->value(0));
    exit(-2);
  }
  
  // Retrieve stored list
  $stor= &new File('cache'.DIRECTORY_SEPARATOR.'lookup.db');
  if ($stor->exists()) {
    Console::writeLinef('---> Retrieving stored parser information');
    $list= unserialize(FileUtil::getContents($stor));
  } else {
    Console::writeLinef('---> No stored parser information available, generating all');
    $list= &new Hashmap();
  }
  
  // Go through all collections found in the property file
  $prop= &new Properties($p->value(1));
  if ($sect= $prop->getFirstSection()) do {

    // Process a single collection
    $base= realpath($prop->readString($sect, 'base')).DIRECTORY_SEPARATOR;
    $prefix= rtrim($prop->readString($sect, 'prefix'), '.').'.';

    Console::writeLinef(
      '===> Processing collection %s based in %s [prefix: %s]', 
      $sect, 
      $base,
      $prefix != '.' ? '"'.$prefix.'"' : '(none)'
    );
    recurse($list, $sect, $base, $prefix);

  } while ($sect= $prop->getNextSection());
  
  // Go through list again, this time finding deleted files and
  // removing them from the file
  Console::writeLine('===> Cleaning up');
  for ($i= &$list->iterator(); $i->hasNext(); ) {
    $fn= $i->next();
    if (file_exists($fn)) continue;
    
    Console::writeLine('---> Removing non-existant ', $fn);
    $list->remove($fn);
  }
  
  // Store list
  Console::writeLinef('---> Storing parser information');
  FileUtil::setContents($stor, serialize($list));
  // }}}
?>
