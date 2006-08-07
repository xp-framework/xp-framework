#!/bin/sh

# Write your favorite xterm/shell command below
# Note: be sure to redirect STDIN and STDERR to /dev/null
#       so nedit will not show the waiting-clock mouse pointer!
wterm +sb -e $SHELL 1>/dev/null 2>/dev/null &
