<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'org.codehaus.stomp.frame';
  abstract class org·codehaus·stomp·frame·Frame extends Object {
    protected
      $headers  = array(),
      $body     = NULL;

    public abstract function command();

    public function requiresImmediateResponse() {
      return $this->hasHeader('receipt');
    }

    public function getHeaders() {
      return $this->headers;
    }

    public function getHeader($key) {
      if (!isset($this->headers[$key])) throw new IllegalArgumentException(
        'No such header "'.$key.'"'
      );
      return $this->headers[$key];
    }

    public function addHeader($key, $value) {
      $this->headers[$key]= $value;
    }

    public function hasHeader($key) {
      return isset($this->headers[$key]);
    }

    public function getBody() {
      return $this->body;
    }

    public function setBody($data) {
      $this->body= $data;
    }

    public function fromWire(InputStreamReader $in) {

      // Read headers
      $line= $in->readLine();
      while (0 != strlen($line)) {
        list($key, $value)= explode(':', $line, 2);
        $this->addHeader($key, $value);

        // Next line
        $line= $in->readLine();
      }

      // Now, read payload
      if ($this->hasHeader('content-length')) {

        // If content-length is given, read that many bytes as body from
        // stream and assert that it is followed by a chr(0) byte.
        $data= $in->read($this->getHeader('content-length'));

        if ("\0\n" != $in->read(2)) throw new ProtocolException(
          'Expected chr(0) and newline after frame w/ given content-length'
        );

      } else {
        $data= '';

        // HACK: Readline of StringReader does not return the newline char
        // itself, so re-add it. At the end of data, newline and chr(0)
        // must be removed, though.
        $line= $in->readLine();
        while (0 != strlen($line) && "\0" != $line) {
          $data.= $line."\n";
          $line= $in->readLine();
        }
      }

      $this->setBody(rtrim($data, "\n\0"));
    }

    public function write(OutputStreamWriter $out) {
      $out->write($this->command()."\n");

      foreach ($this->getHeaders() as $key => $value) {
        $out->write($key.':'.$value."\n");
      }

      $out->write("\n".$this->getBody().chr(0));
    }

    public function toString() {
      $s= $this->getClassName().'@('.$this->hashCode().") {\n";
      $s.= '  Stomp command=    "'.$this->command()."\"\n";

      foreach ($this->headers as $key => $value) {
        $s.= sprintf("  [%-15s] %s\n", $key, $value);
      }

      $s.= sprintf("  [%-15s] (%d bytes) %s\n",
        'body',
        strlen($this->getBody()),
        $this->getBody()
      );

      return $s.'}';
    }
  }
?>
