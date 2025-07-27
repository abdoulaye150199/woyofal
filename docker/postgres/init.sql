CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

-- Fonctions
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE OR REPLACE FUNCTION exists_code_recharge(code text) 
RETURNS boolean AS $$
BEGIN
    RETURN EXISTS (SELECT 1 FROM achats WHERE code_recharge = code);
END;
$$ LANGUAGE plpgsql;