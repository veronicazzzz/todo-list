# todo-list
Web-приложение, позволяющее регистрироваться в системе и управлять личным списком задач (TODO-лист).

### POST /register
Тут происходит регистрация пользователя.
![Registration](./screenshots/register.png)

### POST /api/login_check
Проверяются введеные пользователем данные, если все ок, выдается токен.
![Login check OK](./screenshots/logincheckok.png)

Иначе - ошибка (пользователя нет).
![Login check ERROR](./screenshots/logincheckerror.png)

### POST /api/todo/
После успешной авторизации, используя токен, можно добавлять дела в список.
![Add task #1](./screenshots/add1.png)
![Add task #2](./screenshots/add2.png)

### GET /api/todo/
Также испльзуя токен пользователя, получаем весь список дел.
![Get To-do list](./screenshots/gettodo.png)

### PUT /api/todo/{id}
Можно изменить как состояние таска, так и его название.
![Change task](./screenshots/put.png)

### DELETE /api/todo/{id}
Удалим один таск.
![Delete task](./screenshots/delete5.png)

Тогда в GET /api/todo/ останется только таск на чистку зубов.
![Brush your teeth](./screenshots/get6.png)
