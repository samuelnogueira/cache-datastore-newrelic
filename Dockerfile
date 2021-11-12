FROM php:alpine

ARG UID=1000

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN mkdir -m a=rwx /.composer/ && \
    install-php-extensions @composer xdebug

USER $UID

WORKDIR /app
