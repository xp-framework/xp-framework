#!/usr/bin/perl -w

  use strict;
  use POSIX;

  my %operationVerb= (
    'add' => 'added',
    'mov' => 'moved',
    'del' => 'deleted'
  );
  
  my $me= shift $_;
  my $to= shift $_;
  my $tagName= shift $_;
  my $operation= shift $_;
  my $repository= shift $_;
  my $localPath= $repository;
  $localPath=~ s/$cvsroot\///g;
  
  my @fileInfo; my $filename;
  while ($filename= shift $_) {
    my %file;
    $file{'filename'}= $filename;
    $file{'revision'}= shift $_;
    $file{'oldrevision'}= getLastTagRevision ($repository.'/',$filename, $tagName, $opertaion);
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
  print SENDMAIL "From: $fromEmail\n",
  print SENDMAIL "Reply-To: $to\n";
  print SENDMAIL "Subject: [CVS]    tag: '.$localPath."\n";
  print SENDMAIL "X-CVS: ".$ENV{'CVSROOT'}."\n";
  print SENDMAIL "\n";
  print SENDMAIL $msg;
  close (SENDMAIL);
  
  exit (0);
