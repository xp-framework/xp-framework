<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
  class LzwDecompressor extends Object {
    private static
      $MAX_LZW_BITS = 0x1000;
    
    private
      $fresh, 
      $codeSize, 
      $setCodeSize, 
      $maxCode, 
      $maxCodeSize, 
      $firstCode, 
      $oldCode,
      $clearCode, 
      $endCode, 
      $next, 
      $vals, 
      $stack, 
      $sp, 
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

      $this->setCodeSize= ord(fread($fh, 1));
      $this->codeSize= $this->setCodeSize+ 1;
      $this->clearCode= 1 << $this->setCodeSize;
      $this->endCode= $this->clearCode+ 1;
      $this->maxCode= $this->clearCode+ 2;
      $this->maxCodeSize= $this->clearCode << 1;
      $this->buf= range(0, 279);
      $this->stack= range(0, self::$MAX_LZW_BITS);
      $this->curBit= 0;
      $this->lastBit= 0;
      $this->lastByte= 2;
      $this->sp= 0;
      $this->done= FALSE;
      $this->fresh= TRUE;
      
      // Fill next with zeros, vals with i until clearcode is reached, the rest 
      // with zeros
      $this->next= array_fill(0, self::$MAX_LZW_BITS- 1, 0);
      $this->vals= array_merge(
        range(0, $this->clearCode- 1),
        array_fill($this->clearCode, self::$MAX_LZW_BITS- 1- $this->clearCode, 0)
      );

      do {
        if ($this->fresh) {
          $this->fresh= FALSE;
          do {
            $this->firstCode= $this->getCode($fh);
            $this->oldCode= $this->firstCode;
          } while($this->firstCode == $this->clearCode);

          if (($iIndex= $this->firstCode) < 0) break;
          $decompressed.= chr($iIndex);
          continue;
        } else if ($this->sp > 0) {
          $this->sp--;

          if (($iIndex= $this->stack[$this->sp]) < 0) break; 
          $decompressed.= chr($iIndex);
          continue;
        }

        while (($code= $this->getCode($fh)) >= 0) {
          if ($code == $this->clearCode) {

            // Encountered clearing code, reset next, vals and codes
            $this->next= array_fill(0, self::$MAX_LZW_BITS- 1, 0);
            $this->vals= array_merge(
              range(0, $this->clearCode- 1),
              array_fill($this->clearCode, self::$MAX_LZW_BITS- 1- $this->clearCode, 0)
            );

            $this->codeSize= $this->setCodeSize + 1;
            $this->maxCodeSize= $this->clearCode << 1;
            $this->maxCode= $this->clearCode + 2;
            $this->sp= 0;
            $this->oldCode= $this->firstCode= $this->getCode($fh);

            if (($iIndex= $this->firstCode) < 0) break;
            $decompressed.= chr($iIndex);
            continue 2;
          } else if ($code == $this->endCode) {
          
            // Encountered endcode, finished!
            return $decompressed;
          } else if ($code >= $this->maxCode) {
          
            // Begin stacking...
            $in= $code;
            $this->stack[$this->sp]= $this->firstCode;
            $this->sp++;
            $code = $this->oldCode;
          } else {
            $in= $code;
          }

          while ($code >= $this->clearCode) {
            $this->stack[$this->sp]= $this->vals[$code];
            $this->sp++;

            if ($code == $this->next[$code]) {
              throw new IllegalStateException('Circular table entry encountered');
            }
            $code= $this->next[$code];
          }

          $this->firstCode= $this->vals[$code];
          $this->stack[$this->sp]= $this->firstCode;
          $this->sp++;

          if (($code= $this->maxCode) < self::$MAX_LZW_BITS) {
            $this->next[$code]= $this->oldCode;
            $this->vals[$code]= $this->firstCode;
            $this->maxCode++;

            if (($this->maxCode >= $this->maxCodeSize) && ($this->maxCodeSize < self::$MAX_LZW_BITS)) {
              $this->maxCodeSize*= 2;
              $this->codeSize++;
            }
          }

          $this->oldCode= $in;
          if ($this->sp > 0) {
            $this->sp--;
            if (($iIndex= $this->stack[$this->sp]) < 0) break; 
            $decompressed.= chr($iIndex);
            continue 2;
          }
        }

        if (($iIndex= $code) < 0) break;
        $decompressed.= chr($iIndex);
      } while (TRUE);

      throw new IllegalStateException('End code not found');
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
