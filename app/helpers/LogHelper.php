<?php

class LogHelper
{
    private static $logFile = APPROOT . '/logs/activity.log';
    
    /**
     * Log een actie naar het log bestand
     */
    public static function logAction($action, $module, $details = '', $userId = null)
    {
        // Zorg ervoor dat de log directory bestaat
        $logDir = dirname(self::$logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
        
        // Haal gebruiker info op als deze niet is meegegeven
        if ($userId === null) {
            $userId = $_SESSION['user_id'] ?? 0;
        }
        
        $userName = $_SESSION['username'] ?? 'Unknown';
        $userEmail = $_SESSION['user_email'] ?? 'unknown@email.com';
        
        // Haal client IP op
        $clientIp = self::getClientIp();
        
        // Maak log entry
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = sprintf(
            "[%s] USER_ID:%d USER:%s EMAIL:%s IP:%s MODULE:%s ACTION:%s DETAILS:%s\n",
            $timestamp,
            $userId,
            $userName,
            $userEmail,
            $clientIp,
            strtoupper($module),
            strtoupper($action),
            $details
        );
        
        // Schrijf naar log bestand
        error_log($logEntry, 3, self::$logFile);
    }
    
    /**
     * Log een create actie
     */
    public static function logCreate($module, $itemName, $itemId = null)
    {
        $details = "Created: $itemName";
        if ($itemId) {
            $details .= " (ID: $itemId)";
        }
        self::logAction('CREATE', $module, $details);
    }
    
    /**
     * Log een update actie
     */
    public static function logUpdate($module, $itemName, $itemId = null, $changes = [])
    {
        $details = "Updated: $itemName";
        if ($itemId) {
            $details .= " (ID: $itemId)";
        }
        if (!empty($changes)) {
            $details .= " - Changes: " . implode(', ', $changes);
        }
        self::logAction('UPDATE', $module, $details);
    }
    
    /**
     * Log een delete actie
     */
    public static function logDelete($module, $itemName, $itemId = null, $reason = '')
    {
        $details = "Deleted: $itemName";
        if ($itemId) {
            $details .= " (ID: $itemId)";
        }
        if ($reason) {
            $details .= " - Reason: $reason";
        }
        self::logAction('DELETE', $module, $details);
    }
    
    /**
     * Log een delete poging die werd geweigerd
     */
    public static function logDeleteDenied($module, $itemName, $itemId = null, $reason = '')
    {
        $details = "DELETE DENIED: $itemName";
        if ($itemId) {
            $details .= " (ID: $itemId)";
        }
        if ($reason) {
            $details .= " - Reason: $reason";
        }
        self::logAction('DELETE_DENIED', $module, $details);
    }
    
    /**
     * Haal client IP adres op
     */
    private static function getClientIp()
    {
        $ipKeys = array('HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR');
        
        foreach ($ipKeys as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
    }
    
    /**
     * Lees de laatste log entries
     */
    public static function getRecentLogs($lines = 100)
    {
        if (!file_exists(self::$logFile)) {
            return [];
        }
        
        $logs = [];
        $file = new SplFileObject(self::$logFile);
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();
        
        $startLine = max(0, $totalLines - $lines);
        $file->seek($startLine);
        
        while (!$file->eof()) {
            $line = trim($file->fgets());
            if (!empty($line)) {
                $logs[] = $line;
            }
        }
        
        return array_reverse($logs);
    }
}
