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
    'lang.apidoc.parser.ClassParser',
    'lang.apidoc.parser.SapiParser'
  );
  

  // {{{ void parse(string filename, string indicator) throws lang.FormatException
  //     Parses API doc 
  function parse($filename, $indicator) {
    static $parser= NULL;
    static $appender= NULL;

    // Set up parser
    if (!$parser) $parser= array(
      'class.php' => new ClassParser(),
      'sapi.php'  => new SapiParser()
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

  // {{{ void recurse(&util.Hashmap list, string base, string uri= '') throws lang.IllegalArgumentException
  //     Recurse a folder an build a sub-tree
  function recurse(&$list, $base, $uri= '') {
    static $except= array('META-INF', 'CVS');
    static $include= array('class.php', 'sapi.php');
    
    $f= &new Folder($base.DIRECTORY_SEPARATOR.$uri);
    if (!$f->exists()) {
      return throw(new IllegalArgumentException('Folder "'.$f->getURI().'" does not exist'));
    }

    // Go through folder entries
    while (FALSE !== ($entry= $f->getEntry())) {
      $fn= $f->getURI().$entry;

      // Recurse into subdirectories, ignoring well-known directories 
      // defined in static variable "except"
      if (is_dir($fn) && !in_array($entry, $except)) {
        recurse($list, $base, str_replace($base, '', $f->getURI().$entry));
        continue;
      }

      // Only take documentable files into account
      if (
        (2 != sscanf($entry, '%[^\.].%s', $filename, $indicator)) || 
        (!in_array($indicator, $include))
      ) continue;

      // Check if a file needs updating
      $mtime= filemtime($fn);
      while (1) {
        if (!$list->containsKey($fn)) {
          Console::writeLine('---> Updating added ', $fn);
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
        $result= parse($fn, $indicator);
      } if (catch('FormatException', $e)) {
        $e->printStackTrace();
        continue;
      }

      // Store parsed data in list
      $result['mtime']= $mtime;        
      $list->put($fn, $result);
    }
    $f->close();
  }
  // }}}
 
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLinef('Usage: %s %s <collection_property_file>', $p->value(-1), $p->value(0));
    exit(-2);
  }
  
  // Retrieve stored list
  $stor= &new File('.make.cache');
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
    Console::writeLinef('===> Processing collection %s based in %s', $sect, $base);
    recurse($list, $base);

  } while ($sect= $prop->getNextSection());
  
  // Go through list again, this time finding deleted files and
  // removing them from the file
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
