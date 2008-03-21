<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.cmd.Command',
    'text.doclet.RootDoc',
    'util.cmd.ParamString',
    'net.xp_framework.website.doc.build.GeneratorDoclet'
  );

  /**
   * 
   * @purpose  Command
   */
  class Generate extends Command {
    protected
      $arguments= array();

    /**
     * Set output directory
     *
     * @param   string dir
     */
    #[@arg]
    public function setOutputDirectory($dir) {
      $this->arguments[]= '--build='.$dir;
    }

    /**
     * Set directories to scan for classes
     *
     * @param   string classes
     */
    #[@arg]
    public function setClasses($classes= NULL) {
      if ($classes) {
        $this->arguments[]= '--scan='.$classes;
      } else {
        $this->arguments[]= '--scan='.implode(PATH_SEPARATOR, xp::registry('classpath'));
      }
    }

    /**
     * Main runner method
     *
     */
    public function run() {
      RootDoc::start(new GeneratorDoclet(), new ParamString($this->arguments));
    }
  }
?>
