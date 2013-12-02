#!/bin/sh
/usr/sbin/ngctl -f- <<-EOF
mkpeer ipfw: car 10 upper
name ipfw:10 inet_login
connect inet_login: ipfw: lower 11
msg inet_login: setconf { upstream={ cbs=2621440 ebs=2621440 cir=20971520 greenAction=1 yellowAction=1 redAction=2 mode=2 } downstream={ cbs=2621440 ebs=2621440 cir=20971520 greenAction=1 yellowAction=1 redAction=2 mode=2 } }
EOF>>

