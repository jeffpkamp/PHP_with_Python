<?php


function py($cmd,$return=false,$port=45555){
	$s=socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
	if (socket_connect($s,'localhost',$port)){
		return socket_py($cmd,$return,$s);}
	else{
		return serial_py($cmd,$return);}
}


function serial_py($cmd,$return=false){
    $cmd=str_replace("'","'\''",$cmd);
$session=session_id();
$h=<<<head

import traceback
import dill
import sys

def cleanup():
	for x in globals().keys():
		if not x.startswith("_"):
			del globals()[x]


import os
if "Serial_$session.pkl" in os.listdir("/home/pi/sessions/"):
		dill.load_session("/home/pi/sessions/Serial_$session.pkl")

head
;

$f=<<<foot


import dill
dill.dump_session("/home/pi/sessions/Serial_$session.pkl")


foot
;

    if ($return==false){
        echo shell_exec("python -c '$h$cmd$f' 2>&1");
    }
    else {
        return shell_exec("python -c '$h$cmd$f' 2>&1");
    }
}



function socket_py($cmd,$return=false,$s){


    $session=session_id();
#$cmd=str_replace("'","'\''",$cmd);
$h=<<<head
import time

import os
if ("$_SERVER[DOCUMENT_ROOT]"):
	os.chdir("$_SERVER[DOCUMENT_ROOT]");
else:
	pass
if "$session.pkl" in os.listdir("/home/pi/sessions") and "$session":
	carryover=None
	errorNo=0
	while not carryover:
		try: carryover=dill.load(open("/home/pi/sessions/$session.pkl","rb"))
		except EOFError:
			time.sleep(.01)
	for x in carryover:
		locals()[x]=carryover[x]
cleanup.called=0

head
;

$f=<<<foot

if cleanup.called:
	if "$session.pkl" in os.listdir("/home/pi/sessions"):
		os.remove("/home/pi/sessions/$session.pkl")
else:
	exclude=["conn","dill","carryover","carryoverite","commands","file2","old","traceback"]
	carryover={}
	for carryoverite in locals().keys():
		if carryoverite not in exclude and "__" not in carryoverite :
			carryover[carryoverite]=locals()[carryoverite]
	if "$session":
		dill.dump(carryover,open("/home/pi/sessions/$session.pkl","wb"))




foot
;



	socket_write($s,$h.$cmd.$f);
	$message="";
	$word="";
	$start=time();
	while (1==1){
		socket_clear_error();
		$output=socket_recv($s,$word,1000,MSG_DONTWAIT);
		if (false === $output){
			if (socket_last_error()!=11){
				break;
			}else{
				sleep(.01);
			}
		}
		else if (0 === $output){break;}
		else {$message.=$word;}
	}
	if ($return==false){
		echo $message;
	}
	else {
		return $message;
	}
}

?>
