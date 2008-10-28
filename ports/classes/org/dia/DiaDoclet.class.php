<?php
/*
 *
 * $Id$
 */

  uses(
    'text.doclet.Doclet',
    'util.cmd.Console',
    'org.dia.DiaDiagram',
    'org.dia.DiaMarshaller',
    'org.dia.DiaUMLGeneralization'
  );

  /**
   * Doclet that generated DIAgrams out of XP classes
   * 
   *
   * Options:
   * <ul>
   *  <li>verbose : boolean</li> default FALSE
   *  <li>gzipped : boolean</li> default TRUE
   *  <li>recurse : int</li> default 0
   *  <li>depend  : boolean</li> default FALSE
   *  <li>directory=$DIR : target directory</li> default: './'
   *  <li>diagram=$FILE : output diagram filename</li> default: 'fqcn.dia'
   * </ul>
   *
   */
  class DiaDoclet extends Doclet {

    /**
     * Returns an array of valid options
     *
     * @return  array
     */
    public function validOptions() {
      return array(
        'verbose' => OPTION_ONLY,
        'gzipped' => OPTION_ONLY,
        'depend' => OPTION_ONLY,
        'recurse' => HAS_VALUE,
        'directory'  => HAS_VALUE,
        'diagram' => HAS_VALUE
      );
    }

    /**
     * Run Doclet
     *
     */
    public function start($root) {  
      // test hasNext()?

      while ($root->classes->hasNext()) {
        $ClassDoc= $root->classes->next();
        $classnames[]= $ClassDoc->qualifiedName();
      }

      // generate diagram via DiaMarshaller
      $Dia= DiaMarshaller::marshal(
        $classnames, 
        $root->option('recurse', 0), 
        $root->option('depend', FALSE)
      );

      // default destination is the current directory
      $filename= $root->option('directory', '.').DIRECTORY_SEPARATOR;
      if ($root->option('diagram', FALSE)) {
        $filename.= $root->option('diagram');
      } else {
        $filename.= $ClassDoc->qualifiedName().'.dia';
      }

      // save diagram to file
      $Dia->saveTo($filename, $root->option('gzipped', FALSE));
    }
  }
?>
