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
  use \webservices\json\JsonFactory;

  /**
   * Adds a module
   */
  class AddAction extends \lang\Object {
    protected static $json;

    static function __static() {
      self::$json= JsonFactory::create();
    }

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
        $origin= new GitHubArchive($module->vendor, $module->name, 'master');
      } else if (':' === $version{0}) {
        $target= new Folder($base, $module->name);
        throw new \lang\MethodNotImplementedException($version, 'add');
      } else {
        $target= new Folder($base, $module->name.'@'.$version);
        $origin= new XarRelease($module->vendor, $module->name, $version);
      }

      if ($target->exists()) {
        Console::writeLine($module, ' already exists in ', $target);
      } else {

        // Prepare vendor dir
        if (!$base->exists()) {
          $base->create(0755);
          self::$json->encodeTo(
            array('name' => $base->dirname),
            create(new File($base, 'vendor.json'))->getOutputStream()
          );
        }

        // Fetch
        Console::writeLine($module, ' -> ', $target);
        try {
          $target->create(0755);
          $origin->fetchInto($target);
        } catch (\lang\Throwable $e) {
          Console::writeLine('*** ', $e->compoundMessage());
          $target->unlink();
          return 2;
        }
      }

      // Deselect any previously selected version
      foreach (new FilteredIOCollectionIterator(new FileCollection($cwd), new NameMatchesFilter('#^\.'.$module->vendor.'\.'.$module->name.'.*\.pth#')) as $found) {
        $pth= new File($found->getURI());
        Console::writeLine('Deselect ', $pth);
        $pth->unlink();
      }

      // Rebuild paths based on .pth files found in newly selected
      $pth= new File('.'.$module->vendor.'.'.strtr($target->dirname, DIRECTORY_SEPARATOR, '.').'.pth');
      $out= $pth->getOutputStream();
      $base= strtr(substr($target->getURI(), strlen($cwd->getURI())), DIRECTORY_SEPARATOR, '/');
      Console::writeLine('Select ', $pth);
      foreach (new FilteredIOCollectionIterator(new FileCollection($target), new ExtensionEqualsFilter('.pth')) as $found) {
        $r= new StringReader($found->getInputStream());
        while (NULL !== ($line= $r->readLine())) {
          if ('' === $line || '#' === $line{0}) continue;

          Console::writeLine('+ ', $line, ' @ ', $base);
          if ('!' === $line{0}) {
            $out->write('!'.$base.substr($line, 1)."\n");
          } else {
            $out->write($base.$line."\n");
          }
        }
      }
      $out->close();

      Console::writeLine('Done');
      return 0;
    }
  }
?>