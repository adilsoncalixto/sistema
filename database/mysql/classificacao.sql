CREATE TABLE `classificacoes` (
  `id` INTEGER(11) NOT NULL AUTO_INCREMENT,
  `id_competicao` INTEGER(11) DEFAULT NULL,
  `id_categoria` INTEGER(11) DEFAULT NULL,
  `id_partida` INTEGER(11) DEFAULT NULL,
  `id_equipe1` INTEGER(11) DEFAULT NULL,
  `equipe1` TEXT COLLATE latin1_swedish_ci,
  `gols1` INTEGER(11) DEFAULT NULL,
  `pontos1` INTEGER(11) DEFAULT NULL,
  `id_equipe2` INTEGER(11) DEFAULT NULL,
  `equipe2` TEXT COLLATE latin1_swedish_ci,
  `gols2` INTEGER(11) DEFAULT NULL,
  `pontos2` INTEGER(11) DEFAULT NULL,
  PRIMARY KEY USING BTREE (`id`)
) ENGINE=InnoDB
AUTO_INCREMENT=107 ROW_FORMAT=DYNAMIC CHARACTER SET 'latin1' COLLATE 'latin1_swedish_ci'
;

CREATE DEFINER = 'root'@'localhost' PROCEDURE `proc_classificacao`(
        IN `pcompeticao` INTEGER(11),
        IN `pcategoria` INTEGER(11)
    )
    NOT DETERMINISTIC
    CONTAINS SQL
    SQL SECURITY DEFINER
    COMMENT ''
BEGIN
DECLARE done INT DEFAULT FALSE;
DECLARE VEQUIPE1, VEQUIPE2 TEXT;
DECLARE VCOMPETICAO,  VCATEGORIA, VPARTIDA, VIDEQUIPE1, VIDEQUIPE2, VGOLS1, VGOLS2  INTEGER;
DECLARE VPONTOS1, VPONTOS2 INTEGER;



DECLARE CURSOR1 CURSOR FOR SELECT COM.`id` AS ID_COMPETICAO,
                                                   PAR.`categoria_id` AS ID_CATEGORIA,
                                                   PAR.ID AS ID_PARTIDA,
                                                   PAR.`equipe1_id` AS ID_EQUIPE1,
                                                   EQU1.`nome_equipe` AS EQUIPE1,
                                                   (SELECT COUNT(*) FROM `historico_partidas` HP  WHERE HP.`id_competicao` = COM.`id` AND HP.`id_equipe` = PAR.`equipe1_id`) AS GOLS1,
                                                   PAR.`equipe2_id` AS ID_EQUIPE2,
                                                   EQU2.`nome_equipe` AS EQUIPE2,
                                                   (SELECT COUNT(*) FROM `historico_partidas` HP  WHERE HP.`id_competicao` = COM.`id` AND HP.`id_equipe` = PAR.`equipe2_id`) AS GOLS2
                                                  FROM `competicoes` COM
                                                  JOIN `partidas` PAR ON PAR.`competicao_id` = COM.`id`
                                                  JOIN `equipes` EQU1 ON `EQU1`.`id` = PAR.`equipe1_id`
                                                  JOIN `equipes` EQU2 ON `EQU2`.`id` = PAR.`equipe2_id`
                                                  WHERE COM.`id` = pcompeticao
                                                       AND PAR.`categoria_id` = pcategoria
                                                  ORDER BY PAR.`id`;
DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

DELETE FROM `classificacoes` WHERE ID_COMPETICAO = pcompeticao AND ID_CATEGORIA = pcategoria;

OPEN CURSOR1;

read_loop: LOOP
    FETCH CURSOR1  INTO VCOMPETICAO, VCATEGORIA, VPARTIDA, VIDEQUIPE1, VEQUIPE1, VGOLS1, VIDEQUIPE2, VEQUIPE2, VGOLS2;
    IF done THEN
      LEAVE read_loop;
    END IF;

    SET VPONTOS1 = 0;
    SET VPONTOS2 = 0;

    IF VGOLS1 = VGOLS2 THEN
        SET VPONTOS1 = 1;
        SET VPONTOS2 = 1;
    END IF;
    IF VGOLS1 > VGOLS2 THEN
            SET VPONTOS1 = 2;
    END IF;
    IF VGOLS2 > VGOLS1 THEN
        SET VPONTOS2 = 2;
    END IF;

    INSERT INTO   `classificacoes`
(
  `id_competicao`,
  `id_categoria`,
  `id_partida`,
  `id_equipe1`,
  `equipe1`,
  `gols1`,
  `pontos1`,
  `id_equipe2`,
  `equipe2`,
  `gols2`,
  `pontos2`)
VALUE (
  VCOMPETICAO,
  VCATEGORIA,
  VPARTIDA,
  VIDEQUIPE1,
  VEQUIPE1,
  VGOLS1,
  VPONTOS1,
  VIDEQUIPE2,
  VEQUIPE2,
  VGOLS2,
  VPONTOS2);

END LOOP;


CLOSE CURSOR1;


END;

CREATE ALGORITHM=UNDEFINED DEFINER='root'@'localhost' SQL SECURITY DEFINER VIEW `vw_classificacao`
AS
select
  `q1`.`id_competicao` AS `id_competicao`,
  `q1`.`id_categoria` AS `id_categoria`,
  `q1`.`id_equipe` AS `id_equipe`,
  `q1`.`equipe` AS `equipe`,
  sum(`q1`.`pontos`) AS `pontos`
from
  (
    select
      `liga_hortolandia`.`classificacoes`.`id_competicao` AS `id_competicao`,
      `liga_hortolandia`.`classificacoes`.`id_categoria` AS `id_categoria`,
      `liga_hortolandia`.`classificacoes`.`id_equipe1` AS `id_equipe`,
      `liga_hortolandia`.`classificacoes`.`equipe1` AS `equipe`,
      `liga_hortolandia`.`classificacoes`.`pontos1` AS `pontos`
    from
      `liga_hortolandia`.`classificacoes`
    union
    select
      `liga_hortolandia`.`classificacoes`.`id_competicao` AS `id_competicao`,
      `liga_hortolandia`.`classificacoes`.`id_categoria` AS `id_categoria`,
      `liga_hortolandia`.`classificacoes`.`id_equipe2` AS `id_equipe`,
      `liga_hortolandia`.`classificacoes`.`equipe2` AS `equipe`,
      `liga_hortolandia`.`classificacoes`.`pontos2` AS `pontos`
    from
      `liga_hortolandia`.`classificacoes`
  ) `q1`
group by
  `q1`.`id_competicao`,
  `q1`.`id_categoria`,
  `q1`.`id_equipe`,
  `q1`.`equipe`
order by
  `q1`.`id_competicao`,
  `q1`.`id_categoria`,
  `pontos` desc,
  `q1`.`id_equipe`;

