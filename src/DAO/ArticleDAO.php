<?php

namespace WF3\DAO;

class ArticleDAO extends DAO
{
    /**
     * @var \WF3\DAO\UserDAO
     */
    private $userDAO;

    public function setUserDAO(UserDAO $userDAO) {
        $this->userDAO = $userDAO;
    }

    public function findALLOrderByDate($desc=''){
        $desc = $desc == 'DESC' ? 'DESC' : '';
        $sql = 'SELECT * FROM '. $this->getTableName() . ' ORDER BY date_publi ' . $desc;
        $result = $this->getDb()->fetchAll($sql);
        
        
        // Convert query result to an array of domain objects
        $objects = array();
        foreach ($result as $row) {
            $objects[$row['id']] = $this->buildObject($row);
        }
        return $objects;
    }

    public function findByUser($userId){
        $sql = 'SELECT * FROM '. $this->getTableName() . ' WHERE  author = :author';
        $statement = $this->getDb()->prepare($sql);
        $statement->bindValue('author', $userId);
        $statement->execute();
        $result = $statement->fetchAll();
        
        // Convert query result to an array of domain objects
        $objects = array();
        foreach ($result as $row) {
            $objects[$row['id']] = $this->buildObject($row);
        }
        return $objects;
    }

    public function findALLWithUser(){
        $sql = 'SELECT id, title, content, date_publi, author as userId FROM '. $this->getTableName();
        $result = $this->getDb()->fetchAll($sql);        
        
        // Convert query result to an array of domain objects
        $objects = array();
        $authors = [];
        foreach ($result as $row) {
            //if this user has not been fetched yet
            if(!array_key_exists($row['userId'], $authors)){
              $row['author'] = $this->userDAO->find($row['userId']);
              $authors[$row['userId']] = $row['author'];
            }
            else{
                //user already fetched
                $row['author'] =  $authors[$row['userId']];
            }
            
            $objects[$row['id']] = $this->buildObject($row);
        }
        return $objects;
    }

    public function buildObject(array $row){
        $article = parent::buildObject($row);
        if(array_key_exists('author', $row) AND is_numeric($row['author'])){
            $article->setAuthor($this->userDAO->find($row['author']));
        }
        return $article;
    }
    
}