<?php

use PHPUnit\Framework\TestCase;

class MagazijnvoorraadModelTest extends TestCase
{
    private $model;
    
    protected function setUp(): void
    {
        // Setup voor elke test
        $this->model = new MagazijnvoorraadModel();
    }
    
    protected function tearDown(): void
    {
        // Cleanup na elke test
        $this->model = null;
    }
    
    /**
     * Test EAN generatie
     */
    public function testGenerateUniqueEAN()
    {
        $ean = $this->model->generateUniqueEAN();
        
        // Assert dat EAN 13 cijfers heeft
        $this->assertEquals(13, strlen($ean));
        $this->assertMatchesRegularExpression('/^[0-9]{13}$/', $ean);
    }
    
    /**
     * Test categorie validatie
     */
    public function testValidateProductCategory()
    {
        // Test correcte categorie
        $result = $this->callPrivateMethod('validateProductCategory', ['Aardappelen vastkokend', 1]);
        $this->assertTrue($result['isValid']);
        
        // Test foute categorie
        $result = $this->callPrivateMethod('validateProductCategory', ['Aardappelen vastkokend', 6]); // Bakkerij
        $this->assertFalse($result['isValid']);
        $this->assertStringContainsString('past niet bij categorie', $result['message']);
    }
    
    /**
     * Test product toevoegen
     */
    public function testVoegProductToe()
    {
        $result = $this->model->voegProductToe(
            1, // leverancier_id
            null, // allergie_id
            1, // categorie_id (Aardappelen, groente, fruit)
            'Test Aardappelen',
            '1234567890123',
            10
        );
        
        $this->assertTrue($result);
    }
    
    /**
     * Test product wijzigen
     */
    public function testWijzigProduct()
    {
        // Eerst product toevoegen
        $productId = $this->addTestProduct();
        
        // Dan wijzigen
        $result = $this->model->wijzigProduct(
            $productId,
            1, // leverancier_id
            null, // allergie_id
            1, // categorie_id
            'Gewijzigde Aardappelen',
            15
        );
        
        $this->assertTrue($result);
        
        // Cleanup
        $this->cleanupTestProduct($productId);
    }
    
    /**
     * Test foutieve categorie bij wijzigen
     */
    public function testWijzigProductMetFoutieveCategorie()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('past niet bij categorie');
        
        // Probeer aardappelen in bakkerij categorie te zetten
        $productId = $this->addTestProduct();
        
        $this->model->wijzigProduct(
            $productId,
            1, // leverancier_id
            null, // allergie_id
            6, // categorie_id (Bakkerij en banket)
            'Test Aardappelen',
            10
        );
    }
    
    /**
     * Helper method om private methods te testen
     */
    private function callPrivateMethod($methodName, $args = [])
    {
        $reflection = new ReflectionClass($this->model);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        
        return $method->invokeArgs($this->model, $args);
    }
    
    /**
     * Helper om test product toe te voegen
     */
    private function addTestProduct()
    {
        $this->model->voegProductToe(
            1,
            null,
            1,
            'Test Product',
            '9999999999999',
            5
        );
        
        // Haal product ID op
        $product = $this->model->zoekProductOpEAN('9999999999999');
        return $product->ProductID;
    }
    
    /**
     * Helper om test product op te ruimen
     */
    private function cleanupTestProduct($productId)
    {
        $this->model->verwijderProduct($productId);
    }
}