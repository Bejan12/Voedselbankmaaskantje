<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../mocks/MockDatabase.php';

class MagazijnvoorraadTest extends TestCase  // ← Was MagazijnvoorraadModelTest
{
    private $controller;
    
    protected function setUp(): void
    {
        $this->controller = new Magazijnvoorraad();
    }
    
    /**
     * Test categorie validatie methode
     */
    public function testValidateProductCategory()
    {
        // Test via reflection omdat het een private method is
        $reflection = new ReflectionClass($this->controller);
        $method = $reflection->getMethod('validateProductCategory');
        $method->setAccessible(true);
        
        // Test correcte combinatie
        $result = $method->invoke($this->controller, 'Aardappelen vastkokend', 1);
        $this->assertTrue($result['isValid']);
        
        // Test foute combinatie
        $result = $method->invoke($this->controller, 'Aardappelen vastkokend', 6);
        $this->assertFalse($result['isValid']);
    }
}