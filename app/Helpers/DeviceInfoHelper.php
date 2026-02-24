<?php

namespace App\Helpers;

use Illuminate\Http\Request;

class DeviceInfoHelper
{
    /**
     * Get a human-readable device info string from the request.
     * Uses User-Agent Client Hints (Sec-CH-UA-*) for accuracy,
     * with fallback to traditional User-Agent parsing.
     */
    public static function getDeviceInfo(Request $request): string
    {
        $os = self::getOS($request);
        $browser = self::getBrowser($request);
        $arch = self::getArchitecture($request);

        $result = $os;
        if ($arch) {
            $result .= " ($arch)";
        }
        $result .= " - $browser";

        return $result;
    }

    /**
     * Detect the OS name + version.
     * Prioritizes Sec-CH-UA-Platform and Sec-CH-UA-Platform-Version headers.
     */
    private static function getOS(Request $request): string
    {
        $platform = self::cleanHeader($request->header('Sec-CH-UA-Platform'));
        $platformVersion = self::cleanHeader($request->header('Sec-CH-UA-Platform-Version'));
        $ua = $request->userAgent() ?? '';

        // --- Client Hints available ---
        if ($platform) {
            if (strtolower($platform) === 'windows') {
                if ($platformVersion) {
                    $major = (int) explode('.', $platformVersion)[0];
                    // Windows 11 reports platformVersion >= 13.0.0
                    if ($major >= 13) {
                        return 'Windows 11';
                    }
                    return 'Windows 10';
                }
                return 'Windows';
            }

            if (strtolower($platform) === 'macos' || strtolower($platform) === 'mac os x') {
                return 'macOS';
            }

            if (strtolower($platform) === 'android') {
                return 'Android';
            }

            if (strtolower($platform) === 'linux') {
                return 'Linux';
            }

            if (strtolower($platform) === 'ios') {
                return 'iOS';
            }

            if (strtolower($platform) === 'chrome os') {
                return 'Chrome OS';
            }

            return $platform;
        }

        // --- Fallback: parse traditional User-Agent ---
        return self::parseOSFromUA($ua);
    }

    /**
     * Detect the browser name from Client Hints or User-Agent.
     */
    private static function getBrowser(Request $request): string
    {
        // Try Sec-CH-UA header (e.g. "Chromium";v="120", "Google Chrome";v="120")
        $chUA = $request->header('Sec-CH-UA');
        if ($chUA) {
            // Parse the branded list, pick the most specific brand
            $brands = self::parseClientHintsBrands($chUA);
            if (!empty($brands)) {
                return $brands[0]; // First priority brand
            }
        }

        // Fallback to User-Agent
        return self::parseBrowserFromUA($request->userAgent() ?? '');
    }

    /**
     * Detect architecture (arm vs x86) from Client Hints.
     */
    private static function getArchitecture(Request $request): ?string
    {
        $arch = self::cleanHeader($request->header('Sec-CH-UA-Arch'));
        $platform = self::cleanHeader($request->header('Sec-CH-UA-Platform'));

        if (!$arch) {
            return null;
        }

        $archLower = strtolower($arch);

        // Only label architecture when it's meaningful (e.g. macOS ARM = Apple Silicon)
        if ($platform && strtolower($platform) === 'macos') {
            if ($archLower === 'arm') {
                return 'Apple Silicon';
            }
            if ($archLower === 'x86') {
                return 'Intel';
            }
        }

        if ($archLower === 'arm') {
            return 'ARM';
        }

        return null;
    }

    /**
     * Parse OS info from the traditional User-Agent string.
     */
    private static function parseOSFromUA(string $ua): string
    {
        // iOS
        if (preg_match('/iPhone|iPad|iPod/', $ua)) {
            if (preg_match('/OS (\d+)[_.](\d+)/', $ua, $m)) {
                return "iOS {$m[1]}.{$m[2]}";
            }
            return 'iOS';
        }

        // Android
        if (preg_match('/Android\s*([\d.]+)?/', $ua, $m)) {
            return 'Android' . (isset($m[1]) ? " {$m[1]}" : '');
        }

        // Windows — can't distinguish 10 vs 11 from UA alone
        if (preg_match('/Windows NT 10/', $ua)) {
            return 'Windows 10/11';
        }
        if (preg_match('/Windows NT 6\.3/', $ua)) {
            return 'Windows 8.1';
        }
        if (preg_match('/Windows NT 6\.2/', $ua)) {
            return 'Windows 8';
        }
        if (preg_match('/Windows NT 6\.1/', $ua)) {
            return 'Windows 7';
        }
        if (preg_match('/Windows/', $ua)) {
            return 'Windows';
        }

        // macOS — can't distinguish Intel vs ARM from UA alone
        if (preg_match('/Macintosh.*Mac OS X\s*([\d_]+)?/', $ua, $m)) {
            $ver = isset($m[1]) ? str_replace('_', '.', $m[1]) : '';
            return 'macOS' . ($ver ? " $ver" : '');
        }

        // Linux
        if (preg_match('/Linux/', $ua)) {
            return 'Linux';
        }

        // Chrome OS
        if (preg_match('/CrOS/', $ua)) {
            return 'Chrome OS';
        }

        return 'Unknown OS';
    }

    /**
     * Parse browser info from the traditional User-Agent string.
     */
    private static function parseBrowserFromUA(string $ua): string
    {
        // Order matters: check specific browsers before generic ones
        if (preg_match('/Edg(?:e|A|iOS)?\/(\d+)/', $ua, $m)) {
            return "Edge {$m[1]}";
        }
        if (preg_match('/OPR\/(\d+)/', $ua, $m)) {
            return "Opera {$m[1]}";
        }
        if (preg_match('/Brave/', $ua)) {
            return 'Brave';
        }
        if (preg_match('/Vivaldi\/(\d+)/', $ua, $m)) {
            return "Vivaldi {$m[1]}";
        }
        if (preg_match('/Chrome\/(\d+)/', $ua, $m)) {
            return "Chrome {$m[1]}";
        }
        if (preg_match('/Safari\/.*Version\/(\d+)/', $ua, $m)) {
            return "Safari {$m[1]}";
        }
        if (preg_match('/Firefox\/(\d+)/', $ua, $m)) {
            return "Firefox {$m[1]}";
        }

        return 'Unknown Browser';
    }

    /**
     * Parse Sec-CH-UA branded list into an array of "Brand Version" strings.
     * Prioritizes real brands over generic "Chromium".
     */
    private static function parseClientHintsBrands(string $header): array
    {
        $brands = [];
        $chromiumVersion = null;

        // Match patterns like: "Google Chrome";v="120" or "Chromium";v="120"
        preg_match_all('/"([^"]+)"\s*;\s*v="([^"]+)"/', $header, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $brand = trim($match[1]);
            $version = trim($match[2]);

            // Skip known filler/grease brands
            if (str_contains($brand, 'Not') || str_contains($brand, 'Greasy') || str_contains($brand, '99')) {
                continue;
            }

            if (strtolower($brand) === 'chromium') {
                $chromiumVersion = $version;
                continue;
            }

            $brands[] = "$brand $version";
        }

        // If we only found Chromium, use that
        if (empty($brands) && $chromiumVersion) {
            $brands[] = "Chromium $chromiumVersion";
        }

        return $brands;
    }

    /**
     * Remove surrounding quotes from a header value.
     */
    private static function cleanHeader(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }
        return trim($value, '" ');
    }
}
