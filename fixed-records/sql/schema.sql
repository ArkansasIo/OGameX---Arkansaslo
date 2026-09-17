PRAGMA foreign_keys = ON;
DROP VIEW IF EXISTS fixed_record_export;
DROP TABLE IF EXISTS fixed_records;
DROP TABLE IF EXISTS record_types;

CREATE TABLE record_types (
  code INTEGER PRIMARY KEY CHECK (code BETWEEN 1 AND 4),
  name TEXT NOT NULL UNIQUE
);
INSERT INTO record_types(code,name) VALUES (1,'player'),(2,'planet'),(3,'resource'),(4,'event');

CREATE TABLE fixed_records (
  record_id INTEGER PRIMARY KEY CHECK (record_id BETWEEN 0 AND 4294967295),
  version INTEGER NOT NULL DEFAULT 1 CHECK (version = 1),
  flags INTEGER NOT NULL DEFAULT 1 CHECK (flags BETWEEN 0 AND 3),
  record_type INTEGER NOT NULL REFERENCES record_types(code),
  value INTEGER NOT NULL DEFAULT 0 CHECK (value BETWEEN 0 AND 4294967295),
  quantity INTEGER NOT NULL DEFAULT 0 CHECK (quantity BETWEEN 0 AND 65535),
  owner_id INTEGER NOT NULL DEFAULT 0 CHECK (owner_id BETWEEN 0 AND 65535),
  timestamp INTEGER NOT NULL CHECK (timestamp BETWEEN 0 AND 4294967295),
  created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CHECK ((flags & 252) = 0)
);

CREATE INDEX idx_fixed_records_type_owner ON fixed_records(record_type, owner_id);
CREATE INDEX idx_fixed_records_timestamp ON fixed_records(timestamp);

INSERT INTO fixed_records(record_id,flags,record_type,value,quantity,owner_id,timestamp)
VALUES (1001,1,1,2500,1,7,1726550400),
       (2001,1,2,90000,12,7,1726550400),
       (3001,1,3,450,320,7,1726550400);

CREATE VIEW fixed_record_export AS
SELECT 82 AS magic, r.version, r.flags, r.record_type, t.name AS record_type_name,
       r.record_id, r.value, r.quantity, r.owner_id, r.timestamp
FROM fixed_records r JOIN record_types t ON t.code = r.record_type;

-- Import one record from named parameters in application code:
-- INSERT INTO fixed_records(record_id,version,flags,record_type,value,quantity,owner_id,timestamp)
-- VALUES (:record_id,1,:flags,:record_type,:value,:quantity,:owner_id,:timestamp);
