<?php
  require('lang.base.php');
  uses(
    'xml.Node',
    'io.File', 
    'io.Folder', 
    'lang.apidoc.parser.ClassParser',
    'util.cmd.ParamString'
  );
  
  function recurseFolders($uri, $pattern, &$parser) {
    $folder= &new Folder($uri);
    while ($entry= $folder->getEntry()) {
      if (
        ('.class.php' == substr($entry, -10)) &&
        (preg_match($pattern, $entry))
      ) {
        printf("===> Parse %s%s\n", $folder->uri, $entry);
        try(); {
          $parser->setFile(new File($folder->uri.$entry));
          $result= &$parser->parse();
        } if (catch('Exception', $e)) {
          $e->printStackTrace();
          continue;
        }
        
        $uses= str_replace('/', '.', substr(
          $folder->uri.$entry, 
          strlen(SKELETON_PATH), 
          -10
        ));
        
        $node= &new Node();
        $node->attribute['filename']= $entry;
        $node->attribute['classname']= $uses;
        $node->fromArray($result, 'classdoc');
        
        $out= &new File('xml/'.$uses.'.xpdoc.xml');
        try(); {
          $out->open(FILE_MODE_WRITE);
          $out->write($node->getSource(0));
          $out->close();
        } if (catch('Exception', $e)) {
          $e->printStackTrace();
          continue;
        }
        printf("     >> %d bytes written to %s\n", $out->size(), $out->uri);
        
      } else if (is_dir($folder->uri.$entry)) {
        recurseFolders($folder->uri.$entry, $pattern, $parser);
      }
    }
    $folder->close();
  }
  
  $p= &new ParamString($_SERVER['argv']);
  $pattern= ($p->exists('file')
    ? '/'.str_replace('/', '\/', $p->value('file')).'/i'
    : '/.*/'
  );

  recurseFolders(
    SKELETON_PATH, 
    $pattern,
    new ClassParser()
  );
?>
