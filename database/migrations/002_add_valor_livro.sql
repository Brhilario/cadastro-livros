-- Adicionar um campo de valor (R$) para o livro.
ALTER TABLE Livro
ADD COLUMN Valor DECIMAL(10, 2) NOT NULL DEFAULT 0.00 AFTER AnoPublicacao;