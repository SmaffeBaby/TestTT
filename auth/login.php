<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Авторизация и регистрация");

use Bitrix\Main\Engine\CurrentUser;
use Bitrix\Main\UserTable;
use Bitrix\Main\Security\Password;

global $USER;
$error = '';
$success = '';


if ($USER->IsAuthorized()) {
    LocalRedirect('/');
    exit;
}

// Обработка регистрации
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    $login = trim($_POST['register_login']);
    $email = trim($_POST['register_email']);
    $password = $_POST['register_password'];
    $confirm = $_POST['register_confirm_password'];

    if (!$login || !$email || !$password || !$confirm) {
        $error = "Заполните все поля регистрации.";
    } elseif ($password !== $confirm) {
        $error = "Пароли не совпадают.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Некорректный email.";
    } else {
        // Проверяем, есть ли уже пользователь с таким логином или email
        $userExists = \Bitrix\Main\UserTable::getList([
            'filter' => [
                'LOGIC' => 'OR',
                ['LOGIN' => $login],
                ['EMAIL' => $email],
            ],
            'limit' => 1
        ])->fetch();

        if ($userExists) {
            $error = "Пользователь с таким логином или email уже существует.";
        } else {
            $user = new CUser;
            $fields = [
                "LOGIN" => $login,
                "EMAIL" => $email,
                "PASSWORD" => $password,
                "CONFIRM_PASSWORD" => $confirm,
                "ACTIVE" => "Y",
                "GROUP_ID" => [2], // обычно группа "Пользователи"
            ];
            $ID = $user->Add($fields);
            if (intval($ID) > 0) {
                $success = "Регистрация успешна. Теперь можете войти.";
            } else {
                $error = $user->LAST_ERROR;
            }
        }
    }
}

// Обработка входа
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $login = trim($_POST['login_login']);
    $password = $_POST['login_password'];

    if (!$login || !$password) {
        $error = "Заполните все поля входа.";
    } else {
        global $USER;
        $res = $USER->Login($login, $password);
        if ($res === true) {
            LocalRedirect('/');
            exit;
        } else {
            $error = "Неверный логин или пароль.";
        }
    }
}
?>

<div class="container mt-5" style="max-width: 400px;">
    <h2>Авторизация и регистрация</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <ul class="nav nav-tabs" id="authTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login-tab-pane" type="button" role="tab" aria-controls="login-tab-pane" aria-selected="true">Вход</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register-tab-pane" type="button" role="tab" aria-controls="register-tab-pane" aria-selected="false">Регистрация</button>
        </li>
    </ul>

    <div class="tab-content mt-3" id="authTabsContent">
        <div class="tab-pane fade show active" id="login-tab-pane" role="tabpanel" aria-labelledby="login-tab">
            <form method="POST" novalidate>
                <input type="hidden" name="login" value="1">
                <div class="mb-3">
                    <label for="login_login" class="form-label">Логин</label>
                    <input type="text" class="form-control" id="login_login" name="login_login" required>
                </div>
                <div class="mb-3">
                    <label for="login_password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="login_password" name="login_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Войти</button>
            </form>
        </div>

        <div class="tab-pane fade" id="register-tab-pane" role="tabpanel" aria-labelledby="register-tab">
            <form method="POST" novalidate>
                <input type="hidden" name="register" value="1">
                <div class="mb-3">
                    <label for="register_login" class="form-label">Логин</label>
                    <input type="text" class="form-control" id="register_login" name="register_login" required>
                </div>
                <div class="mb-3">
                    <label for="register_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="register_email" name="register_email" required>
                </div>
                <div class="mb-3">
                    <label for="register_password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="register_password" name="register_password" required>
                </div>
                <div class="mb-3">
                    <label for="register_confirm_password" class="form-label">Повторите пароль</label>
                    <input type="password" class="form-control" id="register_confirm_password" name="register_confirm_password" required>
                </div>
                <button type="submit" class="btn btn-success">Зарегистрироваться</button>
            </form>
        </div>
    </div>
</div>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>
