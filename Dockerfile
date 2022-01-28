FROM composer

ADD test.php /app
RUN composer require aws/aws-sdk-php

ENTRYPOINT []