<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.cmd.Command',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.ExtensionEqualsFilter',
    'util.collections.HashTable',
    'io.File',
    'io.Folder',
    'io.FileUtil',
    'lang.types.String'
  );

  /**
   * Converts to XP6
   *
   * @purpose  Command
   */
  class ToXp6 extends Command {
    protected
      $iterator      = NULL,
      $baseUriLength = 0,
      $nameMap       = NULL;

    /**
     * Set origin directory
     *
     * @param   string origin
     */
    #[@arg]
    public function setOrigin($origin) {
      $this->iterator= new FilteredIOCollectionIterator(
        new FileCollection($origin),
        new ExtensionEqualsFilter('class.php'),
        TRUE
      );
      $this->out->writeLine($this->iterator);
    }

    /**
     * Set base directory
     *
     * @param   string base
     */
    #[@arg]
    public function setBase($base) {
      $this->baseUriLength= strlen(create(new FileCollection($base))->getUri());
    }

    /**
     * Set target directory
     *
     * @param   string base
     */
    #[@arg]
    public function setTarget($target) {
      $this->target= create(new FileCollection($target))->getUri();
    }
    
    /**
     * Returns a token array
     *
     * @param   mixed t
     * @return  array
     */
    protected function tokenOf($t) {
      static $map= array(
        'uses'          => T_USES,
        'newinstance'   => T_NEWINSTANCE,
        'is'            => T_IS,
        'create'        => T_CREATE,
        'raise'         => T_RAISE,
        'finally'       => T_FINALLY,
        'delete'        => T_DELETE,
        'with'          => T_WITH,
        'ref'           => T_REF,
        'deref'         => T_DEREF,
      );

      $normalized= is_array($t) ? $t : array($t, $t);
      if (T_STRING == $normalized[0] && isset($map[$normalized[1]])) {
        $normalized[0]= $map[$normalized[1]];
      }
      return $normalized;
    }
    
    /**
     * Maps a name to its fully qualified name
     *
     * @param   string qname in dot-notation (package.Name)
     * @param   string namespace default NULL in colon-notation
     * @return  string in colon-notation (package::Name)
     */
    protected function mapName($qname, $namespace= NULL) {
      if (NULL === ($mapped= $this->nameMap[$qname])) {
        $this->err->writeLine('*** No mapping for ', $qname);
        return $qname;
      }

      // Return local name if mapped name matches current namespace
      $p= strrpos($mapped, '::');
      if (FALSE !== $p && $namespace == substr($mapped, 0, $p)) {
        return substr($mapped, $p+ 2);
      }
      return $mapped;
    }
    
    /**
     * Convert a file
     *
     * @param   io.collections.FileElement e
     */
    protected function convert(FileElement $e) {
      $qname= strtr(substr($e->getUri(), $this->baseUriLength, -10), '/\\', '..');

      // Calculate class and package name from qualified name
      $p= strrpos($qname, '.');
      $package= substr($qname, 0, $p);
      $namespace= str_replace('.', '::', $package);
      $class= substr($qname, $p+ 1);

      $this->out->writeLine('- ', $class, ' @ ', $package ,' < ', $e);

      // Tokenize file
      $t= token_get_all(file_get_contents($e->getUri()));
      $state= array(ST_INITIAL);
      $out= '';
      for ($i= 0, $s= sizeof($t); $i < $s; $i++) {
        $token= $this->tokenOf($t[$i]);
        
        switch ($state[0].$token[0]) {
        
          // Insert namespace declaration after "This class is part of..." file comment
          case ST_INITIAL.T_COMMENT: {
            $out.= $token[1]."\n\n  namespace ".str_replace('.', '::', $namespace).';';
            array_unshift($state, ST_DECL);
            break;
          }
          
          // Remember loaded classes in uses() for use as mapping
          case ST_DECL.T_USES: {
            $out.= '::'.$token[1];
            array_unshift($state, ST_USES);
            break;
          }
          
          case ST_USES.T_CONSTANT_ENCAPSED_STRING: {
            $name= trim($token[1], "'");
            $this->nameMap[xp::reflect($name)]= new String(str_replace('.', '::', $name));
            $out.= $token[1];
            break;
          }
          
          case ST_USES.')': {
            $out.= $token[1];
            array_shift($state);
            break;
          }
          
          // instanceof X, extends X, new X, catch(X $var)
          case ST_DECL.T_INSTANCEOF: case ST_DECL.T_EXTENDS: case ST_DECL.T_NEW: case ST_DECL.T_CATCH: {
            $out.= $token[1];
            array_unshift($state, ST_CLASS);
            break;
          }
          
          case ST_CLASS.T_STRING: {
            $out.= $this->mapName($token[1], $namespace);
            array_shift($state);
            break;
          }

          case ST_CLASS.T_VARIABLE: {
            $out.= $token[1];
            array_shift($state);
            break;
          }

          // implements X, Y
          case ST_DECL.T_IMPLEMENTS: {
            $out.= $token[1];
            array_unshift($state, ST_INTF);
            break;
          }
          
          case ST_INTF.T_STRING: {
            $out.= $this->mapName($token[1], $namespace);
            break;
          }
          
          case ST_INTF.'{': {
            $out.= $token[1];
            array_shift($state);
            break;
          }
          
          // X::y(), X::$y, X::const
          case ST_DECL.T_STRING: {
            $next= $this->tokenOf($t[$i+ 1]);
            if (T_DOUBLE_COLON == $next[0]) {
              $out.= $this->mapName($token[1], $namespace);
            } else {
              $out.= $token[1];
            }
            break;
          }
          
          // function name(X $var, Y $type)
          case ST_DECL.T_FUNCTION: {
            $out.= $token[1];
            array_unshift($state, ST_FUNC);
            $brackets= 0;
            break;
          }
          
          case ST_FUNC.'(': {
            $out.= $token[1];
            $brackets++;
            break;
          }

          case ST_FUNC.')': {
            $out.= $token[1];
            $brackets--;
            if (0 == $brackets) {
              array_shift($state);
            }
            break;
          }
          
          case ST_FUNC.T_STRING: {
            $var= $this->tokenOf($t[$i+ 2]);
            if ($brackets >= 1 && T_VARIABLE == $var[0]) {
              $out.= ''; // Workaround for bug: Remove typehints! $this->mapName($token[1], $namespace);
            } else {
              $out.= $token[1];
            }
            break;
          }
          
          // XP "keywords" - prefix with "::"
          case ST_DECL.T_NEWINSTANCE: case ST_DECL.T_IS: case ST_DECL.T_CREATE:
          case ST_DECL.T_RAISE: case ST_DECL.T_FINALLY:
          case ST_DECL.T_DELETE: case ST_DECL.T_WITH:
          case ST_DECL.T_REF: case ST_DECL.T_DEREF: {
            $out.= '::'.$token[1];
            break;
          }
          
          default: {
            $out.= $token[1];
          }
        }
      }
      
      // Write converted sourcecode to target directory
      $file= new File($this->target.strtr($qname, '.', DIRECTORY_SEPARATOR).'.class.php');
      $folder= new Folder(dirname($file->getUri()));
      $folder->exists() || $folder->create();
      FileUtil::setContents($file, $out);
    }

    /**
     * Main runner method
     *
     */
    public function run() {
    
      // Build name map
      $this->nameMap= create('new HashTable<String, String>()');
      $this->nameMap['self']= new String('self');
      $this->nameMap['parent']= new String('parent');
      foreach (array_merge(get_declared_interfaces(), get_declared_classes()) as $name) {
        $r= new ReflectionClass($name);
        if ($r->isInternal()) {
          $this->nameMap[$name]= new String('::'.$name);
        } else {
          $this->nameMap[$name]= new String(str_replace('.', '::', xp::nameOf($name)));
        }
      }
      $this->nameMap['xp']= new String('::xp');
      $this->nameMap['null']= new String('::null');
      $this->out->writeLine($this->nameMap);
      
      // Iterate over origin directory, converting each
      while ($this->iterator->hasNext()) {
        $this->convert($this->iterator->next());
      }
    }
  }
?>
