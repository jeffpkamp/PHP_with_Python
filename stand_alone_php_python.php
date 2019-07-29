<?php 
function py($s,$return=false){
	$s=str_replace("'","'\''",$s);		
$h=<<<head


import traceback
import dill
import sys

def cleanup():
	for x in globals().keys():
		if not x.startswith("_"):
			del globals()[x]


import os
if "pyworking.pkl" in os.listdir("."):
		dill.load_session("pyworking.pkl")

head
;

$f=<<<foot


import dill
dill.dump_session("pyworking.pkl")


foot
;

	if ($return==false){	
		echo shell_exec("python -c '$h$s$f' 2>&1");
	}
	else {
		return shell_exec("python -c '$h$s$f' 2>&1");
	}
}

?>
