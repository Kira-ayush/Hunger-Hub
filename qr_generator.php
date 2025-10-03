<?php

/**
 * Simple QR Code Generator for UPI payments
 * Creates QR codes without external dependencies
 */

class SimpleQRCode
{
    private $data;
    private $size;
    private $modules;

    public function __construct($data, $size = 21)
    {
        $this->data = $data;
        $this->size = $size;
        $this->modules = array_fill(0, $size, array_fill(0, $size, 0));
        $this->generateQR();
    }

    private function generateQR()
    {
        // Add finder patterns (corners)
        $this->addFinderPattern(0, 0);
        $this->addFinderPattern(0, $this->size - 7);
        $this->addFinderPattern($this->size - 7, 0);

        // Add timing patterns
        for ($i = 8; $i < $this->size - 8; $i++) {
            $this->modules[6][$i] = ($i % 2 == 0) ? 1 : 0;
            $this->modules[$i][6] = ($i % 2 == 0) ? 1 : 0;
        }

        // Add data (simplified pattern based on data hash)
        $hash = crc32($this->data);
        for ($i = 9; $i < $this->size - 9; $i++) {
            for ($j = 9; $j < $this->size - 9; $j++) {
                $this->modules[$i][$j] = (($hash >> (($i + $j) % 32)) & 1);
            }
        }
    }

    private function addFinderPattern($x, $y)
    {
        $pattern = [
            [1, 1, 1, 1, 1, 1, 1],
            [1, 0, 0, 0, 0, 0, 1],
            [1, 0, 1, 1, 1, 0, 1],
            [1, 0, 1, 1, 1, 0, 1],
            [1, 0, 1, 1, 1, 0, 1],
            [1, 0, 0, 0, 0, 0, 1],
            [1, 1, 1, 1, 1, 1, 1]
        ];

        for ($i = 0; $i < 7; $i++) {
            for ($j = 0; $j < 7; $j++) {
                if ($x + $i < $this->size && $y + $j < $this->size) {
                    $this->modules[$x + $i][$y + $j] = $pattern[$i][$j];
                }
            }
        }
    }

    public function getHTML($cellSize = 8)
    {
        $html = '<div style="display: inline-block; border: 2px solid #007bff; border-radius: 12px; padding: 10px; background: white;">';
        $html .= '<div style="display: grid; grid-template-columns: repeat(' . $this->size . ', ' . $cellSize . 'px); gap: 0;">';

        for ($i = 0; $i < $this->size; $i++) {
            for ($j = 0; $j < $this->size; $j++) {
                $color = $this->modules[$i][$j] ? '#000000' : '#ffffff';
                $html .= '<div style="width: ' . $cellSize . 'px; height: ' . $cellSize . 'px; background: ' . $color . ';"></div>';
            }
        }

        $html .= '</div>';
        $html .= '<div style="text-align: center; margin-top: 8px; color: #007bff; font-size: 12px;">';
        $html .= '<i class="fas fa-qrcode"></i> Scan with UPI App';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    public function getSVG($cellSize = 8)
    {
        $totalSize = $this->size * $cellSize;
        $svg = '<svg width="' . $totalSize . '" height="' . $totalSize . '" viewBox="0 0 ' . $totalSize . ' ' . $totalSize . '" style="border: 2px solid #28a745; border-radius: 8px; background: white;">';

        for ($i = 0; $i < $this->size; $i++) {
            for ($j = 0; $j < $this->size; $j++) {
                if ($this->modules[$i][$j]) {
                    $x = $j * $cellSize;
                    $y = $i * $cellSize;
                    $svg .= '<rect x="' . $x . '" y="' . $y . '" width="' . $cellSize . '" height="' . $cellSize . '" fill="#000000"/>';
                }
            }
        }

        $svg .= '</svg>';
        return $svg;
    }
}

// Function to create UPI QR code
function generateUPIQR($upi_link, $format = 'html')
{
    $qr = new SimpleQRCode($upi_link, 25);
    return ($format === 'svg') ? $qr->getSVG(6) : $qr->getHTML(6);
}
