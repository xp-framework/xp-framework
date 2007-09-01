<?php
/* This class is part of the XP framework
 *
 * $Id: ID3Genre.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace de::fraunhofer::mp3;

  define('ID3_GENRE_BLUES',                     0);  
  define('ID3_GENRE_CLASSIC_ROCK',              1);  
  define('ID3_GENRE_COUNTRY',                   2);  
  define('ID3_GENRE_DANCE',                     3);  
  define('ID3_GENRE_DISCO',                     4);  
  define('ID3_GENRE_FUNK',                      5);  
  define('ID3_GENRE_GRUNGE',                    6);  
  define('ID3_GENRE_HIP_HOP',                   7);  
  define('ID3_GENRE_JAZZ',                      8);  
  define('ID3_GENRE_METAL',                     9);  
  define('ID3_GENRE_NEW_AGE',                  10);
  define('ID3_GENRE_OLDIES',                   11);
  define('ID3_GENRE_OTHER',                    12);
  define('ID3_GENRE_POP',                      13);
  define('ID3_GENRE_RNB',                      14);
  define('ID3_GENRE_RAP',                      15);
  define('ID3_GENRE_REGGAE',                   16);
  define('ID3_GENRE_ROCK',                     17);
  define('ID3_GENRE_TECHNO',                   18);
  define('ID3_GENRE_INDUSTRIAL',               19);
  define('ID3_GENRE_ALTERNATIVE',              20);
  define('ID3_GENRE_SKA',                      21);
  define('ID3_GENRE_DEATH_METAL',              22);
  define('ID3_GENRE_PRANKS',                   23);
  define('ID3_GENRE_SOUNDTRACK',               24);
  define('ID3_GENRE_EURO_TECHNO',              25);
  define('ID3_GENRE_AMBIENT',                  26);
  define('ID3_GENRE_TRIP_HOP',                 27);
  define('ID3_GENRE_VOCAL',                    28);
  define('ID3_GENRE_JAZZ_FUNK',                29);
  define('ID3_GENRE_FUSION',                   30);
  define('ID3_GENRE_TRANCE',                   31);
  define('ID3_GENRE_CLASSICAL',                32);
  define('ID3_GENRE_INSTRUMENTAL',             33);
  define('ID3_GENRE_ACID',                     34);
  define('ID3_GENRE_HOUSE',                    35);
  define('ID3_GENRE_GAME',                     36);
  define('ID3_GENRE_SOUND_CLIP',               37);
  define('ID3_GENRE_GOSPEL',                   38);
  define('ID3_GENRE_NOISE',                    39);
  define('ID3_GENRE_ALTERNATIVE_ROCK',         40);
  define('ID3_GENRE_BASS',                     41);
  define('ID3_GENRE_SOUL',                     42);
  define('ID3_GENRE_PUNK',                     43);
  define('ID3_GENRE_SPACE',                    44);
  define('ID3_GENRE_MEDITATIVE',               45);
  define('ID3_GENRE_INSTRUMENTAL_POP',         46);
  define('ID3_GENRE_INSTRUMENTAL_ROCK',        47);
  define('ID3_GENRE_ETHNIC',                   48);
  define('ID3_GENRE_GOTHIC',                   49);
  define('ID3_GENRE_DARKWAVE',                 50);
  define('ID3_GENRE_TECHNO_INDUSTRIAL',        51);
  define('ID3_GENRE_ELECTRONIC',               52);
  define('ID3_GENRE_POP_FOLK',                 53);
  define('ID3_GENRE_EURODANCE',                54);
  define('ID3_GENRE_DREAM',                    55);
  define('ID3_GENRE_SOUTHERN_ROCK',            56);
  define('ID3_GENRE_COMEDY',                   57);
  define('ID3_GENRE_CULT',                     58);
  define('ID3_GENRE_GANGSTA',                  59);
  define('ID3_GENRE_TOP_40',                   60);
  define('ID3_GENRE_CHRISTIAN_RAP',            61);
  define('ID3_GENRE_POP_FUNK',                 62);
  define('ID3_GENRE_JUNGLE',                   63);
  define('ID3_GENRE_NATIVE_US',                64);
  define('ID3_GENRE_CABARET',                  65);
  define('ID3_GENRE_NEW_WAVE',                 66);
  define('ID3_GENRE_PSYCHADELIC',              67);
  define('ID3_GENRE_RAVE',                     68);
  define('ID3_GENRE_SHOWTUNES',                69);
  define('ID3_GENRE_TRAILER',                  70);
  define('ID3_GENRE_LO_FI',                    71);
  define('ID3_GENRE_TRIBAL',                   72);
  define('ID3_GENRE_ACID_PUNK',                73);
  define('ID3_GENRE_ACID_JAZZ',                74);
  define('ID3_GENRE_POLKA',                    75);
  define('ID3_GENRE_RETRO',                    76);
  define('ID3_GENRE_MUSICAL',                  77);
  define('ID3_GENRE_ROCK_N_ROLL',              78);
  define('ID3_GENRE_HARD_ROCK',                79);
  define('ID3_GENRE_FOLK',                     80);
  define('ID3_GENRE_FOLK_ROCK',                81);
  define('ID3_GENRE_NATIONAL_FOLK',            82);
  define('ID3_GENRE_SWING',                    83);
  define('ID3_GENRE_FAST_FUSION',              84);
  define('ID3_GENRE_BEBOB',                    85);
  define('ID3_GENRE_LATIN',                    86);
  define('ID3_GENRE_REVIVAL',                  87);
  define('ID3_GENRE_CELTIC',                   88);
  define('ID3_GENRE_BLUEGRASS',                89);
  define('ID3_GENRE_AVANTGARDE',               90);
  define('ID3_GENRE_GOTHIC_ROCK',              91);
  define('ID3_GENRE_PROGRESSIVE_ROCK',         92);
  define('ID3_GENRE_PSYCHEDELIC_ROCK',         93);
  define('ID3_GENRE_SYMPHONIC_ROCK',           94);
  define('ID3_GENRE_SLOW_ROCK',                95);
  define('ID3_GENRE_BIG_BAND',                 96);
  define('ID3_GENRE_CHORUS',                   97);
  define('ID3_GENRE_EASY_LISTENING',           98);
  define('ID3_GENRE_ACOUSTIC',                 99);
  define('ID3_GENRE_HUMOUR',                  100);
  define('ID3_GENRE_SPEECH',                  101);
  define('ID3_GENRE_CHANSON',                 102);
  define('ID3_GENRE_OPERA',                   103);
  define('ID3_GENRE_CHAMBER_MUSIC',           104);
  define('ID3_GENRE_SONATA',                  105);
  define('ID3_GENRE_SYMPHONY',                106);
  define('ID3_GENRE_BOOTY_BASS',              107);
  define('ID3_GENRE_PRIMUS',                  108);
  define('ID3_GENRE_PORN_GROOVE',             109);
  define('ID3_GENRE_SATIRE',                  110);
  define('ID3_GENRE_SLOW_JAM',                111);
  define('ID3_GENRE_CLUB',                    112);
  define('ID3_GENRE_TANGO',                   113);
  define('ID3_GENRE_SAMBA',                   114);
  define('ID3_GENRE_FOLKLORE',                115);
  define('ID3_GENRE_BALLAD',                  116);
  define('ID3_GENRE_POWER_BALLAD',            117);
  define('ID3_GENRE_RHYTMIC_SOUL',            118);
  define('ID3_GENRE_FREESTYLE',               119);
  define('ID3_GENRE_DUET',                    120);
  define('ID3_GENRE_PUNK_ROCK',               121);   
  define('ID3_GENRE_DRUM_SOLO',               122);   
  define('ID3_GENRE_ACAPELLA',                123);   
  define('ID3_GENRE_EURO_HOUSE',              124);   
  define('ID3_GENRE_DANCE_HALL',              125);   
  define('ID3_GENRE_GOA',                     126);
  define('ID3_GENRE_DRUM_N_BASS',             127);
  define('ID3_GENRE_CLUB_HOUSE',              128);
  define('ID3_GENRE_HARDCORE',                129);
  define('ID3_GENRE_TERROR',                  130);
  define('ID3_GENRE_INDIE',                   131);
  define('ID3_GENRE_BRITPOP',                 132);
  define('ID3_GENRE_NEGERPUNK',               133);
  define('ID3_GENRE_POLSK_PUNK',              134);
  define('ID3_GENRE_BEAT',                    135);
  define('ID3_GENRE_CHRISTIAN_GANGSTA_RAP',   136);
  define('ID3_GENRE_HEAVY_METAL',             137);
  define('ID3_GENRE_BLACK_METAL',             138);
  define('ID3_GENRE_CROSSOVER',               139);
  define('ID3_GENRE_CONTEMPORARY_CHRISTIAN',  140);
  define('ID3_GENRE_CHRISTIAN_ROCK',          141);
  define('ID3_GENRE_MERENGUE',                142);
  define('ID3_GENRE_SALSA',                   143);
  define('ID3_GENRE_TRASH_METAL',             144);
  define('ID3_GENRE_ANIME',                   145);
  define('ID3_GENRE_JPOP',                    146);
  define('ID3_GENRE_SYNTHPOP',                147);

  /**
   * Represents a genre
   *
   */
  class ID3Genre extends lang::Object {
    public
      $id= -1;
      
    /**
     * Constructor
     *
     * @param   int id
     */
    public function __construct($id) {
      $this->id= $id;
      
    }
      
    /**
     * Create a string representation - returns 'ID_GENRE_UNKNOWN' when 
     * genre id is unknown
     *
     * @return  string
     */
    public function toString() {
      static $genre= array(
        ID3_GENRE_BLUES                   => 'ID_GENRE_BLUES',         
        ID3_GENRE_CLASSIC_ROCK            => 'ID_GENRE_CLASSIC_ROCK',    
        ID3_GENRE_COUNTRY                 => 'ID_GENRE_COUNTRY',       
        ID3_GENRE_DANCE                   => 'ID_GENRE_DANCE',         
        ID3_GENRE_DISCO                   => 'ID_GENRE_DISCO',         
        ID3_GENRE_FUNK                    => 'ID_GENRE_FUNK',          
        ID3_GENRE_GRUNGE                  => 'ID_GENRE_GRUNGE',        
        ID3_GENRE_HIP_HOP                 => 'ID_GENRE_HIP_HOP',       
        ID3_GENRE_JAZZ                    => 'ID_GENRE_JAZZ',          
        ID3_GENRE_METAL                   => 'ID_GENRE_METAL',         
        ID3_GENRE_NEW_AGE                 => 'ID_GENRE_NEW_AGE',           
        ID3_GENRE_OLDIES                  => 'ID_GENRE_OLDIES',            
        ID3_GENRE_OTHER                   => 'ID_GENRE_OTHER',             
        ID3_GENRE_POP                     => 'ID_GENRE_POP',               
        ID3_GENRE_RNB                     => 'ID_GENRE_RNB',               
        ID3_GENRE_RAP                     => 'ID_GENRE_RAP',               
        ID3_GENRE_REGGAE                  => 'ID_GENRE_REGGAE',            
        ID3_GENRE_ROCK                    => 'ID_GENRE_ROCK',              
        ID3_GENRE_TECHNO                  => 'ID_GENRE_TECHNO',            
        ID3_GENRE_INDUSTRIAL              => 'ID_GENRE_INDUSTRIAL',        
        ID3_GENRE_ALTERNATIVE             => 'ID_GENRE_ALTERNATIVE',       
        ID3_GENRE_SKA                     => 'ID_GENRE_SKA',               
        ID3_GENRE_DEATH_METAL             => 'ID_GENRE_DEATH_METAL',       
        ID3_GENRE_PRANKS                  => 'ID_GENRE_PRANKS',            
        ID3_GENRE_SOUNDTRACK              => 'ID_GENRE_SOUNDTRACK',        
        ID3_GENRE_EURO_TECHNO             => 'ID_GENRE_EURO_TECHNO',       
        ID3_GENRE_AMBIENT                 => 'ID_GENRE_AMBIENT',           
        ID3_GENRE_TRIP_HOP                => 'ID_GENRE_TRIP_HOP',          
        ID3_GENRE_VOCAL                   => 'ID_GENRE_VOCAL',             
        ID3_GENRE_JAZZ_FUNK               => 'ID_GENRE_JAZZ_FUNK',         
        ID3_GENRE_FUSION                  => 'ID_GENRE_FUSION',            
        ID3_GENRE_TRANCE                  => 'ID_GENRE_TRANCE',            
        ID3_GENRE_CLASSICAL               => 'ID_GENRE_CLASSICAL',         
        ID3_GENRE_INSTRUMENTAL            => 'ID_GENRE_INSTRUMENTAL',      
        ID3_GENRE_ACID                    => 'ID_GENRE_ACID',              
        ID3_GENRE_HOUSE                   => 'ID_GENRE_HOUSE',             
        ID3_GENRE_GAME                    => 'ID_GENRE_GAME',              
        ID3_GENRE_SOUND_CLIP              => 'ID_GENRE_SOUND_CLIP',        
        ID3_GENRE_GOSPEL                  => 'ID_GENRE_GOSPEL',            
        ID3_GENRE_NOISE                   => 'ID_GENRE_NOISE',             
        ID3_GENRE_ALTERNATIVE_ROCK        => 'ID_GENRE_ALTERNATIVE_ROCK',  
        ID3_GENRE_BASS                    => 'ID_GENRE_BASS',              
        ID3_GENRE_SOUL                    => 'ID_GENRE_SOUL',              
        ID3_GENRE_PUNK                    => 'ID_GENRE_PUNK',              
        ID3_GENRE_SPACE                   => 'ID_GENRE_SPACE',             
        ID3_GENRE_MEDITATIVE              => 'ID_GENRE_MEDITATIVE',        
        ID3_GENRE_INSTRUMENTAL_POP        => 'ID_GENRE_INSTRUMENTAL_POP',  
        ID3_GENRE_INSTRUMENTAL_ROCK       => 'ID_GENRE_INSTRUMENTAL_ROCK', 
        ID3_GENRE_ETHNIC                  => 'ID_GENRE_ETHNIC',            
        ID3_GENRE_GOTHIC                  => 'ID_GENRE_GOTHIC',            
        ID3_GENRE_DARKWAVE                => 'ID_GENRE_DARKWAVE',          
        ID3_GENRE_TECHNO_INDUSTRIAL       => 'ID_GENRE_TECHNO_INDUSTRIAL', 
        ID3_GENRE_ELECTRONIC              => 'ID_GENRE_ELECTRONIC',        
        ID3_GENRE_POP_FOLK                => 'ID_GENRE_POP_FOLK',          
        ID3_GENRE_EURODANCE               => 'ID_GENRE_EURODANCE',         
        ID3_GENRE_DREAM                   => 'ID_GENRE_DREAM',             
        ID3_GENRE_SOUTHERN_ROCK           => 'ID_GENRE_SOUTHERN_ROCK',     
        ID3_GENRE_COMEDY                  => 'ID_GENRE_COMEDY',            
        ID3_GENRE_CULT                    => 'ID_GENRE_CULT',              
        ID3_GENRE_GANGSTA                 => 'ID_GENRE_GANGSTA',           
        ID3_GENRE_TOP_40                  => 'ID_GENRE_TOP_40',            
        ID3_GENRE_CHRISTIAN_RAP           => 'ID_GENRE_CHRISTIAN_RAP',     
        ID3_GENRE_POP_FUNK                => 'ID_GENRE_POP_FUNK',          
        ID3_GENRE_JUNGLE                  => 'ID_GENRE_JUNGLE',            
        ID3_GENRE_NATIVE_US               => 'ID_GENRE_NATIVE_US',         
        ID3_GENRE_CABARET                 => 'ID_GENRE_CABARET',           
        ID3_GENRE_NEW_WAVE                => 'ID_GENRE_NEW_WAVE',          
        ID3_GENRE_PSYCHADELIC             => 'ID_GENRE_PSYCHADELIC',       
        ID3_GENRE_RAVE                    => 'ID_GENRE_RAVE',              
        ID3_GENRE_SHOWTUNES               => 'ID_GENRE_SHOWTUNES',         
        ID3_GENRE_TRAILER                 => 'ID_GENRE_TRAILER',           
        ID3_GENRE_LO_FI                   => 'ID_GENRE_LO_FI',             
        ID3_GENRE_TRIBAL                  => 'ID_GENRE_TRIBAL',            
        ID3_GENRE_ACID_PUNK               => 'ID_GENRE_ACID_PUNK',         
        ID3_GENRE_ACID_JAZZ               => 'ID_GENRE_ACID_JAZZ',         
        ID3_GENRE_POLKA                   => 'ID_GENRE_POLKA',             
        ID3_GENRE_RETRO                   => 'ID_GENRE_RETRO',             
        ID3_GENRE_MUSICAL                 => 'ID_GENRE_MUSICAL',           
        ID3_GENRE_ROCK_N_ROLL             => 'ID_GENRE_ROCK_N_ROLL',       
        ID3_GENRE_HARD_ROCK               => 'ID_GENRE_HARD_ROCK',         
        ID3_GENRE_FOLK                    => 'ID_GENRE_FOLK',              
        ID3_GENRE_FOLK_ROCK               => 'ID_GENRE_FOLK_ROCK',         
        ID3_GENRE_NATIONAL_FOLK           => 'ID_GENRE_NATIONAL_FOLK',     
        ID3_GENRE_SWING                   => 'ID_GENRE_SWING',             
        ID3_GENRE_FAST_FUSION             => 'ID_GENRE_FAST_FUSION',       
        ID3_GENRE_BEBOB                   => 'ID_GENRE_BEBOB',             
        ID3_GENRE_LATIN                   => 'ID_GENRE_LATIN',             
        ID3_GENRE_REVIVAL                 => 'ID_GENRE_REVIVAL',           
        ID3_GENRE_CELTIC                  => 'ID_GENRE_CELTIC',            
        ID3_GENRE_BLUEGRASS               => 'ID_GENRE_BLUEGRASS',         
        ID3_GENRE_AVANTGARDE              => 'ID_GENRE_AVANTGARDE',        
        ID3_GENRE_GOTHIC_ROCK             => 'ID_GENRE_GOTHIC_ROCK',       
        ID3_GENRE_PROGRESSIVE_ROCK        => 'ID_GENRE_PROGRESSIVE_ROCK',  
        ID3_GENRE_PSYCHEDELIC_ROCK        => 'ID_GENRE_PSYCHEDELIC_ROCK',  
        ID3_GENRE_SYMPHONIC_ROCK          => 'ID_GENRE_SYMPHONIC_ROCK',    
        ID3_GENRE_SLOW_ROCK               => 'ID_GENRE_SLOW_ROCK',         
        ID3_GENRE_BIG_BAND                => 'ID_GENRE_BIG_BAND',          
        ID3_GENRE_CHORUS                  => 'ID_GENRE_CHORUS',            
        ID3_GENRE_EASY_LISTENING          => 'ID_GENRE_EASY_LISTENING',    
        ID3_GENRE_ACOUSTIC                => 'ID_GENRE_ACOUSTIC',          
        ID3_GENRE_HUMOUR                  => 'ID_GENRE_HUMOUR',
        ID3_GENRE_SPEECH                  => 'ID_GENRE_SPEECH',
        ID3_GENRE_CHANSON                 => 'ID_GENRE_CHANSON',
        ID3_GENRE_OPERA                   => 'ID_GENRE_OPERA',
        ID3_GENRE_CHAMBER_MUSIC           => 'ID_GENRE_CHAMBER_MUSIC',
        ID3_GENRE_SONATA                  => 'ID_GENRE_SONATA',
        ID3_GENRE_SYMPHONY                => 'ID_GENRE_SYMPHONY',
        ID3_GENRE_BOOTY_BASS              => 'ID_GENRE_BOOTY_BASS',
        ID3_GENRE_PRIMUS                  => 'ID_GENRE_PRIMUS',
        ID3_GENRE_PORN_GROOVE             => 'ID_GENRE_PORN_GROOVE',
        ID3_GENRE_SATIRE                  => 'ID_GENRE_SATIRE',
        ID3_GENRE_SLOW_JAM                => 'ID_GENRE_SLOW_JAM',
        ID3_GENRE_CLUB                    => 'ID_GENRE_CLUB',
        ID3_GENRE_TANGO                   => 'ID_GENRE_TANGO',
        ID3_GENRE_SAMBA                   => 'ID_GENRE_SAMBA',
        ID3_GENRE_FOLKLORE                => 'ID_GENRE_FOLKLORE',
        ID3_GENRE_BALLAD                  => 'ID_GENRE_BALLAD',
        ID3_GENRE_POWER_BALLAD            => 'ID_GENRE_POWER_BALLAD',
        ID3_GENRE_RHYTMIC_SOUL            => 'ID_GENRE_RHYTMIC_SOUL',
        ID3_GENRE_FREESTYLE               => 'ID_GENRE_FREESTYLE',
        ID3_GENRE_DUET                    => 'ID_GENRE_DUET',
        ID3_GENRE_PUNK_ROCK               => 'ID_GENRE_PUNK_ROCK',   
        ID3_GENRE_DRUM_SOLO               => 'ID_GENRE_DRUM_SOLO',   
        ID3_GENRE_ACAPELLA                => 'ID_GENRE_ACAPELLA',   
        ID3_GENRE_EURO_HOUSE              => 'ID_GENRE_EURO_HOUSE',   
        ID3_GENRE_DANCE_HALL              => 'ID_GENRE_DANCE_HALL',   
        ID3_GENRE_GOA                     => 'ID_GENRE_GOA',
        ID3_GENRE_DRUM_N_BASS             => 'ID_GENRE_DRUM_N_BASS',
        ID3_GENRE_CLUB_HOUSE              => 'ID_GENRE_CLUB_HOUSE',
        ID3_GENRE_HARDCORE                => 'ID_GENRE_HARDCORE',
        ID3_GENRE_TERROR                  => 'ID_GENRE_TERROR',
        ID3_GENRE_INDIE                   => 'ID_GENRE_INDIE',
        ID3_GENRE_BRITPOP                 => 'ID_GENRE_BRITPOP',
        ID3_GENRE_NEGERPUNK               => 'ID_GENRE_NEGERPUNK',
        ID3_GENRE_POLSK_PUNK              => 'ID_GENRE_POLSK_PUNK',
        ID3_GENRE_BEAT                    => 'ID_GENRE_BEAT',
        ID3_GENRE_CHRISTIAN_GANGSTA_RAP   => 'ID_GENRE_CHRISTIAN_GANGSTA_RAP',
        ID3_GENRE_HEAVY_METAL             => 'ID_GENRE_HEAVY_METAL',
        ID3_GENRE_BLACK_METAL             => 'ID_GENRE_BLACK_METAL',
        ID3_GENRE_CROSSOVER               => 'ID_GENRE_CROSSOVER',
        ID3_GENRE_CONTEMPORARY_CHRISTIAN  => 'ID_GENRE_CONTEMPORARY_CHRISTIAN',
        ID3_GENRE_CHRISTIAN_ROCK          => 'ID_GENRE_CHRISTIAN_ROCK',
        ID3_GENRE_MERENGUE                => 'ID_GENRE_MERENGUE',
        ID3_GENRE_SALSA                   => 'ID_GENRE_SALSA',
        ID3_GENRE_TRASH_METAL             => 'ID_GENRE_TRASH_METAL',
        ID3_GENRE_ANIME                   => 'ID_GENRE_ANIME',
        ID3_GENRE_JPOP                    => 'ID_GENRE_JPOP',
        ID3_GENRE_SYNTHPOP                => 'ID_GENRE_SYNTHPOP'
      );
      return isset($genre[$this->id]) ? $genre[$this->id] : 'ID_GENRE_UNKNOWN';
    }
  
  }
?>
