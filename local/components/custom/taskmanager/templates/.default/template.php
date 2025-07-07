<?php
$grouped = [
    'task_to_do' => [],
    'task_done' => [],
    'task_closed' => []
];

foreach ($arResult['TASKS'] as $task) {
    $grouped[$task['STATUS']][] = $task;
}

// Читаем кастомные названия статусов, если есть
$customStatusTitles = [];
$file = $_SERVER['DOCUMENT_ROOT'].'/local/status_titles.json';
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
        const status = form.querySelector('[name="old_status"]').value;

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


    document.querySelectorAll('.edit-status-btn').forEach(button => {
        button.addEventListener('click', () => {
            const modal = new bootstrap.Modal(document.getElementById('editStatusModal'));
            const form = document.getElementById('edit-status-form');

            form.status_key.value = button.dataset.status;
            form.status_title.value = button.dataset.title;

            modal.show();
        });
    });

    document.getElementById('edit-status-form').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        fetch('/local/components/custom/taskmanager/ajax/edit_status.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Название статуса обновлено!');
                    location.reload();
                } else {
                    alert('Ошибка: ' + result.error);
                }
            })
            .catch(() => alert('Сбой запроса'));
    });

</script>
