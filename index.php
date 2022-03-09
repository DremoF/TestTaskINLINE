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

    public function getPosts($events)
    {
        $query = htmlspecialchars($events);
        $query = "%$query%";
        $statement = $this->pdo->prepare("SELECT posts.title, comments.name, comments.body FROM comments LEFT JOIN posts ON comments.post_Id = posts.Id WHERE comments.body LIKE ? GROUP BY posts.title,comments.name, comments.body");
        $statement->execute(array($query));
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }
}


$handler = new DB;
$posts = $handler->getPosts($_POST['comment']);

?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>search</title>
</head>
<div class="d-flex justify-content-center bg-light text-dark">
    <div class="w-50 p-3">
        <form action="" method="POST">
            <div class="hstack gap-3 input-group mb-2">
                <input type="text" class="form-control" name="comment" required placeholder="Введите текст комментария" minlength="3" aria-describedby="button-addon">
                <button class="btn btn-outline-secondary" type="submit" id="button-addon">Найти</button>
            </div>

        </form>
    </div>
</div>
<? if (empty($posts)) : echo "<p> Запись не найдена <p>" ?>
<? else : ?>
    <? foreach ($posts as $post) : ?>
        <div class="card">
            <h5 class="card-header"><?php echo $post['title']; ?></h5>
            <div class="card-body">
                <h5 class="card-title"><?php echo $post['name']; ?></h5>
                <p class="card-text"><?php echo $post['body']; ?></p>
            </div>
        </div>
        <br>
    <? endforeach; ?>
<? endif; ?>

</body>

</html>