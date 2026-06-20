FROM php:8.2-cli

WORKDIR /app
COPY webhook.php /app/webhook.php
COPY router.php /app/router.php

# Render передаёт порт через переменную окружения PORT
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000} /app/router.php"]
