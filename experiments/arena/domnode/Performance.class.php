<?php
/* This file is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.cmd.Command',
    'xml.Tree',
    'xml.DomBackedTree',
    'xml.Node',
    'xml.DomXSLProcessor',
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
     * @param   int nodes
     * @return  xml.Tree tree
     */
    public function xpTree($nodes) {
      $tree= new Tree();
      $n= $tree->addChild(new Node('nodes'));
      for ($i= 0; $i < $nodes; $i++) {
        $n->addChild(new Node('node', '<&">', array(
          'id' => '&'.$i
        )));
      }
      return $tree;
    }

    /**
     * (Insert method's description here)
     *
     * @param   int nodes
     * @return  xml.Tree tree
     */
    public function domTree($nodes) {
      $tree= new DomBackedTree();
      $n= $tree->addChild(new DomBackedNode('nodes'));
      for ($i= 0; $i < $nodes; $i++) {
        $n->addChild(new DomBackedNode('node', '<&">', array(
          'id' => '&'.$i
        )));
      }
      return $tree;
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
     * (Insert method's description here)
     *
     * @param   string m
     */
    #[@arg]
    public function setImplementation($i) {
      $this->tree= $this->getClass()->getMethod($i);
    }

    /**
     * Main runner method
     *
     */
    public function run() {
      $this->out->writeLine('---> ', $this->method);
      $this->out->writeLine('---> ', $this->tree, ' : ', $this->tree->invoke($this, array(1))->getSource(INDENT_NONE));
      $t= new Timer();

      $processor= new DomXSLProcessor();
      $processor->setXSLBuf($this->getClass()->getPackage()->getResource('test.xsl'));
      
      // Compose tree
      with ($t->start()); {
        $tree= $this->tree->invoke($this, array(10000));
        $t->stop();
        $this->out->writeLinef('    >> Create tree: %.3f seconds', $create= $t->elapsedTime());
      }
      
      // Set XML
      with ($t->start()); {
        $this->method->invoke($this, array($processor, $tree));
        $t->stop();
        $this->out->writeLinef('    >> Pass to XSL processor: %.3f seconds', $proc= $t->elapsedTime());
      }
      
      // Run processor
      with ($t->start()); {
        $processor->run();
        $t->stop();
        $this->out->writeLinef('    >> Run processor: %.3f seconds', $run= $t->elapsedTime());
      }
      
      $this->out->writeLine('---> Done, output= ', $processor->output());
      $this->out->writeLinef(
        '===> Elapsed time: %.3f seconds',
        $create+ $proc+ $run
      );
      $this->out->writeLinef(
        '===> Memory usage: %.3f / peak: %.3f', 
        memory_get_usage() / 1024,
        memory_get_peak_usage() / 1024
      );
    }
  }
?>
