<?php 
if (!function_exists('env')) {
    function env($key, $default = null) {
        $filePath = FCPATH . '.env';

        if (!file_exists($filePath)) {
            return $default;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue; // Skip comments
            }
            list($envKey, $envValue) = explode('=', $line, 2) + [null, null];
            $envKey = trim($envKey);
            $envValue = trim($envValue, '"');

            if ($envKey === $key) {
                return $envValue;
            }
        }

        return $default;
    }
}

?>