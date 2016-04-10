
CREATE DATABASE modelos;
use modelos;

SET foreign_key_checks = 0;

DROP TABLE IF EXISTS models;
CREATE TABLE models(
    id      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name    VARCHAR(100) NOT NULL,
    size    INT NOT NULL
) ENGINE='INNODB' CHARSET=UTF8 COMMENT='Tabla Modelos';

DROP TABLE IF EXISTS terms;
CREATE TABLE terms(
    id      BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    text    VARCHAR(30) NOT NULL
)ENGINE='INNODB' CHARSET=UTF8 COMMENT='Tabla Términos';

DROP TABLE IF EXISTS models_tagged;
CREATE TABLE models_tagged(
    idmodel     BIGINT UNSIGNED NOT NULL,
        CONSTRAINT mod_mod_fk FOREIGN KEY(idmodel) REFERENCES models(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
    idterm     BIGINT UNSIGNED NOT NULL,
        CONSTRAINT mod_term_fk FOREIGN KEY(idterm) REFERENCES terms(id)
            ON DELETE CASCADE
            ON UPDATE CASCADE,
    CONSTRAINT modtag_pk PRIMARY KEY(idmodel,idterm)
)ENGINE='INNODB' CHARSET=UTF8 COMMENT='Tabla Modelos Etiquetados';