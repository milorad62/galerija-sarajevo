-- Marketplace (C) - preporučene migracije (pokreni u MySQL)
-- 1) Umjetnici: dodaj polja za profil (ako ne postoje)
ALTER TABLE umjetnici
  ADD COLUMN IF NOT EXISTS biografija TEXT NULL,
  ADD COLUMN IF NOT EXISTS web VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS instagram VARCHAR(255) NULL,
  ADD COLUMN IF NOT EXISTS facebook VARCHAR(255) NULL;

-- 2) Umjetnine: featured + status (ako ne postoje)
ALTER TABLE umjetnine
  ADD COLUMN IF NOT EXISTS featured TINYINT(1) NOT NULL DEFAULT 0,
  ADD COLUMN IF NOT EXISTS status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending';

-- 3) Indeksi
CREATE INDEX IF NOT EXISTS idx_umjetnine_featured ON umjetnine(featured);
CREATE INDEX IF NOT EXISTS idx_umjetnine_umjetnik ON umjetnine(umjetnik_id);
