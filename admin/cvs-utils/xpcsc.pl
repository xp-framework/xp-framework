#!/usr/bin/perl

##
# Checks XP classes for conformance to coding standards
#
# Put into CVSROOT/commitinfo as follows:
#   ALL /path/to/xpcsc.pl
#
# $Id$

use constant ETAB       => "ETAB";
use constant EINDENT    => "EINDENT";
use constant ECOMMENT   => "ECOMMENT";
use constant ENOHEADER  => "ENOHEADER";
use constant ESHORTOPEN => "ESHORTOPEN";

use constant WTBD       => "WTBD";
use constant WOUTPUT    => "WOUTPUT";
use constant WINDENT    => "WINDENT";
use constant WNOHINT    => "WNOHINT";

%LINK = (
  # Errors
  ETAB        => "http://xp-framework.net/devel/coding.html#5",
  EINDENT     => "http://xp-framework.net/devel/coding.html#5",
  ECOMMENT    => "http://xp-framework.net/devel/coding.html#9",
  ENOHEADER   => "http://xp-framework.net/devel/coding.html#2",
  ESHORTOPEN  => "http://xp-framework.net/devel/coding.html#3",

  # Warnings
  WTBD        => "http://xp-framework.net/devel/coding.html#13",
  WOUTPUT     => "n/a",
  WNOHINT     => "n/a",
  WNOHINT     => "http://xp-framework.net/devel/coding.html#5",
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

  open (SENDMAIL, "| /usr/sbin/sendmail -t");
  print SENDMAIL "To: friebe\@php3.de, kiesel\@php3.de\n";
  print SENDMAIL "From: \"".getRealname ($ENV{'USER'})."\" <".getEmail ($ENV{'USER'}).">\n";
  print SENDMAIL "Reply-To: $to\n";
  print SENDMAIL "Subject: [CVS] commit failure\n";
  print SENDMAIL "MIME-Version: 1.0\n";
  print SENDMAIL "Content-type: text/plain; charset=iso-8859-1\n";
  print SENDMAIL "Content-transfer-encoding: 8bit\n";
  print SENDMAIL "X-CVS: ".$ENV{'CVSROOT'}."\n";
  print SENDMAIL "\n";
  print SENDMAIL $out;
  close (SENDMAIL);
  
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
  
  # Go through the file, line by line
  while (<FILE>) {
    $l++;

    SWITCH: {
      if (1 == $l && $_ !~ /^\<\?php/) { &error("First line does not contain <?php", ESHORTOPEN); }
      if (2 == $l && $_ !~ /^\/\*/) { &error("Second line does not contain XP header", ENOHEADER); }
      if (4 == $l && $_ !~ /\$Id/) { &error("Second line does not contain CVS Id-Tag", ENOHEADER); }
      if ($l < 5) { next; }
    }
    
    # Check whether we have a comment
    if ($_ =~ /(\s*)\/\*\*?/) {
      $comment= 1;
    }

    if ($_ =~ /\t/) {
      &error("Tab character found", ETAB);
    }

    if ($_ =~ /^(\s+)class/ && 2 != length($1)) {
      &error("Class declarations must be indented with 2 spaces", EINDENT);
    }

    if ($_ =~ /^(\s+)function/ && 4 != length($1)) {
      &error("Methods must be indented with 4 spaces", EINDENT);
    }

    if ($_ =~ /(.)\/\*[^\*]/ && $l > 2 && $1 ne "'") {
      &error("Block comments may not be contained within source, use // instead", ECOMMENT);
    }
    
    if (!$string && !$comment && $_ =~ /^(\s*)(.*)$/) {
      if ($2) {
        0 && print "### ".length($1)." - ".$indent."= ".(length($1) - $indent)."###\n";
        
        # The difference in indent may be one of -4, -2, 0 or 2, where -4
        # occurs in switch / case statements. Other
        # values should not occur; and in *absolutely* no case should the 
        # indentation difference be anything odd.
        $diff= (length($1) - $indent);
        if ($diff % 2) {
          &error("Your indentation is incorrect (difference to previous line is $diff chars)", EINDENT);
        }
        if ($diff != -4 && $diff != -2 && $diff != 0 && $diff != 2) {
          &warning("Your indentation seems to be incorrect (difference to previous line is $diff chars)", WINDENT);
        }
        
        $indent= length($1);
        
        # If a line ends with a brace, force indent
        $indent+= 2 if ($2 =~ /(\{|\()$/);
        $indent-= 2 if ($2 =~ /(\}|\))$/);
      }
    }
    
    if ($_ =~ /(.*)(echo|var_dump|print_r)/ && !$comment) {
      &warning("You should not be using direct output statements ($2)", WOUTPUT);
    }

    if ($_ =~ /(TODO|TBI|TBD|FIXME)/) {
      &warning("You have a $1 comment in your sourcecode...", WTBD);
    }
    
    if ($_ =~ /\@(access|param|return|throws)\s+$/) {
      &warning("Your inline documentation is incomplete.", WNOHINT);
    }
    
    if ($_ =~ /\(Insert method's description here\)/) {
      &warning("You should supply a description for your method", WNOHINT);
    }
    
    0 && print "[".$comment."|".$indent."]".$_;
    
    if ($_ =~ /(\s*)\*\//) {
      $comment= 0;
    }
  }
  close FILE;

  print $FILE." coding standards conformance ok [".$warnings." warning(s)]\n";
}
# }}}
