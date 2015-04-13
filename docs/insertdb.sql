INSERT INTO role(`id`,`name`, `internal_code`) VALUES(1, 'Root', 'root');
INSERT INTO role(`id`,`name`, `internal_code`) VALUES(2, 'Amministratore', 'admin');
INSERT INTO role(`id`,`name`, `internal_code`) VALUES(3, 'Utente', 'utente');

INSERT INTO permission(`id`,`name`, `internal_code`) VALUES (1, 'Root permissions', 'root_permissions');
INSERT INTO permission(`id`,`name`, `internal_code`) VALUES (2, 'Lista utenti', 'list_users');
INSERT INTO permission(`id`,`name`, `internal_code`) VALUES (3, 'Aggiungere utente', 'add_user');
INSERT INTO permission(`id`,`name`, `internal_code`) VALUES (4, 'Modificare utente', 'edit_user');
INSERT INTO permission(`id`,`name`, `internal_code`) VALUES (5, 'Aggiungere hotel', 'add_hotel');
INSERT INTO permission(`id`,`name`, `internal_code`) VALUES (6, 'Cancellare hotel', 'delete_hotel');
INSERT INTO permission(`id`,`name`, `internal_code`) VALUES (7, 'Modificare hotel', 'edit_hotel');
INSERT INTO permission(`id`,`name`, `internal_code`) VALUES (8, 'Aggiungere preziario', 'add_price');
INSERT INTO permission(`id`,`name`, `internal_code`) VALUES (9, 'Modificare preziario', 'edit_price');
INSERT INTO permission(`id`,`name`, `internal_code`) VALUES (10, 'Cancellare preziario', 'delete_price');

INSERT INTO role_permission(role_id, permission_id) VALUES (1, 1);
INSERT INTO role_permission(role_id, permission_id) VALUES (1, 2);
INSERT INTO role_permission(role_id, permission_id) VALUES (1, 3);
INSERT INTO role_permission(role_id, permission_id) VALUES (1, 4);
INSERT INTO role_permission(role_id, permission_id) VALUES (1, 5);
INSERT INTO role_permission(role_id, permission_id) VALUES (1, 6);
INSERT INTO role_permission(role_id, permission_id) VALUES (1, 7);
INSERT INTO role_permission(role_id, permission_id) VALUES (1, 8);
INSERT INTO role_permission(role_id, permission_id) VALUES (1, 9);
INSERT INTO role_permission(role_id, permission_id) VALUES (1, 10);

INSERT INTO role_permission(role_id, permission_id) VALUES (2, 2);
INSERT INTO role_permission(role_id, permission_id) VALUES (2, 3);
INSERT INTO role_permission(role_id, permission_id) VALUES (2, 4);
INSERT INTO role_permission(role_id, permission_id) VALUES (2, 5);
INSERT INTO role_permission(role_id, permission_id) VALUES (2, 6);
INSERT INTO role_permission(role_id, permission_id) VALUES (2, 7);
INSERT INTO role_permission(role_id, permission_id) VALUES (2, 8);
INSERT INTO role_permission(role_id, permission_id) VALUES (2, 9);
INSERT INTO role_permission(role_id, permission_id) VALUES (2, 10);

INSERT INTO `status`(id, name) VALUES (1, 'Status utente');
INSERT INTO `status`(id, name, parent_id) VALUES (2, 'Attivo', 1);
INSERT INTO `status`(id, name, parent_id) VALUES (3, 'Inattivo', 1);

INSERT INTO `status`(id, name) VALUES (4, 'Status hotel');
INSERT INTO `status`(id, name, parent_id) VALUES (5, 'Attivo', 4);
INSERT INTO `status`(id, name, parent_id) VALUES (6, 'Inattivo', 4);

INSERT INTO user(id, name, lastname, username, pwd, role_id, status_id)
VALUES (1, 'Root', 'Root', 'root', MD5('123456'), 1, 2);

CREATE VIEW roles_noroot AS
SELECT DISTINCT 
	r.id, 
    r.name
FROM role r
WHERE NOT EXISTS (
	SELECT DISTINCT rp.role_id
    FROM role_permission rp
    JOIN permission p
    ON rp.permission_id = p.id
    WHERE p.internal_code = 'root_permissions'
    AND rp.role_id = r.id
);