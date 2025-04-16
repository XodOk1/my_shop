<?php

class ShopProduct
{
    private int $id = 0;
    public $numPages;
    public $title;
    public $producerMainName;
    public $producerFirstName;
    public $price;


    public function __construct(string $title, string $firstName, string $mainName, float $price,)
    {
        $this->title = $title;
        $this->producerFirstName = $firstName;
        $this->producerMainName = $mainName;
        $this->price = $price;
    }

    public static function getInstanse (int $id, \PDO $pdo): ShopProduct
    {
        $stmt = $pdo->prepare('select * from products where id=?');
        $result = $stmt-> execute(($id));
        $row = $stmt->fetch();
        if (empty($row))
        {
            return null;
        }

        if ($row['type'] = 'book')
        {
            $product = new BookProduct (
                $row['title'],
                $row['firstName'],
                $row['mainName'],
                (float) $row['price'],
                (int) $row['numpages']
            );
        } 
        
        elseif ($row['type'] == 'cd')
        {
            $product = new CDProduct (
                $row['title'],
                $row['firstName'],
                $row['mainName'],
                (float) $row['price'],
                (int) $row['playLangth']
            );
        }

        else
        {
            $firstName = (is_null($row['firstName'])) ? '':
            $row['firstName'];
            $product = new ShopProduct (
                $row['title'],
                $row,
                $row['mainName'],
                (float) $row['price'],
            );
        }

        $product->setId((int) $row['id']);
        $product->setDiscount((int) $row['discount']);
        return $product;
    } 

    public function getProducer(): string 
    {
        return $this->producerFirstName . ''
        . $this->producerMainName;
    }

    public function getSummeryLine(): string
    {
        $base = '{$this ->title} ( {$this->pruducerMainName}, ';
        $base .= '{$this->producerFirstName} ) .';
        return $base;
    }
}



class CDProduct extends ShopProduct
{
    public $playLength;

    public function __constract (string $title, string $firstName, string $mainName, float $price, int $playLength)
    {
       parent::__construct($title, $firstName, $mainName, $prise,);
        $this->playLength = $playLength;
    }
    

    public function getPlayLength(): int
    {
        
        return $this->playLength;
    }

    public function getSummaryLine(): string
    {
        $base = '{$this ->title} ( {$this->pruducerMainName}, ';
        $base .= '{$this->producerFirstName} ) .';
        $base .= ': Время звучания - {$this->playLangth} ';
        return $base;
    }
}



class BookProduct extends ShopProduct
{
    public $numPages;

    public function __construct(string $title, string $firstName, string $mainName, float $price, int $numPages)
    {
        $numPages = $this->$numPages;
    }

    public function getNumberOfPages(): int
    {
        return $this->numPages;
    }

    public function getSummaryLine(): string
    {
        $base = '{$this ->title} ( {$this->pruducerMainName}, ';
        $base .= '{$this->producerFirstName} ) .';
        $base .= ':{$this->numPages} стр.';
        return $base;
    }
}