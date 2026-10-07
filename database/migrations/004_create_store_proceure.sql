DROP PROCEDURE IF EXISTS sp_obter_resumo_autor;

CREATE PROCEDURE sp_obter_resumo_autor(IN p_autor_id INT)
BEGIN
    SELECT 
        a.CodAu AS autor_id,
        a.Nome AS autor_nome,
        COUNT(DISTINCT la.Livro_Codl) AS total_livros,
        COALESCE(SUM(l.Valor), 0.00) AS valor_total_acervo,
        COALESCE(AVG(l.Valor), 0.00) AS preco_medio_livro
    FROM Autor a
    LEFT JOIN Livro_Autor la ON a.CodAu = la.Autor_CodAu
    LEFT JOIN Livro l ON la.Livro_Codl = l.Codl
    WHERE a.CodAu = p_autor_id
    GROUP BY a.CodAu, a.Nome;
END;