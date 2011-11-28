<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('rdbms.tds.TdsProtocol');

  /**
   * TDS V5 protocol implementation
   *
   */
  class TdsV5Protocol extends TdsProtocol {

    /**
     * Connect
     *
     * @param   string user
     * @param   string password
     * @throws  io.IOException
     */
    public function login($user, $password) {
     $packet= pack('a30Ca30Ca30Ca30C',
        'localhost', min(30, strlen('localhost')),
        $user, min(30, strlen($user)),
        $password, min(30, strlen($password)),
        (string)getmypid(), min(30, strlen(getmypid()))
      );

      $packet.= pack('CCCCCCCCCN',
        0x03, 0x01, 0x06, 0x0a, 0x09, 0x01,   // magic bytes (1)
        0,                                    // connection->bulk_copy ?
        0x00, 0x00,                           // magic bytes (2)
        0x00                                  // 4 null bytes for TDS5.0
      );

      $packet.= pack('CCCa30Ca30C',
        0x00, 0x00, 0x00,                    // magic bytes (3)
        $this->getClassName(), min(30, strlen($this->getClassName())),
        'localhost', min(30, strlen('localhost'))
      );

      // Put password2
      if (strlen($password) > 253) {
        throw new IllegalArgumentException('Password length must not exceed 253 bytes.');
      }
      $packet.= pack('xCa253C', strlen($password), $password, strlen($password)+ 2);

      // Protocol & program version (TDS 5.0)
      $packet.= pack('CCCC', 0x05, 0x00, 0x00, 0x00); // Protocol
      $packet.= pack('a10C', 'Ct-Library', strlen('Ct-Library')); // Client library name
      $packet.= pack('CCCC', 0x05, 0x00, 0x00, 0x00); // Program


      $packet.= pack('CCC', 0x00, 0x0d, 0x11);        // Magic bytes (4)
      $packet.= pack('a30CC',
        'us_english',                                 // language,
        strlen('us_english'),                         // length of language
        0x00                                          // "connection->suppress_language"
      );
      $packet.= pack('xx');                           // Magic bytes (5)
      $packet.= pack('C', 0);                         // connection->encryption_level (1 / 0)
      $packet.= pack('xxxxxxxxxx');                   // Magic bytes (6)

      // Char set
      $packet.= pack('a30CC', 'iso_1', strlen('iso_1'), 1);

      // Network packet size (in text!)
      $packet.= pack('a6C', '512', strlen('512'));

      // TDS 5.0 specific end
      $packet.= pack('xxxx');
      $packet.= pack('C', 0xE2);                      // 0xE2 = 226 = TDS_CAPABILITY_TOKEN
      $packet.= pack('n', 22);                        // 22 = TDS_MAX_CAPABILITY

      // TODO: Capability tokens should not be hardcoded, but meaningful;
      // anyways, this works.
      $packet.= pack('CCCCCCCCCCCCCCCCCCxxxx',
        0x01, 0x07, 0x00, 0x60, 0x81, 0xcf, 0xFF, 0xFE, 0x3e,
        0x02, 0x07, 0x00, 0x00, 0x00, 0x78, 0xc0, 0x00, 0x00
      );

      // Login
      $this->stream->write(self::MSG_LOGIN, $packet);
    }
  }
?>
