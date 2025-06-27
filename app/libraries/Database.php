<?php
/**
 * Dit is de database class die alle communicatie met de database verzorgt
 */

class Database
{
    private $dbHost = DB_HOST;
    private $dbName = DB_NAME;
    private $dbUser = DB_USER;
    private $dbPass = DB_PASS;

    private $dbHandler;
    private $statement;

    public function __construct()
    {
        // Connectiestring voor PDO
        $conn = 'mysql:host=' . $this->dbHost . ';dbname=' . $this->dbName;

        // PDO opties
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false
        );

        try {
            $this->dbHandler = new PDO($conn, $this->dbUser, $this->dbPass, $options);
        } catch (PDOException $e) {
            // Fout afhandeling
            logger(__LINE__, __METHOD__, __FILE__, $e->getMessage());
            echo "Op dit moment kunnen we u niet helpen... probeer het later nog eens.";
            header('Refresh:30; url=' . URLROOT . '/homepages/index');
            exit;
        }
    }

    public function query($sql)
    {
        // Sluit de vorige statement als die nog open staat (voor stored procedures)
        if ($this->statement) {
            $this->statement->closeCursor();
        }
        $this->statement = $this->dbHandler->prepare($sql);
    }

    public function resultSet()
    {
        $this->statement->execute();
        $result = $this->statement->fetchAll(PDO::FETCH_OBJ);
        $this->statement->closeCursor(); // cursor sluiten na ophalen
        return $result;
    }

    public function bind($parameter, $value, $type = null)
    {
        // Bepaal type als deze niet expliciet is opgegeven
        if (is_null($type)) {
            switch (true) {
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        return $this->statement->bindValue($parameter, $value, $type);
    }

    public function execute()
    {
        return $this->statement->execute();
    }

    public function single()
    {
        $this->statement->execute();
        $result = $this->statement->fetch(PDO::FETCH_OBJ);
        $this->statement->closeCursor();
        return $result;
    }

    public function outQuery($sql)
    {
        return $this->dbHandler->query($sql);
    }
}
