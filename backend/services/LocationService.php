<?php

require_once __DIR__ . '/../../config/Database.php';

class LocationService
{
    /**
     * Check if the user's IP is allowed for check-in/check-out
     *
     * @param string|null $ip
     * @return bool
     */
    public static function isAllowedIP(?string $ip): bool
    {
        if (!$ip) return false;

        $db = Database::getConnection();

        // Assuming you have a table locations with columns: id, name, ip_address
        $stmt = $db->prepare("SELECT COUNT(*) FROM locations WHERE ip_address = ?");
        $stmt->execute([$ip]);

        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get location info by IP
     *
     * @param string|null $ip
     * @return array|null
     */
    public static function getLocationByIP(?string $ip): ?array
    {
        if (!$ip) return null;

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM locations WHERE ip_address = ?");
        $stmt->execute([$ip]);
        $location = $stmt->fetch(PDO::FETCH_ASSOC);

        return $location ?: null;
    }
}