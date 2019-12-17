<?php

namespace Feeds;

use Exception;
use PDO;
use PDOStatement;

class FeedMapper {
    
    /**
     * Holds the PDO connection
     * @var object
     */
    private $db;
    /**
     * Holds the feed reader class
     * @var object
     */
    protected $reader;
    /**
     * Table name for use in queries
     * @var string
     */
    protected $table = 'feeds';


	/**
	 * The constructor requires a PDO object for the database connection
	 * and a feed reader object to handle the remote feeds
	 *
	 * @param PDO $db 
	 * @param object $reader 
	 */
    public function __construct(\PDO $db, $reader)
    {
        $this->db = $db;
		$this->reader = $reader;
    }
    
    public function new($data = []) : Feed
    {
        return new Feed($data, $this->reader);
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
		$st->setFetchMode(PDO::FETCH_ASSOC);
        $st->execute($where);

		$out = [];
		while ($row = $st->fetch())
		{
			$out[] = $this->new($row);
		}
		
        return $out;
        
    }
	/**
	 * Returns a list of the users feeds, used in the nav list
	 *
	 * @return array
	 */
	public function fetchNavList() : Array
	{
        $sql = "SELECT id, name, url FROM `$this->table` ORDER BY `name` ASC";
        $st = $this->db->prepare($sql);
		$st->setFetchMode(PDO::FETCH_ASSOC);
        $st->execute();

		$out = [];
		// just return an array
		// there's no need to instantiate objects for the nav list
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

        if (! isset($data['id']) or empty($data['id'])) {
            throw new Exception("Feed not found");
        }

    }
	
    public function findBy($key, $value) : Array
    {
        $sql = "SELECT * FROM `$this->table` WHERE `$key`=:$key";
        $st = $this->db->prepare($sql);
		$st->setFetchMode(PDO::FETCH_ASSOC);
        $st->execute([$key => $value]);

		$out = [];
		while ($row = $st->fetch())
		{
			$out[] = $this->new($row);
		}
		
        return $out;

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
    
    public function delete(Feed $entity) : bool
    {
        $sql = "DELETE FROM `$this->table` WHERE `id`=:id";
        
        $st = $this->db->prepare($sql);
		
		return (bool) $st->execute(['id'=>$entity->id]);
        
    }

}
