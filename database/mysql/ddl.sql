
ALTER TABLE `sumula_jogadores` ADD COLUMN `gols` INTEGER DEFAULT 0 AFTER `camisa`;
ALTER TABLE `sumula_jogadores` ADD COLUMN `faltas` INTEGER DEFAULT 0 AFTER `gols`;
ALTER TABLE `sumula_jogadores` ADD COLUMN `amarelo` VARCHAR(5) DEFAULT NULL AFTER `faltas`;
ALTER TABLE `sumula_jogadores` ADD COLUMN `vermelho` VARCHAR(5) DEFAULT NULL AFTER `amarelo`;

ALTER TABLE `sumulas` ADD COLUMN `anotador`          VARCHAR(50) DEFAULT NULL AFTER `data_partida`;
ALTER TABLE `sumulas` ADD COLUMN `cronometrista`     VARCHAR(50) DEFAULT NULL AFTER `anotador`;
ALTER TABLE `sumulas` ADD COLUMN `periodo_1_inicio`  VARCHAR(5)  DEFAULT NULL AFTER `cronometrista`;
ALTER TABLE `sumulas` ADD COLUMN `periodo_1_termino` VARCHAR(5)  DEFAULT NULL AFTER `periodo_1_inicio`;
ALTER TABLE `sumulas` ADD COLUMN `periodo_2_inicio`  VARCHAR(5)  DEFAULT NULL AFTER `periodo_1_termino`;
ALTER TABLE `sumulas` ADD COLUMN `periodo_2_termino` VARCHAR(5)  DEFAULT NULL AFTER `periodo_2_inicio`;
ALTER TABLE `sumulas` ADD COLUMN `periodo_3_inicio`  VARCHAR(5)  DEFAULT NULL AFTER `periodo_2_termino`;
ALTER TABLE `sumulas` ADD COLUMN `periodo_3_termino` VARCHAR(5)  DEFAULT NULL AFTER `periodo_3_inicio`;
ALTER TABLE `sumulas` ADD COLUMN `periodo_4_inicio`  VARCHAR(5)  DEFAULT NULL AFTER `periodo_3_termino`;
ALTER TABLE `sumulas` ADD COLUMN `periodo_4_termino` VARCHAR(5)  DEFAULT NULL AFTER `periodo_4_inicio`;

ALTER TABLE `sumulas` ADD COLUMN `fase` VARCHAR(10)  DEFAULT NULL AFTER `data_partida`;
ALTER TABLE `sumulas` ADD COLUMN `serie` VARCHAR(10)  DEFAULT NULL AFTER `fase`;

ALTER TABLE `sumulas` ADD COLUMN `marcador_1_equipe1` VARCHAR(5) DEFAULT NULL AFTER `serie`;
ALTER TABLE `sumulas` ADD COLUMN `marcador_1_equipe2` VARCHAR(5) DEFAULT NULL AFTER `marcador_1_equipe1`;
ALTER TABLE `sumulas` ADD COLUMN `marcador_2_equipe1` VARCHAR(5) DEFAULT NULL AFTER `marcador_1_equipe2`;
ALTER TABLE `sumulas` ADD COLUMN `marcador_2_equipe2` VARCHAR(5) DEFAULT NULL AFTER `marcador_2_equipe1`;
ALTER TABLE `sumulas` ADD COLUMN `marcador_3_equipe1` VARCHAR(5) DEFAULT NULL AFTER `marcador_2_equipe2`;
ALTER TABLE `sumulas` ADD COLUMN `marcador_3_equipe2` VARCHAR(5) DEFAULT NULL AFTER `marcador_3_equipe1`;
ALTER TABLE `sumulas` ADD COLUMN `marcador_4_equipe1` VARCHAR(5) DEFAULT NULL AFTER `marcador_3_equipe2`;
ALTER TABLE `sumulas` ADD COLUMN `marcador_4_equipe2` VARCHAR(5) DEFAULT NULL AFTER `marcador_4_equipe1`;

ALTER TABLE `sumula_equipes` ADD COLUMN `faltas` INTEGER DEFAULT 0 AFTER `equipe_id`;


CREATE TABLE `historico_partidas` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`id_sumula` INT(10) UNSIGNED NOT NULL,
	`id_jogador` INT(10) UNSIGNED NOT NULL,
	`id_equipe` INT(10) UNSIGNED NOT NULL,
	`id_competicao` INT(10) UNSIGNED NOT NULL,
	`id_categoria` INT(10) UNSIGNED NULL DEFAULT NULL,
	`id_sub` INT(10) UNSIGNED NULL DEFAULT NULL,
	`camisa` INT(11) NULL DEFAULT NULL,
	`type_param` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`type_form` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`tempo` VARCHAR(255) NOT NULL COLLATE 'utf8_unicode_ci',
	`created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`),
	INDEX `historico_partidas_id_jogador_index` (`id_jogador`),
	INDEX `historico_partidas_id_equipe_index` (`id_equipe`),
	INDEX `historico_partidas_id_competicao_index` (`id_competicao`),
	INDEX `historico_partidas_id_categoria_index` (`id_categoria`),
	INDEX `historico_partidas_id_sub_index` (`id_sub`),
	INDEX `historico_partidas_id_sumula_index` (`id_sumula`),
	CONSTRAINT `historico_partidas_id_categoria_foreign` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id`),
	CONSTRAINT `historico_partidas_id_competicao_foreign` FOREIGN KEY (`id_competicao`) REFERENCES `competicoes` (`id`),
	CONSTRAINT `historico_partidas_id_equipe_foreign` FOREIGN KEY (`id_equipe`) REFERENCES `equipes` (`id`),
	CONSTRAINT `historico_partidas_id_jogador_foreign` FOREIGN KEY (`id_jogador`) REFERENCES `jogadores` (`id`),
	CONSTRAINT `historico_partidas_id_sub_foreign` FOREIGN KEY (`id_sub`) REFERENCES `jogadores` (`id`)
)
COLLATE='utf8_unicode_ci'
ENGINE=InnoDB
;

ALTER TABLE `historico_partidas` ADD COLUMN `id_sumula` INT(10) UNSIGNED NOT NULL AFTER `id`;
ALTER TABLE `historico_partidas` ADD COLUMN `camisa` INT(10) UNSIGNED NOT NULL AFTER `id_sumula`;

CREATE TABLE dirigente_tipos
(
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	nome VARCHAR(50),
	`created_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	`updated_at` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
	PRIMARY KEY (`id`)
);

CREATE INDEX dirigentes_tipo_index ON dirigentes (dirigente_tipo_id);


INSERT INTO dirigente_tipos (nome) VALUE ('Técnico');
INSERT INTO dirigente_tipos (nome) VALUE ('Auxiliar técnico');
INSERT INTO dirigente_tipos (nome) VALUE ('Massagista');

ALTER TABLE dirigentes ADD dirigente_tipo_id INT NULL AFTER nome_dirigente;
ALTER TABLE dirigentes
	ADD CONSTRAINT dirigentes_tipo__fk
FOREIGN KEY (dirigente_tipo_id) REFERENCES dirigentes (id);

ALTER TABLE `partidas`
	ADD COLUMN `rodada` INT(10) UNSIGNED NULL DEFAULT NULL AFTER `categoria_id`;

ALTER TABLE `historico_partidas`
	ADD COLUMN `id_partida` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `id`;

ALTER TABLE `historico_partidas`
	ADD COLUMN `rodada` INT(10) UNSIGNED NOT NULL DEFAULT '0' AFTER `id`;