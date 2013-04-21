<?php
  namespace xp\install;

  use \io\Folder;
  use \io\File;
  use \io\collections\FileCollection;
  use \io\collections\iterate\FilteredIOCollectionIterator;
  use \io\collections\iterate\ExtensionEqualsFilter;
  use \io\collections\iterate\NameMatchesFilter;
  use \io\streams\StringReader;
  use \util\cmd\Console;

  /**
   * XPI Installer - remove modules
   * ==============================
   *
   * Basic usage
   * -----------
   * # This will remove the module in the given version
   * $ xpi remove vendor/module@1.0.0
   */
  class RemoveAction extends Action {

    /**
     * Execute this action
     *
     * @param  string[] $args command line args
     * @return int exit code
     */
    public function perform($args) {
      sscanf($args[0], '%[^@]@%s', $name, $version);
      $module= Module::valueOf($name);

      $cwd= new Folder('.');
      $target= new Folder($cwd, $module->vendor, $module->name.'@'.$version);

      if (!$target->exists()) {
        Console::writeLine($module, ' not installed in version ', $version);
        return 1;
      }

      // Unlink
      $pth= new File('.'.$module->vendor.'.'.strtr($target->dirname, DIRECTORY_SEPARATOR, '.').'.pth');
      if ($pth->exists()) {
        Console::writeLine('Removing active ', $target);
        $pth->unlink();
        $active= TRUE;
      } else {
        Console::writeLine('Removing ', $target);
        $active= FALSE;
      }
      $target->unlink();

      // If there is no other version available, remove the module reference completely. 
      // If there is another version installed, and the deinstalled module was active,
      // then select this other version.
      $base= new FileCollection(new Folder($cwd, $module->vendor));
      $it= new FilteredIOCollectionIterator($base, new NameMatchesFilter('#^'.$module->name.'@.+#'));
      if ($it->hasNext()) {
        if ($active) {
          $next= $it->next();
          $pth= new File('.'.$module->vendor.'.'.basename($next->getURI()).'.pth');
          $out= $pth->getOutputStream();
          $base= strtr(substr($next->getURI(), strlen($cwd->getURI())), DIRECTORY_SEPARATOR, '/');
          Console::writeLine('Select ', $pth);
          foreach (new FilteredIOCollectionIterator($next, new ExtensionEqualsFilter('.pth')) as $found) {
            $r= new StringReader($found->getInputStream());
            while (NULL !== ($line= $r->readLine())) {
              if ('' === $line || '#' === $line{0}) {
                continue;
              } else if ('!' === $line{0}) {
                $out->write('!'.$base.substr($line, 1)."\n");
              } else {
                $out->write($base.$line."\n");
              }
            }
          }
        }
      } else {
        Console::writeLine('Removing module reference');
        $base->removeElement($module->name.'.json');
      }

      Console::writeLine('Done');
      return 0;
    }
  }
?>