<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses(
    'lang.cca.Archive', 
    'io.File',
    'io.Folder',
    'util.Properties',
    'util.cmd.ParamString',
    'util.text.PHPTokenizer'
  );
  
  // {{{ main
  $p= &new ParamString();
  $t= &new PHPTokenizer();
  $dir= $p->value(1);
  printf("===> Init <dist> for %s [in %s]\n", basename($dir), $dir);
  
  // Read global configuration
  $prop= &new Properties($dir.'/port.ini');
  try(); {
    $prop->reset();
    $name= $prop->readString('port', 'name');
  } if (catch('Exception', $e)) {
    printf("*** Error: Missing port.ini (%s)\n", $e->getStackTrace());
    exit();
  }
  
  // Create temporary build dir
  $build= &new Folder($dir.'/build/');
  try(); {
    if (!$build->exists()) $build->create();
  } if (catch('Exception', $e)) {
    printf("*** Error: Cannot create build dir (%s)\n", $e->getStackTrace());
    exit();
  }
  
  // Create archive
  $a= &new Archive(new File($build->uri.'/port.cca'));
  printf("---> Create archive...\n");
  try(); {
    do {
      $a->open(ARCHIVE_CREATE);
    
      // Package files
      foreach ($prop->readArray('files', 'list', array()) as $fileref) {
        list($scheme, $urn)= explode('://', $fileref, 2);
        switch ($scheme) {
          case 'xp': 
            $origin= SKELETON_PATH.('__BASE__' == $urn
              ? 'lang.base.php'
              : strtr($urn, '.', DIRECTORY_SEPARATOR).'.class.php'
            );
            $idx= $urn;
            break;

          default:
            throw(new FormatException('Scheme "'.$scheme.'" not supported'));
            break 3;
        }
        
        // Strip
        $t->setTokens(token_get_all(implode('', file($origin))));
        $tok= $t->getFirstToken();
        $f= &new File($build->uri.basename($origin));
        $f->open(FILE_MODE_WRITE);
        do {
          switch ($tok[0]) {
            case T_COMMENT: 
              // Strip comments
              break;

            case T_WHITESPACE:
              // Reduce whitespace
              $f->write(' ');
              break;

            case T_OPEN_TAG:
              // Reduce whitespace after open tag
              $f->write('<?php');
              break;

            default:
              $f->write($tok[1]);
          }
        } while ($tok= $t->getNextToken());
        $f->close();

        // Add
        printf("     >> Adding %s [%d bytes] as %s\n", $f->filename, $f->size(), $idx);
        $a->add($f, $idx);
      }
      
      // Write archive to disk
      $a->create();
    } while(0);
  } if (catch('Exception', $e)) {
    printf("*** Error: Archive creation failed (%s)\n", $e->getStackTrace());
    @$a->file->unlink();
    exit;
  }
  
  printf("===> Finished <dist>\n");
  // }}}
?>
