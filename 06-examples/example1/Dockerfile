FROM phpswoole/swoole:latest

WORKDIR /app

RUN apt update && apt install supervisor inotify-tools

COPY ./supervisor.conf /etc/supervisor/conf.d/supervisord.conf

COPY ./entrypoint.sh /entrypoint.sh

RUN chmod +x /entrypoint.sh

ENTRYPOINT /entrypoint.sh