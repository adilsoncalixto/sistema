# -*- coding: utf-8 -*-
import mysql.connector
from mysql.connector import errorcode


config = {
    'user': 'root',
    'password': 'memor1961',
    'host': 'vps.battistini.com.br',
    'database': 'liga_hortolandia'
}

query = "SELECT sm.id," \
        "jd.id," \
        "eq.id, " \
        "co.id," \
        "co.nome," \
        "eq.nome_equipe," \
        "ca.id," \
        "ca.nome_categoria," \
        "jd.nome_jogador," \
        "sum(sd.gols) Gols " \
        "FROM sumulas sm,  sumula_jogadores sd,  equipes eq,  jogadores jd,  categorias ca,  competicoes co " \
        "WHERE sd.jogador_id = jd.id " \
        "AND jd.equipe_id = eq.id " \
        "AND jd.categoria_id = ca.id " \
        "AND sm.id = sd.sumula_id " \
        "AND sm.competicao_id = co.id      " \
        "AND sd.gols > 1      " \
        "AND co.nome = 'COPA HORTO 1ª DIVISÃO 2017' " \
        "GROUP BY  sm.id,jd.id,eq.id,co.nome,eq.nome_equipe,ca.nome_categoria, jd.nome_jogador " \
        "ORDER BY 3, 5, 7 DESC;"

cnx = cur = None
try:
    cnx = mysql.connector.connect(**config)
except mysql.connector.Error as err:
    if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
        print('Something is wrong with your user name or password')
    elif err.errno == errorcode.ER_BAD_DB_ERROR:
        print("Database does not exist")
    else:
        print(err)
else:
    f = file('classificacao.sql','w')
    f.write('truncate table historico_partidas;\n')
    cur = cnx.cursor()
    cur.execute(query)
    for row in cur.fetchall():
        sumula_id  = row[0]
        jogador_id = row[1]
        equipe     = row[2]
        competicao = row[3]
        competicaoN = row[4]
        equipenome = row[5]
        categoria  = row[6]
        categoriaN = row[7]
        jogador    = row[8]
        gols       = row[9]
        for gol in range(0,gols):
            comando = "INSERT INTO historico_partidas " \
                      "(id_sumula,id_jogador,id_equipe,id_competicao,id_categoria,type_param,type_form,tempo) " \
                      "VALUES (%(sumula)s,%(jogador)s,%(equipe)s,%(competicao)s,%(categoria)s," \
                      "'F','GF','08:55');" % {'sumula':sumula_id,'jogador':jogador_id,'equipe':equipe,
                                             'competicao':competicao,'categoria':categoria}
            print comando
            f.write(comando+'\n')

finally:
    if f:
        f.close()
    if cur:
        cur.close()
    if cnx:
        cnx.close()