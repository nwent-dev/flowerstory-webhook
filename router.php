<?php
// Router for php -S: send every single request, regardless of path, to webhook.php
// This way it doesn't matter if Tilda calls / or /webhook.php or anything else.
require __DIR__ . '/webhook.php';
