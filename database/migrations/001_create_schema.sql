CREATE TABLE IF NOT EXISTS Autor (
    CodAu INT AUTO_INCREMENT PRIMARY KEY,
    Nome VARCHAR(40) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Assunto (
    codAs INT AUTO_INCREMENT PRIMARY KEY,
    Descricao VARCHAR(20) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Livro (
    Codl INT AUTO_INCREMENT PRIMARY KEY,
    Titulo VARCHAR(40) NOT NULL,
    Editora VARCHAR(40) NOT NULL,
    Edicao INT NOT NULL,
    AnoPublicacao VARCHAR(4) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Livro_Autor (
    Livro_Codl INT NOT NULL,
    Autor_CodAu INT NOT NULL,
    PRIMARY KEY (Livro_Codl, Autor_CodAu),
    INDEX Livro_Autor_FKIndex1 (Livro_Codl),
    INDEX Livro_Autor_FKIndex2 (Autor_CodAu),
    CONSTRAINT fk_livro_autor_livro FOREIGN KEY (Livro_Codl) REFERENCES Livro (Codl) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_livro_autor_autor FOREIGN KEY (Autor_CodAu) REFERENCES Autor (CodAu) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS Livro_Assunto (
    Livro_Codl INT NOT NULL,
    Assunto_codAs INT NOT NULL,
    PRIMARY KEY (Livro_Codl, Assunto_codAs),
    INDEX Livro_Assunto_FKIndex1 (Livro_Codl),
    INDEX Livro_Assunto_FKIndex2 (Assunto_codAs),
    CONSTRAINT fk_livro_assunto_livro FOREIGN KEY (Livro_Codl) REFERENCES Livro (Codl) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_livro_assunto_assunto FOREIGN KEY (Assunto_codAs) REFERENCES Assunto (codAs) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;