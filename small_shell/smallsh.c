#include <stdio.h>
#include <stdlib.h>
#include <unistd.h>
#include <signal.h>
#include <string.h>
#include <sys/types.h>
#include <sys/wait.h>
#include <pwd.h>
#include <errno.h>

int status;
int background[10] = {0, 0, 0, 0, 0, 0, 0, 0, 0, 0};

void interrupt_new_line()       //adds new line character and flushes stdout
{
    printf("\n: ");
    fflush(stdout);
}

int my_command(char *command) {             //returns number for each type of command
    if(strcmp(command, "cd") == 0)          //built ins are > 1 and everything else is 0
        return 1;
    else if(strcmp(command, "exit") == 0)
        return 2;
    else if(strcmp(command, "status") == 0)
        return 3;
    else
        return 0;
}

char* get_command(char *user_input) {       //This grabs the first argument and returns
    int i = 0;                              //it as a string
    char *stop_char = " \n\0";

    i = strcspn(user_input, stop_char);
    char *command = malloc(sizeof(user_input));
    strncpy(command, user_input, i);

    return command;
}

void set_background_pid(pid_t pid) {    //This puts a background processes pid into
    int i;                              //the global array for storing background pids
    for (i = 0; i < 10; ++i) {
        if(background[i] == 0) 
        {
            background[i] = pid;
            return;
        }
    }
}

//This is an embarassingly huge function.  It takes the user input, checks if the commands exist,
//checks for comments or empty lines, calls the relevant functions and does error checking otherwise
//takes the user inptu as input, and uses the first argument as well.  no output.

void call_built_in(char *command, char *user_input){
    char *temp = malloc(1025 * sizeof(user_input));
    char *delim = " ";
    strcpy(temp, user_input);
    char *str_args = temp;                  //This part splits the user input into an array of 
    char *args[strlen(str_args) + 1];       //strings.  This is useful later for calling commands.
    int i = 0;
    char *tok = malloc(1025 * sizeof(str_args));
    tok = strtok(str_args, delim);
    while(tok != NULL) {
        args[i] = malloc(1025 * sizeof(tok));
        strcpy(args[i], tok);
        tok = strtok(NULL, delim);
        i++;
    }
    
    if(i == 0) {    //This should only happen if there are no arguments
        return;      //In this case it should should exit and no hastle should happen
    }
    if(user_input[0] == '#')    //This should happen if a line is a comment
       return;                  //It just returns and a newline should print

    args[i] = malloc(5 * sizeof(tok));
    args[i + 1] = malloc(5 * sizeof(tok));
    args[i] = NULL;                 //I set both to NULL because of how I deal with
    args[i + 1] = NULL;             //input and output redirection later

    int j = 0;
    while(args[j] != NULL) {        //This finds the number of arguments in total
        j++;                        //it leaves the result in j
    }

    pid_t forked_pid = -4;

    forked_pid = fork();            //It's the fork!

    switch (forked_pid) {
        case -1:
            perror("There was an error!\n");
            _exit(1);
            break;
        case 0: //child process
            ;                                           //this random semi-colon is required 
            int is_background = 0;                      //for the declarations to work
            int stdin_used = 0;
            int stdout_used = 0;
            if(strcmp(args[i - 1], "&") == 0 && i > 1) {        //checks for background character
                args[i - 1] = NULL;
                is_background = 1;                              //sets that 'flag' if so
            }
            int not_used = setpgid(0, 0);   //Changes process group ID of the child 
                                            //This prevents SIGINT being sent to the
            int f = 0;                      //parent and killing the child.

    
            //This starts by checking if a file exists and if it's writable.  The key here is
            //I don't care if a file exists or not, but if it exists I want it to be writable.
            //So the only condition that really matters, regarding errors, is if the file exists
            //but I can't write to it.  The function access does the heavy lifting here
            while(args[f] != NULL) {
                if(strcmp(args[f], ">") == 0 && args[f + 1] != NULL ) {
                    if(access(args[f + 1], W_OK) == -1 && access(args[f + 1], F_OK) == 0 ){
                        printf("Error: Unable to open file %s for output.\n", args[f+1]);
                        _exit(1);   
                    }                           
                    else {
                        freopen(args[f + 1], "w", stdout);      //this 'replaces' stdout with an
                                                                //fstream to the args[f + 1] file
                        int n = f;
                        while(args[n] != NULL) {
                            args[n] = args[n+2];            //This removes the arrow and the file
                            n++;
                        }

                        f--;
                        stdout_used = 1;            //'flag' for if we're writing or not
                    }
                }
    
                //Here we only care if a file is readable.  If it doesn't exist we don't like it
                //either, but checking just R_OK is sufficient, as it fails if the file isn't there
                if(strcmp(args[f], "<") == 0 && args[f + 1] != NULL ) {
                    if(access(args[f + 1], R_OK) == -1){
                        printf("Error: Unable to access file %s for input.\n", args[f+1]);
                        _exit(1);
                    }
                    else {
                        freopen(args[f + 1], "r", stdin);       //'replace' stdin with stream from
                                                                //args[f+1] for reading

                        int n = f;
                        while(args[n] != NULL) {
                            args[n] = args[n+2];
                            n++;
                        }

                        f--;
                        stdin_used = 1;
                    }
                }

                f++;
            }
            if(is_background == 1) {                    //This redirects background
                if(stdin_used == 0) {                   //processes to use /dev/null
                    freopen("/dev/null", "r", stdin);   //instead of stdin, stdout
                }                                       //and stderr
                if(stdout_used == 0) {
                    freopen("/dev/null", "w", stdout);
                }
                freopen("/dev/null", "w", stderr);
            }
            

            if(execvp(command, args) == -1) {               //calls exec and automatically finds
                fprintf(stderr, "command doesn't exist\n"); //the file.  only returns if there is
                                                            //and issue
                _exit(1);
            }
            break;
        default:    //parent
            if(strcmp(args[i - 1], "&") == 0 && i > 1) {        //how backward, lol, the parent's 
                printf("background pid is: %d\n", forked_pid);  //job is easy.  just check for 
                set_background_pid(forked_pid);                 //background and skip those
                forked_pid = waitpid(forked_pid, &status, WNOHANG);
                break;
            }
            else {
                forked_pid = waitpid(forked_pid, &status, 0);
                break;
            }
    }
}

//This checks if the most recent foreground process exited normally or not
//Then it outputs the status of that result
void status_func() {
    if(WIFEXITED(status) > 0) {
        printf("exit value %d\n",  WEXITSTATUS(status));
    }
    if(WIFSIGNALED(status) > 0) {
        printf("terminated by signal: %d\n", WTERMSIG(status));
    }
}

//Simple exit function
//just call exit
void exit_func() {
    _exit(0);
}

//This is the built-in 'cd' function
//it takes the user_input as a string
//It acts very similar to the standard cd function included in most shells

void cd_func(char *user_input) {
    char *homedir;
    if((homedir = getenv("HOME")) == NULL) {        //This gets the home folder location
        struct passwd *pw = getpwuid(getuid());  
        homedir = pw->pw_dir;
    }
    
    char *delim = " ";
    char *args[3];
    int i = 0;
    char *tok = malloc(1025 * sizeof(user_input));      //This splits the user input into tokens
    tok = strtok(user_input, delim);                    //It does this to split the arguments up
    while(tok != NULL) {
        args[i] = malloc(1025 * sizeof(tok));
        strcpy(args[i], tok);
        tok = strtok(NULL, delim);
        i++;
        if(i > 2) {                                     //anything with more than 2 arguments is incorrect
            printf("Error: Entered too many arguments.\n");
            return;
        }
    }

    if(i == 1) {
        chdir(homedir);
    }
    else if(i == 2) {
        if(args[1][0] == '~'){          //checks for the '~' character and replaces it with the home folder location
            char *temp = args[1] + 1;
            char *homedir_temp = malloc(strlen(homedir) * sizeof(homedir)); 
            strcpy(homedir_temp, homedir);
            strcat(homedir_temp, temp);
            strcpy(args[1], homedir_temp);
        }
        if(chdir(args[1]) == 0) {       
        }
        else {
            if(errno == ENOENT) {
                printf("The named directory doesn't exist.\n");
            }
            else{
                printf("The function 'cd' ran into an error. \nYou entered: %s\n", args[1]);
            }
        }
    }
}

//This checks the first argument to see if it's built in or not
//if it's build in, it calls the relevant function.  
//Everything else is redirected to the call_built_in function

void execute(char *user_input) {
    char *command = get_command(user_input);
    switch(my_command(command)) {
        case 0:
            call_built_in(command, user_input);
            break;
        case 1:
            cd_func(user_input);
            break;
        case 2:
            exit_func();
            break;
        case 3:
            status_func();
            break;
        default:
            break;
    }
}

//This takes no input.  
//It accesses the global variables of background[] and status.
//It checks to see if there area background processes running, or if they ended
//if they ended it will output that they ended, the pid and if they were terminated.

void check_background() {              
    int i;
    for(i = 0; i < 10; ++i) {
        if(background[i] > 0){
            int temp = status;
            int check = waitpid(background[i], &status, WNOHANG);
            if (check == 0)
                return;

            if(WIFEXITED(status) > 0) {
                printf("background pid %d is done: exit value %d\n", background[i], WEXITSTATUS(status));
                background[i] = 0;
            }
            if(WIFSIGNALED(status) > 0) {
                printf("background pid %d is done: terminated by %d\n", background[i], WTERMSIG(status));
                background[i] = 0;
            }
            status = temp;
        }
    }
}

int main()
{
    char c = '\0';
    int *user_input_mem = malloc(sizeof(int));  
    *user_input_mem = 1025;
    char *user_input = malloc(*user_input_mem);
    memset(user_input, 0, sizeof(user_input));
    signal(SIGINT, SIG_IGN);                    //Catches SIGINT and gives new line
    signal(SIGINT, interrupt_new_line);
    check_background();
    printf(": ");
    while(c != EOF) {
        c = getchar();
        if(c == '\n'){
            execute(user_input);
            memset(user_input, 0, sizeof(user_input));
            check_background();                         //checks for backround processes
            printf(": ");
        }
        else {
            strncat(user_input, &c, 1);     
        }

    }
    printf("\n");
    free(user_input);
    
    free(user_input_mem);

    return 0;
}
