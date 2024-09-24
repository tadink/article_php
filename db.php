<?php

class Db
{
    /**
     * @var PDO $pdo 
     */
    private $pdo;

    public function __construct(array $config)
    {
        try {
            $host = $config["host"];
            $dbName = $config["db_name"];
            $userName = $config["user_name"];
            $password = $config["password"];
            $port = $config["port"];
            $this->pdo = new PDO("mysql:host={$host};port={$port};dbname={$dbName}", $userName, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } catch (Throwable $throwable) {
            var_dump($throwable->getTraceAsString(), $throwable->getMessage());
            echo "数据库连接错误";
            exit;
        }
    }

    public function getSiteConfig($host): array
    {
        $stmt = $this->pdo->prepare("select * from site_config where domain=?");
        $stmt->bindParam(1, $host);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getArticleCount()
    {
        $stmt = $this->pdo->prepare("select count(*) from article");
        $stmt->execute();
        $count = $stmt->fetchColumn();
        return $count;
    }



    public function getArticleList($limit = 20): array
    {
        $order = "order by id ";
        switch (mt_rand(1, 2)) {
            case 1:
                $order = "order by title ";
                break;
            case 2:
                $order = "order by created_at ";
                break;
        }
        $count = $this->getArticleCount();
        $offset = mt_rand(0, $count);
        if ($count < $limit) {
            $offset = 0;
        }
        if ($offset > 0 && $offset > $count - $limit) {
            $offset = $count - $limit;
        }
        $stmt = $this->pdo->prepare("select * from article {$order} limit ?,?");
        $stmt->bindParam(1, $offset);
        $stmt->bindParam(2, $limit);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRandomArticleId()
    {
        $count = $this->getArticleCount();

        $order = "order by id ";
        switch (mt_rand(1, 2)) {
            case 1:
                $order = "order by title ";
                break;
            case 2:
                $order = "order by created_at ";
                break;
        }
        $offset = mt_rand(0, $count - 1);
        $s = sprintf("select id from article %s limit %d,1", $order, $offset);
        $stmt = $this->pdo->prepare($s);
        $stmt->execute();
        return $stmt->fetchColumn();

    }

    public function getArticle($articleId): array
    {
        $stmt = $this->pdo->prepare("select * from article where id=?");
        $stmt->bindParam(1, $articleId);
        $stmt->execute();
        $articel = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$articel) {
            $id = $this->getRandomArticleId();
            return $this->getArticle(articleId: $id);
        }
        return $articel;
    }


}
