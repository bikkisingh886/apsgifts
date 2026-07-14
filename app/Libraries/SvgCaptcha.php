<?php

namespace App\Libraries;

class SvgCaptcha
{
    /**
     * Generate a random code.
     */
    public static function generateCode(int $length = 5): string
    {
        // Avoid confusing characters: 0, O, o, 1, I, l
        $chars = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
        $code = '';
        $max = strlen($chars) - 1;
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, $max)];
        }
        return $code;
    }

    /**
     * Generate SVG string for the given code.
     */
    public static function generateSvg(string $code, int $width = 150, int $height = 50): string
    {
        $length = strlen($code);
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '" style="background:#f4f6f9; border-radius: 4px; user-select:none; pointer-events:none;">';
        
        // Add random grid lines / noise lines
        for ($i = 0; $i < 6; $i++) {
            $x1 = random_int(0, $width);
            $y1 = random_int(0, $height);
            $x2 = random_int(0, $width);
            $y2 = random_int(0, $height);
            $color = sprintf('#%06X', random_int(0x888888, 0xDDDDDD));
            $strokeWidth = random_int(1, 3);
            $svg .= '<line x1="' . $x1 . '" y1="' . $y1 . '" x2="' . $x2 . '" y2="' . $y2 . '" stroke="' . $color . '" stroke-width="' . $strokeWidth . '" opacity="0.6"/>';
        }

        // Add some noise circles
        for ($i = 0; $i < 15; $i++) {
            $cx = random_int(0, $width);
            $cy = random_int(0, $height);
            $r = random_int(1, 4);
            $color = sprintf('#%06X', random_int(0xAAAAAA, 0xEEEEEE));
            $svg .= '<circle cx="' . $cx . '" cy="' . $cy . '" r="' . $r . '" fill="' . $color . '" opacity="0.7"/>';
        }

        // Draw distorted text
        $charWidth = ($width - 20) / $length;
        for ($i = 0; $i < $length; $i++) {
            $char = $code[$i];
            $x = 10 + ($i * $charWidth) + random_int(0, 5);
            $y = $height / 2 + random_int(4, 10);
            $angle = random_int(-25, 25);
            $fontSize = random_int(22, 28);
            
            // Generate dark readable colors for text
            $r_color = random_int(20, 100);
            $g_color = random_int(20, 100);
            $b_color = random_int(20, 100);
            $color = sprintf('rgb(%d,%d,%d)', $r_color, $g_color, $b_color);
            
            // Use standard clean sans-serif/monospace font
            $fontFamily = ['Arial', 'Helvetica', 'Verdana', 'Impact', 'Courier New'][random_int(0, 4)];
            
            // Text transformation
            $svg .= '<text x="' . $x . '" y="' . $y . '" fill="' . $color . '" font-size="' . $fontSize . '" font-family="' . $fontFamily . '" font-weight="bold" transform="rotate(' . $angle . ' ' . $x . ' ' . $y . ')" style="user-select:none;">' . esc($char) . '</text>';
        }

        // Add overlapping wavy wave lines
        for ($i = 0; $i < 2; $i++) {
            $color = sprintf('#%06X', random_int(0x444444, 0x888888));
            $strokeWidth = random_int(1, 2);
            $amplitude = random_int(5, 12);
            $frequency = random_int(20, 40);
            $phase = random_int(0, 100);
            
            $d = "M 0 " . ($height / 2);
            for ($x = 0; $x <= $width; $x += 10) {
                $y = ($height / 2) + sin(($x + $phase) / $frequency) * $amplitude;
                $d .= " L $x $y";
            }
            $svg .= '<path d="' . $d . '" fill="none" stroke="' . $color . '" stroke-width="' . $strokeWidth . '" opacity="0.4"/>';
        }

        $svg .= '</svg>';
        return $svg;
    }
}
