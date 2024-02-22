FROM node:19

WORKDIR /home/node/app 

COPY ./nginx.conf /etc/nginx/nginx.conf

RUN npm install -g @vue/cli
