<?php
require_once __DIR__ . '/../layouts/header.php';
?>

<div class="lead-wrapper">
    <div class="create-lead">
        <div class="create-container">
            <form action="/create-lead" method="POST">
                <div class="input-container">
                    <label for="lead_first_name">Имя</label>
                    <input type="text" id="lead_first_name" name="lead_first_name" value="Михаил" required >
                </div>
                <div class="input-container">
                    <label for="lead_second_name">Фамилия</label>
                    <input type="text" id="lead_second_name" name="lead_second_name" value="Козлов" required>
                </div>
                <div class="input-container">
                    <label for="tel">Введите телефон</label>
                    <input type="tel" id="phone" name="phone"
                           placeholder="+7 (___) ___-__-__"
                           pattern="\+7\s\(\d{3}\)\s\d{3}-\d{2}-\d{2}"
                           title="Введите номер в формате +7 (123) 456-78-90"
                           required>
                </div>
                <div class="input-container">
                    <label for="email">Введите email</label>
                    <input type="email" id="email" name="email"
                           placeholder="example@domain.com" required>
                </div>
                <div class="input-container">
                    <label for="cost">Введите сумму</label>
                    <input type="number" id="cost" name="cost"
                           placeholder="Например, 1000"
                           min="0" step="1" required>
                </div>
                <button type="submit">Отправить</button>
            </form>
        </div>
    </div>
    <div class="create-lead">
        <div class="create-container">
            <form action="/info" method="POST">
                <span>Информация об аккаунте</span>
                <button type="submit">Получить</button>
            </form>
        </div>
    </div>
</div>
