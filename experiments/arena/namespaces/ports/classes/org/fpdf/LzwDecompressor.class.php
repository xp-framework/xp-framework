<?php
/* This class is part of the XP framework
 *
 * $Id: LzwDecompressor.class.php 10693 2007-06-30 15:43:06Z friebe $ 
 */

  namespace org::fpdf;

  /**
   * Decompress LZW-compressed data. Based on GIF Util - (C) 2003 Yamasoft (S/C)
   * 
   * <pre>
   * // GIF Util - (C) 2003 Yamasoft (S/C)
   * // http://www.yamasoft.com
   * // All Rights Reserved
   * // This file can be frelly copied, distributed, modified, updated by anyone under the only
   * // condition to leave the original address (Yamasoft, http://www.yamasoft.com) and this header.
   * </pre>
   *
   * @purpose  GIF support for FPDF class
   */
  class LzwDecompressor extends lang::Object {
    private static
      $MAX_LZW_BITS = 0x1000;
    
    private
      $codeSize, 
      $buf, 
      $curBit, 
      $lastBit, 
      $done, 
      $lastByte;


    /**
     * Decompress data from a file
     *
     * @param   resource fh file handle
     * @return  string
     */
    public function deCompress($fh) {
      $decompressed= '';

      $setCodeSize= ord(fread($fh, 1));
      $this->codeSize= $setCodeSize+ 1;
      $clearCode= 1 << $setCodeSize;
      $endCode= $clearCode+ 1;
      $maxCode= $clearCode+ 2;
      $maxCodeSize= $clearCode << 1;
      $this->buf= range(0, 279);
      $stack= range(0, self::$MAX_LZW_BITS);
      $this->curBit= 0;
      $this->lastBit= 0;
      $this->lastByte= 2;
      $sp= 0;
      $this->done= FALSE;
      $fresh= TRUE;
      
      // Fill next with zeros, vals with i until clearcode is reached, the rest 
      // with zeros
      $next= array_fill(0, self::$MAX_LZW_BITS- 1, 0);
      $vals= array_merge(
        range(0, $clearCode- 1),
        array_fill($clearCode, self::$MAX_LZW_BITS- 1- $clearCode, 0)
      );

      do {
        if ($fresh) {
          $fresh= FALSE;
          do {
            $firstCode= $this->getCode($fh);
            $old= $firstCode;
          } while($firstCode == $clearCode);

          if (($index= $firstCode) < 0) break;
          $decompressed.= chr($index);
          continue;
        } else if ($sp > 0) {
          $sp--;

          if (($index= $stack[$sp]) < 0) break; 
          $decompressed.= chr($index);
          continue;
        }

        while (($code= $this->getCode($fh)) >= 0) {
          if ($code == $clearCode) {

            // Encountered clearing code, reset next, vals and codes
            $next= array_fill(0, self::$MAX_LZW_BITS- 1, 0);
            $vals= array_merge(
              range(0, $clearCode- 1),
              array_fill($clearCode, self::$MAX_LZW_BITS- 1- $clearCode, 0)
            );

            $this->codeSize= $setCodeSize + 1;
            $maxCodeSize= $clearCode << 1;
            $maxCode= $clearCode + 2;
            $sp= 0;
            $old= $firstCode= $this->getCode($fh);

            if (($index= $firstCode) < 0) break 2;
            $decompressed.= chr($index);
            continue 2;
          } else if ($code == $endCode) {
          
            // Encountered endcode, finished!
            return $decompressed;
          } else if ($code >= $maxCode) {
          
            // Begin stacking...
            $in= $code;
            $stack[$sp]= $firstCode;
            $sp++;
            $code= $old;
          } else {
            $in= $code;
          }

          while ($code >= $clearCode) {
            $stack[$sp]= $vals[$code];
            $sp++;

            if ($code == $next[$code]) {
              throw new lang::IllegalStateException('Circular table entry encountered');
            }
            $code= $next[$code];
          }

          $firstCode= $vals[$code];
          $stack[$sp]= $firstCode;
          $sp++;

          if (($code= $maxCode) < self::$MAX_LZW_BITS) {
            $next[$code]= $old;
            $vals[$code]= $firstCode;
            $maxCode++;

            if (($maxCode >= $maxCodeSize) && ($maxCodeSize < self::$MAX_LZW_BITS)) {
              $maxCodeSize*= 2;
              $this->codeSize++;
            }
          }

          $old= $in;
          if ($sp > 0) {
            $sp--;
            if (($index= $stack[$sp]) < 0) break; 
            $decompressed.= chr($index);
            continue 2;
          }
        }

        if (($index= $code) < 0) break;
        $decompressed.= chr($index);
      } while (TRUE);

      throw new lang::IllegalStateException('End code not found: '.$index);
    }

    /**
     * Get a code
     *
     * @param   resource fh
     * @return  int
     */
    private function getCode($fh) {
      if (($this->curBit + $this->codeSize) >= $this->lastBit) {
        if ($this->done) return ($this->curBit >= $this->lastBit) ? 0 : -1;

        $this->buf[0]= $this->buf[$this->lastByte- 2];
        $this->buf[1]= $this->buf[$this->lastByte- 1];

        if ($c= ord(fread($fh, 1))) {
          for($i= 0; $i < $c; $i++) {
            $this->buf[2+ $i]= ord(fread($fh, 1));
          }
        } else {
          $this->done= TRUE;
        }

        $this->lastByte= 2 + $c;
        $this->curBit= ($this->curBit - $this->lastBit) + 16;
        $this->lastBit= (2 + $c) << 3;
      }

      $r= 0;
      for ($i= $this->curBit, $j= 0; $j < $this->codeSize; $i++, $j++) {
        $r |= (($this->buf[$i >> 3] & (1 << ($i % 8))) != 0) << $j;
      }

      $this->curBit += $this->codeSize;
      return $r;
    }
  }
?>
