all: smallsh

smallsh: smallsh.o
	gcc -Wall -o smallsh smallsh.o

smallsh.o: smallsh.c
	gcc -c smallsh.c

clean:
	rm smallsh.o smallsh
