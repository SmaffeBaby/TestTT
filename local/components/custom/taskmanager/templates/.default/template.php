<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

global $USER;
if (isset($_GET['logout']) && $_GET['logout'] === 'yes') {
    $USER->Logout();
    LocalRedirect('/auth/login.php'); // редирект после выхода
    exit;
}

if (!$USER->IsAuthorized()) {
    LocalRedirect('/auth/login.php');
    exit;
}

$grouped = [
    'task_to_do' => [],
    'task_done' => [],
    'task_closed' => []
];

use Bitrix\Highloadblock as HL;
use Bitrix\Main\Loader;

Loader::includeModule('highloadblock');

$statusList = ['task_to_do', 'task_done', 'task_closed'];
$arResult['TASKS'] = [];

foreach ($statusList as $statusCode) {
    $hlblock = HL\HighloadBlockTable::getList([
        'filter' => ['=TABLE_NAME' => $statusCode]
    ])->fetch();

    if (!$hlblock) continue;

    $entity = HL\HighloadBlockTable::compileEntity($hlblock);
    $entityClass = $entity->getDataClass();

    $rows = $entityClass::getList([
        'filter' => ['UF_USER_ID' => $USER->GetID()],
        'order' => ['UF_DATETIME' => 'ASC']
    ])->fetchAll();

    foreach ($rows as $row) {
        $row['STATUS'] = $statusCode;
        $arResult['TASKS'][] = $row;
    }
}


foreach ($arResult['TASKS'] as $task) {
    $grouped[$task['STATUS']][] = $task;
}


$customStatusTitles = [];
$file = $_SERVER['DOCUMENT_ROOT'].'/local/status_titles_' . $USER->GetID() . '.json';
if (file_exists($file)) {
    $json = file_get_contents($file);
    $customStatusTitles = json_decode($json, true);
}

$statusTitles = [
    'task_to_do' => $customStatusTitles['task_to_do'] ?? 'Надо сделать',
    'task_done' => $customStatusTitles['task_done'] ?? 'Выполнено',
    'task_closed' => $customStatusTitles['task_closed'] ?? 'Завершено',
];
?>


<div class="container mt-4">
    <h3 class="mb-4">📋 Мои задачи</h3>
    <div class="row">
        <?php foreach ($grouped as $status => $tasks): ?>
            <div class="col-md-4">
                <div class="status-header position-relative mb-3" style="padding-right: 30px;">
                    <h5 class="d-inline"><?= $statusTitles[$status] ?></h5>
                    <button class="btn btn-sm btn-outline-secondary edit-status-btn position-absolute"
                            style="top: 0; right: 0; display: none;"
                            data-status="<?= $status ?>"
                            data-title="<?= htmlspecialcharsbx($statusTitles[$status]) ?>"
                            title="Редактировать статус">
                        ✏️
                    </button>
                </div>

                <ul class="list-group mb-4 task-list" data-status="<?= $status ?>">
                    <?php if (!empty($tasks)): ?>
                        <?php foreach ($tasks as $task): ?>
                            <li class="list-group-item task-card"
                                draggable="true"
                                data-id="<?= $task['ID'] ?>"
                                data-status="<?= $task['STATUS'] ?>"
                                data-name="<?= htmlspecialcharsbx($task['UF_NAME']) ?>"
                                data-description="<?= htmlspecialcharsbx($task['UF_DESCRIPTION']) ?>"
                                data-datetime="<?= date('Y-m-d\TH:i', strtotime($task['UF_DATETIME'])) ?>"
                            >
                                <strong><?= htmlspecialcharsbx($task['UF_NAME']) ?></strong><br>
                                <small><?= nl2br(htmlspecialcharsbx($task['UF_DESCRIPTION'])) ?></small><br>
                                <small class="text-muted"><?= htmlspecialcharsbx($task['UF_DATETIME']) ?></small><br>

                                <button
                                        class="btn btn-sm btn-outline-primary mt-2 edit-task-btn"
                                        data-id="<?= $task['ID'] ?>"
                                        data-status="<?= $task['STATUS'] ?>"
                                        data-name="<?= htmlspecialcharsbx($task['UF_NAME']) ?>"
                                        data-description="<?= htmlspecialcharsbx($task['UF_DESCRIPTION']) ?>"
                                        data-datetime="<?= date('Y-m-d\TH:i', strtotime($task['UF_DATETIME'])) ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editTaskModal"
                                >
                                    ✏️ Редактировать
                                </button>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>

                    <?php endif; ?>
                </ul>

            </div>
        <?php endforeach; ?>
    </div>
</div>



<div class="text-center mt-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
        ➕ Создать задачу
    </button>
</div>

<div class="text-center mt-4">
    <a href="?logout=yes" class="btn btn-outline-danger">Выйти из аккаунта</a>
</div>
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="add-task-form" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Создать задачу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="status" class="form-label">Статус</label>
                    <select class="form-select" name="status" required>
                        <?php foreach ($statusTitles as $key => $title): ?>
                            <option value="<?= $key ?>"><?= htmlspecialcharsbx($title) ?></option>
                        <?php endforeach; ?>
                    </select>

                </div>
                <div class="mb-3">
                    <label for="name" class="form-label">Название задачи</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Описание</label>
                    <textarea class="form-control" name="description"></textarea>
                </div>
                <div class="mb-3">
                    <label for="datetime" class="form-label">Дата и время</label>
                    <input type="datetime-local" class="form-control" name="datetime">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-success">Сохранить</button>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="edit-task-form" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать задачу</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id">
                <input type="hidden" name="old_status" value="">
                <div class="mb-3">
                    <label class="form-label">Статус</label>
                    <select class="form-select" name="status" required>
                        <?php foreach ($statusTitles as $key => $title): ?>
                            <option value="<?= $key ?>"><?= htmlspecialcharsbx($title) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Название</label>
                    <input type="text" class="form-control" name="name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Описание</label>
                    <textarea class="form-control" name="description"></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Дата и время</label>
                    <input type="datetime-local" class="form-control" name="datetime">
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between">
                <button type="button" class="btn btn-danger" id="delete-task-btn">Удалить</button>
                <button type="submit" class="btn btn-success">Сохранить изменения</button>
            </div>
        </form>
    </div>
</div>


<div class="modal fade" id="editStatusModal" tabindex="-1" aria-labelledby="editStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="edit-status-form" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Редактировать название статуса</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Закрыть"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="status_key">
                <div class="mb-3">
                    <label class="form-label" for="status-title-input">Название статуса</label>
                    <input type="text" class="form-control" name="status_title" id="status-title-input" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Сохранить</button>
            </div>
        </form>
    </div>
</div>


<script src="/local/templates/TestTT/main.js"></script>




