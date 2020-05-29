<?php

return [
    'EXPIRED_JWT' => time() + 60 * 10,
    'EXPIRED_REFRESH_TOKEN' => time() + 60 * 60 * 24 * 365
];
