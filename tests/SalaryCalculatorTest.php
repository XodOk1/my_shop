<?php

namespace Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
// use PHPUnit\Framework\TestCase;
use App\SalaryCalculator;
use mysqli;

class SalaryCalculatorTest extends WebTestCase
{
    private \mysqli $mysqli;
    private $salaryCalculator;

    public function setUp(): void
    {
        // parent::setUp();
        $this->mysqli = new \mysqli('127.0.0.1', 'phpunit', '12344321', 'test_db');
        if ($this->mysqli->error) {
            echo $this->mysqli->error . PHP_EOL;
            exit(1);
        }
        $this->salaryCalculator = new SalaryCalculator();
    }


    public function testCalculateSalary(): void
    {
        $this->mysqli->query('INSERT INTO account SET email = "email", name = "name", salary = 11');
        $accountId = $this->mysqli->insert_id;

        $result = $this->salaryCalculator->calculateSalary($accountId);
        
        self::assertEquals( 9.9, $result);
    }





    #[\PHPUnit\Framework\Attributes\DataProvider('salaryProvider')]
    public function testCalculate(float $salary, float $expected): void
    {
        $salaryCalculate = new SalaryCalculator();
        $result = $salaryCalculate->calculate($salary);

        self::assertEquals($expected, $result);
    }

    public static function salaryProvider(): array
    {
        return [
            [20, 18],

            [20, 18],

            [10.5, 9.45],

            [15.5, 13.95],
        ];
    }
}
