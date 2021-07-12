ARG VERSION

# Dev image
FROM nginx:${VERSION}-alpine as prod

# Copy nginx config
COPY ./docker/nginx/default.prod.conf /etc/nginx/conf.d/default.conf
RUN mkdir -p /var/ssl/

COPY docker/nginx/ssl/ /var/ssl/