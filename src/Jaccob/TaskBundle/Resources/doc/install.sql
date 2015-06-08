-- Main task table
CREATE TABLE task (
    id  SERIAL PRIMARY KEY,
    id_account INTEGER NOT NULL,
    is_done BOOLEAN DEFAULT FALSE,
    is_starred BOOLEAN DEFAULT FALSE,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL DEFAULT '',
    priority INTEGER NOT NULL DEFAULT 0,
    ts_added TIMESTAMP NOT NULL DEFAULT NOW(),
    ts_updated TIMESTAMP NOT NULL DEFAULT NOW(),
    ts_deadline TIMESTAMP DEFAULT NULL,
    FOREIGN KEY (id_account) REFERENCES account (id) ON DELETE CASCADE
);

-- Task tags table
CREATE TABLE task_tag (
    id SERIAL PRIMARY KEY,
    id_account INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (id_account) REFERENCES account (id) ON DELETE CASCADE,
    UNIQUE (id_account, name)
);

-- Task to tag table
CREATE TABLE task_tag_map (
    id_tag INTEGER NOT NULL,
    id_task INTEGER NOT NULL,
    FOREIGN KEY (id_tag) REFERENCES task (id) ON DELETE CASCADE,
    FOREIGN KEY (id_task) REFERENCES task_tag (id) ON DELETE CASCADE
);
