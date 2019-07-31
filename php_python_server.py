import socket
import multiprocessing as mp
import dill
import os
import traceback
import time
import sys



def execute(conn,dill,sys,traceback):
	try:
		import time
		commands=conn.recv(2**24)
		print "\n",commands,"\n"
		file2=conn.makefile('w',0)
		old=sys.stdout
		sys.stdout=file2
		exec(commands)
		sys.stdout=old
		try:conn.shutdown(1)
		except: pass
		file2.close()
		conn.close()
		#print "conn is closed",conn
		return
	except Exception as e:
		print "ERROR:"
		print traceback.format_exc()
		try:conn.shutdown(1)
		except: pass
		sys.stdout=old
		file2.close()
		conn.close()
		print "Error"
		return


def cleanup():
	cleanup.called=1

cleanup.called=0

try:
	if (len(sys.argv)>1):
		port=int(sys.argv[1])
	else:
		port=45555
	s=socket.socket()
	print "PhP python listening on",port
	s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
	s.bind(('',port))
	s.listen(5)

	while True:
		conn,addr=s.accept()
		print "Connection from",addr
		p=mp.Process(target=execute,args=(conn,dill,sys,traceback))
		p.start()

except:
	print "ERROR:\n",traceback.print_exc()
	s.close()
