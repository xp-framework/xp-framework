<?php
/*
 *
 * $Id:$
 */

  uses(
    'text.doclet.Doclet',
    'util.cmd.Console',
    'org.dia.DiaDiagram',
    'org.dia.DiaMarshaller'
  );

  /**
   * Doclet that generated DIAgrams out of XP classes
   *
   * Options:
   * <ul>
   *  <li>verbose : boolean</li>
   *  <li>recurse : boolean</li>
   *  <li>gzipped : boolean</li>
   *  <li>directory=$DIR : target directory</li>
   *  <li>file=$FILE : output filename</li>
   * </ul>
   *
   *
   */
  class DiaDoclet extends Doclet {

    var
      $_options= array(
        'verbose' => OPTION_ONLY,
        'recurse' => OPTION_ONLY,
        'gzipped' => OPTION_ONLY,
        'directory'  => HAS_VALUE,
        'file'  => HAS_VALUE
      ),
      $_recurse= FALSE,   // recurse or not
      $_dia= NULL;        // the diagram class

    /**
     * Initialize DiaDoclet with some options
     */
    function __construct() {
      // initialize DIAgram
      $this->_dia= &new DiaDiagram();
      $this->_dia->initialize();

      // get reference to background layer
      $Layers= &$this->_dia->getChildByType('DiaLayer');
      $this->_layer= &$Layers[0];
    }

    /**
     * Returns an array of valid options
     *
     * @access  public
     * @return  array
     */
    function validOptions() {
      return $this->_options;
    }

    /**
     * Run Doclet
     *
     * - loop over classes (recursively) and add each class to the DIAgram
     * - add dependencies to the DIAgram...
     */
    function start(&$root) {  
      while ($root->classes->hasNext()) {
        $ClassDoc= &$root->classes->next();
        if (!is('ClassDoc', $ClassDoc)) {
          Console::writeLine('Fatal ERROR: Class is no "ClassDoc" instance: '.xp::stringOf($ClassDoc));
          exit(-1);
        }
        Console::writeLine('Adding ', $ClassDoc->qualifiedName(), '...');
        try (); {
          $Dia_umlclass= &DiaMarshaller::marshal($ClassDoc);
        } if (catch('Exception', $e)) {
          Console::writeLine('Fatal Exception: ', $e->toString());
          exit(-1);
        }
        $this->_layer->add($Dia_umlclass);

        // recurse?
        if ($root->option('recurse')) {
          $Class= &$ClassDoc;
          while ($SuperClass= &$Class->superclass) {
            Console::writeLine('Adding ', $SuperClass->qualifiedName(), '...');
            try (); {
              $Dia_umlclass= &DiaMarshaller::marshal($SuperClass);
            } if (catch('Exception', $e)) {
              Console::writeLine('Fatal Exception: ', $e->toString());
              exit(-1);
            }
            $this->_layer->add($Dia_umlclass);
            $Class= &$SuperClass;
          }
        }
      }

      // default destination is the current directory
      $filename= $root->option('directory', '.').DIRECTORY_SEPARATOR;
      if ($root->option('file', FALSE)) {
        $filename.= $root->option('file');
      } else {
        $filename.= $ClassDoc->qualifiedName().'.dia';
      }
      $this->_dia->saveTo($filename, $root->option('gzipped', FALSE));
    }
  }
?>
