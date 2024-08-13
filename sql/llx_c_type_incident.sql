CREATE TABLE llx_c_type_incident(
    -- BEGIN MODULEBUILDER FIELDS
      rowid     integer PRIMARY KEY,
      code    	varchar(16) NOT NULL,
      label 	varchar(128),
      entity 	int DEFAULT 0,
      active  	tinyint DEFAULT 1  NOT NULL
-- END MODULEBUILDER FIELDS
) ENGINE=innodb;

INSERT INTO llx_c_type_incident (rowid, code, label, active)
VALUES (1, 'ANO', 'Anomalie', 1), (2, 'HU', 'Humain', 1), (3, 'MAT', 'Matériel', 1), (4, 'PRO', 'Procédure', 1);
