#!/usr/bin/perl -w

# $Id$
# Suche Dateien, deren Revision aktueller als STABLE ist...

   my $DEBUG= 0;
   my $CVSROOT= "/home/cvs/repositories/xp/";
   
   my $verbose= 0;

   # Parameter parsen
   if (1 > $#ARGV) {
     print "Usage: cvscheck.pl [-v] [TAG] [filename]\n";
     print "       -v       show all files, no matter if they are modified or differ in revision\n";
     print "       TAG      which tag to check for\n";
     print "       filename filename to check, leave empty to check all files in repository\n";
     exit;
   }

   my $tag=     shift @ARGV;
   my $cvsfile= shift @ARGV;

   if ($tag eq "-v") {
     $tag= $cvsfile;
     $verbose= 1;
     $cvsfile= "";
   }

   my $cvscmd= "cvs status -v ".$cvsfile;
   $DEBUG && print "cmd: $cvscmd \n";
   
   open (CVS, $cvscmd." 2>/dev/null |") or die "Can't open pipe!\n";
   my @output;
   while ($line= <CVS>) { push @output, $line; }
   close (CVS);
   push @output, "=====================================\n"; # fake last line...

   my $cvs_file;
   my $cvs_status;
   my $cvs_revision;
   my $cvs_stable_revision;
   for $line (@output) {
      chop ($line);
      #print $line."\n";
      if ($line =~ /^File: (\S+)\s+Status: ([\S\s]+)$/) {
         $cvs_file= $1;
         $cvs_status= $2;
      }
      
      if ($line =~ /^\s+Repository revision:\s+(\S+)\s+(\S+)/) {
         $cvs_revision= $1;
         $cvs_filename= $2;
      }
      
      if ($line =~ /^\s+$tag\s+\(revision: (\S+)\)/) {
         $cvs_stable_revision= $1;
      }
      
      if ($line =~ /^\=+/) {
         # First line of next cvs status...
         
         if (defined ($cvs_file)) {
            if (!defined ($cvs_stable_revision)) {
               $cvs_stable_revision= "N/A";
            }
            
            if (!defined ($cvs_revision)) {
               $cvs_revision= "N/A";
            }
            
            $cvs_filename=~ s/^$CVSROOT//;
            $cvs_filename=~ s/,v$//;
            
            if ($verbose || 
                $cvs_revision ne $cvs_stable_revision || 
                $cvs_status ne "Up-to-date") {
               print sprintf ("%-33s => %-s\n",
                  "(r:$cvs_revision|s:$cvs_stable_revision) $cvs_status",
                  $cvs_filename
               );
            }
            
            undef ($cvs_file);
            undef ($cvs_filename);
            undef ($cvs_status);
            undef ($cvs_revision);
            undef ($cvs_stable_revision);
         }
      }
   
   }
