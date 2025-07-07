<?php
use Bitrix\Main\Loader;
use Bitrix\Highloadblock as HL;

class TaskManagerComponent extends CBitrixComponent
{
    // Названия таблиц HL-блоков
    private $hlBlocks = ['task_to_do', 'task_done', 'task_closed'];

    public function executeComponent()
    {
        if ($this->StartResultCache()) {
            Loader::includeModule('highloadblock');

            $this->arResult['TASKS'] = $this->getAllTasks();
            $this->IncludeComponentTemplate();
        }
    }

    private function getAllTasks()
    {
        $tasks = [];

        foreach ($this->hlBlocks as $tableName) {
            $hlblock = HL\HighloadBlockTable::getList([
                'filter' => ['=TABLE_NAME' => $tableName]
            ])->fetch();

            if (!$hlblock) {
                continue;
            }

            $entity = HL\HighloadBlockTable::compileEntity($hlblock);
            $entityClass = $entity->getDataClass();

            $rsData = $entityClass::getList([
                'select' => ['ID', 'UF_NAME', 'UF_DESCRIPTION', 'UF_DATETIME'],
                'order' => ['UF_DATETIME' => 'ASC'],
            ]);

            while ($task = $rsData->fetch()) {
                $task['STATUS'] = $tableName; // добавим статус задачи по названию HL-блока
                $tasks[] = $task;
            }
        }

        // Сортируем все задачи по дате UF_DATETIME
        usort($tasks, function ($a, $b) {
            return strtotime($a['UF_DATETIME']) <=> strtotime($b['UF_DATETIME']);
        });

        return $tasks;
    }
}
