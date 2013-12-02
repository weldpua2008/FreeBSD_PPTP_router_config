# $FreeBSD: src/etc/root/dot.cshrc,v 1.30.6.1 2008/11/25 02:59:29 kensmith Exp $
#
# .cshrc - csh resource script, read at beginning of execution by each shell
#
# see also csh(1), environ(7).
#

alias h		history 25
alias j		jobs -l
alias la	ls -a
alias lf	ls -FA
alias ll	ls -lA

# A righteous umask
umask 22

set filec
set autolist
set nobeep

set path = (/sbin /bin /usr/sbin /usr/bin /usr/games /usr/local/sbin /usr/local/bin $HOME/bin)

setenv	EDITOR	vi
setenv	PAGER	more
setenv	BLOCKSIZE	K


set MainC = 'ESC[=3FESC[=0G'
       set HostC = 'ESC[36m'
       set TimeC = 'ESC[32m'
       set DayC = 'ESC[1mESC[32m'
       set DateC = 'ESC[0mESC[32m'
       set ttyC  = 'ESC[32m'
       set EvntC = 'ESC[0mESC[36m'
       set PathC = 'ESC[0mESC[32m'
       set GtC   = 'ESC[1mESC[37m'
       set ttyS = `expr ${tty} : 'tty\(.*\)'`
       set HostS = %m
       set Sep1 =  ':'
       set Sep2 =  '-'
       set Sep3 =  '/'

if ($?prompt) then
	# An interactive shell -- set some stuff up
	#set prompt = "`/bin/hostname -s`:`pwd`# "
	#set prompt="%n@%m [%/]%# "
	set prompt="%{^[[40;33;1m%}%n@%m %{^[[40;32;1m%} %/ >"
	 if( `id -un` == 'root' ) then
         set SUColor = 'ESC[1mESC[31m'
         set Sep2 = "%{$SUColor%}#%{ESC[0m%}"
       endif
       set promptchars = '.!'
	# set prompt = "%{$DayC%d$DateC${Sep3}%W.%y${Sep1}$PathC%B%/%b\n%}%B%{$HostC%}${HostS}%b${Sep1}%B%{$ttyC%}${ttyS}%b${Sep2}%{$EvntC%}%h%{$TimeC%}${Sep3}%T%{$GtC>ESC[0%}m"

#	Set prompt = "%{`echotc md``echotc AF 1`%}:%{`echotc AF 4`%}%m %{`echotc AF 1`%}(%{`echotc AF 3`%}%~%{`echotc AF 1`%})%{`echotc AF 2`%}%#%{`echotc me`%} "

	set filec
	set history = 2100
	set savehist = 2100
	set mail = (/var/mail/$USER)
	if ( $?tcsh ) then
		bindkey "^W" backward-delete-word
		bindkey -k up history-search-backward
		bindkey -k down history-search-forward
	endif
endif
