<?php
use PHPUnit\Framework\TestCase;

class KlantModelTest extends TestCase
{
    public function testTotaalAantalPersonen()
    {
        $klant = [
            'aantal_volwassenen' => 2,
            'aantal_kinderen' => 1,
            'aantal_babys' => 1
        ];
        $totaal = $klant['aantal_volwassenen'] + $klant['aantal_kinderen'] + $klant['aantal_babys'];
        $this->assertEquals(4, $totaal);
    }
}
