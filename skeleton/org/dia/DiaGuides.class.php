<?php
/*
 *
 * $Id:$
 */

  uses(
    'org.dia.DiaAttribute',
    'org.dia.DiaComposite'
  );

  /**
   * Represents a 'dia:composite type="guides"' node
   *
   */
  class DiaGuides extends DiaComposite {
    
    var
      $type= 'guides';

    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="hguides"]', value= 'string')]
    function setHorizontalGuides($hguides) {
      $this->set('hguides', new DiaAttribute('hguides'));
    }

    #[@fromDia(xpath= 'dia:composite/dia:attribute[@name="vguides"]', value= 'string')]
    function setVerticalGuides($vguides) {
      $this->set('vguides', new DiaAttribute('vguides'));
    }

    function &getNode() {
      $Node= &new Node('dia:attribute');
      $Node->setAttribute('name', 'guides');
      $Node->addChild(parent::getNode());
      return $Node;
    }
  }
?>
