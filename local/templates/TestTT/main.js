document.addEventListener('DOMContentLoaded', () => {
    // Toast notification setup
    const toastContainer = document.createElement('div');
    toastContainer.className = 'toast-container position-fixed bottom-0 end-0 p-3';
    toastContainer.style.zIndex = '1050';
    document.body.appendChild(toastContainer);

    function showToast(message, isSuccess = true) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white ${isSuccess ? 'bg-success' : 'bg-danger'} border-0`;
        toast.role = 'alert';
        toast.ariaLive = 'assertive';
        toast.ariaAtomic = 'true';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        toastContainer.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        setTimeout(() => toast.remove(), 3000);
    }

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
        const status = formData.get('status');
        fetch('/local/components/custom/taskmanager/ajax/add_task.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(result => {
                if (result.success && result.task) {
                    const taskList = document.querySelector(`.task-list[data-status="${result.task.status}"]`);
                    if (taskList) {
                        const taskCard = document.createElement('li');
                        taskCard.className = 'list-group-item task-card';
                        taskCard.draggable = true;
                        taskCard.dataset.id = result.task.id;
                        taskCard.dataset.status = result.task.status;
                        taskCard.dataset.name = result.task.name;
                        taskCard.dataset.description = result.task.description;
                        taskCard.dataset.datetime = result.task.datetime ? result.task.datetime.replace(' ', 'T').slice(0, 16) : '';
                        taskCard.innerHTML = `
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5>${result.task.name}</h5>
                                    <p>${result.task.description}</p>
                                    <small class="text-muted">${result.task.datetime || 'Без даты'}</small>
                                </div>
                                <button class="btn btn-sm btn-primary edit-task-btn"
                                        data-id="${result.task.id}"
                                        data-status="${result.task.status}"
                                        data-name="${result.task.name}"
                                        data-description="${result.task.description}"
                                        data-datetime="${result.task.datetime ? result.task.datetime.replace(' ', 'T').slice(0, 16) : ''}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editTaskModal">
                                    ✏️ Редактировать
                                </button>
                            </div>
                        `;
                        taskList.appendChild(taskCard);
                        updateEmptyMessage(taskList);
                        e.target.reset();
                        const addModalEl = document.getElementById('addTaskModal');
                        const addModal = bootstrap.Modal.getInstance(addModalEl);
                        if (addModal) addModal.hide();
                        showToast('Задача добавлена!');
                        // Add event listeners to new task card
                        taskCard.addEventListener('dragstart', ev => {
                            draggedTask = taskCard;
                            taskCard.classList.add('dragging');
                            ev.dataTransfer.effectAllowed = 'move';
                            ev.dataTransfer.setData('text/plain', taskCard.dataset.id);
                        });
                        taskCard.addEventListener('dragend', () => {
                            if (draggedTask) {
                                draggedTask.classList.remove('dragging');
                                draggedTask = null;
                            }
                        });
                        taskCard.querySelector('.edit-task-btn').addEventListener('click', () => {
                            const form = document.getElementById('edit-task-form');
                            form.querySelector('[name="id"]').value = taskCard.dataset.id;
                            form.querySelector('[name="status"]').value = taskCard.dataset.status;
                            form.querySelector('[name="old_status"]').value = taskCard.dataset.status;
                            form.querySelector('[name="name"]').value = taskCard.dataset.name;
                            form.querySelector('[name="description"]').value = taskCard.dataset.description;
                            form.querySelector('[name="datetime"]').value = taskCard.dataset.datetime;
                        });
                    }
                } else {
                    showToast('Ошибка: ' + result.error, false);
                }
            })
            .catch(() => showToast('Сбой запроса', false));
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
        const id = formData.get('id');
        const oldStatus = formData.get('old_status');
        const newStatus = formData.get('status');
        fetch('/local/components/custom/taskmanager/ajax/edit_task.php', {
            method: 'POST',
            body: formData
        })
            .then(res => res.json())
            .then(result => {
                if (result.success && result.task) {
                    const taskId = result.task.old_id ?? result.task.id;
                    const taskCard = document.querySelector(`.task-card[data-id="${taskId}"]`);
                    if (!taskCard) return;

                    // Обновляем data-атрибуты
                    taskCard.dataset.id = result.task.id;
                    taskCard.dataset.name = result.task.name;
                    taskCard.dataset.description = result.task.description;
                    taskCard.dataset.datetime = result.task.datetime
                        ? result.task.datetime.replace(' ', 'T').slice(0, 16)
                        : '';

                    // Обновляем DOM содержимое
                    taskCard.querySelector('h5').textContent = result.task.name;
                    taskCard.querySelector('p').textContent = result.task.description;
                    taskCard.querySelector('small').textContent = result.task.datetime || 'Без даты';

                    // Обновляем кнопку редактирования
                    const editBtn = taskCard.querySelector('.edit-task-btn');
                    if (editBtn) {
                        editBtn.dataset.id = result.task.id;
                        editBtn.dataset.status = result.task.status;
                        editBtn.dataset.name = result.task.name;
                        editBtn.dataset.description = result.task.description;
                        editBtn.dataset.datetime = result.task.datetime
                            ? result.task.datetime.replace(' ', 'T').slice(0, 16)
                            : '';
                    }

                    // Перемещаем карточку, если статус изменился
                    if (oldStatus !== newStatus) {
                        const oldList = document.querySelector(`.task-list[data-status="${oldStatus}"]`);
                        const newList = document.querySelector(`.task-list[data-status="${newStatus}"]`);
                        if (oldList && newList) {
                            taskCard.dataset.status = newStatus;
                            newList.appendChild(taskCard);
                            updateEmptyMessage(oldList);
                            updateEmptyMessage(newList);
                        }
                    }

                    // Сброс формы и закрытие модального окна
                    e.target.reset();
                    const editModal = bootstrap.Modal.getInstance(document.getElementById('editTaskModal'));
                    if (editModal) editModal.hide();

                    showToast('Задача обновлена!');
                } else {
                    showToast('Ошибка: ' + result.error, false);
                }
            })
            .catch(() => showToast('Сбой запроса', false));

    });

    document.getElementById('delete-task-btn').addEventListener('click', () => {
        if (!confirm('Вы действительно хотите удалить задачу?')) return;
        const form = document.getElementById('edit-task-form');
        const id = form.querySelector('[name="id"]').value;
        const status = form.querySelector('[name="old_status"]').value;
        if (!id || !status) {
            showToast('Не удалось определить задачу для удаления', false);
            return;
        }
        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);
        fetch('/local/components/custom/taskmanager/ajax/delete_task.php', { method: 'POST', body: formData })
            .then(res => res.json())
            .then(result => {
                if (result.success) {
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
                    showToast('Задача удалена!');
                } else {
                    showToast('Ошибка: ' + result.error, false);
                }
            })
            .catch(() => showToast('Сбой запроса', false));
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

    function addDragListeners(card) {
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
    }

// Инициализация drag & drop для карточек
    document.querySelectorAll('.task-card').forEach(addDragListeners);

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