<?php
use PHPUnit\Framework\TestCase;

// Direct laden van bestanden
if (!class_exists('Magazijnvoorraad')) {
    $appPath = dirname(dirname(__DIR__)) . '/app';
    
    if (!defined('TESTING')) define('TESTING', true);
    if (!isset($_SESSION)) $_SESSION = [];
    
    require_once $appPath . '/config/config.php';
    require_once $appPath . '/libraries/Database.php';
    require_once $appPath . '/libraries/BaseController.php';
    require_once $appPath . '/models/MagazijnvoorraadModel.php';
    require_once $appPath . '/controllers/Magazijnvoorraad.php';
    require_once dirname(__DIR__) . '/mocks/MockDatabase.php';
}

class MagazijnvoorraadTest extends TestCase
{
    private $controller;
    
    protected function setUp(): void
    {
        $this->controller = new Magazijnvoorraad();
    }
    
    /**
     * Test categorie validatie - Focus op het hoofdprobleem: Aardappelen
     */
    public function testValidateProductCategoryExtensive()
    {
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductCategory');
        $method->setAccessible(true);
        
        // Test het HOOFDPROBLEEM: Aardappelen mogen niet naar andere categorieën
        $aardappelTestCases = [
            // Aardappelen categorie (ID 1) - CORRECT
            ['Aardappelen vastkokend 2kg', 1, true, 'Aardappelen hoort bij Aardappelen categorie'],
            ['aardappel kruimig', 1, true, 'Aardappel (lowercase) hoort bij Aardappelen categorie'],
            ['Rode aardappelen', 1, true, 'Rode aardappelen hoort bij Aardappelen categorie'],
            
            // Aardappelen in VERKEERDE categorieën - FOUT (het probleem dat we hebben opgelost)
            ['Aardappelen vastkokend 2kg', 6, false, 'Aardappelen hoort NIET bij Bakkerij categorie'],
            ['aardappel kruimig', 5, false, 'Aardappelen hoort NIET bij Vlees categorie'],
            ['Rode aardappelen', 4, false, 'Aardappelen hoort NIET bij Zuivel categorie'],
            ['Aardappelen vastkokend 2kg', 2, false, 'Aardappelen hoort NIET bij Groente categorie'],
            ['Aardappelen vastkokend 2kg', 3, false, 'Aardappelen hoort NIET bij Fruit categorie'],
        ];
        
        foreach ($aardappelTestCases as [$productNaam, $categorieId, $verwachtResult, $beschrijving]) {
            $result = $method->invoke($this->controller, $productNaam, $categorieId);
            
            $this->assertEquals($verwachtResult, $result['isValid'], 
                "FAILED: $beschrijving (Product: '$productNaam', Categorie: $categorieId)");
            
            if (!$result['isValid']) {
                $this->assertArrayHasKey('message', $result, 'Error result should have message');
                $this->assertStringContainsString('past niet bij categorie', $result['message']);
            }
        }
    }
    
    /**
     * Test specifiek het scenario uit het probleem
     */
    public function testMainProblemScenario()
    {
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductCategory');
        $method->setAccessible(true);
        
        // Het exacte probleem: "Aardappelen vastkokend 2kg" naar "Bakkerij en banket" (ID 6)
        $result = $method->invoke($this->controller, 'Aardappelen vastkokend 2kg', 6);
        
        $this->assertFalse($result['isValid'], 
            'HOOFDPROBLEEM: Aardappelen vastkokend 2kg mag NIET naar Bakkerij categorie');
        $this->assertArrayHasKey('message', $result);
        $this->assertStringContainsString('past niet bij categorie', $result['message']);
    }
    
    /**
     * Test verschillende aardappel varianten
     */
    public function testAardappelVarianten()
    {
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductCategory');
        $method->setAccessible(true);
        
        $aardappelVarianten = [
            'Aardappelen vastkokend 2kg',
            'aardappel kruimig 1kg',
            'Rode aardappelen',
            'AARDAPPELEN BIOLOGISCH',
            'Aardappel nieuwe oogst',
            'Zoete aardappelen'
        ];
        
        foreach ($aardappelVarianten as $variant) {
            // Test dat ze WEL mogen bij categorie 1
            $result = $method->invoke($this->controller, $variant, 1);
            $this->assertTrue($result['isValid'], 
                "Aardappel variant '$variant' should be valid for category 1");
            
            // Test dat ze NIET mogen bij categorie 6 (Bakkerij)
            $result = $method->invoke($this->controller, $variant, 6);
            $this->assertFalse($result['isValid'], 
                "Aardappel variant '$variant' should NOT be valid for Bakkerij category");
        }
    }
    
    /**
     * Test edge cases voor categorie validatie
     */
    public function testValidateProductCategoryEdgeCases()
    {
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductCategory');
        $method->setAccessible(true);
        
        // Test lege/ongeldige input
        $result = $method->invoke($this->controller, '', 1);
        $this->assertFalse($result['isValid'], 'Empty product name should be invalid');
        
        $result = $method->invoke($this->controller, 'Test Product', 0);
        $this->assertFalse($result['isValid'], 'Invalid category ID should be invalid');
        
        $result = $method->invoke($this->controller, 'Test Product', -1);
        $this->assertFalse($result['isValid'], 'Negative category ID should be invalid');
        
        // Test zeer lange productnaam met aardappelen
        $longName = str_repeat('Aardappelen ', 50);
        $result = $method->invoke($this->controller, $longName, 1);
        $this->assertTrue($result['isValid'], 'Long product name with aardappelen should be valid for category 1');
        
        // Test hoofdletters variaties
        $result = $method->invoke($this->controller, 'AARDAPPELEN VASTKOKEND', 1);
        $this->assertTrue($result['isValid'], 'Uppercase aardappelen should be valid for category 1');
        
        $result = $method->invoke($this->controller, 'AARDAPPELEN VASTKOKEND', 6);
        $this->assertFalse($result['isValid'], 'Uppercase aardappelen should NOT be valid for category 6');
    }
    
    /**
     * Test controller instantiatie en basis functionaliteit
     */
    public function testControllerInstantiation()
    {
        $this->assertInstanceOf('Magazijnvoorraad', $this->controller);
        $this->assertInstanceOf('BaseController', $this->controller);
    }
    
    /**
     * Test dat benodigde methods bestaan
     */
    public function testRequiredMethodsExist()
    {
        $this->assertTrue(
            method_exists($this->controller, 'validateProductCategory'),
            "Method 'validateProductCategory' should exist in Magazijnvoorraad controller"
        );
        
        // Test dat de methode private is (zoals het hoort)
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductCategory');
        $this->assertTrue($method->isPrivate(), 'validateProductCategory should be private');
    }
}