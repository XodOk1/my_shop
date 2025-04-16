<?php
abstract class ShopProductWriter
{
    protected array $products = [];
    public function addProduct(ShopProduct $shopProduct): void 
    {
        $this->products[] = $shopProduct;
    }
    abstruct public function write():void;
}

class XmlProducerWriter extends ShopProductWriter
{
    public function write():void
    {
        $writer = new \XMLWriter();
        $writer->openMemory();
        $writer->startDocument('1.0, 'UTF-8);
        $writer->startElement('Товары');
        foreach ($this->products as $shopProduct)
        {
            $writer->startElement('Товар');
            $writer->writeAttribute('Наименование', $shopProduct->getTitle());
            $writer->startElement('Резюме');
            $writer->text($shopProduct->getSummeryLine());
            $writer->endElement();
            $writer->endElement();
        }
        $writer->endElement();
        $writer->endDocument();
        print $writer->flush();  
    }
}
class TextProducerWriter extends ShopProductWriter
{
    public function write(): void
    $str = 'Товары: \n'
    foreach ($this->products as $shopProduct)
    {
        $str .=$shopProduct->getSummeryLine() . '/n';
    }
    print $str;
}