# PHP_with_Python

PHP with python allows the user to write inline python code that is executed on the sever side.
by including the file "php_python_client.php" in the php file the user can then use the py() function with python code inside to execute python code in php.

There are two methods of executing the code, one is to just run the client side which starts a new python instance each time and can lead to significant time penalties in execution with more than one or two instances in your code.

The second is to run the php_python_sever.py in the background on your server.  The Client will open a socket connection with the sevrer and the sever will execute the code and return the stdout.   This is significantly faster (>15x) than running the client by itself.

This program requires the dill module for python.  This is used to pass the state of one instance to the next one.  It is suggested you run the cleanup() function at the end of each webpage to prevent unexpected interference from previous states.  

The py() function in php takes 3 arguments: ($cmd, $return=false, $port=45555).

The $cmd argument is a string, and should be the formatted python code.  This is easiest done in a HEREDOC as shown below, but can also be a single line for simple python scripts.  

The $return argument defaults to false.   When false the stdout of the python command is echoed from php.  When $return is true, the stdout string is returned.

the $port argument is the port that php_python_server.py program is listening on.  This defaults to port 45555, just because.  If the socket fails to connect it will run the much slower py_serial() function.  if it connects it will run the much faster py_socket() function.  The execution time difference is about 15x, depending on how much information is passed between each instance of the functions.  

#Troubleshooting

The code will likely need some tweaking to run on your sever.  One thing that will likely need changing is the path to where dill saves its sessions, unless you are running this on a raspberry pi.  If you have any issues check the permissions!  If you start the php_python_server.py program with your user account, it will execute with your privileges, while the py() function will execute with apache's privileges.  


EXAMPLE: php code

	<?php
	include "~/bin/php_python_client.php";
	py(<<<p
	print "hello world"
	k=100
	p=10
	print k+p
	import os
	print os.listdir(".")
	p
	);?>

	<html> Python ran up above!"

	<?php
	py(<<<p
	print "I remember things from the last instance!"
	r=k+p
	print "R is:",r
	#cleanup your instance at the end!
	cleanup()
	p
	);?>
	</html>
