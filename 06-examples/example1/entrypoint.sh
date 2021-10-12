#!/usr/bin/env bash

# Start the supervisor program.
/usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf &

WORK_DIR=$1
if [ ! -n "${WORK_DIR}" ] ;then
    WORK_DIR="."
fi

echo "Starting inotifywait..."

LOCKING=0 # Used to block the signal if it is not needed.

inotifywait --event modify --event create --event move --event delete -mrq   ${WORK_DIR}  | while read file

do
    if [[ ! ${file} =~ .php$ ]] ;then
        continue
    fi
    if [ ${LOCKING} -eq 1 ] ;then
        echo "Reloading, skipped."
        continue
    fi
    echo "File ${file} has been modified."
    LOCKING=1

    kill -9 $(cat ./http-server-pid) # kill the server.
    kill -9 $(cat ./ws-server-pid) # kill the server.

    kill -9 $(cat ./http-server-pid2) # kill the server.
    kill -9 $(cat ./ws-server-pid2) # kill the server.
    
    # Remove the server pid file - this will avoid problems when it comes to restart the server.
    # rm -f ./http-server-pid
    # rm -f ./supervisord.pid
    
    sleep 2
    
    LOCKING=0
done

exit 0