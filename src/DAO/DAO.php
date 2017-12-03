<?php

namespace WF3\DAO;

use Doctrine\DBAL\Connection;

abstract class DAO
{
    /**
     * Database connection
     *
     * @var \Doctrine\DBAL\Connection
     */
    private $db;

    /**
     * Database table name
     *
     * @var string
     */
    private $tableName;

    /**
     * domain object class
     *
     * @var string
     */
    private $domainObjectClass;

    /**
     * Constructor
     *
     * @param \Doctrine\DBAL\Connection The database connection object
     * @param string database table name
     * @param string domain object class name
     */
    public function __construct(Connection $db, $tableName, $domainObjectClass) {
        $this->db = $db;
        $this->tableName = $tableName;
        $this->domainObjectClass = $domainObjectClass;
    }

    /**
     * Grants access to the database connection object
     *
     * @return \Doctrine\DBAL\Connection The database connection object
     */
    protected function getDb() {
        return $this->db;
    }

    /**
     * The database table name
     *
     * @return string database table name
     */
    protected function getTableName() {
        return $this->tableName;
    }

    /**
     * The domain object class name
     *
     * @return string domain object class name
     */
    protected function getDomainObjectName() {
        return $this->domainObjectClass;
    }

     /**
     * Creates an Article object based on a DB row.
     *
     * @param array $row The DB row containing data.
     * @param string $className : the complete name of the class to instanciate
     * @return hydrated object of $className
     */
    protected function buildObject(array $row){
        $class = $this->getDomainObjectName();
        $object = new $class();
        foreach ($row as $key => $value){
            // On récupère le nom du setter correspondant à l'attribut.
            $method = 'set'.ucfirst($key);
            // Si le setter correspondant existe.
            if (method_exists($object, $method)){
                // On appelle le setter.
                $object->$method($value);
            }
        }
        return $object;
    }

    /**
     * Read database and return all rows
     *
     * @return array of domain objects
     */
    public function findAll()
    { 
        
        $sql = 'SELECT * FROM '.$this->tableName;
        $result = $this->getDb()->fetchAll($sql);
        
        // Convert query result to an array of domain objects
        $objects = array();
        foreach ($result as $row) {
            $objects[$row['id']] = $this->buildObject($row);
        }
        return $objects;
    }

    /**
     * Selectionne une ligne de donnée en fonction de son identifiant
     * @param int $id L'identifiant de la ligne
     * @return array Les données provenant de la requete SQL
     */ 
    public function find($id) // Read
    {
        // On vérifie que l'id soit bien numérique
        if(!empty($id) && is_numeric($id)){
            $select = $this->getDb()->prepare('SELECT * FROM '.$this->tableName.'  WHERE id = :idSelect');
            $select->bindValue(':idSelect', $id);

            if($select->execute()){
                return $this->buildObject($select->fetch()); // Retournera un tableau avec les données correspondantes trouvées
            }
        }

        return false;
    }

    /**
     * Supprime une ligne en fonction d'un identifiant
     * @param int $id L'id de la ligne concernée
     * @return bool true si tout est ok, false sinon
     */
    public function delete($id) // Delete
    {
        // On vérifie que l'id soit bien numérique
        if(!empty($id) && is_numeric($id)){
            $delete = $this->getDb()->prepare('DELETE FROM '.$this->tableName.' WHERE id = :idASupprimer');
            $delete->bindValue(':idASupprimer', $id, PDO::PARAM_INT);

            if($delete->execute()){
                return true;
            }
        }

        return false;

    }


    /**
     * Ajoute un article dans la base de données
     * @param array $data Un tableau associatif de valeurs à insérer
     * @return true si ok, false sinon
     */
    public function insert($data) // Create
    {
        if(is_array($data)){ // Permet de déterminer que $data est bien un tableau
            $colNames = array_keys($data); // Permet de stocker sous forme de tableau les clés de $data
            $colNamesString = implode(', ', $colNames); // Permet de transformer le tableau $colNames en chaine de caractères. Chaque entrée sera séparée par une virgule


            $sql = 'INSERT INTO ' . $this->tableName . ' (' . $colNamesString . ') VALUES (';

            foreach($data as $key => $value){
                $sql .= ":$key, ";
            }


            $sql = substr($sql, 0, -2); // Permet de retirer le dernier espace et la dernière virgule (supprime les deux derniers caractères)
            $sql .= ')'; // Rajoute la parenthèse finale

            $insert = $this->getDb()->prepare($sql); // On prépare la requete SQL
            
            foreach($data as $key => $value){ // Permet d'associer les marqueurs SQL (:firstname par exemple) à leurs valeurs
                $insert->bindValue(':'.$key, strip_tags($value));
            }
        
            if($insert->execute()){  // On exécute la requete
                return true;
            }
        }
        
        return false;
    }

    /**
     * Met à jour un article dans la base de données en fonction d'un identifiant
     * @param array $data Un tableau associatif de valeurs à insérer
     * @param int $id L'identifiant de la ligne à mettre à jour
     * @return true si ok, false sinon
     */
    public function update($data, $id) // Create
    {
        if(is_array($data)){ // Permet de déterminer que $data est bien un tableau
            $colNames = array_keys($data); // Permet de stocker sous forme de tableau les clés de $data
            $colNamesString = implode(', ', $colNames); // Permet de transformer le tableau $colNames en chaine de caractères. Chaque entrée sera séparée par une virgule


            // UPDATE tableName SET colonne = new_value, colonne_2 = new_value WHERE id = mon_id

            $sql = 'UPDATE ' . $this->tableName . ' SET ';

            foreach($data as $key => $value){
                //colonne = :colonne, colonne2 = :colonne2, 
                $sql .= "$key = :$key, ";
            }

            $sql = substr($sql, 0, -2); // Permet de retirer le dernier espace et la dernière virgule (supprime les deux derniers caractères)
            $sql .= ' WHERE id = :id'; // Rajoute la clause WHERE

            $update = $this->getDb()->prepare($sql); // On prépare la requete SQL
            
            foreach($data as $key => $value){ // Permet d'associer les marqueurs SQL (:firstname par exemple) à leurs valeurs
                $update->bindValue(':'.$key, strip_tags($value));
            }
            // Rempli le marqueur :id en lui donnant pour valeur le paramètre de la fonction/méthode
            $update->bindValue(':id', $id, PDO::PARAM_INT);

            if($update->execute()){  // On exécute la requete
                return true;
            }
        }
        
        return false;
    }
}