CREATE
OR REPLACE VIEW vw_relatorio_livros_por_autor AS
SELECT
    a.CodAu AS autor_id,
    a.Nome AS autor_nome,
    l.Codl AS livro_id,
    l.Titulo AS livro_titulo,
    l.Editora AS livro_editora,
    l.Edicao AS livro_edicao,
    l.AnoPublicacao AS livro_ano,
    l.Valor AS livro_valor,
    COALESCE(
        GROUP_CONCAT(
            DISTINCT s.Descricao
            ORDER BY s.Descricao SEPARATOR ', '
        ),
        'Nenhum'
    ) AS assuntos,
    COALESCE(
        GROUP_CONCAT(
            DISTINCT co_a.Nome
            ORDER BY co_a.Nome SEPARATOR ', '
        ),
        a.Nome
    ) AS todos_autores
FROM
    Autor a
    INNER JOIN Livro_Autor la ON a.CodAu = la.Autor_CodAu
    INNER JOIN Livro l ON la.Livro_Codl = l.Codl
    LEFT JOIN Livro_Assunto las ON l.Codl = las.Livro_Codl
    LEFT JOIN Assunto s ON las.Assunto_codAs = s.codAs
    LEFT JOIN Livro_Autor co_la ON l.Codl = co_la.Livro_Codl
    LEFT JOIN Autor co_a ON co_la.Autor_CodAu = co_a.CodAu
GROUP BY
    a.CodAu,
    a.Nome,
    l.Codl,
    l.Titulo,
    l.Editora,
    l.Edicao,
    l.AnoPublicacao,
    l.Valor
ORDER BY a.Nome ASC, l.Titulo ASC;