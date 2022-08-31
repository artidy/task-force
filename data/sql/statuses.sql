USE task_force;

INSERT INTO statuses (title, code)
VALUES ('Новая', 'new'),
       ('В процессе', 'proceed'),
       ('Отменена', 'cancel'),
       ('Завершена', 'complete'),
       ('Просрочена', 'expired');
