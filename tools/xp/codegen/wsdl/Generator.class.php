<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  $package= 'xp.codegen.wsdl';
  
  uses(
    'xp.codegen.AbstractGenerator',
    'xml.DomXSLProcessor', 
    'text.StringTokenizer',
    'io.File', 
    'io.Folder',
    'peer.http.HttpUtil',
    'peer.http.HttpConnection'
  );

  /**
   * WSDL
   * ====
   * This utility generates SOAP client classes from a given WSDL
   *
   * Usage:
   * <pre>
   *   $ cgen ... wsdl {uri} [-p {package}] [-P {prefix}] [-l {language}]
   * </pre>
   *
   * Options
   * -------
   * <ul>
   *   <li>package: The package name, default "soap"</li>
   *   <li>prefix: What to prefix generated classes with, defaults to empty</li>
   *   <li>language: Language to generate, defaults to "xp5"</li>
   * </ul>
   *
   * Languages
   * ---------
   * The following languages are supported: xp5
   *
   * @purpose  Code generator
   */
  class xp·codegen·wsdl·Generator extends AbstractGenerator {
    const 
      BOUNDARY   = 'd4c3$bd1e091e.e245bfe04';
    
    protected
      $uri       = '',
      $processor = NULL,
      $package   = NULL;
    
    /**
     * Constructor
     *
     * @param   util.cmd.ParamString args
     */
    public function __construct(ParamString $args) {
      $this->uri= $args->value(0);
      $this->package= $args->value('package', 'p', 'soap');
      $this->processor= new DomXSLProcessor();
      $this->processor->setXSLBuf($this->getClass()->getPackage()->getResource($args->value('lang', 'l', 'xp5.php').'.xsl'));
      $this->processor->setParam('collection', $this->package);
      $this->processor->setParam('prefix', $args->value('prefix', 'P', ''));
      $this->processor->setParam('boundary', self::BOUNDARY);
    }
    
    /**
     * Fetches WSDL file
     *
     */
    #[@target]
    public function fetchWsdl() {
      if (
        0 == strncmp('https://', $this->uri, 8) ||
        0 == strncmp('http://', $this->uri, 7)
      ) {
        return HttpUtil::get(new HttpConnection(new URL($this->uri)));
      } else {
        return FileUtil::getContents(new File($this->uri));
      }
    }

    /**
     * Apply XSL stylesheet
     *
     */
    #[@target(input= 'fetchWsdl')]
    public function applyStylesheet($wsdl) {
      $this->processor->setXMLBuf($wsdl);
      $this->processor->run();
   
      return $this->processor->output();
    }

    /**
     * Generate sourcecode
     *
     */
    #[@target(input= array('applyStylesheet', 'output'))]
    public function parseBody($multiPart, $output) {
      $inHeaders= FALSE;
      $nextPart= '------_=_NextPart_'.self::BOUNDARY;
      
      $st= new StringTokenizer($multiPart, "\r\n");
      $out= '';
      while ($st->hasMoreTokens()) {
        $t= $st->nextToken();

        if (0 == strncmp($nextPart, $t, strlen($nextPart))) {
          $inHeaders= TRUE;
          $out && $output->append(strtr($name, '.', '/').xp::CLASS_FILE_EXT, $out);
          continue;
        }
        
        // Header state
        if ($inHeaders) {
          if (0 == strlen($t)) {    // End of headers
            sscanf($headers['Content-Type'], '%[^;]; name="%[^"]"', $type, $name);
            $out= '';
            $inHeaders= FALSE; 
            continue;
          }

          sscanf($t, "%[^:]: %[^\r]", $header, $value);
          $headers[$header]= $value;
          continue;
        }

        $out.= $t."\n";
      }
    }

    /**
     * Creates a string representation of this generator
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'['.$this->uri.']';
    }
  }
?>
