<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'xml.Tree',
    'xml.Node',
    'lang.Collection',
    'webservices.soap.CommonSoapFault',
    'webservices.soap.xp.XPSoapNode',
    'webservices.soap.xp.XPSoapHeaderElement',
    'webservices.soap.xp.XPSoapMapping',
    'scriptlet.rpc.AbstractRpcMessage'
  );
  
  define('XMLNS_SOAPENV',       'http://schemas.xmlsoap.org/soap/envelope/');
  define('XMLNS_SOAPENC',       'http://schemas.xmlsoap.org/soap/encoding/');
  define('XMLNS_SOAPINTEROP',   'http://soapinterop.org/xsd');
  define('XMLNS_XSD',           'http://www.w3.org/2001/XMLSchema');
  define('XMLNS_XSI',           'http://www.w3.org/2001/XMLSchema-instance');
  define('XMLNS_XP',            'http://xp-framework.net/xmlns/xp');
  
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
   * @test     xp://net.xp_framework.unittest.soap.SoapTest
   * @purpose  Represent SOAP Message
   */
  class XPSoapMessage extends Tree implements AbstractRpcMessage {
    public 
      $body         = NULL,
      $namespace    = 'ctl',
      $encoding     = 'iso-8859-1',
      $mapping      = NULL,
      $nodeType     = 'XPSoapNode',
      $action       = '',
      $class        = '',
      $method       = '';

    public 
      $namespaces   = array(
        XMLNS_SOAPENV     => 'SOAP-ENV',
        XMLNS_XSD         => 'xsd',
        XMLNS_XSI         => 'xsi',
        XMLNS_SOAPENC     => 'SOAP-ENC',
        XMLNS_SOAPINTEROP => 'si'
      );

    /**
     * Create a message
     *
     * @param   string action
     * @param   string method
     * @param   string targetNamespace default NULL
     * @param   webservices.soap.xp.XPSoapHeader[] headers default array()
     */
    public function createCall($action, $method, $targetNamespace= NULL, $headers= array()) {
      $this->action= $action;
      $this->method= $method;

      $this->root= new Node('SOAP-ENV:Envelope', NULL, array(
        'xmlns:SOAP-ENV'              => XMLNS_SOAPENV,
        'xmlns:xsd'                   => XMLNS_XSD,
        'xmlns:xsi'                   => XMLNS_XSI,
        'xmlns:SOAP-ENC'              => XMLNS_SOAPENC,
        'xmlns:si'                    => XMLNS_SOAPINTEROP,
        'SOAP-ENV:encodingStyle'      => XMLNS_SOAPENC,
        'xmlns:'.$this->namespace     => (NULL !== $targetNamespace ? $targetNamespace : $this->action)
      ));
      
      if (!empty($headers)) {
        $header= $this->root->addChild(new Node('SOAP-ENV:Header'));
        for ($i= 0, $s= sizeof($headers); $i < $s; $i++) {
          $header->addChild($headers[$i]->getNode($this->namespaces));
        }
      }
      
      $this->body= $this->root->addChild(new Node('SOAP-ENV:Body'));
      $this->body->addChild(new Node($this->namespace.':'.$this->method));
    }
    
    /**
     * Create a message
     *
     * @param   webservices.soap.xp.XPSoapMessage msg
     */
    public function create($msg= NULL) {
      if ($msg) {
        $this->action= $msg->action;
        $this->method= $msg->method;
      }

      $ns= array(
        'xmlns:SOAP-ENV'              => XMLNS_SOAPENV,
        'xmlns:xsd'                   => XMLNS_XSD,
        'xmlns:xsi'                   => XMLNS_XSI,
        'xmlns:SOAP-ENC'              => XMLNS_SOAPENC,
        'xmlns:si'                    => XMLNS_SOAPINTEROP,
        'SOAP-ENV:encodingStyle'      => XMLNS_SOAPENC
      );
      
      if ($this->action) $ns['xmlns:'.$this->namespace]= $this->action;
      $this->root= new Node('SOAP-ENV:Envelope', NULL, $ns);
      
      if (!empty($headers)) {
        $header= $this->root->addChild(new Node('SOAP-ENV:Header'));
        for ($i= 0, $s= sizeof($headers); $i < $s; $i++) {
          $header->addChild($headers[$i]->getNode($this->namespaces));
        }
      }
      
      $this->body= $this->root->addChild(new Node('SOAP-ENV:Body'));
      $this->body->addChild(new Node($this->namespace.':'.$this->method));
      $this->mapping= new XPSoapMapping();
    }

    /**
     * Set Mapping
     *
     * @param   webservices.soap.xp.XPSoapMapping mapping
     */
    public function setMapping($mapping) {
      $this->mapping= $mapping;
    }
    
    /**
     * Set data
     *
     * @param   array arr
     */
    public function setData($arr) {
      $node= XPSoapNode::fromArray($arr, 'item', $this->mapping);
      $node->namespace= $this->namespace;
      if (empty($node->children)) return;
      
      // Copy all of node's children to root element
      foreach (array_keys($node->children) as $i) {
        $this->body->children[0]->addChild($node->children[$i]);
      }
    }

    /**
     * Retrieve Content-type for requests.
     *
     * @return  string
     */
    public function getContentType() { return 'text/xml'; }    

    /**
     * Deserialize a single node
     *
     * @param   xml.Node child
     * @param   string context default NULL
     * @param   webservices.soap.xp.XPSoapMapping mapping
     * @return  mixed result
     */
    public function unmarshall($child, $context= NULL) {
      // DEBUG Console::writeLine('Unmarshalling ', $child->name, ' (', var_export($child->attribute, 1), ') >>> ', $child->content, '<<<', "\n"); // DEBUG
      if (
        isset($child->attribute[$this->namespaces[XMLNS_XSI].':null']) or       // Java
        isset($child->attribute[$this->namespaces[XMLNS_XSI].':nil'])           // SOAP::Lite
      ) {
        return NULL;
      }

      // References
      if (isset($child->attribute['href'])) {
        $body= $this->_bodyElement();
        foreach (array_keys($body->children) as $idx) {
          if (0 != strcasecmp(
            @$body->children[$idx]->attribute['id'],
            substr($child->attribute['href'], 1)
          )) continue;
 
          // Create a copy and pass name to it
          $c= $body->children[$idx];
          $c->name= $child->name;
          return $this->unmarshall($c, $context);
          break;
        }
      }
      
      // Update namespaces list
      foreach ($child->attribute as $key => $val) {
        if (0 == strncmp('xmlns:', $key, 6)) $this->namespaces[$val]= substr($key, 6);
      }
      
      // Type dependant
      if (!isset($child->attribute[$this->namespaces[XMLNS_XSI].':type']) || !preg_match(
        '#^([^:]+):([^\[]+)(\[[0-9+]\])?$#', 
        $child->attribute[$this->namespaces[XMLNS_XSI].':type'],
        $regs
      )) {
        // E.g.: SOAP-ENV:Fault
        $regs= array(0, 'xsd', 'string');
      }

      // SOAP-ENC:arrayType="xsd:anyType[4]"
      if (isset($child->attribute[$this->namespaces[XMLNS_SOAPENC].':arrayType'])) {
        $regs[1]= $child->attribute[$this->namespaces[XMLNS_SOAPENC].':arrayType'];
        $regs[2]= 'Array';
      }

      switch (strtolower($regs[2])) {
        case 'array':
          
          // Check for specific type information
          list($ns, $typeSpec)= explode(':', $regs[1]);
          if (2 == sscanf($typeSpec, '%[^[][%d]', $childType, $length) && 'anyType' != $childType) {

            // Arrays of XP objects
            // ~~~~~~~~~~~~~~~~~~~~
            // <item
            //  xsi:type="SOAP-ENC:Array"
            //  xmlns:xp="http://xp-framework.net/xmlns/xp"
            //  SOAP-ENC:arrayType="xp:de.schlund.db.irc.IrcChannel[2]"
            // >
            // 
            // vs.
            //
            // Arrays of other types
            // ~~~~~~~~~~~~~~~~~~~~~
            // <item SOAP-ENC:arrayType="xsd:int[4]"/>
            if ($this->namespaces[XMLNS_XP] == $ns) {
              try {
                $result= Collection::forClass(XPClass::forName($childType)->getName());
              } catch (ClassNotFoundException $e) {
                $result= Collection::forClass('lang.Object');
                $result->__qname= $childType;
              }
              
              foreach ($this->_recurseData($child, FALSE, 'ARRAY') as $val) {
                $result->add($val);
              }
              break;
            } else for ($i= 0; $i < $length; ++$i) {
              $child->children[$i]->setAttribute($this->namespaces[XMLNS_XSI].':type', $ns.':'.$childType);
            }
          }
          
          // Break missing intentionally
        case 'vector':
          $result= $this->_recurseData($child, FALSE, 'ARRAY');
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
            $key= $item->children[0]->getContent($this->getEncoding(), $this->namespaces);
            $result[$key]= ((empty($item->children[1]->children) && !isset($item->children[1]->attribute['href']))
              ? $item->children[1]->getContent($this->getEncoding(), $this->namespaces)
              : $this->unmarshall($item->children[1], 'MAP')
            );
          }
          break;

        case 'soapstruct':
        case 'struct':      
        case 'ur-type':
          $result= $this->_recurseData($child, TRUE, 'HASHMAP');
          break;
          
        default:
          if (!empty($child->children)) {
            if ($this->namespaces[XMLNS_XSD] == $regs[1]) {
              $result= $this->_recurseData($child, TRUE, 'STRUCT');
              break;
            }

            // Check for mapping
            //
            // XP objects
            // ~~~~~~~~~~
            // <item xmlns:xp="http://xp-framework.net/xmlns/xp" xsi:type="xp:de.schlund.db.irc.IrcChannel"/>        
            //
            // For other objects, check SOAPMapping registry
            if ($this->namespaces[XMLNS_XP] == $regs[1]) {
              try {
                $xpclass= XPClass::forName($regs[2]);
              } catch (ClassNotFoundException $e) {
                $xpclass= NULL;
              }
            } else {
            
              // TBD: Fix mapping passing when SOAPMessage was build from
              // SOAPTransport::retrieve() function which currently doesn't
              // care about mapping and $mapping is just an empty array.
              if ($this->mapping) {
                $xpclass= $this->mapping->classFor(new QName(array_search($regs[1], $this->namespaces), $regs[2]));
              } else {
                $xpclass= NULL;
              }
            }
            
            if ($xpclass) {
              $result= $xpclass->newInstance();
            } else {
              $result= new Object();
              $result->__qname= $regs[2];
            }
            foreach ($this->_recurseData($child, TRUE, 'OBJECT') as $key => $val) {
              $result->$key= $val;
            }
            break;
          }

          $result= $child->getContent($this->getEncoding(), $this->namespaces);
      }

      return $result;
    }

    /**
     * Recursively unmarshall data
     *
     * @param   xml.Node node
     * @param   bool names default FALSE
     * @param   string context default NULL
     * @param   array mapping
     * @return  mixed data
     */    
    protected function _recurseData($node, $names= FALSE, $context= NULL) {
      if (empty($node->children)) {
        $a= array();
        return $a;
      }

      foreach ($node->attribute as $key => $val) {
        if (0 != strncmp('xmlns:', $key, 6)) continue;
        $this->namespaces[$val]= substr($key, 6);
      }
      
      $results= array();
      for ($i= 0, $s= sizeof($node->children); $i < $s; $i++) {
        $key= $i;
        if ($names) {
          $key= substr($node->children[$i]->name, intval(strpos($node->children[$i]->name, ':')));
        }
        // More than one result, so store it in array
        if (isset($results[$key])) {
          // 0 isn't allowed as node-name, so we only check for [0] here
          if (!isset($results[$key][0])) $results[$key]= array($results[$key]);

          $results[$key][]= $this->unmarshall(
            $node->children[$i], 
            $context
          );
        } else {
          $results[$key]= $this->unmarshall(
            $node->children[$i], 
            $context
          );
        }
      }
      return $results;
    }

    /**
     * Set fault
     *
     * @param   int faultcode
     * @param   string faultstring
     * @param   string faultactor default NULL
     * @param   mixed detail default NULL
     */    
    public function setFault($faultcode, $faultstring, $faultactor= NULL, $detail= NULL) {
      $this->root->children[0]->children[0]= XPSoapNode::fromObject(new CommonSoapFault(
        $faultcode,
        $faultstring,
        $faultactor,
        $detail
      ), 'SOAP-ENV:Fault', $this->mapping);
      $this->root->children[0]->children[0]->name= 'SOAP-ENV:Fault';
    }

    /**
     * Construct a SOAP message from a string
     *
     * <code>
     *   $msg= SOAPMessage::fromString('<SOAP-ENV:Envelope>...</SOAP-ENV:Envelope>');
     * </code>
     *
     * @param   string string
     * @return  xml.Tree
     */
    public static function fromString($string) {
      return parent::fromString($string, 'XPSoapMessage');
    }

    /**
     * Construct a SOAP message from a file
     *
     * <code>
     *   $msg= SOAPMessage::fromFile(new File('foo.soap.xml');
     * </code>
     *
     * @param   io.File file
     * @return  xml.Tree
     */ 
    public static function fromFile($file) {
      return parent::fromFile($file, 'XPSoapMessage');
    }
    
    /**
     * Inspect the given node whether it contains any
     * namespace declarations. If a declaration is found,
     * register the new namespace alias in the namespaces
     * list.
     *
     * @param   xml.SOAPNode node
     */
    protected function _retrieveNamespaces($node) {
      foreach ($node->attribute as $key => $val) {
        if (0 != strncmp('xmlns:', $key, 6)) continue;
        $this->namespaces[$val]= substr($key, 6);
      }
    }
    
    /**
     * Retrieve header element or return FALSE if no header
     * exists.
     *
     * @return  xml.SOAPNode
     */
    protected function _headerElement() {
      
      // The header element must - if it exists - be the first child
      // of the SOAP envelope.
      $this->_retrieveNamespaces($this->root);
      
      if (0 == strcasecmp(
        $this->root->children[0]->getName(),
        $this->namespaces[XMLNS_SOAPENV].':Header'
      )) return $this->root->children[0];
      
      return FALSE;
    }

    /**
     * Retrieve body element
     *
     * @return  xml.SOAPNode
     * @throws  lang.FormatException in case the body element could not be found
     */    
    public function _bodyElement() {

      // Look for namespaces in the root node
      $this->_retrieveNamespaces($this->root);
      
      // Search for the body node. For usual, this will be the first element,
      // but some SOAP clients may include a header node (which we silently 
      // ignore for now).
      for ($i= 0, $s= sizeof($this->root->children); $i < $s; $i++) {
        if (0 == strcasecmp(
          $this->root->children[$i]->getName(), 
          $this->namespaces[XMLNS_SOAPENV].':Body'
        )) return $this->root->children[$i];
      }

      throw new FormatException('Could not locate Body element');
    }

    /**
     * Get fault
     *
     * @return  webservices.soap.CommonSoapFault or NULL if none exists
     */
    public function getFault() {
      if ($body= $this->_bodyElement()) {
        if (0 != strcasecmp(
          $body->children[0]->getName(), 
          $this->namespaces[XMLNS_SOAPENV].':Fault'
        )) return NULL;

        $return= $this->_recurseData($body->children[0], TRUE, 'OBJECT', array());
        // DEBUG Console::writeLine('RETURN >>> ', var_export($return, 1), '***'); // DEBUG
        return new CommonSoapFault(
          isset($return['faultcode'])   ? $return['faultcode']    : '',
          isset($return['faultstring']) ? $return['faultstring']  : '',
          isset($return['faultactor'])  ? $return['faultactor']   : '',
          isset($return['detail'])      ? $return['detail']       : ''
        );
      }
    }
    
    /**
     * Get data
     *
     * @param   string context default 'ENUM'
     * @param   webservices.soap.xp.XPSoapMapping mapping
     * @return  mixed data
     * @throws  lang.FormatException in case no XMLNS_SOAPENV:Body was found
     */
    public function getData($context= 'ENUM') {
      if ($body= $this->_bodyElement()) {
        if ($body->children[0]->getName() == $this->namespaces[XMLNS_SOAPENV].':Fault') {
          $n= NULL; return $n;
        }
        return $this->_recurseData($body->children[0], FALSE, $context, $this->mapping);
      }
    }
    
    /**
     * Retrieve string representation of message as used in the
     * protocol.
     *
     * @return  string
     */
    public function serializeData() {
      return $this->getDeclaration()."\n".$this->getSource(0);
    }
    
    /**
     * Get headers from envelope.
     *
     * @return  webservices.soap.xp.XPSoapHeaderElement[]
     */
    public function getHeaders() {
      if (!($h= $this->_headerElement())) return NULL;
      
      // Go through all children
      $headers= array();
      foreach (array_keys($h->children) as $idx) {
        $headers[]= XPSoapHeaderElement::fromNode(
          $h->children[$idx], 
          $this->namespaces,
          $this->encoding
        );
      }
      
      return $headers;
    }

    /**
     * Set Class
     *
     * @param   string class
     */
    public function setHandlerClass($class) {
      $this->class= $class;
      
      // Needed in case of a SOAP fault
      $this->action= $class;
    }

    /**
     * Get Class
     *
     * @return  string
     */
    public function getHandlerClass() {
      return $this->class;
    }

    /**
     * Set Method
     *
     * @param   string method
     */
    public function setMethod($method) {
      $this->method= $method;
    }

    /**
     * Get Method
     *
     * @return  string
     */
    public function getMethod() {
      return $this->method;
    }
  } 
?>
