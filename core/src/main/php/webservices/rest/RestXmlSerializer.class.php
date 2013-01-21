<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('webservices.rest.RestSerializer', 'xml.Tree');

  /**
   * A serializer
   *
   * @test  xp://net.xp_framework.unittest.webservices.rest.RestXmlSerializerTest
   * @see   xp://webservices.rest.RestRequest#setPayload
   */
  class RestXmlSerializer extends RestSerializer {

    /**
     * Return the Content-Type header's value
     *
     * @return  string
     */
    public function contentType() {
      return 'text/xml; charset=utf-8';
    }
    
    /**
     * Serialize
     *
     * @param   var value
     * @return  string
     */
    public function serialize($payload) {
      $t= new Tree();
      $t->setEncoding('UTF-8');

      if ($payload instanceof Payload) {
        $root= isset($payload->properties['name']) ? $payload->properties['name'] : 'root';
        $payload= $payload->value;
      } else {
        $root= 'root';
      }

      if ($payload instanceof Generic || is_array($payload)) {
        $t->root= Node::fromArray($this->convert($payload), $root);
      } else {
        $t->root= new Node($root, $this->convert($payload));
      }
      return $t->getDeclaration()."\n".$t->getSource(INDENT_NONE);
    }
  }
?>
