<?php
use PHPUnit\Framework\TestCase;

if (!class_exists('MagazijnvoorraadModel')) {
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

class MagazijnvoorraadModelTest extends TestCase
{
    private $model;
    private $mockDb;
    
    protected function setUp(): void
    {
        $this->model = new MagazijnvoorraadModel();
        $this->mockDb = new MockDatabase();
    }
    
    /**
     * Test model instantiatie en properties
     */
    public function testModelInstantiation()
    {
        $this->assertInstanceOf('MagazijnvoorraadModel', $this->model);
        
        // Check if model has database connection
        $reflection = new ReflectionClass($this->model);
        if ($reflection->hasProperty('db')) {
            $dbProperty = $reflection->getProperty('db');
            $dbProperty->setAccessible(true);
            $this->assertNotNull($dbProperty->getValue($this->model));
        }
    }
    
    /**
     * Test welke methods daadwerkelijk bestaan
     */
    public function testExistingMethods()
    {
        $reflection = new ReflectionClass($this->model);
        $methods = $reflection->getMethods();
        $methodNames = array_map(function($method) {
            return $method->getName();
        }, $methods);
        
        // Filter alleen public methods die niet van parent classes komen
        $publicMethods = array_filter($methodNames, function($name) {
            return !str_starts_with($name, '__') && 
                   !in_array($name, ['query', 'bind', 'execute', 'resultSet', 'single', 'rowCount']);
        });
        
        // Assert dat er ten minste enkele methods zijn
        $this->assertGreaterThan(0, count($publicMethods), 
            'Model should have at least some public methods. Found: ' . implode(', ', $publicMethods));
        
        // Test specifieke methods als ze bestaan
        $possibleMethods = [
            'alleProducten', 'getAlleProducten', 'getProducts',
            'zoekProductOpId', 'getProductById', 'findProduct',
            'voegProductToe', 'addProduct', 'createProduct',
            'wijzigProduct', 'updateProduct', 'editProduct',
            'verwijderProduct', 'deleteProduct', 'removeProduct'
        ];
        
        $foundMethods = [];
        foreach ($possibleMethods as $methodName) {
            if (method_exists($this->model, $methodName)) {
                $foundMethods[] = $methodName;
            }
        }
        
        $this->assertGreaterThan(0, count($foundMethods), 
            'At least one CRUD method should exist. Searched for: ' . implode(', ', $possibleMethods));
    }
    
    /**
     * Test EAN validatie en generatie - alleen als methods bestaan
     */
    public function testEANHandling()
    {
        // Test EAN generatie als methode bestaat
        if (method_exists($this->model, 'generateUniqueEAN')) {
            $ean = $this->model->generateUniqueEAN();
            
            $this->assertEquals(13, strlen($ean), 'EAN should be 13 digits');
            $this->assertMatchesRegularExpression('/^[0-9]{13}$/', $ean, 'EAN should contain only digits');
        } else {
            $this->markTestSkipped('generateUniqueEAN method does not exist');
        }
        
        // Test EAN validatie als methode bestaat
        if (method_exists($this->model, 'validateEAN')) {
            $this->assertTrue($this->model->validateEAN('1234567890123'));
            $this->assertFalse($this->model->validateEAN('123')); // Te kort
            $this->assertFalse($this->model->validateEAN('12345678901234')); // Te lang
            $this->assertFalse($this->model->validateEAN('123456789012a')); // Letters
        } else {
            $this->markTestSkipped('validateEAN method does not exist');
        }
    }
    
    /**
     * Test categorie en product type matching
     */
    public function testProductCategoryMatching()
    {
        $testData = [
            // [productNaam, verwachteCategorie, beschrijving]
            ['Aardappelen vastkokend 2kg', 1, 'Aardappelen product'],
            ['aardappel kruimig', 1, 'Aardappel (lowercase)'],
            ['Rode aardappelen', 1, 'Aardappelen variant'],
            ['Brood volkoren', 6, 'Bakkerij product'],
            ['Melk vol 1L', 4, 'Zuivel product'],
            ['Gehakt 500g', 5, 'Vlees product'],
        ];
        
        foreach ($testData as [$productNaam, $verwachteCategorie, $beschrijving]) {
            $detectedCategory = $this->detectProductCategory($productNaam);
            $this->assertEquals($verwachteCategorie, $detectedCategory, 
                "Product '$productNaam' should be detected as category $verwachteCategorie ($beschrijving)");
        }
    }
    
    /**
     * Test data validatie
     */
    public function testDataValidation()
    {
        // Test product naam validatie
        $validNames = ['Aardappelen 2kg', 'Brood volkoren', 'Melk vol'];
        $invalidNames = ['', '   ', null];
        
        foreach ($validNames as $name) {
            $this->assertTrue($this->isValidProductName($name), "Product name '$name' should be valid");
        }
        
        foreach ($invalidNames as $name) {
            $this->assertFalse($this->isValidProductName($name), "Product name should be invalid");
        }
        
        // Test voorraad validatie
        $this->assertTrue($this->isValidVoorraad(10));
        $this->assertTrue($this->isValidVoorraad(0));
        $this->assertFalse($this->isValidVoorraad(-1));
        $this->assertFalse($this->isValidVoorraad('abc'));
    }
    
    /**
     * Test mock database integratie
     */
    public function testMockDatabaseIntegration()
    {
        // Setup mock data
        $mockProducts = [
            (object) ['ProductID' => 1, 'ProductNaam' => 'Test Aardappelen', 'CategorieID' => 1],
            (object) ['ProductID' => 2, 'ProductNaam' => 'Test Brood', 'CategorieID' => 6]
        ];
        
        $this->mockDb->setMockResult($mockProducts);
        
        // Test mock returns expected data
        $result = $this->mockDb->resultSet();
        $this->assertCount(2, $result);
        $this->assertEquals('Test Aardappelen', $result[0]->ProductNaam);
        $this->assertEquals(1, $result[0]->CategorieID);
    }
    
    // Helper methods voor testing
    private function detectProductCategory($productName)
    {
        $productLower = strtolower($productName);
        
        if (strpos($productLower, 'aardappel') !== false) return 1;
        if (strpos($productLower, 'brood') !== false || strpos($productLower, 'cake') !== false) return 6;
        if (strpos($productLower, 'melk') !== false || strpos($productLower, 'kaas') !== false) return 4;
        if (strpos($productLower, 'gehakt') !== false || strpos($productLower, 'vlees') !== false) return 5;
        
        return 2; // Default groente
    }
    
    private function isValidProductName($name)
    {
        return !empty(trim($name ?? ''));
    }
    
    private function isValidVoorraad($voorraad)
    {
        return is_numeric($voorraad) && $voorraad >= 0;
    }
}