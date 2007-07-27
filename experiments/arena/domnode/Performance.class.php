<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.cmd.Command',
    'xml.Tree',
    'xml.DomXSLProcessor',
    'xml.Node',
    'util.profiling.Timer'
  );

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class Performance extends Command {

    /**
     * (Insert method's description here)
     *
     * @param   xml.DomXSLProcessor processor
     * @param   xml.Tree tree
     */
    public function treeSource($processor, $tree) {
      $processor->setXMLBuf($tree->getSource());
    }

    /**
     * (Insert method's description here)
     *
     * @param   xml.DomXSLProcessor processor
     * @param   xml.Tree tree
     */
    public function noIndentTreeSource($processor, $tree) {
      $processor->setXMLBuf($tree->getSource(INDENT_NONE));
    }

    /**
     * (Insert method's description here)
     *
     * @param   xml.DomXSLProcessor processor
     * @param   xml.Tree tree
     */
    public function domDocument($processor, $tree) {
      $processor->setXMLDocument($tree->getDomTree());
    }
    
    /**
     * (Insert method's description here)
     *
     * @param   string m
     */
    #[@arg]
    public function setMethod($m) {
      $this->method= $this->getClass()->getMethod($m);
    }

    /**
     * Main runner method
     *
     */
    public function run() {
      $this->out->writeLine('---> ', $this->method);
      $t= new Timer();

      $processor= new DomXSLProcessor();
      $processor->setXSLBuf($this->getClass()->getPackage()->getResource('test.xsl'));
      
      // Compose tree
      with ($t->start()); {
        $tree= new Tree();
        $nodes= $tree->addChild(new Node('nodes'));
        for ($i= 0; $i < 10000; $i++) {
          $nodes->addChild(new Node('node', NULL, array(
            'id' => $i
          )));
        }
        $t->stop();
        $this->out->writeLinef('    >> Create tree: %.3f seconds', $t->elapsedTime());
      }
      
      // Set XML
      with ($t->start()); {
        $this->method->invoke($this, array($processor, $tree));
        $t->stop();
        $this->out->writeLinef('    >> Pass to XSL processor: %.3f seconds', $t->elapsedTime());
      }
      
      // Run processor
      with ($t->start()); {
        $processor->run();
        $t->stop();
        $this->out->writeLinef('    >> Run processor: %.3f seconds', $t->elapsedTime());
      }
      
      $this->out->writeLine('---> Done, output= ', $processor->output());
      $this->out->writeLinef(
        '===> Memory usage: %.3f / peak: %.3f', 
        memory_get_usage() / 1024,
        memory_get_peak_usage() / 1024
      );
    }
  }
?>
