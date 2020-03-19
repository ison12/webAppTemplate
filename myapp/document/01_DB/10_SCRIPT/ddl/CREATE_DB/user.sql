flush privileges;

DROP USER IF EXISTS 'myappapp'@'localhost';
DROP USER IF EXISTS 'myappope'@'localhost';
DROP USER IF EXISTS 'myappman'@'localhost';
DROP USER IF EXISTS 'myappadmin'@'localhost';

CREATE USER 'myappapp'@'localhost' IDENTIFIED WITH mysql_native_password BY 'myappapp';
CREATE USER 'myappope'@'localhost' IDENTIFIED WITH mysql_native_password BY 'myappope';
CREATE USER 'myappman'@'localhost' IDENTIFIED WITH mysql_native_password BY 'myappman';
CREATE USER 'myappadmin'@'localhost' IDENTIFIED WITH mysql_native_password BY 'myappadmin';

-- for myapp
grant select,insert,update,delete on myapp.* to 'myappapp'@'localhost';
grant select on myapp.* to 'myappope'@'localhost';
grant select,insert,update,delete on myapp.* to 'myappman'@'localhost';
grant all on myapp.* to 'myappadmin'@'localhost';

flush privileges;
