drop table if exists {$table_prefix}imegateleport;create table {$table_prefix}imegateleport(id bigint(20)unsigned not null auto_increment,object_id bigint(20)unsigned not null default'0',name varchar(50)default null,guid varchar(36)default null,primary key(id),unique key guid(guid,name),key name(name),key object_id(object_id))engine=innodb default charset=utf8;