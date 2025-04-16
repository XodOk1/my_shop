<?php
namespace App\Service;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PaginatorService
{
/**
* Пагинирует результаты запроса
*
* @param QueryBuilder $queryBuilder Построитель запросов Doctrine
* @param int $page Текущая страница (начинается с 1)
* @param int $limit Количество элементов на странице
*
* @return array Массив с результатами и метаданными пагинации
*/
public function paginate(QueryBuilder $queryBuilder, int $page = 1, int $limit = 10): array
{
// Получаем запрос из построителя
$query = $queryBuilder->getQuery();

// Создаем пагинатор Doctrine
$paginator = new Paginator($query);

// Устанавливаем параметры пагинации
$paginator
->getQuery()
->setFirstResult($limit * ($page - 1)) // Смещение
->setMaxResults($limit);              // Лимит

// Подсчитываем общее количество элементов
$totalItems = count($paginator);

// Возвращаем результаты и метаданные
return [
'items' => iterator_to_array($paginator->getIterator()),
'total_items' => $totalItems,
'current_page' => $page,
'items_per_page' => $limit,
'total_pages' => ceil($totalItems / $limit),
'has_previous_page' => $page > 1,
'has_next_page' => $page < ceil($totalItems / $limit),
];
}
}
?>