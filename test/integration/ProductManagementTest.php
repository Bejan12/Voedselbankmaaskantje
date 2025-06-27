<?php
use PHPUnit\Framework\TestCase;

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

class ProductManagementTest extends TestCase
{
    private $controller;
    private $model;
    
    protected function setUp(): void
    {
        $this->controller = new Magazijnvoorraad();
        $this->model = new MagazijnvoorraadModel();
    }
    
    /**
     * Integration test: Het complete probleem scenario
     */
    public function testAardappelenCategorieProblemScenario()
    {
        // Scenario: Gebruiker probeert "Aardappelen vastkokend 2kg" 
        // te wijzigen van categorie naar "Bakkerij en banket"
        
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductCategory');
        $method->setAccessible(true);
        
        // Step 1: Controleer dat aardappelen WEL past bij categorie 1
        $result = $method->invoke($this->controller, 'Aardappelen vastkokend 2kg', 1);
        $this->assertTrue($result['isValid'], 
            'Aardappelen should be valid for Aardappelen category (ID 1)');
        
        // Step 2: Controleer dat aardappelen NIET past bij categorie 6 (Bakkerij)
        $result = $method->invoke($this->controller, 'Aardappelen vastkokend 2kg', 6);
        $this->assertFalse($result['isValid'], 
            'Aardappelen should NOT be valid for Bakkerij category (ID 6)');
        $this->assertStringContainsString('past niet bij categorie', $result['message']);
        
        // Step 3: Test verschillende aardappel varianten
        $aardappelVarianten = [
            'Aardappelen vastkokend 2kg',
            'aardappel kruimig 1kg',
            'Rode aardappelen',
            'AARDAPPELEN BIOLOGISCH'
        ];
        
        foreach ($aardappelVarianten as $variant) {
            $result = $method->invoke($this->controller, $variant, 6);
            $this->assertFalse($result['isValid'], 
                "Aardappel variant '$variant' should NOT be allowed in Bakkerij category");
        }
    }
    
    /**
     * Test volledige workflow van product wijziging
     */
    public function testCompleteProductUpdateWorkflow()
    {
        // Simuleer het wijzigen van een product
        $productData = [
            'id' => 1,
            'naam' => 'Aardappelen vastkokend 2kg',
            'oude_categorie' => 1,
            'nieuwe_categorie' => 6, // Probeer naar bakkerij te wijzigen
            'leverancier_id' => 1,
            'voorraad' => 10
        ];
        
        // Test de validatie
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductCategory');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->controller, 
            $productData['naam'], 
            $productData['nieuwe_categorie']);
        
        // Verwachting: Dit zou moeten falen
        $this->assertFalse($result['isValid']);
        $this->assertArrayHasKey('message', $result);
        
        // Test met correcte categorie
        $result = $method->invoke($this->controller, 
            $productData['naam'], 
            $productData['oude_categorie']);
        
        $this->assertTrue($result['isValid']);
    }
}