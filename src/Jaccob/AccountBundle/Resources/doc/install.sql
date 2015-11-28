-- Account table
CREATE TABLE account (
    id  SERIAL PRIMARY KEY,
    mail VARCHAR(255) UNIQUE NOT NULL,
    user_name VARCHAR(255) NOT NULL,
    user_database VARCHAR(64) NOT NULL DEFAULT 'jaccob_business',
    password_hash VARCHAR(255),
    salt VARCHAR(128),
    key_public BYTEA,
    key_private BYTEA,
    key_type VARCHAR(10),
    is_active BOOLEAN NOT NULL DEFAULT FALSE,
    is_admin BOOLEAN NOT NULL DEFAULT FALSE,
    validate_token VARCHAR(64),
    ts_added TIMESTAMP NOT NULL DEFAULT NOW(),
    UNIQUE (mail)
);

-- Some test data
INSERT INTO account (id, mail, user_name, is_active, is_admin) VALUES (0, 'Anonymous', 'Anonymous', FALSE, FALSE);
INSERT INTO account (mail, user_name, is_active, is_admin) VALUES ('pounard@processus.org', 'Pierre', TRUE, TRUE);
-- INSERT INTO account (mail, user_name, is_active, is_admin) VALUES ('jean.test@processus.org', 'Jean Test', TRUE, FALSE);

-- One time login temporary storage
CREATE TABLE account_onetime (
    id_account INTEGER NOT NULL,
    login_token VARCHAR(255) DEFAULT NULL,
    ts_expire TIMESTAMP NOT NULL DEFAULT NOW(),
    FOREIGN KEY (id_account) REFERENCES account (id) ON DELETE CASCADE
);

CREATE TABLE session (
    id VARCHAR(255) NOT NULL,
    created TIMESTAMP NOT NULL DEFAULT NOW(),
    touched TIMESTAMP NOT NULL DEFAULT NOW(),
    data TEXT,
    PRIMARY KEY (id)
);
