#!/usr/bin/perl -w

  #use strict;
  use POSIX;

  my %operationVerb= (
    'add' => 'added',
    'mov' => 'moved',
    'del' => 'deleted'
  );
  
  my $me=         shift @_;
  my $to=         shift @_;
  my $tagName=    shift @_;
  my $operation=  shift @_;
  my $repository= shift @_;
  my $localPath=  $repository;
  my $cvsroot=    $ENV{'CVSROOT'};
  $localPath=~    s/$cvsroot\///g;
  
  my @fileInfo; my $filename;
  while ($filename= shift @_) {
    my %file;
    $file{'filename'}= $filename;
    $file{'revision'}= shift @_;
    $file{'oldrevision'}= getLastTagRevision ($repository.'/',$filename, $tagName, $operation);
    push @fileInfo, [$file];
  }
  
  $realname= getRealname ($ENV{'USER'});
  
  $msg= sprintf ("Update of %s\n\n", $repository);
  $head= sprintf ("%s %s the tag %s:\n",
    $ENV{'USER'},
    $operationVerb{$operation},
    $tagName
  );
  $head= "============================================================\n\n";
  $msg.= $head;
  
  my $vFormat;
  $_= $operation; 
  SWITCH: {
    if (/mov/) { $vFormat= '  %-30s  %6s --> %-6s';   last SWITCH; }
    if (/del/) { $vFormat= '  %-30s  %3$6s --> [  ]'; last SWITCH; }
    #if (/add/) { 
      $vFormat= '  %-30s  %3$6s';          last SWITCH; 
    #}
  }
  
  foreach $file (@fileInfo) {
    $msg.= sprintf ($vFormat."\n",
      $file{'filename'},
      '['.$file{'oldrevision'}.']',
      '['.$file{'revision'}.']'
    );
  }
  
  $msg.= "\n";
  
  # Append signature
  $msg.= "-- \n".$realname."\n";
  
  open (SENDMAIL, "/usr/sbin/sendmail -t |");
  
  print SENDMAIL "To: $to\n";
  print SENDMAIL "From: \"".getRealName ($ENV{'USER'})."\" <".getEmail ($ENV{'USER'}).">\n";
  print SENDMAIL "Reply-To: $to\n";
  print SENDMAIL "Subject: [CVS]    tag: $localPath\n";
  print SENDMAIL "X-CVS: ".$ENV{'CVSROOT'}."\n";
  print SENDMAIL "\n";
  print SENDMAIL $msg;
  close (SENDMAIL);
  
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

    return $realname;
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
    
    open (FILE, $file);
    my $tags= 0;
    while (<FILE>) {
      if ('symbols;' eq trim ($_)) {
        close (FILE);
        return 'N/A';
      }
      
      if ('symbol' eq trim ($_)) {
        $tags= 1;
      }
      
      if ($tags == 1 && -1 != index ($_, $tagName)) {
        my ($tagInfo, $revision)= split /:/, $_;
        close (FILE);
        $revision=~ s/;//g;
        
        return $revision;
      }
    }
    
    close (FILE);
    return 'PFN';
  }
