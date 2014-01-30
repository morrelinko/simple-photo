CREATE TABLE IF NOT EXISTS photo (
    "photo_id" INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL UNIQUE,
    "storage_name" TEXT NOT NULL,
    "file_name" TEXT NOT NULL,
    "file_extension" TEXT NOT NULL,
    "file_path" TEXT NOT NULL,
    "file_mime" TEXT NOT NULL,
    "created_at" DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updated_at" DATETIME DEFAULT CURRENT_TIMESTAMP
);