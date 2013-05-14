<?php namespace xp\install;

use io\Folder;
use io\File;
use io\collections\FileCollection;
use io\collections\iterate\FilteredIOCollectionIterator;
use io\collections\iterate\ExtensionEqualsFilter;
use io\collections\iterate\NameMatchesFilter;
use io\streams\StringReader;
use util\cmd\Console;

/**
 * XPI Installer - remove modules
 * ==============================
 *
 * Basic usage
 * -----------
 * This will remove the module in the given version
 * $ xpi remove vendor/module@1.0.0
 *
 * This will remove the module in all installed versions
 * $ xpi remove vendor/module
 */
class RemoveAction extends Action {

  /**
   * Removes a given module version
   *
   * @param  io.Folder $cwd The working directory
   * @param  io.collections.FileCollection $version The version
   * @return bool whether the module was active
   */
  protected function remove($cwd, FileCollection $version) {
    $versioned= basename($version->getURI());
    $vendor= basename($version->getOrigin()->getURI());
    Console::writeLine('Removing ', $vendor, '/', $versioned, ' -> ', $version);

    // Remove corresponding .pth file
    $pth= new File($cwd, '.'.$vendor.'.'.$versioned.'.pth');
    if ($pth->exists()) {
      $active= true;
      $pth->unlink();
    } else {
      $active= false;
    }

    // Remove folder
    $fld= new Folder($version->getURI());
    $fld->unlink();

    return $active;
  }

  /**
   * Removes all versions of a given module
   *
   * @param  io.Folder $cwd The working directory
   * @param  io.collections.iterate.IOCollectionIterator $versions The version
   */
  protected function removeAll($cwd, $versions) {
    while ($versions->hasNext()) {
      $this->remove($cwd, $versions->next());
    }
  }

  /**
   * Execute this action
   *
   * @param  string[] $args command line args
   * @return int exit code
   */
  public function perform($args) {
    if (empty($args)) {
      Console::$err->writeLine('*** Missing argument #1: Module name');
      return 2;
    }

    sscanf($args[0], '%[^@]@%s', $name, $version);
    $module= Module::valueOf($name);

    $cwd= new Folder('.');
    $vendor= new FileCollection(new Folder($cwd, $module->vendor));
    $versions= new FilteredIOCollectionIterator($vendor, new NameMatchesFilter('#^'.$module->name.'@.+#'));

    if (null === $version) {

      // No version given: Remove all installed modules, and the module reference
      if (!$vendor->findElement($module->name.'.json')) {
        Console::writeLine($module, ' not installed');
        return 1;
      }

      $this->removeAll($cwd, $versions);
      Console::writeLine('Removing module reference');
      $vendor->removeElement($module->name.'.json');
    } else {

      // Specific version given: Remove this version, if it's the last one, the
      // module reference, if not, select next possible one.
      if (!($coll= $vendor->findCollection($module->name.'@'.$version))) {
        Console::writeLine($module, ' not installed in version ', $version);
        return 1;
      }

      $active= $this->remove($cwd, $coll);
      if ($versions->hasNext()) {
        if ($active) {
          $next= $versions->next();
          $pth= new File('.'.$module->vendor.'.'.basename($next->getURI()).'.pth');
          $out= $pth->getOutputStream();
          $base= strtr(substr($next->getURI(), strlen($cwd->getURI())), DIRECTORY_SEPARATOR, '/');
          Console::writeLine('Select ', $pth);
          foreach (new FilteredIOCollectionIterator($next, new ExtensionEqualsFilter('.pth')) as $found) {
            $r= new StringReader($found->getInputStream());
            while (null !== ($line= $r->readLine())) {
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
        $vendor->removeElement($module->name.'.json');
      }
    }
    Console::writeLine('Done');
    return 0;
  }
}