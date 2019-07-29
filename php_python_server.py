import socket
import multiprocessing as mp
import dill
import os
import traceback
import time
import sys



def execute(conn,dill,sys):
	try:
		import time
		commands=conn.recv(2**24)
		file2=conn.makefile('w',0)
		old=sys.stdout
		sys.stdout=file2
		exec(commands)
		sys.stdout=old
		try:conn.shutdown(1)
		except Exception as e: print e
		file2.close()
		conn.close()
		#print "conn is closed",conn
		return
	except Exception as e:
		sys.stdout=old
		print "ERROR CLOSED",e
		traceback.print_exc();
		try:conn.shutdown(1)
		except:print "Shutdown Failed"
		file2.close()
		conn.close()
		print "ENDING with ERROR!"
		return


try:
	if (len(sys.argv)>1):
		port=int(sys.argv[1])
	else:
		port=45555
	s=socket.socket()
	print "ready on",port
	s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
	s.bind(('',port))
	s.listen(5)

	while True:
		conn,addr=s.accept();
		print "Connection from",addr
		p=mp.Process(target=execute,args=(conn,dill,sys))
		p.start()

except:
	print "ERROR:\n",traceback.print_exc()
	s.close()
