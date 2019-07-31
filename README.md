# PHP_with_Python

PHP with python allows the user to write inline python code that is executed on the sever side.
by including the file "php_python_client.php" in the php file the user can then use the py() function with python code inside to execute python code in php.

There are two methods of executing the code, one is to just run the client side which starts a new python instance each time and can lead to significant time penalties in execution with more than one or two instances in your code.

The second is to run the php_python_sever.py in the background on your server.  The Client will open a socket connection with the sever and the sever will execute the code and return the stdout.   This is significantly faster (>15x) than running the client by itself.

This program requires the dill module for python.  This is used to pass the state of one instance to the next one.  It is suggested you run the cleanup() function at the end of each webpage to prevent unexpected interference from previous states.  


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
