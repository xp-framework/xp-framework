#!/usr/bin/perl
##
# This file is part of the XP Framework's experiments
#
# $Id$

use EASC::Protocol::XpProtocolHandler;

sub sizeof {
    my $hash= shift;
    my @keys= keys %$hash;
    
    return scalar $keys;
}

# {{{ main
$proto= EASC::Protocol::XpProtocolHandler::->new({
    versionMajor => 1,
    versionMinor => 0,
    host         => $ARGV[0],
    port         => 6448,
});

$runner= $proto->lookup('xp/test/TestRunner');
$results= $runner->runTestClass($ARGV[1]);

printf(
    "Results for test run: %d succeeded, %d failed, %d skipped\n%s\n",
    sizeof($results->{succeeded}),
    sizeof($results->{failed}),
    sizeof($results->{skipped}),
    '=' x 72,
);

foreach $id (keys(%{$results->{succeeded}})) {
    printf(
        "+ %s(test= %s::%s(), time= %.3f seconds)\n",
        $results->{succeeded}{$id}->{classname},
        $results->{succeeded}{$id}->{test}{classname},
        $results->{succeeded}{$id}->{test}{name},
        $results->{succeeded}{$id}->{elapsed}->value()
    );
}
foreach $id (keys(%{$results->{failed}})) {
    printf(
        "! %s(test= %s::%s(), time= %.3f seconds)\n  Reason: %s(%s)",
        $results->{failed}{$id}->{classname},
        $results->{failed}{$id}->{test}{classname},
        $results->{failed}{$id}->{test}{name},
        $results->{failed}{$id}->{elapsed}->value(),
        $results->{failed}{$id}->{reason}{classname},
        $results->{failed}{$id}->{reason}{message}
    );
}
foreach $id (keys(%{$results->{skipped}})) {
    printf(
        "- %s(test= %s::%s(), time= %.3f seconds)\n  Reason: %s(%s)",
        $results->{skipped}{$id}->{classname},
        $results->{skipped}{$id}->{test}{classname},
        $results->{skipped}{$id}->{test}{name},
        $results->{skipped}{$id}->{elapsed}->value(),
        $results->{skipped}{$id}->{reason}{classname},
        $results->{skipped}{$id}->{reason}{message}
    );
}
# }}}
