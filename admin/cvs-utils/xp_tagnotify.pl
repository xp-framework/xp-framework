#!/usr/bin/perl -w

  #use strict;
  use POSIX;
  use Data::Dumper;
  
  my %operationVerb= (
    'add' => 'added',
    'mov' => 'moved',
    'del' => 'deleted'
  );
  
  my $to=         shift @ARGV;
  my $tagName=    shift @ARGV;
  my $operation=  shift @ARGV;
  my $repository= shift @ARGV;
  my $localPath=  $repository;
  my $cvsroot=    $ENV{'CVSROOT'};
  $localPath=~    s/$cvsroot\///g;
  
  my @fileInfo; my $filename;
  while ($filename= shift @ARGV) {
    my %file= ();
    $file{'filename'}= $filename;
    $file{'revision'}= shift @ARGV;
    $file{'oldrevision'}= getLastTagRevision ($repository.'/'.$filename, $tagName, $operation);
    push @fileInfo, {%file};
  }
  
  $realname= getRealname ($ENV{'USER'});
  
  $msg= sprintf ("Update of %s\n\n", $repository);
  $head= sprintf ("%s %s the tag %s:\n",
    $ENV{'USER'},
    $operationVerb{$operation},
    $tagName
  );
  $head.= "============================================================\n\n";
  $msg.= $head;
  
  my $vFormat= '  %-30s  %6s';
  $_= $operation; 
  SWITCH: {
    if (/mov/) { $vFormat= '  %-30s  %6s --> %-6s'; last SWITCH; }
    if (/del/) { $vFormat= '  %-30s  %6s --> [  ]'; last SWITCH; }
    last SWITCH;
  }
  
  foreach $file (@fileInfo) {
    if ($operation =~ /add|del/) {
      $msg.= sprintf ($vFormat."\n", 
        $file->{'filename'},
        '['.$file->{'revision'}.']'
      );
    } else {
      $msg.= sprintf ($vFormat."\n",
        $file->{'filename'},
        '['.$file->{'oldrevision'}.']',
        '['.$file->{'revision'}.']'
      );
    }
  }
  
  $msg.= "\n";
  
  # Append signature
  $msg.= "-- \n".$realname."\n";
  
  open (SENDMAIL, "| /usr/sbin/sendmail -t");
  print SENDMAIL "To: $to\n";
  print SENDMAIL "From: \"".getRealname ($ENV{'USER'})."\" <".getEmail ($ENV{'USER'}).">\n";
  print SENDMAIL "Reply-To: $to\n";
  print SENDMAIL "Subject: [CVS]    tag: $localPath\n";
  print SENDMAIL "X-CVS: ".$ENV{'CVSROOT'}."\n";
  print SENDMAIL "\n";
  print SENDMAIL $msg;
  close (SENDMAIL);
  
  # Show success
  exit (0);

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
    
    if ('' eq $realname) {
      $realname= 'Devnull';
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
    return $username.'@schlund.de';
  }

  sub getLastTagRevision {
    my $file=       shift;
    my $tagName=    shift;
    my $operation=  shift;
    
    open (MYFILE, $file) || open (MYFILE, $file.",v") || return 'EACCESS';
    my $tags= 0;
    while (<MYFILE>) {
      if ('symbols;' eq trim ($_)) {
        close (MYFILE);
        return 'N/A';
      }
      
      if ('symbols' eq trim ($_)) {
        $tags= 1;
      }
      
      if ($tags == 1 && -1 != index ($_, $tagName)) {
        my ($tagInfo, $revision)= split /:/, $_;
        close (MYFILE);
        $revision=~ s/;//g;
        
        chop $revision;
        return $revision;
      }
    }
    
    close (MYFILE);
    return 'PFN';
  }
