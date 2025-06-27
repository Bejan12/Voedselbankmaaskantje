<?php
/**
 * Mock Database class voor unit testing
 * Simuleert database operaties zonder echte database calls
 */
class MockDatabase
{
    private $queries = [];
    private $results = [];
    
    public function query($sql)
    {
        $this->queries[] = $sql;
        return $this;
    }
    
    public function bind($param, $value)
    {
        // Mock binding - sla parameters op voor testing
        return $this;
    }
    
    public function execute()
    {
        return true;
    }
    
    public function single()
    {
        return (object) ['count' => 0];
    }
    
    public function resultSet()
    {
        return $this->results;
    }
    
    public function setMockResult($result)
    {
        $this->results = $result;
    }
    
    public function getQueries()
    {
        return $this->queries;
    }
    
    public function rowCount()
    {
        return count($this->results);
    }
}