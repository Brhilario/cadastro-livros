SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE Livro_Assunto;
TRUNCATE TABLE Livro_Autor;
TRUNCATE TABLE Livro;
TRUNCATE TABLE Assunto;
TRUNCATE TABLE Autor;
SET FOREIGN_KEY_CHECKS = 1;

INSERT INTO
    Autor (Nome)
VALUES ('Martin Fowler'),
    ('Robert C. Martin'),
    ('Kent Beck'),
    ('Erich Gamma');

INSERT INTO
    Assunto (Descricao)
VALUES ('Arquitetura'),
    ('Eng. de Software'),
    ('Metodologias Ágeis'),
    ('Padrões de Projeto');

INSERT INTO
    Livro (
        Titulo,
        Editora,
        Edicao,
        AnoPublicacao,
        Valor
    )
VALUES (
        'Refactoring',
        'Addison-Wesley',
        2,
        '2018',
        215.50
    ),
    (
        'Clean Code',
        'Prentice Hall',
        1,
        '2008',
        189.90
    ),
    (
        'Design Patterns',
        'Addison-Wesley',
        1,
        '1994',
        280.00
    ),
    (
        'Extreme Programming Explained',
        'Addison-Wesley',
        2,
        '2004',
        150.00
    );

-- Vínculos Livro <-> Autor
-- Refactoring (Martin Fowler e Kent Beck)
INSERT INTO
    Livro_Autor (Livro_Codl, Autor_CodAu)
VALUES (1, 1),
    (1, 3);
-- Clean Code (Robert C. Martin)
INSERT INTO Livro_Autor (Livro_Codl, Autor_CodAu) VALUES (2, 2);
-- Design Patterns (Erich Gamma)
INSERT INTO Livro_Autor (Livro_Codl, Autor_CodAu) VALUES (3, 4);
-- Extreme Programming (Kent Beck)
INSERT INTO Livro_Autor (Livro_Codl, Autor_CodAu) VALUES (4, 3);

-- Vínculos Livro <-> Assunto
-- Refactoring (Engenharia de Software, Padrões de Projeto)
INSERT INTO
    Livro_Assunto (Livro_Codl, Assunto_codAs)
VALUES (1, 2),
    (1, 4);
-- Clean Code (Engenharia de Software)
INSERT INTO Livro_Assunto (Livro_Codl, Assunto_codAs) VALUES (2, 2);
-- Design Patterns (Arquitetura, Padrões de Projeto)
INSERT INTO
    Livro_Assunto (Livro_Codl, Assunto_codAs)
VALUES (3, 1),
    (3, 4);
-- Extreme Programming (Metodologias Ágeis)
INSERT INTO Livro_Assunto (Livro_Codl, Assunto_codAs) VALUES (4, 3);