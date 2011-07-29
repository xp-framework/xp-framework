<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.soap.SoapTypeMapper');

  /**
   * Represents supported SoapTypes
   *
   * @test     TODO
   * @see      TODO
   * @purpose  Type implementation
   */
  class XPSoapTypeMapper extends SoapTypeMapper {

    protected function boxSoapType($object) {
      $node= new Node($object->getItemName() != FALSE ? $object->getItemName() : 'item');
      $node->setContent($object->toString());

      if (isset($object->item)) {
        // $node->children[0]= clone $object->item;
        $node->attribute= $object->item->attribute;
        $node->children= $object->item->children;
      }

      $node->setAttribute('xsi:type', $object->getType());

      return $node;
    }

    protected function boxParameter($object) {
      return new Node($object->name);
    }

    protected function boxLong($object) {
      return new Node('item', $object->value, array('xsi:type' => 'xsd:long'));
    }

    protected function boxShort($object) {
    }

    protected function boxDouble($object) {
      return new Node('item', $object->doubleValue(), array('xsi:type' => 'xsd:float'));
    }

    protected function boxInteger($object) {
      return new Node('item', $object->intValue(), array('xsi:type' => 'xsd:int'));
    }

    protected function boxString($object) {
      return new Node('item', (string)$object, array('xsi:type' => 'xsd:string'));
    }

    protected function boxDate($object) {
    }

    protected function boxBytes($object) {
    }

    protected function boxBoolean($object) {
      return new Node('item', $object->value ? 'true' : 'false', array('xsi:type' => 'xsd:boolean'));
    }

    protected function boxCharacter($object) {
    }
  }
?>
