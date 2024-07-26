<?php

namespace AMDarter;

class ColorHelpers
{
    /**
     * Adjusts the brightness of a color by a given percentage.
     *
     * @param string $color The color to adjust.
     * @param float $percent The percentage to adjust the brightness by.
     * @return string The adjusted color.
     */
    public static function adjustBrightness(string $color, float $percent)
    {
        $isHex = self::isValidHexColor($color);
        // Convert the color to RGB format
        $rgb = $isHex ? self::hexToRGB($color) : $color;

        // Get the RGB values
        preg_match('/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/', $rgb, $matches);
        $r = $matches[1];
        $g = $matches[2];
        $b = $matches[3];

        // Calculate the adjusted RGB values
        $r_adj = max(0, min(255, round($r * (1 + $percent))));
        $g_adj = max(0, min(255, round($g * (1 + $percent))));
        $b_adj = max(0, min(255, round($b * (1 + $percent))));

        // Return the adjusted color
        if ($isHex) {
            return self::RGBToHex("rgb($r_adj, $g_adj, $b_adj)");
        } else {
            return "rgb($r_adj, $g_adj, $b_adj)";
        }
    }

    /**
     * Calculates the brightness of a given hex color, as a value between 0 and 255.
     *
     * @param string $color The hex color code.
     * @return int The color brightness, from 0 (black) to 255 (white).
     */
    public static function getHexColorBrightness($color)
    {
        // Remove the hash mark (#) if present
        $color = trim($color, '#');

        // Get the red, green, and blue values
        $r = hexdec(substr($color, 0, 2));
        $g = hexdec(substr($color, 2, 2));
        $b = hexdec(substr($color, 4, 2));

        // Calculate the color brightness using the ITU-R BT.709 formula
        $brightness = sqrt(
            $r * $r * 0.2126 +
            $g * $g * 0.7152 +
            $b * $b * 0.0722
        );

        return $brightness;
    }

    /**
     * Returns the darker hex color between the provided colors.
     *
     * @param string $color1 The first color.
     * @param string $color2 The second color.
     * @return string The darker color.
     */
    public static function getDarkerHexColor(string $color1, string $color2)
    {
        $color1_brightness = self::getHexColorBrightness($color1);
        $color2_brightness = self::getHexColorBrightness($color2);
        return $color1_brightness > $color2_brightness ? $color1 : $color2;
    }

    /**
     * Determines whether the provided string is a valid hexadecimal color.
     *
     * @param string $color The color string to check.
     * @return bool True if the string is a valid hexadecimal color, false otherwise.
     */
    public static function isValidHexColor(string $color) 
    {
        return preg_match('/^#?([A-Fa-f0-9]{3}){1,2}$/', $color) === 1;
    }

    /**
     * Determines whether the provided string is a valid RGB color.
     *
     * @param string $color The color string to check.
     * @return bool True if the string is a valid RGB color, false otherwise.
     */
    public static function isValidRGBColor(string $color) 
    {
        $color = str_replace(' ', '', $color);
        return preg_match('/^rgb\((\d{1,3}),(\d{1,3}),(\d{1,3})\)$/', $color) === 1;
    }

    /**
     * Converts a color from HEX format to RGB format.
     *
     * @param string $color The color to convert.
     * @return string The color in RGB format.
     */
    public static function hexToRGB(string $color)
    {
        if (self::isValidRGBColor($color)) {
            // Color is already in RGB format
            return $color;
        }

        $color = ltrim($color, '#');
        $hexLength = strlen($color);

        if ($hexLength == 3) {
            $r = hexdec(substr($color, 0, 1) . substr($color, 0, 1));
            $g = hexdec(substr($color, 1, 1) . substr($color, 1, 1));
            $b = hexdec(substr($color, 2, 1) . substr($color, 2, 1));
        } elseif ($hexLength == 6) {
            $r = hexdec(substr($color, 0, 2));
            $g = hexdec(substr($color, 2, 2));
            $b = hexdec(substr($color, 4, 2));
        } else {
            // Invalid color
            return '';
        }

        return "rgb($r, $g, $b)";
    }

    /**
     * Converts a color from RGB format to HEX format.
     *
     * @param string $color The color to convert.
     * @return string The color in HEX format.
     */
    public static function RGBToHex(string $color)
    {
        if (self::isValidHexColor($color)) {
            // Color is already in HEX format
            return $color;
        }

        preg_match('/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/', $color, $matches);

        if (!isset($matches[1]) || !isset($matches[2]) || !isset($matches[3])) {
            // Invalid color
            return '';
        }

        $r = dechex($matches[1]);
        $g = dechex($matches[2]);
        $b = dechex($matches[3]);

        return '#' . str_pad($r, 2, '0', STR_PAD_LEFT) . str_pad($g, 2, '0', STR_PAD_LEFT) . str_pad($b, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Returns the color that provides the highest contrast to the provided color.
     * 
     * Use case: Should we use black or white text for the given background color?
     *
     * @param string $color The color to find the contrast color for.
     * @return string The contrast color.
     */
    public static function getContrastColor(string $color)
    {
        $isHex = self::isValidHexColor($color);
        // Convert the color to RGB format
        $rgb = $isHex ? self::hexToRGB($color) : $color;

        // Get the RGB values
        preg_match('/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/', $rgb, $matches);
        $r = $matches[1];
        $g = $matches[2];
        $b = $matches[3];

        // Calculate the luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;

        // Choose the contrast color based on the luminance
        if ($luminance > 0.5) {
            return '#000000'; // Black
        } else {
            return '#FFFFFF'; // White
        }
    }

    /**
     * Returns an array containing the complementary colors of a given color code.
     *
     * @param string $color The hex or RGB color code to find the complementary colors for.
     * @return string The complementary color in the same format as the input color.
     */
    public static function getComplementaryColor(string $color): string
    {
        $isHex = self::isValidHexColor($color);
        // Convert the color to RGB format
        $rgb = $isHex ? self::hexToRGB($color) : $color;

        // Get the RGB values
        preg_match('/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/', $rgb, $matches);
        $r = intval($matches[1]);
        $g = intval($matches[2]);
        $b = intval($matches[3]);

        // Find the complementary color
        $complementary_r = 255 - $r;
        $complementary_g = 255 - $g;
        $complementary_b = 255 - $b;

        // Return the complementary colors in the same format as the input
        if ($isHex) {
            return self::RGBToHex("rgb($complementary_r, $complementary_g, $complementary_b)");
        } else {
            return "rgb($complementary_r, $complementary_g, $complementary_b)";
        }
    }

    /**
     * Compares the brightness of two colors.
     *
     * @param string $color1 The first color.
     * @param string $color2 The second color.
     * @return int -1 if $color1 is darker than $color2, 0 if they have the same brightness, or 1 if $color1 is lighter than $color2.
     */
    public static function compareColorsByBrightness(string $color1, string $color2)
    {
        $brightness1 = self::getHexColorBrightness($color1);
        $brightness2 = self::getHexColorBrightness($color2);

        if ($brightness1 == $brightness2) {
            return 0;
        }

        return ($brightness1 < $brightness2) ? -1 : 1;
    }

    /**
     * Sorts an array of colors by their brightness in ascending order.
     *
     * @param array $colors An array of color values.
     * @return array The sorted array of color values.
     */
    public static function sortColorsByBrightness(array $colors)
    {
        usort($colors, [self::class, 'compareColorsByBrightness']);
        return $colors;
    }

    /**
     * Converts an HSL color value to RGB.
     *
     * @param float $hue The hue value in the range [0, 360].
     * @param float $saturation The saturation value in the range [0, 1].
     * @param float $lightness The lightness value in the range [0, 1].
     * @return array An array of integers representing the RGB color values.
     */
    public static function hslToRgb(float $hue, float $saturation, float $lightness)
    {
        $hue /= 360;
        $q = $lightness < 0.5 ? $lightness * (1 + $saturation) : $lightness + $saturation - $lightness * $saturation;
        $p = 2 * $lightness - $q;
        $rgb = [];
        for ($i = 0; $i < 3; $i++) {
            $t = [$hue + 1 / 3, $hue, $hue - 1 / 3][$i];
            if ($t < 0) {
                $t += 1;
            } elseif ($t > 1) {
                $t -= 1;
            }
            if ($t < 1 / 6) {
                $rgb[$i] = $p + ($q - $p) * 6 * $t;
            } elseif ($t < 1 / 2) {
                $rgb[$i] = $q;
            } elseif ($t < 2 / 3) {
                $rgb[$i] = $p + ($q - $p) * (2 / 3 - $t) * 6;
            } else {
                $rgb[$i] = $p;
            }
        }
        return array_map(function ($channel) {
            return intval(round($channel * 255));
        }, $rgb);
    }

    /**
     * Converts RGB values to HSL values.
     *
     * @param int $r The red value.
     * @param int $g The green value.
     * @param int $b The blue value.
     * @return array The corresponding HSL values.
     */
    public static function rgbToHsl($r, $g, $b)
    {
        $r /= 255;
        $g /= 255;
        $b /= 255;
        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $lightness = ($max + $min) / 2;
        if ($max == $min) {
            $hue = $saturation = 0;
        } else {
            $delta = $max - $min;
            $saturation = $lightness > 0.5 ? $delta / (2 - $max - $min) : $delta / ($max + $min);
            switch ($max) {
                case $r:
                    $hue = ($g - $b) / $delta + ($g < $b ? 6 : 0);
                    break;
                case $g:
                    $hue = ($b - $r) / $delta + 2;
                    break;
                case $b:
                    $hue = ($r - $g) / $delta + 4;
                    break;
            }
            $hue /= 6;
        }
        return [round($hue * 360), round($saturation * 100), round($lightness * 100)];
    }

    /**
     * Returns an array containing $numColors of analogous colors for a given color code.
     *
     * @param string $color The color code in HEX or RGB format.
     * @param int $numColors The number of analogous colors to return.
     * @return array An array of $numColors analogous colors in the same format as the input color code.
     */
    public static function getAnalogousColors(string $color, int $numColors = 3)
    {
        $isHex = self::isValidHexColor($color);
        // Convert the color to RGB format
        $rgb = $isHex ? self::hexToRGB($color) : $color;

        // Get the RGB values
        preg_match('/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/', $rgb, $matches);
        $r = isset($matches[1]) ? intval($matches[1]) : 0;
        $g = isset($matches[2]) ? intval($matches[2]) : 0;
        $b = isset($matches[3]) ? intval($matches[3]) : 0;

        // Calculate the hue of the color
        list($hue, $saturation, $lightness) = self::rgbToHsl($r, $g, $b);

        // Calculate the hues of the analogous colors
        $hues = [];
        $hues[] = $hue - 30;
        for ($i = 1; $i < $numColors; $i++) {
            $hues[] = $hues[$i - 1] - 30;
        }

        // Convert the hues back to RGB colors
        $colors = [];
        foreach ($hues as $h) {
            $h = fmod($h, 360);
            list($r, $g, $b) = self::hslToRgb($h, $saturation, $lightness);
            if ($isHex) {
                $colors[] = self::RGBToHex("rgb($r, $g, $b)");
            } else {
                $colors[] = "rgb($r, $g, $b)";
            }
        }

        return $colors;
    }

    /**
     * Returns an array containing the triadic colors for a given color.
     *
     * @param string $color The color to find the triadic colors for.
     * @return array An array of triadic colors.
     */
    public static function getTriadicColors(string $color)
    {
        $isHex = self::isValidHexColor($color);
        // Convert color to RGB format
        $rgb = $isHex ? self::hexToRGB($color) : $color;

        // Get RGB values
        $matches = [];
        preg_match('/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/', $rgb, $matches);
        $r = $matches[1];
        $g = $matches[2];
        $b = $matches[3];

        // Calculate the triadic colors
        // $triadic1 = self::RGBToHex($b, $r, $g);
        // $triadic2 = self::RGBToHex($g, $b, $r);
        $triadic1 = "rgb($b, $r, $g)";
        $triadic2 = "rgb($g, $b, $r)";

        // Return the results in the same format as the input color
        if ($isHex) {
            return array(
                self::RGBToHex($triadic1),
                self::RGBToHex($triadic2)
            );
        } else {
            return array("rgb($triadic1)", "rgb($triadic2)");
        }
    }
}