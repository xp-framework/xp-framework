<?php
  namespace xp\install;

  use \io\Folder;
  use \io\File;
  use \util\cmd\Console;

  /**
   * Removes a module
   */
  class RemoveAction extends \lang\Object {

    /**
     * Execute this action
     *
     * @param  string[] $args command line args
     * @return int exit code
     */
    public function perform($args) {
      $module= Module::valueOf($args[0]);
      $cwd= new Folder('.');

      // Determine origin and target
      $base= new Folder($module->vendor);
      $version= isset($args[1]) ? $args[1] : '';
      if ('' === $version) {
        $target= new Folder($base, $module->name.'@master');
      } else if (':' === $version{0}) {
        $target= new Folder($base, $module->name);
      } else {
        $target= new Folder($base, $module->name.'@'.$version);
      }

      if (!$target->exists()) {
        Console::writeLine($module, ' not installed');
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