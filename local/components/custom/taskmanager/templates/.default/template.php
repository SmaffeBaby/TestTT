<?php
$grouped = [
    'task_to_do' => [],
    'task_done' => [],
    'task_closed' => []
];

foreach ($arResult['TASKS'] as $task) {
    $grouped[$task['STATUS']][] = $task;
}

$statusTitles = [
    'task_to_do' => 'Надо сделать',
    'task_done' => 'Выполнено',
    'task_closed' => 'Завершено'
];
?>

<div class="container mt-4">
    <h3 class="mb-4">📋 Мои задачи</h3>
    <div class="row">
        <?php foreach ($grouped as $status => $tasks): ?>
            <div class="col-md-4">
                <h5><?= $statusTitles[$status] ?></h5>
                <?php if (!empty($tasks)): ?>
                    <ul class="list-group mb-4">
                        <?php foreach ($tasks as $task): ?>
                            <li class="list-group-item">
                                <strong><?= htmlspecialcharsbx($task['UF_NAME']) ?></strong><br>
                                <small><?= nl2br(htmlspecialcharsbx($task['UF_DESCRIPTION'])) ?></small><br>
                                <small class="text-muted"><?= htmlspecialcharsbx($task['UF_DATETIME']) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Нет задач</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- КНОПКА -->
<div class="text-center mt-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
        ➕ Создать задачу
    </button>
</div>

<!-- МОДАЛЬНОЕ ОКНО -->
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
                        <option value="task_to_do">Надо сделать</option>
                        <option value="task_done">Выполнено</option>
                        <option value="task_closed">Завершено</option>
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

<script>
    document.getElementById('add-task-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch('/local/components/custom/taskmanager/ajax/add_task.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Задача добавлена!');
                    location.reload();
                } else {
                    alert('Ошибка: ' + result.error);
                }
            })
            .catch(err => alert('Сбой запроса'));
    });
</script>
