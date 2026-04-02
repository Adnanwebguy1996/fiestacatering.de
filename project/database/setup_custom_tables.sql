-- Create user_auth table
CREATE TABLE IF NOT EXISTS user_auth (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    apikey TEXT NOT NULL,
    token TEXT NOT NULL,
    fp_token TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create user_details table
CREATE TABLE IF NOT EXISTS user_details (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    country_id INTEGER NOT NULL,
    full_name TEXT NOT NULL,
    profile_img TEXT,
    dob DATE NOT NULL,
    email TEXT NOT NULL UNIQUE,
    temp_email TEXT,
    phno_cc TEXT NOT NULL,
    phno TEXT NOT NULL,
    password TEXT NOT NULL,
    role INTEGER NOT NULL DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    status INTEGER NOT NULL DEFAULT 1
);
