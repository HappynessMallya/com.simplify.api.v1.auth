ARG VERSION

# Dev image
FROM nginx:${VERSION}-alpine as dev

# Copy nginx config
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Prod image
FROM dev as prod
COPY ./docker/nginx/default.prod.conf /etc/nginx/conf.d/default.conf
RUN mkdir -p /var/ssl/

COPY docker/nginx/ssl/ /var/ssl/

