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
    public 
      $body         = '',
      $namespace    = 'ctl',
      $encoding     = XML_ENCODING_DEFAULT,
      $nodeType     = 'SOAPNode',
      $action       = '',
      $method       = '';

    /**
     * Create a message
     *
     * @access  public
     * @param   string action
     * @param   string method
     */
    public function create($action, $method) {
      $this->action= $action;
      $this->method= $method;

      $this->root= new Node(array(
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
    public function setData($arr) {
      $node= SOAPNode::fromArray($arr, 'item');
      $node->namespace= $this->namespace;
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
    private function unmarshall(&$child, $context= NULL) {
      if (
        isset($child->attribute['xsi:null']) or       // Java
        isset($child->attribute['xsi:nil'])           // SOAP::Lite
      ) {
        return NULL;
      }

      // References
      if (isset($child->attribute['href'])) {
        foreach (array_keys($this->root->children[0]->children) as $idx) {
          if (0 != strcasecmp(
            @$this->root->children[0]->children[$idx]->attribute['id'],
            substr($child->attribute['href'], 1)
          )) continue;
 
          // Create a copy and pass name to it
          $c= $this->root->children[0]->children[$idx];
          $c->name= $child->name;
          return self::unmarshall($c, $context);
          break;
        }
      }
      
      // Recognize XP object
      foreach ($child->attribute as $key => $val) {
        if ('xmlns' == substr($key, 0, 5) && 'http://xp-framework.net/xmlns/xp' == substr($val, 0, 32)) {
          try {
            $n= ClassLoader::loadClass(substr($child->attribute['xsi:type'], strlen($key) - 5));
          } catch (XPException $e) {
          
            // Handle this gracefully
            trigger_error($e->message, E_USER_NOTICE);
            $n= 'Object';
          }
          $result= XPClass::forName($n)->newInstance();
          foreach (self::_recurseData($child, TRUE, 'OBJECT') as $key=> $val) {
            $result->$key= $val;
          }
        
          return $result;          
        }
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

      switch (strtolower($regs[2])) {
        case 'array':
        case 'vector':
          $result= self::_recurseData($child, FALSE, 'ARRAY');
          break;

        case 'map':
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
            $key= $item->children[0]->getContent(self::getEncoding());
            $result[$key]= (empty($item->children[1]->children) 
              ? $item->children[1]->getContent(self::getEncoding())
              : self::unmarshall($item->children[1], 'MAP')
            );
          }
          break;

        case 'soapstruct':
        case 'struct':      
        case 'ur-type':
          if ('xsd' == $regs[1]) {
            $result= self::_recurseData($child, TRUE, 'HASHMAP');
            break;
          }

          $result= new stdClass();
          foreach (self::_recurseData($child, TRUE, 'OBJECT') as $key=> $val) {
            $result->$key= $val;
          }
          break;
          
        default:
          if (!empty($child->children)) {
            if ($regs[1]== 'xsd') {
              $result= self::_recurseData($child, TRUE, 'STRUCT');
              break;
            }

            $result= new stdClass();
            foreach (self::_recurseData($child, TRUE, 'OBJECT') as $key=> $val) {
              $result->$key= $val;
            }
            break;
          }

          $result= $child->getContent(self::getEncoding());
      }

      // HACK
      if (
        ($context == NULL) &&
        ($child->name != 'item') &&                    // PHP, XP, ...
        (substr($child->name, 1, 7) != '-gensym') &&   // Perl
        (sscanf($child->name, 'arg%d', $num) < 1)      // Axis
      ) {
        $result= new SOAPNamedItem($child->name, $result);
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
    private function _recurseData(&$node, $names= FALSE, $context= NULL) {
      if (empty($node->children)) return array();
      
      $results= array();
      for ($i= 0, $s= sizeof($node->children); $i < $s; $i++) {
        $results[$names ? $node->children[$i]->name : $i]= self::unmarshall(
          $node->children[$i], 
          $context
        );
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
    public function setFault($faultcode, $faultstring, $faultactor= NULL, $detail= NULL) {
      $this->root->children[0]->children[0]= SOAPNode::fromObject(new SOAPFault(array(
        'faultcode'      => $faultcode,
        'faultstring'    => $faultstring,
        'faultactor'     => $faultactor,
        'detail'         => $detail
      )), 'SOAP-ENV:Fault');
      $this->root->children[0]->children[0]->name= 'SOAP-ENV:Fault';
    }

    /**
     * Construct a SOAP message from a string
     *
     * <code>
     *   $msg= SOAPMessage::fromString('<SOAP-ENV:Envelope>...</SOAP-ENV:Envelope>');
     * </code>
     *
     * @model   static
     * @access  public
     * @param   string string
     * @return  &xml.Tree
     */
    public static function fromString($string) {
      return parent::fromString($string, 'SOAPMessage');
    }

    /**
     * Construct a SOAP message from a file
     *
     * <code>
     *   $msg= SOAPMessage::fromFile(new File('foo.soap.xml');
     * </code>
     *
     * @model   static
     * @access  public
     * @param   &io.File file
     * @return  &xml.Tree
     */ 
    public static function fromFile(&$file) {
      return parent::fromFile($file, 'SOAPMessage');
    }

    /**
     * Get fault
     *
     * @access  public
     * @return  &xml.soap.SOAPFault or NULL if none exists
     */
    public function getFault() {
      if (!strstr($this->root->children[0]->children[0]->name, ':Fault')) return NULL;
      
      list($return)= self::_recurseData($this->root->children[0], FALSE, 'OBJECT');
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
    public function getData($context= 'ENUM') {
      foreach ($this->root->attribute as $key=> $val) { // Namespace suchen
        if ($val == $this->action) $this->namespace= substr($key, strlen('xmlns:'));
      }

      $return= self::_recurseData($this->root->children[0]->children[0], FALSE, $context);
      return $return;
    }
  }
?>
