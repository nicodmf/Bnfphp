#!/bin/bash
# ascii.sh
# ver. 0.2, reldate 26 Aug 2008
# Patched by ABS Guide author.

# Original script by Sebastian Arming.
# Used with permission (thanks!).

STDOUT=true

table=false

start=41
end=126
sep=" "

[[ "$STDOUT" != "true" ]] && exec >ASCII.txt      

MAXNUM=256
COLUMNS=5
OCT=8
OCTSQU=64
LITTLESPACE=-3
BIGSPACE=-8

i=1 # Decimal counter
o=1 # Octal counter

while [ "$i" -lt "$MAXNUM" ]; do  # We don't have to count past 400 octal.
        if (( i >= $start && i <= $end )); then
		      paddi="    $i $o"
		      [[ $table = true ]] && echo -n "${paddi: $BIGSPACE}  "       # Column spacing.
		      paddo="00$o"
		      echo -ne "\\0${paddo: $LITTLESPACE}"  # Fixup.
		      [[ $table = true ]] && echo -n "     "
		      if (( i % $COLUMNS == 0)); then       # New line.
		        [[ $table = true ]] &&  echo
		      fi
		      echo -n "$sep"
		    fi
        ((i++, o++))
        # The octal notation for 8 is 10, and 64 decimal is 100 octal.
        (( i % $OCT == 0))    && ((o+=2))
        (( i % $OCTSQU == 0)) && ((o+=20))
done

#exit $?

# Compare this script with the "pr-asc.sh" example.
# This one handles "unprintable" characters.
