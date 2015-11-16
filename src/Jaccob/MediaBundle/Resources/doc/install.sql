
CREATE TABLE album (
    id SERIAL PRIMARY KEY,
    id_account INTEGER NOT NULL,
    id_media_preview INTEGER,
    access_level INTEGER DEFAULT 0,
    path VARCHAR(1024) NOT NULL,
    user_name VARCHAR(255),
    file_count INTEGER NOT NULL DEFAULT 0,
    ts_added TIMESTAMP NOT NULL DEFAULT NOW(),
    ts_updated TIMESTAMP NOT NULL DEFAULT NOW(),
    ts_user_date_begin TIMESTAMP NOT NULL DEFAULT NOW(),
    ts_user_date_end TIMESTAMP NOT NULL DEFAULT NOW(),
    share_enabled BOOLEAN DEFAULT FALSE,
    share_token VARCHAR(255) DEFAULT NULL,
    share_password VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (id_account) REFERENCES account (id)
);

CREATE TABLE session_share (
    id_session VARCHAR(255) NOT NULL,
    id_album INTEGER NOT NULL,
    PRIMARY KEY (id_session, id_album),
    FOREIGN KEY (id_album) REFERENCES album (id) ON DELETE CASCADE
);

CREATE INDEX album_share_idx ON album (share_token, share_enabled, id);

CREATE TABLE album_acl (
    id_album INTEGER NOT NULL,
    id_account INTEGER NOT NULL,
    can_read BOOLEAN DEFAULT TRUE,
    can_write BOOLEAN DEFAULT FALSE,
    PRIMARY KEY (id_album, id_account),
    FOREIGN KEY (id_album) REFERENCES album (id) ON DELETE CASCADE,
    FOREIGN KEY (id_account) REFERENCES account (id) ON DELETE CASCADE
);

CREATE TABLE device (
    id SERIAL PRIMARY KEY,
    id_account INTEGER NOT NULL,
    name VARCHAR(1024) NOT NULL,
    FOREIGN KEY (id_account) REFERENCES account (id)
);

-- Store main media information
CREATE TABLE media (
    id SERIAL PRIMARY KEY,
    id_album INTEGER NOT NULL,
    id_account INTEGER NOT NULL,
    id_device INTEGER NOT NULL,
    access_level INTEGER DEFAULT 0,
    name VARCHAR(1024) NOT NULL,
    path VARCHAR(1024) NOT NULL,
    physical_path VARCHAR(1024) NOT NULL,
    filesize INTEGER NOT NULL DEFAULT 0,
    width INTEGER,
    height INTEGER,
    orientation INTEGER NOT NULL DEFAULT 1,
    user_name VARCHAR(255),
    md5_hash VARCHAR(255),
    mimetype VARCHAR(255) NOT NULL DEFAULT 'application/octet-stream',
    ts_added TIMESTAMP NOT NULL DEFAULT NOW(),
    ts_updated TIMESTAMP NOT NULL DEFAULT NOW(),
    ts_user_date TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (id_album) REFERENCES album(id),
    FOREIGN KEY (id_account) REFERENCES account(id),
    FOREIGN KEY (id_device) REFERENCES device(id)
);

-- Store transcoded versions of media when it makes sense, for example videos
-- will need to be transcoded to be played into the browser, so we need this
-- to reference all generated derivatives
CREATE TABLE media_derivative (
    id SERIAL PRIMARY KEY,
    id_media INTEGER NOT NULL,
    name VARCHAR(1024) NOT NULL,
    physical_path VARCHAR(1024) NOT NULL,
    filesize INTEGER NOT NULL DEFAULT 0,
    width INTEGER,
    height INTEGER,
    md5_hash VARCHAR(255),
    mimetype VARCHAR(255) NOT NULL DEFAULT 'application/octet-stream',
    FOREIGN KEY (id_media) REFERENCES media (id) ON DELETE CASCADE
);

-- Jobs to be done by cron tasks, such as archive creation or video
-- transcoding
CREATE TABLE media_job_queue (
    id SERIAL PRIMARY KEY,
    id_media INTEGER NOT NULL,
    type VARCHAR(64) NOT NULL,
    data BYTEA,
    ts_added TIMESTAMP NOT NULL DEFAULT NOW(),
    ts_started TIMESTAMP NOT NULL DEFAULT NOW(),
    is_running BOOLEAN DEFAULT FALSE,
    is_failed BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (id_media) REFERENCES media (id) ON DELETE CASCADE
);

CREATE TABLE media_metadata (
    id_media INTEGER NOT NULL,
    name VARCHAR(255) NOT NULL,
    data BYTEA,
    FOREIGN KEY (id_media) REFERENCES media(id) ON DELETE CASCADE
);

