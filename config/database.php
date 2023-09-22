<?php

return [
    'driver'    => getenv('DATABASE_DRIVER') ?: 'mysql',
    'database'  => getenv('DATABASE_NAME') ?: 'catalyst',
];
