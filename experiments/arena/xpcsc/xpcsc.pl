#!/usr/bin/perl

##
# Checks XP classes for conformance to coding standards
#
# Put into CVSROOT/commitinfo as follows:
#   ALL /path/to/xpcsc.pl
#
# $Id$

$DEBUG= 0;
$NOTIFY= 'friebe@php3.de, kiesel@php3.de';

use constant ETAB           => "ETAB";
use constant EINDENT        => "EINDENT";
use constant ECOMMENT       => "ECOMMENT";
use constant ENOHEADER      => "ENOHEADER";
use constant ESHORTOPEN     => "ESHORTOPEN";
use constant ECONSTRUCT     => "ECONSTRUCT";
use constant EWHITESPACE    => "EWHITESPACE";
use constant ECALLTIMEREFERENCE => "ECALLTIMEREFERENCE";
use constant ELEAKING       => "ELEAKING";
use constant ECLASSNAME     => "ECLASSNAME";

use constant WTBD           => "WTBD";
use constant WOUTPUT        => "WOUTPUT";
use constant WINDENT        => "WINDENT";
use constant WNOHINT        => "WNOHINT";
use constant WCOPY          => "WCOPY";  
use constant WPERFORM       => "WPERFORM";

%LINK = (
  # Errors
  ETAB                => "http://xp-framework.net/devel/coding.html#5",
  EINDENT             => "http://xp-framework.net/devel/coding.html#5",
  ECOMMENT            => "http://xp-framework.net/devel/coding.html#9",
  ENOHEADER           => "http://xp-framework.net/devel/coding.html#2",
  ESHORTOPEN          => "http://xp-framework.net/devel/coding.html#3",
  ECONSTRUCT          => "http://xp-framework.net/devel/coding.html#25",
  EWHITESPACE         => "http://xp-framework.net/devel/coding.html#24",
  ECALLTIMEREFERENCE  => "http://de3.php.net/manual/en/language.references.pass.php",
  ELEAKING            => "n/a",
  ECLASSNAME          => "n/a",

  # Warnings
  WTBD        => "http://xp-framework.net/devel/coding.html#13",
  WOUTPUT     => "n/a",
  WNOHINT     => "n/a",
  WINDENT     => "http://xp-framework.net/devel/coding.html#5",
  WCOPY       => "http://xp-framework.net/devel/tips.performance.html#5",
  WPERFORM    => "http://xp-framework.net/devel/tips.performance.html",
);

%BETTER = (
  "count"           => "sizeof() (or empty() to check for empty arrays)",
  "ereg"            => "preg_match()",
  "eregi"           => "preg_match() with /i modifier",
  "ereg_replace"    => "preg_replace()",
  "eregi_replace"   => "preg_replace() with /i modifier",
  "split"           => "explode()",
  "join"            => "implode()",
  "strtok"          => "text.StringTokenizer"
);

# {{{ utility functions for mail notify
sub getRealname {
  my $sysname= shift;
  open (UDB, "/etc/passwd");
  my @lines= <UDB>;
  my $line;
  close (UDB);

  my $realname= 'Mister Booombastic';
  foreach $line (@lines) {
    my ($uname, $pass, $uid, $id, $info, $home, $shell)= split /:/, $line;
    if ($sysname eq $uname) {
      $realname= $info;
      $realname =~ s/,.*$//g;
    }
  }

  return $realname;
}

sub trim {
  my ($x) = @_;
  $x =~ s/^\s+//;
  $x =~ s/\s+$//;
  return $x;
}

sub getEmail {
  my $sysname= shift;
  my $username= lc(getRealname ($sysname));

  $username =~ s/\ /\./g;
  $username =~ s/ä/ae/g;
  $username =~ s/ö/oe/g;
  $username =~ s/ü/ue/g;
  $username =~ s/ß/ss/g;
  return $username.'@php3.de';
}
# }}}

# {{{ void error (string message, string code)
sub error() {
  my $message= shift;
  my $code= shift;
  
  $_ =~ s/\t/\\t/g;
  chomp $_;
  my $out= "*** Error: ".$message." at line ".$l." of ".$FILE."\n    ".$_."\n---> [".$code."] ".$LINK{$code}."\n";
  print $out;
  
  if (0 && $NOTIFY) {
    open (SENDMAIL, "| /usr/sbin/sendmail -t");
    print SENDMAIL "To: ".$NOTIFY."\n";
    print SENDMAIL "From: \"".getRealname ($ENV{'USER'})."\" <".getEmail ($ENV{'USER'}).">\n";
    print SENDMAIL "Reply-To: ".getEmail ($ENV{'USER'})."\n";
    print SENDMAIL "Subject: [CVS] commit failure\n";
    print SENDMAIL "MIME-Version: 1.0\n";
    print SENDMAIL "Content-type: text/plain; charset=iso-8859-1\n";
    print SENDMAIL "Content-transfer-encoding: 8bit\n";
    print SENDMAIL "X-CVS: ".$ENV{'CVSROOT'}."\n";
    print SENDMAIL "\n";
    print SENDMAIL $out;
    close (SENDMAIL);
  }
  
  close FILE;
  exit 32;
}
# }}}

# {{{ void warning (string message, string code)
sub warning() {
  my $message= shift;
  my $code= shift;
  
  $_ =~ s/\t/\\t/g;
  chomp $_;
  my $out= "--- Warning: ".$message." at line ".$l." of ".$FILE."\n    ".$_."\n---> [".$code."] ".$LINK{$code}."\n";
  print $out;
  $warnings++;
}
# }}}

# {{{ main
while (@ARGV) {
  $FILE= shift @ARGV;
  $warnings= 0;
  
  if (!-f $FILE || $FILE !~ /\.class\.php$/) { next; }
  
  open(FILE, $FILE) || die "Cannot open $FILE";
  
  $l= 0;              # Line no.
  $comment= 0;        # Whether we are inside a comment
  $indent= 0;         # Indentation
  $string= 0;         # String
  $class= "";         # Class name
  $php= 1;            # Within PHP

  # Go through the file, line by line
  while (<FILE>) {
    $l++;

    SWITCH: {
      if (1 == $l && $_ !~ /^\<\?php/) { &error("First line does not contain <?php", ESHORTOPEN); }
      if (2 == $l && $_ !~ /^\/\*/) { &error("Second line does not contain XP header", ENOHEADER); }
      if (4 == $l && $_ !~ /\$Id/) { &error("Second line does not contain CVS Id-Tag", ENOHEADER); }
      if ($l < 5) { next; }
    }
    
    # Check for output after PHP mode
    if (0 == $php) { &error("Lines after closing PHP tags", ELEAKING); }
    
    # Check whether we have a comment
    if ($_ =~ /(\s*)\/\*\*?/) {
      $comment= 1;
    }
    
    # Check whether we have a multi-line string
    if (!$comment && $_ =~ /(<<<[A-Z]+|['"])(\s*)$/ && $_ !~ /(#|\/\/)/) {
      $string= 1;
    }
    
    # Remove all strings 
    s/\\"//g;
    s/\\'//g;
    s/"[^"]+"/""/;
    s/'[^']+'/''/;
    
    # Check class name
    if (!$comment && $_ =~ /^  class ([^\s]+)/) {
      $class= $1;

      # Check classname <-> filename match
      if (!($FILE =~ /$class\.class\.php$/)) {
        &error("Class name does not correlate to filename", ECLASSNAME);
      }
    }
    
    # Check for tabs. I HATE TABS!
    if ($_ =~ /\t/) {
      &error("Tab character found", ETAB);
    }

    # Check for PHP mode end
    if (!$comment && $_ =~ /^\?\>/) {
      $php= 0;
    }
    
    # Check class indentation
    if (!$comment && $_ =~ /^(\s+)class/ && 2 != length($1)) {
      &error("Class declarations must be indented with 2 spaces", EINDENT);
    }

    # Check method indentation
    if (!$comment && $_ =~ /^(\s+)function/ && ($class ? 4 : 2) != length($1)) {
      &error(($class ? "Methods" : "Functions")." must be indented with ".($class ? 4 : 2)." spaces", EINDENT);
    }

    # Check for block comments in code
    if (!$comment && $_ =~ /(.)\/\*[^\*]/ && $l > 2) {
      &error("Block comments may not be contained within source, use // instead", ECOMMENT);
    }
    
    # Check whitespace before variable
    if (!$comment && !$string && $_ =~ /([,;}])(\$[a-zA-Z0-9_]+)/) {
      &error("Not enough whitespace found before variable '$2' (preceding char: '$1')", EWHITESPACE);
    }
    
    # Check for whitespace after function header
    if (!$comment && !$string && $_ =~ /\(.*\)(\s*)\{/ && !length($1)) {
      &error("Not enough whitespace found after function declaration", EWHITESPACE);
    }
    
    # Check for parameter pass by reference
    if (!$comment && !$string && $_ =~ /(\-\>|::)[^\(]+\([^\)]*(&\$|, &\$)[^\)]*\)/ && length($2)) {
      &warning("Possible occurance of call-time pass by reference", ECALLTIMEREFERENCE);
    }
    
    # Check whitespace in assigments / comparisons
    if (!$comment && !$string && $_ =~ /(\$[a-zA-Z0-9_]+)=([^\s]+)/) {
      &error("Not enough whitespace found after variable '$1' (following: '$2')", EWHITESPACE);
    }
    
    # Check whitespace before and after operators
    if (!$comment && !$string && $_ =~ /(\s*)(===|==|!==|!=|<=|>=|&&|\|\|)(\s*)/ && length($1.$3) < 2) {
      &error("Not enough whitespace found in expression '$1$2$3'", EWHITESPACE);
    }
    
    # Check for whitespace after inline comments
    if (!$comment && !$string && $_ =~ /^(\s*)\/\/[^\s]/) {
      &error("Not enough whitespace found after inline comment", EWHITESPACE);
    }
    
    # Check for whitespace after keywords
    if ($_ =~ /\b(if|else|elseif|foreach|while|switch|for)\(/) {
      &error("Not enough whitespace after keyword $1", EWHITESPACE);
    }
    
    # Check if there is a method that has the same name as a class
    if ($_ =~ /function ([^\s\(]+)/ && "\L$1" eq "\L$class" && $class ne "Object" && $class ne "Interface") {
      &error("You may not have a method '$1' in your class '$class' (constructors are called __construct)", ECONSTRUCT);
    }
    
    # Check whether catch is not surrounded by whitespace
    if ($_ =~ /if \(( )*catch( )*\(/ && (length($1) || length($2))) {
      &error("Superfluous whitespace in catch statement", WHITESPACE);
    }
    
    # Check indenting
    if (!$string && !$comment && $_ =~ /^(\s*)(.*)$/) {
      if ($2) {
        $DEBUG && print "### ".length($1)." - ".$indent."= ".(length($1) - $indent)."###\n";
        
        # The difference in indent may be one of -4, -2, 0 or 2, where -4
        # occurs in switch / case statements. Other values should not occur; 
        # and in *absolutely* no case should the indentation difference be 
        # anything with an odd value
        $diff= (length($1) - $indent);
        if ($diff % 2) {
          &error("Your indentation is incorrect (difference to previous line is $diff chars)", EINDENT);
        }
        if ($diff != -4 && $diff != -2 && $diff != 0 && $diff != 2) {
          &warning("Your indentation seems to be incorrect (difference to previous line is $diff chars)", WINDENT);
        }
        
        $indent= length($1);
      }
    }

    # Check for direct output statements
    if (!$comment && !$string && $_ =~ /\b(echo|var_dump|print_r)/) {
      &warning("You should not be using direct output statements ($1)", WOUTPUT);
    }

    # Check for bad performers
    if (!$comment && !$string && $_ =~ /\b(count|eregi?|eregi?_replace|split|join)\([^\)]+\)/ && $_ !~ /function/) {
      &warning("You should be using $BETTER{$1} instead of $1", WPERFORM);
    }

    # Check for special tokens indicating this part of the is not yet complete
    if (!$string && $_ =~ /(TODO|TBI|TBD|FIXME)/) {
      &warning("You have a $1 comment in your sourcecode...", WTBD);
    }
    
    # Check for incomplete API doc #1
    if ($comment && $_ =~ /\@(access|throws|see|ext|purpose)\s+$/) {
      &warning("Your inline documentation is incomplete.", WNOHINT);
    }
    
    # Reset lastFuncIsRef when seeing next function
    if ($comment && $_ =~ /\@access\s+/) { $lastFuncIsRef= ""; }
    
    # Check for incomplete API doc #2
    if ($comment && $_ =~ /\(Insert (class'|method's) description here\)/) {
      &warning("You should supply a description for your method", WNOHINT);
    }
    
    # Check for omitted parameter types or their names
    if ($comment && $_ =~ /\@param\s+([a-zA-Z0-9\.\&\[\]\-\*]+)?\s?(\w?)/) {
      if (!length($1)) { &warning("Your inline documentation misses the type of the parameter", WNOHINT); }
      if (!length($2)) { &warning("Your inline documentation misses the parameter's name", WNOHINT); }
    }
    
    # Check for inconsistent API doc #1
    if ($comment && $_ =~/\@return\s+(\&)?([a-zA-Z0-9\.\&\-\*]+)?/) {
      if (!length($2)) { &warning("Your inline documentations misses the functions return type", WNOHINT); }
      if (length($2)) { $lastFuncIsRef= $1; }
    }
    
    # Check for inconsistent API doc #2
    if (!$comment && $_ =~ /function (\&)?[a-zA-Z0-9]+/) {
      if (length($1) && !length($lastFuncIsRef)) { &warning("Apidoc states function returns value but function returns reference", WNOHINT); }
      if (!length($1) && length($lastFuncIsRef)) { &warning("Apidoc states function returns reference but function does not", WNOHINT); }
    }
    
    $DEBUG && print "[".$class."|".$comment."|".$string."|".$indent."]".$_;
    
    # Check if a comment ends
    if ($_ =~ /(\s*)\*\//) {
      $comment= 0;
    }
    
    # Check if multi-line string ends
    if ($string && $_ =~ /^((\s*)['"]|^[A-Z]+$)/) {
      $string= 0;
      $indent= length($2);
    }
  }
  close FILE;

  print $FILE." coding standards conformance ok [".$warnings." warning(s)]\n";
}
# }}}
