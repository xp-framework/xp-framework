<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.Tree',
    'xml.Node',
    'xml.soap.SOAPNode',
    'xml.soap.SOAPFault'
  );
  
  /**
   * A SOAP Message consists of an envelope containing a body, and optionally,
   * headers.
   *
   * Example message in its XML representation:
   * <pre>
   * <?xml version="1.0" encoding="iso-8859-1"?>
   * <SOAP-ENV:Envelope
   *  xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/"
   *  xmlns:xsd="http://www.w3.org/2001/XMLSchema"
   *  xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
   *  xmlns:SOAP-ENC="http://schemas.xmlsoap.org/soap/encoding/"
   *  xmlns:si="http://soapinterop.org/xsd"
   *  SOAP-ENV:encodingStyle="http://schemas.xmlsoap.org/soap/encoding/"
   *  xmlns:ctl="urn://binford/Power"
   * >
   *   <SOAP-ENV:Body>
   *     <ctl:getPower/>
   *   </SOAP-ENV:Body>
   * </SOAP-ENV:Envelope>
   * </pre>
   *
   * @see      xp://xml.Tree
   * @purpose  Represent SOAP Message
   */
  class SOAPMessage extends Tree {
    var 
      $body         = '',
      $namespace    = 'ctl',
      $encoding     = XML_ENCODING_DEFAULT,
      $nodeType     = 'SOAPNode';

    /**
     * Create a message
     *
     * @access  public
     * @param   string action
     * @param   string method
     */
    function create($action, $method) {
      $this->action= $action;
      $this->method= $method;

      $this->root= &new Node(array(
        'name'          => 'SOAP-ENV:Envelope',
        'attribute'     => array(
          'xmlns:SOAP-ENV'              => 'http://schemas.xmlsoap.org/soap/envelope/', 
          'xmlns:xsd'                   => 'http://www.w3.org/2001/XMLSchema', 
          'xmlns:xsi'                   => 'http://www.w3.org/2001/XMLSchema-instance', 
          'xmlns:SOAP-ENC'              => 'http://schemas.xmlsoap.org/soap/encoding/', 
          'xmlns:si'                    => 'http://soapinterop.org/xsd', 
          'SOAP-ENV:encodingStyle'      => 'http://schemas.xmlsoap.org/soap/encoding/',
          'xmlns:'.$this->namespace     => $this->action   
        )
      ));
      $this->root->addChild(new Node(array('name' => 'SOAP-ENV:Body')));
      $this->root->children[0]->addChild(new Node(array('name' => $this->namespace.':'.$this->method)));
    }
    
    /**
     * Set data
     *
     * @access  public
     * @param   array arr
     */
    function setData($arr) {
      $node= &new SOAPNode();
      $node->namespace= $this->namespace;
      $node->fromArray($arr, 'item');
      if (empty($node->children)) return;
      
      // Copy all of node's children to root element
      foreach (array_keys($node->children) as $i) {
        $this->root->children[0]->children[0]->addChild($node->children[$i]);
      }
    }

    /**
     * Deserialize a single node
     *
     * @access  private
     * @param   &xml.Node child
     * @return  &mixed result
     */
    function &unmarshall(&$child, $context= NULL) {
      if (
        isset($child->attribute['xsi:null']) or       // Java
        isset($child->attribute['xsi:nil'])           // SOAP::Lite
      ) {
        return NULL;
      }

      // Typenabhängig
      if (!isset($child->attribute['xsi:type']) || !preg_match(
        '#^([^:]+):([^\[]+)(\[[0-9+]\])?$#', 
        $child->attribute['xsi:type'],
        $regs
      )) {
        // Zum Beispiel SOAP-ENV:Fault
        $regs= array(0, 'xsd', 'string');
      }

      // SOAP-ENC:arrayType="xsd:anyType[4]"
      if (isset($child->attribute['SOAP-ENC:arrayType'])) {
        $regs[2]= 'Array';
      }

      # DEBUG echo "*** {$child->name} [{$names}, {$context}, {$idx}] is {$regs[2]}\n";
      # DEBUG echo $child->getSource(0);

      switch (strtolower($regs[2])) {
        case 'array':
        case 'vector':
          # DEBUG echo "+++ Using array/vector deserializer\n";
          $result= $this->_recurseData($child, FALSE, 'ARRAY');
          break;

        case 'map':
          # DEBUG echo "+++ Using map deserializer\n";
          // <old_data xmlns:ns4="http://xml.apache.org/xml-soap" xsi:type="ns4:Map">
          // <item>
          // <key xsi:type="xsd:string">Nachname</key>
          // <value xsi:type="xsd:string">Braun</value>
          // </item>
          // <item>
          // <key xsi:type="xsd:string">PLZ</key>
          // <value xsi:type="xsd:string">76135</value>
          // </item>
          // <item>
          if (empty($child->children)) break;
          foreach ($child->children as $item) {
            $key= $item->children[0]->getContent($this->getEncoding());
            $result[$key]= (empty($item->children[1]->children) 
              ? $item->children[1]->getContent($this->getEncoding())
              : $this->unmarshall($item->children[1], 'MAP')
            );
          }
          break;

        case 'soapstruct':
        case 'struct':      
        case 'ur-type':
          # DEBUG echo "+++ Using struct.{$regs[1]} deserializer\n";
          if ($regs[1]== 'xsd') {
            $result= $this->_recurseData($child, TRUE, 'HASHMAP');
            break;
          }

          $n= 'stdClass';
          if (isset($child->attribute['xmlns:xp'])) {
            try(); {
              $n= ClassLoader::loadClass($regs[2]);
            } if (catch('Exception', $e)) {
              $n= 'stdClass';
            }
          }
          $result= &new $n();
          foreach ($this->_recurseData($child, TRUE, 'OBJECT') as $key=> $val) {
            $result->$key= $val;
          }
          break;

        default:
          if (!empty($child->children)) {
            if ($regs[1]== 'xsd') {
              # DEBUG echo "+++ Using hashmap.{$regs[1]} deserializer\n";
              $result= $this->_recurseData($child, TRUE, 'STRUCT');
              break;
            }

            # DEBUG echo "+++ Using object.{$regs[1]} deserializer\n";
            $result= &new stdClass();
            foreach ($this->_recurseData($child, TRUE, 'OBJECT') as $key=> $val) {
              $result->$key= $val;
            }
            break;
          }

          # DEBUG echo "+++ Using scalar deserializer\n";
          $result= $child->getContent($this->getEncoding());
      }

      /* HACK */
      if (
        $context == NULL and 
        $child->name != 'item' and
        substr($child->name, 0, 8) != 'c-gensym'
      ) {
        $result= &new SOAPNamedItem($child->name, $result);
      }
      
      return $result;
    }

    /**
     * Recursively unmarshall data
     *
     * @access  private
     * @param   &xml.Node node
     * @param   bool names default FALSE
     * @param   string context default NULL
     * @return  &mixed data
     */    
    function &_recurseData(&$node, $names= FALSE, $context= NULL) {
      if (empty($node->children)) return array();
      
      $results= array();
      for ($i= 0, $s= sizeof($node->children); $i < $s; $i++) {
        $results[$names ? $node->children[$i]->name : $i]= $this->unmarshall(
          $node->children[$i], 
          $context
        );
        # DEBUG var_dump($results[$names ? $node->children[$i]->name : $i]);
      }
      return $results;
    }

    /**
     * Set fault
     *
     * @access  public
     * @param   int faultcode
     * @param   string faultstring
     * @param   string faultactor default NULL
     * @param   mixed detail default NULL
     */    
    function setFault($faultcode, $faultstring, $faultactor= NULL, $detail= NULL) {
      $this->root->children[0]->children[0]= &new SOAPNode();
      $this->root->children[0]->children[0]->fromObject(new SOAPFault(array(
        'faultcode'      => $faultcode,
        'faultstring'    => $faultstring,
        'faultactor'     => $faultactor,
        'detail'         => $detail
      )));
      unset($this->root->children[0]->children[0]->attribute);
      $this->root->children[0]->children[0]->name= 'SOAP-ENV:Fault';
    }

    /**
     * Get fault
     *
     * @access  public
     * @return  &xml.soap.SOAPFault or NULL if none exists
     */
    function &getFault() {
      //echo "+++ SOAPMessage::getFault\n";
      if ($this->root->children[0]->children[0]->name != 'SOAP-ENV:Fault') return NULL;
      
      list($return)= $this->_recurseData($this->root->children[0], FALSE, 'OBJECT');
      return new SOAPFault(array(
        'faultcode'      => $return['faultcode'],
        'faultstring'    => $return['faultstring'],
        'faultactor'     => $return['faultactor'],
        'detail'         => $return['detail']
      ));
    }
    
    /**
     * Get data
     *
     * @access  public
     * @param   string context default 'ENUM'
     * @return  &mixed data
     */
    function &getData($context= 'ENUM') {
      foreach ($this->root->attribute as $key=> $val) { // Namespace suchen
        if ($val == $this->action) $this->namespace= substr($key, strlen('xmlns:'));
      }
      
      return $this->_recurseData($this->root->children[0]->children[0], FALSE, $context);
    }
  }
?>
