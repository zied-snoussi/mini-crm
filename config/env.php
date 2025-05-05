<?php
class Env {
    private static $variables = [];
    
    /**
     * Load environment variables from .env file
     * 
     * @param string $path Path to .env file
     * @return bool True if file was loaded successfully
     */
    public static function load($path = '.env') {
        if (!file_exists($path)) {
            return false;
        }
        
        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            list($name, $value) = explode('=', $line, 2);
            $name = trim($name);
            $value = trim($value);
            
            // Remove quotes if present
            if (strpos($value, '"') === 0 && strrpos($value, '"') === strlen($value) - 1) {
                $value = substr($value, 1, -1);
            } elseif (strpos($value, "'") === 0 && strrpos($value, "'") === strlen($value) - 1) {
                $value = substr($value, 1, -1);
            }
            
            self::$variables[$name] = $value;
        }
        
        return true;
    }
    
    /**
     * Get environment variable
     * 
     * @param string $name Variable name
     * @param mixed $default Default value if variable is not set
     * @return mixed Variable value or default
     */
    public static function get($name, $default = null) {
        return isset(self::$variables[$name]) ? self::$variables[$name] : $default;
    }
}
?>
