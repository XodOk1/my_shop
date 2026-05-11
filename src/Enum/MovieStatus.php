<?php
namespace App\Enum;

enum MovieStatus: string
{
    case Ingested   = 'ingested';    // загружен мастер, ждёт обработки
    case Processing = 'processing';  // идёт транскодирование/пакетирование
    case Ready      = 'ready';       // готов к показу
    case Archived   = 'archived';    // убран из каталога
}
