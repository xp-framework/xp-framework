<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  uses(
    'util.cmd.Command',
    'xml.DomXSLProcessor', 
    'text.StringTokenizer',
    'io.File', 
    'io.Folder',
    'peer.http.HttpUtil',
    'peer.http.HttpConnection'
  );

  /**
   * Generate SOAP client from WSDL files. Also takes care of
   * multiple classes within the WSDL
   *
   * @purpose   WSDL generator
   */
  class WsdlStubGenerator extends Command {
    const 
      BOUNDARY      = '------_=_NextPart_d4c3$bd1e091e.e245bfe04';
    
    protected
      $wsdl       = NULL,
      $xsl        = NULL,
      $package    = NULL;
    
    /**
     * Set WSDL file to process
     *
     * @param   string filename
     */
    #[@arg(position= 0)]
    public function setWsdl($w) {
      if (
        0 == strncmp('https://', $w, 8) ||
        0 == strncmp('http://', $w, 7)
      ) {
        $this->wsdl= HttpUtil::get(new HttpConnection(new URL($w)));
      } else if (is_file($w)) {
        $this->wsdl= FileUtil::getContents(new File($w));
      }
      
      if (NULL === $this->wsdl)
        throw new IllegalArgumentException('Could not load WSDL information from '.$w);
    }
    
    /**
     * Set package to put generated classes into.
     *
     * @param   string package
     */
    #[@arg(position= 1)]
    public function setPackage($c) {
      $this->package= $c;
    }
    
    /**
     * Run
     *
     */
    public function run() {
      $proc= new DomXSLProcessor();
      $proc->setParam('collection', $this->package);
      $proc->setXSLBuf($this->getClass()->getPackage()->getResource('wsdl.xsl'));
      $proc->setXMLBuf($this->wsdl);

      $proc->run();

      // Parse multi-part body
      $inHeaders= FALSE;
      $out= NULL;
      $st= new StringTokenizer($proc->output(), "\n");
      while ($st->hasMoreTokens()) {
        $t= $st->nextToken();

        if (0 == strncmp(self::BOUNDARY, $t, strlen(self::BOUNDARY))) {
          $inHeaders= TRUE;
          continue;
        }

        // Header state
        if ($inHeaders) {
          if (0 == strlen($t)) {    // End of headers
            $out && $out->close();

            sscanf($headers['Content-Type'], '%[^;]; name="%[^"]"', $type, $name);

            $this->out->writeLine('---> ', $name);
            $out= new File(strtr($name, '.', '/').'.class.php');

            $contained= new Folder($out->getPath());
            $contained->exists() || $contained->create();
            $out->open(FILE_MODE_WRITE);
            $inHeaders= FALSE; 
            continue;
          }

          list($header, $value)= explode(': ', $t, 2);
          $headers[$header]= $value;
          continue;
        }

        $out->write($t."\n");
      }
    }
  }
?>
