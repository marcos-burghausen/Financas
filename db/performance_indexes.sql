-- Otimizações de Performance para MySQL/MariaDB
-- Execute este script para melhorar a performance das consultas

-- Índices para a tabela lancamentos
-- Otimiza consultas por usuário e data
CREATE INDEX IF NOT EXISTS idx_lancamentos_user_date 
ON lancamentos(user_id, data_vencimento);

-- Otimiza consultas por usuário e tipo
CREATE INDEX IF NOT EXISTS idx_lancamentos_user_tipo 
ON lancamentos(user_id, tipo_lancamento);

-- Otimiza consultas por usuário, tipo e data combinados
CREATE INDEX IF NOT EXISTS idx_lancamentos_user_tipo_date 
ON lancamentos(user_id, tipo_lancamento, data_vencimento);

-- Otimiza consultas por conta
CREATE INDEX IF NOT EXISTS idx_lancamentos_conta 
ON lancamentos(conta_id);

-- Índices para a tabela contas
-- Otimiza consultas por usuário e status
CREATE INDEX IF NOT EXISTS idx_contas_user_status 
ON contas(user_id, tipo_conta);

-- Otimiza consultas por usuário e incluir em soma inicial
CREATE INDEX IF NOT EXISTS idx_contas_user_soma 
ON contas(user_id, incluir_em_soma_inicial);

-- Índices para a tabela credit_card_invoices (se existir)
-- Otimiza consultas de faturas por conta
CREATE INDEX IF NOT EXISTS idx_invoices_conta 
ON credit_card_invoices(conta_id);

-- Otimiza consultas por competência
CREATE INDEX IF NOT EXISTS idx_invoices_competencia 
ON credit_card_invoices(competencia);

-- Otimiza consultas por status
CREATE INDEX IF NOT EXISTS idx_invoices_status 
ON credit_card_invoices(status_fatura);

-- Verifica os índices criados
SHOW INDEX FROM lancamentos;
SHOW INDEX FROM contas;
