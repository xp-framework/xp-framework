<?php
  require('lang.base.php');
  uses(
    'xml.Node',
    'io.File', 
    'io.Folder', 
    'lang.apidoc.parser.ClassParser'
  );
  
  function recurseFolders($uri, &$parser) {
    $folder= &new Folder($uri);
    while ($entry= $folder->getEntry()) {
      if ('.class.php' == substr($entry, -10)) {
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
        recurseFolders($folder->uri.$entry, $parser);
      }
    }
    $folder->close();
  }

  recurseFolders(SKELETON_PATH, new ClassParser());
?>
