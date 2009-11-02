<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'webservices.xmlrpc.XmlRpcClient',
    'webservices.xmlrpc.transport.XmlRpcHttpTransport',
    'com.flickr.api.FlickrInterestingness',
    'xml.meta.Unmarshaller'
  );
  
  define('FLICKR_XMLRPC_ENDPOINT',    'http://www.flickr.com/services/xmlrpc/');

  /**
   * Flickr XmlRpc Client
   *
   * @see       http://www.flickr.com/services/api/
   * @purpose   Flickr client
   */
  class FlickrClient extends XmlRpcClient {
    public
      $apiKey     = '',
      $sharedKey  = '';  

    /**
     * Set ApiKey
     *
     * @param   string apiKey
     */
    public function setApiKey($apiKey) {
      $this->apiKey= $apiKey;
    }

    /**
     * Get ApiKey
     *
     * @return  string
     */
    public function getApiKey() {
      return $this->apiKey;
    }

    /**
     * Set SharedKey
     *
     * @param   string sharedKey
     */
    public function setSharedKey($sharedKey) {
      $this->sharedKey= $sharedKey;
    }

    /**
     * Get SharedKey
     *
     * @return  string
     */
    public function getSharedKey() {
      return $this->sharedKey;
    }
    
    /**
     * Calculates the signature for the given argument array
     *
     * @param   mixed<string,string> arguments[]
     * @return  mixed<string,string>
     */
    public function signArray($arguments) {
      $arguments['api_key']= $this->getApiKey();
      ksort($arguments);

      $signature= '';
      foreach ($arguments as $key => $value) {
        $signature.= $key.$value;
      }
      $arguments['api_sig']= md5($this->getSharedKey().$signature);
      return $arguments;
    }
    
    /**
     * Unserializes xml data
     *
     * @param   string xml
     * @return  mixed
     */
    public function unserialize($xml) {
      $tree= Tree::fromString('<?xml version="1.0" encoding="utf-8"?><data>'.$xml.'</data>');
      return $this->_recurse($tree->root);
    }    
    
    /**
     * Recurses the tree
     *
     * @param   &xml.Node node
     * @return  mixed
     */
    protected function _recurse($node) {
      if (sizeof($node->children)) {
        $ret= array();
        foreach ($node->children as $index => $value) {
          $ret[$value->getName()]= $this->_recurse($node->children[$index]);
        }
        
        return $ret;
      }
      
      if (sizeof($node->attribute)) {
        $ret= array();
        foreach ($node->attribute as $name => $value) {
          $ret[$name]= $value;
        }
        
        return $ret;
      }
      
      return $node->getContent();
    }

    /**
     * Invoke a method on Flickr
     *
     * @param   mixed*
     * @return  mixed
     */
    public function invoke() {
      $args= func_get_args();
      
      $method= array_shift($args);
      $arguments= $this->signArray(array_merge(
        array_shift($args),
        array('method'  => $method)
      ));

      $res= parent::invoke($method, $arguments);
      return $this->unserialize($res[0]);
    }
    
    /**
     * Invoke a method on Flickr, the response is being deserialized
     * into the given expected class.
     *
     * @param   string method
     * @param   mixed args[]
     * @param   string expect
     * @return  Object
     */
    public function invokeExpecting($method, $args, $expect) {
      $arguments= $this->signArray(array_merge(
        $args,
        array('method'  => $method)
      ));

      $res= parent::invoke($method, $arguments);
      
      $return= Unmarshaller::unmarshal($res[0], $expect);
      $return->setClient($this);
      return $return;
    }
    
    /**
     * Fetch interestingness package
     *
     * @return  &com.flickr.api.FlickrInterestingness
     */
    public function getInterestingnessInterface() {
      $if= new FlickrInterestingness();
      $if->setClient($this);
      return $if;
    }    
  }
?>
