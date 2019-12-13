<?php

namespace Feeds;

use Exception;
use PDO;
use PDOStatement;

class FeedMapper {
    
    /**
     * Holder of the PDO connection
     * @var string
     */
    protected $db;
    
    /**
     * Table name for use in queries
     * @var string
     */
    protected $table = 'feeds';


    public function __construct(\PDO $db)
    {
        $this->db = $db;
    }
    
    public function new($data = []) : Feed
    {
		xdebug_break();
        return new Feed($data);
    }

    public function fetch($where=[]) : Array
    {
        $sql = "SELECT * FROM `$this->table` ";
		if ( ! empty($where))
		{
			$sql.= "WHERE ";
	        foreach ($where as $k=>$v)
			{
	            $sql.= "`$k` LIKE :$k AND ";
	        }
	        $sql = rtrim($sql, ' AND ');
			$sql.= " COLLATE NOCASE"; // quick hack for case-insensitive searching in sqlite move to container setup
		}
        $st = $this->db->prepare($sql);
		$st->setFetchMode(PDO::FETCH_ARR);
        $st->execute($where);

		$out = [];
		while ($row = $st->fetch())
		{
			$out[] = $row;
		}
		
        return $out;
        
    }
    
    public function find($id) : Feed
    {
        $sql = "SELECT * FROM `$this->table` WHERE `id`=:id";
        $st = $this->db->prepare($sql);
		$st->setFetchMode(PDO::FETCH_ASSOC);
        $st->execute(['id' => $id]);
		$data = $st->fetch();
		xdebug_break();
        return $this->new($data);

    }

    public function insert(Feed $entity) : Feed
    {
        $sql = "INSERT INTO `$this->table` (";
        foreach ($entity->getData() as $k=>$v) {
            $sql.= "`$k`,";
        }
        $sql = rtrim($sql, ',');
        $sql.= ') VALUES (';
        foreach ($entity->getData() as $k=>$v) {
            $sql.= ":$k,";
        }
        $sql = rtrim($sql, ',');
        $sql.= ')';
        
        $st = $this->db->prepare($sql);
        $rows = $st->execute($entity->getData());
        
        if ($rows == 1) {
            $entity->id = $this->db->lastInsertId();
            return $entity;
        }
        
        throw new Exception("Insert error", 500);
        
    }

    public function update(Feed $entity) : Feed
    {
        $sql = "UPDATE `$this->table` SET ";
        foreach ($entity->getData() as $k=>$v) {
            if ($k != 'id') {
                $sql.= "`$k`=:$k,";
            }
        }
        $sql = rtrim($sql, ',');
        $sql.= ' WHERE `id` = :id';
        
        $st = $this->db->prepare($sql);
        $rows = $st->execute($entity->getData());
        
        if ($rows == 1) {
            return $entity;
        }
        
        throw new Exception("Update error", 500);
        
    }
    
    public function delete($params = []) : bool
    {
        if (empty($params)) {
            throw new Exception('DELETE requires some parameters, none passed');
        }
        
        $sql = "DELETE FROM `$this->table` WHERE ";
        foreach ($params as $k=>$v) {
            $sql.= "`$k`=:$k AND ";
        }
        $sql = rtrim($sql, ' AND ');
        
        return $this->db->prepare($sql)->execute($params);
        
    }
}
