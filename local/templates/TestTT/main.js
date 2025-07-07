document.addEventListener('DOMContentLoaded', () => {
    function updateEmptyMessage(taskList) {
        const tasks = taskList.querySelectorAll('.task-card');
        const emptyMessage = taskList.querySelector('.empty-message');

        if (tasks.length === 0) {
            if (!emptyMessage) {
                const li = document.createElement('li');
                li.className = 'list-group-item text-muted empty-message';
                li.textContent = 'Нет задач';
                taskList.appendChild(li);
            }
        } else {
            if (emptyMessage) {
                emptyMessage.remove();
            }
        }
    }

    document.querySelectorAll('.task-list').forEach(list => updateEmptyMessage(list));

    document.getElementById('add-task-form').addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(e.target);
        fetch('/local/components/custom/taskmanager/ajax/add_task.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Задача добавлена!');
                    location.reload();
                } else alert('Ошибка: ' + result.error);
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

    document.getElementById('edit-task-form').addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(e.target);
        fetch('/local/components/custom/taskmanager/ajax/edit_task.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Задача обновлена!');
                    location.reload();
                } else alert('Ошибка: ' + result.error);
            })
            .catch(() => alert('Сбой запроса'));
    });

    document.getElementById('delete-task-btn').addEventListener('click', () => {
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
        fetch('/local/components/custom/taskmanager/ajax/delete_task.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Задача удалена');
                    const taskList = document.querySelector(`.task-list[data-status="${status}"]`);
                    if (taskList) {
                        const taskElem = taskList.querySelector(`.task-card[data-id="${id}"]`);
                        if (taskElem) taskElem.remove();
                        updateEmptyMessage(taskList);
                    }
                    form.reset();
                    const editModalEl = document.getElementById('editTaskModal');
                    const editModal = bootstrap.Modal.getInstance(editModalEl);
                    if (editModal) editModal.hide();
                } else alert('Ошибка: ' + result.error);
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

    document.getElementById('edit-status-form').addEventListener('submit', e => {
        e.preventDefault();
        const formData = new FormData(e.target);
        fetch('/local/components/custom/taskmanager/ajax/edit_status.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
                    alert('Название статуса обновлено!');
                    location.reload();
                } else alert('Ошибка: ' + result.error);
            })
            .catch(() => alert('Сбой запроса'));
    });



    let draggedTask = null;

    document.querySelectorAll('.task-card').forEach(card => {
        card.addEventListener('dragstart', e => {
            draggedTask = card;
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', card.dataset.id);
        });

        card.addEventListener('dragend', () => {
            if (draggedTask) {
                draggedTask.classList.remove('dragging');
                draggedTask = null;
            }
        });
    });

    document.querySelectorAll('.task-list').forEach(list => {
        list.addEventListener('dragenter', e => {
            e.preventDefault();
            list.classList.add('drag-over');
        });

        list.addEventListener('dragleave', e => {
            list.classList.remove('drag-over');
        });

        list.addEventListener('dragover', e => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        });

        list.addEventListener('drop', e => {
            e.preventDefault();
            list.classList.remove('drag-over');
            if (!draggedTask) return;

            const oldStatus = draggedTask.dataset.status;
            const newStatus = list.dataset.status;
            if (oldStatus === newStatus) return;


            list.appendChild(draggedTask);
            draggedTask.dataset.status = newStatus;


            const editBtn = draggedTask.querySelector('.edit-task-btn');
            if (editBtn) {
                editBtn.dataset.status = newStatus;
            }


            const editModalEl = document.getElementById('editTaskModal');
            const editModal = bootstrap.Modal.getInstance(editModalEl);
            if (editModal && editModal._isShown) {
                const form = document.getElementById('edit-task-form');
                const currentId = form.querySelector('[name="id"]').value;
                if (currentId === draggedTask.dataset.id) {
                    form.querySelector('[name="status"]').value = newStatus;
                    form.querySelector('[name="old_status"]').value = oldStatus;
                }
            }


            const formData = new FormData();
            formData.append('id', draggedTask.dataset.id);
            formData.append('status', newStatus);
            formData.append('old_status', oldStatus); // добавлено сюда
            formData.append('name', draggedTask.dataset.name);
            formData.append('description', draggedTask.dataset.description);
            formData.append('datetime', draggedTask.dataset.datetime);

            fetch('/local/components/custom/taskmanager/ajax/move_task.php', {
                method: 'POST',
                body: formData
            })
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        if (result.new_id) {
                            draggedTask.dataset.id = result.new_id;
                            const editBtn = draggedTask.querySelector('.edit-task-btn');
                            if (editBtn) editBtn.dataset.id = result.new_id;
                        }

                        alert('Статус задачи обновлён!');
                        const oldList = document.querySelector(`.task-list[data-status="${oldStatus}"]`);
                        if (oldList) updateEmptyMessage(oldList);
                        if (list) updateEmptyMessage(list);
                    } else {
                        alert('Ошибка при обновлении статуса: ' + result.error);
                        location.reload();
                    }
                })
                .catch(() => {
                    location.reload();
                });
        });
    });
});