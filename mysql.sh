#!/usr/bin/expect -f
spawn mysql -u root -p mud
match_max 100000
expect "Enter password:"
send -- "7dks2ld0s2k1mndiud-2503203s\r"
send -- "\r"
interact
