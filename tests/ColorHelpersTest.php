<?php

use PHPUnit\Framework\TestCase;
use ColorHelpers\ColorHelpers;

class ColorHelpersTest extends TestCase
{
    public function testAdjustBrightness()
    {
        $color = '#000000';
        $adjustedColor = ColorHelpers::adjustBrightness($color, 0.1);
        $this->assertEquals('#1a1a1a', $adjustedColor);
    }

    public function testGetHexColorBrightness()
    {
        $brightness = ColorHelpers::getHexColorBrightness('#ffffff');
        $this->assertEquals(255, $brightness);
    }

    // Add more tests for other methods...
}
