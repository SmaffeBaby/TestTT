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
                    </ul>
                <?php else: ?>
                    <p class="text-muted">Нет задач</p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>


<div class="text-center mt-4">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTaskModal">
        ➕ Создать задачу
    </button>
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
                        <option value="task_to_do">Надо сделать</option>
                        <option value="task_done">Выполнено</option>
                        <option value="task_closed">Завершено</option>
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


<script>
    document.addEventListener('DOMContentLoaded', () => {
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
                .catch(() => alert('Сбой запроса'));
        });

        document.querySelectorAll('.edit-task-btn').forEach(button => {
            button.addEventListener('click', () => {
                const form = document.getElementById('edit-task-form');
                form.querySelector('[name="id"]').value = button.dataset.id;
                form.querySelector('[name="status"]').value = button.dataset.status;
                form.querySelector('[name="old_status"]').value = button.dataset.status;
                form.querySelector('[name="name"]').value = button.dataset.name;
                form.querySelector('[name="description"]').value = button.dataset.description;
                form.querySelector('[name="datetime"]').value = button.dataset.datetime;
            });
        });

        document.getElementById('edit-task-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            fetch('/local/components/custom/taskmanager/ajax/edit_task.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        alert('Задача обновлена!');
                        location.reload();
                    } else {
                        alert('Ошибка: ' + result.error);
                    }
                })
                .catch(() => alert('Сбой запроса'));
        });
    });


    document.getElementById('delete-task-btn').addEventListener('click', function() {
        if (!confirm('Вы действительно хотите удалить задачу?')) return;

        const form = document.getElementById('edit-task-form');
        const id = form.querySelector('[name="id"]').value;
        const status = form.querySelector('[name="old_status"]').value; // чтобы знать из какого HL-блока удалять

        if (!id || !status) {
            alert('Не удалось определить задачу для удаления');
            return;
        }

        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);

        fetch('/local/components/custom/taskmanager/ajax/delete_task.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Задача удалена');
                    location.reload();
                } else {
                    alert('Ошибка: ' + result.error);
                }
            })
            .catch(() => alert('Сбой запроса'));
    });

</script>
