ALTER TABLE liga_hortolandia.equipes DROP FOREIGN KEY equipes_categoria_id_foreign;

DROP INDEX equipes_categoria_id_index ON equipes;

ALTER TABLE jogadores ADD categoria_id INT(10) UNSIGNED NULL AFTER equipe_id;

ALTER TABLE partidas ADD categoria_id INT(10) UNSIGNED NULL AFTER competicao_id;

UPDATE partidas SET categoria_id = (SELECT e.categoria_id FROM equipes e WHERE  id = partidas.equipe1_id);


UPDATE jogadores set jogadores.categoria_id = (select equipes.categoria_id from equipes WHERE equipes.id = jogadores.equipe_id);



DROP  TABLE  sumula_dirigentes;
CREATE TABLE sumula_dirigentes
(
  id INT(11) unsigned PRIMARY KEY NOT NULL AUTO_INCREMENT,
  sumula_id INT(11) NOT NULL ,
  lado_equipe integer NOT NULL ,
  dirigente_id INT(11) NOT NULL,
  created_at TIMESTAMP DEFAULT '0000-00-00 00:00:00',
  updated_at TIMESTAMP DEFAULT '0000-00-00 00:00:00'
);


ALTER TABLE equipes ADD congresso BOOLEAN DEFAULT FALSE  NULL AFTER email_equipe;
ALTER TABLE equipes ADD abertura BOOLEAN DEFAULT FALSE  NULL AFTER  congresso;



ALTER TABLE sumula_dirigentes ADD lado_equipe INTEGER NULL AFTER id;