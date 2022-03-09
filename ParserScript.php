<?php

class Connection
{
    public PDO $pdo;

    public function __construct()
    {
        $this->pdo = new PDO('mysql:server=localhost;dbname=test', 'root', '',);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}

class DB extends Connection
{
    function __construct()
    {
        parent::__construct();
    }

    public function addPosts($events)
    {
        $statement = $this->pdo->prepare("INSERT INTO posts (id, user_Id, title, body) VALUES (:id, :userId, :title, :body)");
        $statement->bindValue('id', $events['id']);
        $statement->bindValue('userId', $events['userId']);
        $statement->bindValue('title', $events['title']);
        $statement->bindValue('body', $events['body']);
        return $statement->execute();
    }

    public function addComments($events)
    {
        $statement = $this->pdo->prepare("INSERT INTO comments (id, post_Id, name, email, body) VALUES (:id, :postId, :name, :email, :body)");
        $statement->bindValue('id', $events['id']);
        $statement->bindValue('postId', $events['postId']);
        $statement->bindValue('name', $events['name']);
        $statement->bindValue('email', $events['email']);
        $statement->bindValue('body', $events['body']);
        return $statement->execute();
    }
}

class Parsing extends DB
{
    private DB $db;
    protected $postsCount = 0;
    protected $commentsCount = 0;

    function __construct()
    {
        $this->db = new DB;
    }

    public function postsPars()
    {
        $content = file_get_contents('https://jsonplaceholder.typicode.com/posts');
        foreach (json_decode($content, true) as $events) {
            $this->postsCount++;
            $this->db->addPosts($events);
        }
        echo "Постов загруженно: $this->postsCount", ' ';
    }

    public function commentPars()
    {
        $content = file_get_contents('https://jsonplaceholder.typicode.com/comments');
        foreach (json_decode($content, true) as $events) {
            $this->commentsCount++;
            $this->db->addComments($events);
        }
        echo "Коментарие загруженно: $this->commentsCount";
    }
}

$class = new Parsing;
$class->postsPars();
$class->commentPars();
