PRAGMA foreign_keys = ON;

CREATE TABLE 'log_group' (
 'id' INTEGER NOT NULL PRIMARY KEY ,
 'last_status' INTEGER ,
 'last_comment' TEXT ,
 'last_confirm_date' INTEGER ,
 'log_types' TEXT ,
 'first_log_date' INTEGER,
 'last_log_date' INTEGER );

CREATE TABLE 'log_data' (
 'id' INTEGER PRIMARY KEY,
 'group_id' INTEGER ,
 'new_content' TEXT,
 'all_content' TEXT,
 'file_path' TEXT,
 'log_type' TEXT ,
 'date' INTEGER,
  FOREIGN KEY (group_id) references log_group(id) ON DELETE CASCADE );
