<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'webservices.uddi.BusinessList',
    'xml.Node',
    'webservices.uddi.InquiryCommand'
  );

  /**
   * Find businesses
   *
   * @purpose  UDDI command container
   * @see      xp://webservices.uddi.InquiryCommand
   */
  class FindBusinessesCommand extends Object implements InquiryCommand {
    public
      $names            = array(),
      $findQualifiers   = array(),
      $maxRows          = 0;

    /**
     * Constructor
     *
     * @param   string[] name
     * @param   string[] qualifiers default array()
     * @param   int maxRows default 0
     */
    public function __construct(
      $names, 
      $findQualifiers= array(), 
      $maxRows= 0
    ) {
      $this->names= $names;
      $this->findQualifiers= $findQualifiers;
      $this->maxRows= $maxRows;
    }
  
    /**
     * Marshal command to a specified node
     *
     * @param   xml.Node node
     * @see     xp://webservices.uddi.UDDICommand#marshalTo
     */
    public function marshalTo($node) {
      $node->setName('find_business');
      
      // Add maxRows if non-zero
      $this->maxRows && $node->setAttribute('maxRows', $this->maxRows);

      // Add find qualifiers if existant
      if (!empty($this->findQualifiers)) {
        $q= $node->addChild(new Node('findQualifiers'));
        foreach ($this->findQualifiers as $findQualifier) {
          $q->addChild(new Node('findQualifier', $findQualifier));
        }
      }
      
      // Add names
      foreach ($this->names as $name) {
        $node->addChild(new Node('name', $name));
      }
    }
    
    /**
     * Unmarshal return value from a specified node
     *
     * @param   xml.Node node
     * @return  webservices.uddi.BusinessList
     * @see     xp://webservices.uddi.UDDICommand#unmarshalFrom
     * @throws  lang.FormatException in case of an unexpected response
     */
    public function unmarshalFrom($node) {
      if (0 != strcasecmp($node->name, 'businessList')) {
        throw new FormatException('Expected "businessList", got "'.$node->name.'"');
      }
      
      // Create business list object from XML representation
      with ($list= new BusinessList(), $children= $node->nodeAt(0)->getChildren()); {
        $list->setOperator($node->getAttribute('operator'));
        $list->setTruncated(0 == strcasecmp('true', $node->getAttribute('truncated')));
        
        for ($i= 0, $s= sizeof($children); $i < $s; $i++) {
          $b= new Business($children[$i]->getAttribute('businessKey'));
          
          for ($j= 0, $t= sizeof($children[$i]->getChildren()); $j < $s; $j++) {
            switch ($children[$i]->nodeAt($j)->name) {
              case 'name': 
                $b->names[]= $children[$i]->nodeAt($j)->getContent();
                break;

              case 'description': 
                $b->description= $children[$i]->nodeAt($j)->getContent();
                break;
            }
          }
          $list->items[]= $b;
        }
      }
      
      return $list;
    }
  
  } 
?>
