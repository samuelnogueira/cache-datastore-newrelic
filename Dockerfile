FROM php:alpine

ARG UID=1000

# Install composer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN mkdir -m a=rwx /.composer/ && \
    install-php-extensions @composer

USER $UID

WORKDIR /app
