<?php

function py2($cmd,$return=false){
$session=session_id();
#$cmd=str_replace("'","'\''",$cmd);
$h=<<<head
import time
print "Start",time.time()

def cleanup():
	for x in globals().keys():
		if not x.startswith("_"):
			del globals()[x]
	for x in locals().keys():
		if not x.startswith("_"):
			del locals()[x]

import os
if "$session.pkl" in os.listdir("/home/pi/sessions") and "$session":
	carryover=None
	errorNo=0
	while not carryover:
		try: carryover=dill.load(open("/home/pi/sessions/$session.pkl","rb"))
		except EOFError:
			time.sleep(.01)
	for x in carryover:
		locals()[x]=carryover[x]

head
;

$f=<<<foot

exclude=["conn","dill","carryover","carryoverite","commands","file2","old","traceback"]
carryover={}
for carryoverite in locals().keys():
	if carryoverite not in exclude and "__" not in carryoverite :
		carryover[carryoverite]=locals()[carryoverite]

if "$session":
	dill.dump(carryover,open("/home/pi/sessions/$session.pkl","wb"))


print "done",time.time()

foot
;



	$s=socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
	socket_connect($s,'localhost',45555);
	socket_write($s,$h.$cmd.$f);
	$message="";
	$word="";
	$start=time();
	while (1==1){
		socket_clear_error();
		$output=socket_recv($s,$word,1000,MSG_DONTWAIT);
		if (false === $output){if (socket_last_error()!=11) break;else{sleep(.01);}}
		else if (0 === $output){break;}
		else {$message.=$word;}
	}
	#socket_shutdown($s,1);
	if ($return==false){
		echo ($message);
	}
	else {
		return $message;
	}
}

?>
