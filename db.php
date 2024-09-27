<?php

class Db
{
    /**
     * @var PDO $pdo 
     */
    private $pdo;

    public function __construct()
    {
        try {
            $this->pdo = new PDO("sqlite:site.db");
            $this->pdo->exec("PRAGMA journal_mode=WAL;");
            $this->pdo->exec("PRAGMA busy_timeout=5000;");
            $this->pdo->exec("PRAGMA cache=shared");
            $this->createTable();
        } catch (Throwable $throwable) {
            var_dump($throwable->getTraceAsString(), $throwable->getMessage());
            echo "数据库连接错误";
            exit;
        }
    }

    private function createTable()
    {
        $sql = <<<SQL
CREATE TABLE IF NOT EXISTS `article` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT ,
  `title` varchar(255)  NOT NULL,
  `summary` varchar(255)  NOT NULL DEFAULT '',
  `pic` varchar(255) NOT NULL DEFAULT '',
  `content` text  NOT NULL,
  `author` varchar(255)  NOT NULL DEFAULT '',
  `type_id` int NOT NULL DEFAULT '0',
  `type_name` varchar(255)  NOT NULL DEFAULT '',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS  title_idx on article(title);
CREATE INDEX IF NOT EXISTS  created_at_idx on article(created_at);

CREATE TABLE IF NOT EXISTS `site_config` (
  `id`  INTEGER PRIMARY KEY  AUTOINCREMENT,
  `domain` varchar(100)  NOT NULL,
  `index_title` varchar(100)  NOT NULL DEFAULT '',
  `index_keywords` varchar(255)  NOT NULL DEFAULT '',
  `index_description` varchar(255)  NOT NULL DEFAULT '',
  `template_name` varchar(100)  NOT NULL DEFAULT '',
  `routes` text  NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX IF NOT EXISTS domain_uni on site_config(domain);
SQL;
    $this->pdo->exec($sql);

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
