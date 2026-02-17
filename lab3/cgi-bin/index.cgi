#!/usr/bin/env node

const mysql = require('mysql2/promise');
const querystring = require('querystring');

(async () => {
  let body = '';

  process.stdin.on('data', chunk => body += chunk);
  process.stdin.on('end', async () => {

    const data = querystring.parse(body);

    const nameRegex = /^[A-Za-zА-Яа-яЁё\s]{1,150}$/;

    if (!data.full_name || !nameRegex.test(data.full_name)) {
      return error("Некорректное ФИО");
    }

    if (!data.phone || data.phone.length > 20) {
      return error("Некорректный телефон");
    }

    if (!data.email || data.email.length > 150) {
      return error("Некорректный email");
    }

    if (!data.birth_date) {
      return error("Укажите дату рождения");
    }

    if (!['male','female','other'].includes(data.gender)) {
      return error("Некорректный пол");
    }

    if (!data.languages) {
      return error("Выберите минимум один язык");
    }

    const languages = Array.isArray(data.languages) ? data.languages : [data.languages];

    if (languages.length < 1) {
      return error("Выберите минимум один язык");
    }

    if (!data.biography) {
      return error("Введите биографию");
    }

    if (!data.contract) {
      return error("Необходимо принять контракт");
    }

    try {
      const connection = await mysql.createConnection({
        host: 'localhost',
        user: 'u82380',
        password: '43t3w4wE$',
        database: 'u82380'
      });

      const [result] = await connection.execute(
        `INSERT INTO application (full_name, phone, email, birth_date, gender, biography, contract_accepted)
            VALUES (?, ?, ?, ?, ?, ?, ?)`,
        [
          data.full_name,
          data.phone,
          data.email,
          data.birth_date,
          data.gender,
          data.biography,
          1
        ]
      );

      const applicationId = result.insertId;

      for (let langId of languages) {
        await connection.execute(
          `INSERT INTO application_language (application_id, language_id)
            VALUES (?, ?)`,
          [applicationId, langId]
        );
      }

      await connection.end();

      success("Данные успешно сохранены!");

    } catch (err) {
      error("Ошибка базы данных: " + err.message);
    }

  });

})();

function success(message) {
  console.log("Content-Type: text/html\n");
  console.log(`<h2 style="color:green;">${message}</h2>`);
}

function error(message) {
  console.log("Content-Type: text/html\n");
  console.log(`<h2 style="color:red;">Ошибка: ${message}</h2>`);
  process.exit();
}