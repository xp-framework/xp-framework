<?php
  namespace xp\install;

  use \io\Folder;
  use \io\File;
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
  class RemoveAction extends \lang\Object {

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
      $target->unlink();
      $pth= new File('.'.$module->vendor.'.'.strtr($target->dirname, DIRECTORY_SEPARATOR, '.').'.pth');
      if ($pth->exists()) {
        Console::writeLine('Removing active ', $target);
        $pth->unlink();
      } else {
        Console::writeLine('Removing ', $target);
      }

      Console::writeLine('Done');
      return 0;
    }
  }
?>