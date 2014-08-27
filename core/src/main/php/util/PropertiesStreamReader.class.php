<?php
/* This class is part of the XP framework
 * 
 * $Id$
 */

  uses(
    'io.streams.InputStream',
    'io.streams.TextReader',
    'text.TextTokenizer',
    'util.Properties'
  );

  /**
   * Read util.Properties objects off a stream.
   *
   */
  class PropertiesStreamReader extends Object {
    protected $charset = NULL;

    /**
     * Constructor.
     *
     * @param string charset
     */
    public function __construct($charset= NULL) {
      $this->charset= $charset;
    }

    /**
     * Read stream into properies object
     * 
     * @param  io.streams.InputStream in
     * @return [:[:var]]
     */
    public function read(InputStream $in) {
      $s= new TextTokenizer(new TextReader($in, $this->charset), "\r\n");
      $data= array();

      $section= NULL;
      while ($s->hasMoreTokens()) {
        $t= $s->nextToken();
        $trimmedToken=trim($t);
        if ('' === $trimmedToken) continue;                // Empty lines
        $c= $trimmedToken{0};
        if (';' === $c || '#' === $c) {                            // One line comments
          continue;                    
        } else if ('[' === $c) {
          if (FALSE === ($p= strrpos($trimmedToken, ']'))) {
            throw new FormatException('Unclosed section "'.$trimmedToken.'"');
          }
          $section= substr($trimmedToken, 1, $p- 1);
          $data[$section]= array();
        } else if (FALSE !== ($p= strpos($t, '='))) {
          $key= trim(substr($t, 0, $p));
          $value= ltrim(substr($t, $p+ 1));
          if (strlen($value) && ('"' === ($q= $value{0}))) {       // Quoted strings
            if (FALSE === ($p= strrpos($value, $q, 1))) {
              $value= substr($value, 1)."\n".$s->nextToken($q);
            } else {
              $value= substr($value, 1, $p- 1);
            }
          } else {        // unquoted string
            if (FALSE !== ($p= strpos($value, ';'))) {        // Comments at end of line
              $value= substr($value, 0, $p);
            }
            $value= rtrim($value);
          }
          

          // Arrays and maps: key[], key[0], key[assoc]
          if (']' === substr($key, -1)) {
            if (FALSE === ($p= strpos($key, '['))) {
              throw new FormatException('Invalid key "'.$key.'"');
            }
            $offset= substr($key, $p+ 1, -1);
            $key= substr($key, 0, $p);
            if (!isset($data[$section][$key])) {
              $data[$section][$key]= array();
            }
            if ('' === $offset) {
              $data[$section][$key][]= $value;
            } else {
              $data[$section][$key][$offset]= $value;
            }
          } else {
            $data[$section][$key]= $value;
          }
        } else if ('' !== trim($t)) {
          throw new FormatException('Invalid line "'.$t.'"');
        }
      }

      return $data;
    }
  }