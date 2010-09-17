<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.streams.TextReader', 'text.csv.AbstractCsvProcessor', 'text.csv.CsvFormat');

  /**
   * Abstract base class
   *
   * @see   xp://text.csv.CsvListReader
   * @see   xp://text.csv.CsvObjectReader
   * @see   xp://text.csv.CsvBeanReader
   */
  abstract class CsvReader extends AbstractCsvProcessor {
    protected $reader= NULL;
    protected $delimiter= ';';
    protected $quote= '"';
    protected $line= 0;

    /**
     * Creates a new CSV reader reading data from a given TextReader
     *
     * @param   io.streams.TextReader reader
     * @param   text.csv.CsvFormat format
     */
    public function  __construct(TextReader $reader, CsvFormat $format= NULL) {
      $this->reader= $reader;
      with ($f= $format ? $format : CsvFormat::$DEFAULT); {
        $this->delimiter= $f->getDelimiter();
        $this->quote= $f->getQuote();
      }
    }

    /**
     * Get header line
     *
     * @return  string[]
     * @throws  lang.IllegalStateException if reading has already started
     */
    public function getHeaders() {
      if ($this->line > 0) {
        throw new IllegalStateException('Cannot read headers - already started reading data');
      }
      return $this->readValues(TRUE);
    }

    /**
     * Raise an exception
     *
     * @param   string message
     */
    protected function raise($message) {
      throw new FormatException(sprintf('Line %d: %s', $this->line, $message));
    }
    
    /**
     * Reads values
     *
     * @param   bool raw
     * @return  string[]
     * @throws  lang.FormatException if a formatting error is detected
     */
    protected function readValues($raw= FALSE) {
      if (NULL === ($ln= $this->reader->readLine())) return NULL;

      $line= (string)$ln;
      // Parse line. 
      // * In the easiest form, we have values separated by the delimiter 
      //   character, e.g. "A,B,C".
      // * Whitespace around the values is ignored, "A , B, C" is the same
      //   as "A,B,C".
      // * Values containing the delimiter must be quoted, "'A,B',C,D" 
      //   resembles the list "A,B" "C" and "D".
      // * The quote character must be doubled inside quoted values to be
      //   escaped, e.g. "'He said ''hello'' when he arrived',B,C"
      // * Quoted values may span multiple lines.
      $exception= NULL;
      $values= array(); 
      $v= 0;
      $escape= $this->quote.$this->quote;
      $whitespace= " \t";
      $this->line++;
      $o= 0; $l= strlen($line);
      do {
        $b= $o + strspn($line, $whitespace, $o);                  // Skip leading WS
        if ($b >= $l) {
          $value= '';
        } else if ($this->quote === $line{$b}) {

          // Find end of quoted value (= quote not preceded by quote)
          $q= $b + 1;
          $e= 0;
          $bl= $this->line;
          do {
            $p= strcspn($line, $this->quote, $q);
            $e+= $p;
            if ($q + $p >= $l) {
              if (NULL === ($chunk= $this->reader->readLine())) {
                $this->raise('Unterminated quoted value beginning at line '.$bl);
              }
              $this->line++;
              $line.= "\n".$chunk;
              $q+= $p + 1;
              $e+= 1;
              $l= strlen($line);
              continue;
            } else if ($escape === substr($line, $p + $q, 2)) {
              $q+= $p + 2;
              $e+= 2;
              continue;
            }
            break;
          } while (1);
          $value= str_replace($escape, $this->quote, substr($line, $b + 1, $e));
          $e+= 2;
          $e+= strspn($line, $whitespace, $b+ $e);                // Skip trailing WS
          if ($b + $e < $l && $this->delimiter !== $line{$b + $e}) {
            $this->raise(sprintf(
              'Illegal quoting, expected [%s or <END>], have [%s] beginning at line %d',
              $this->delimiter,
              $line{$b + $e},
              $bl
            ));
          }
        } else {

          // Find end of unquoted value (= delimiter)
          $e= strcspn($line, $this->delimiter, $b);
          $value= rtrim(substr($line, $b, $e), $whitespace);   // Trim trailing WS
        }
        
        // Run processors
        if (!$raw && isset($this->processors[$v])) {
          try {
            $values[$v]= $this->processors[$v]->process($value);
          } catch (Throwable $exception) {
            // Store for later
          }
        } else {
          $values[$v]= $value;
        }
        $v++;
        $o= $b + $e + 1;
      } while ($o <= $l);

      if ($exception) throw $exception;
      return $values;
    }
  }
?>
