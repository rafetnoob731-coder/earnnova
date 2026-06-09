-- ============================================
-- EARNNOVA - Supabase Database Schema
-- ============================================

-- Users Table
CREATE TABLE IF NOT EXISTS public.users (
    id BIGSERIAL PRIMARY KEY,
    uid TEXT UNIQUE NOT NULL,
    email TEXT UNIQUE NOT NULL,
    username TEXT UNIQUE NOT NULL,
    balance DECIMAL(12,4) DEFAULT 0,
    referral_balance DECIMAL(12,4) DEFAULT 0,
    activation_status TEXT DEFAULT 'inactive',
    activation_key TEXT,
    activation_date TIMESTAMPTZ,
    referral_code TEXT UNIQUE,
    referred_by TEXT,
    is_banned BOOLEAN DEFAULT FALSE,
    status TEXT DEFAULT 'active',
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Indexes
CREATE INDEX IF NOT EXISTS idx_users_uid ON public.users(uid);
CREATE INDEX IF NOT EXISTS idx_users_email ON public.users(email);
CREATE INDEX IF NOT EXISTS idx_users_referral_code ON public.users(referral_code);
CREATE INDEX IF NOT EXISTS idx_users_status ON public.users(activation_status);

-- Ad Rewards Table
CREATE TABLE IF NOT EXISTS public.ad_rewards (
    id BIGSERIAL PRIMARY KEY,
    user_id TEXT NOT NULL REFERENCES public.users(uid) ON DELETE CASCADE,
    ad_type TEXT NOT NULL,
    reward_amount DECIMAL(10,4) NOT NULL,
    ip_address TEXT,
    device_fingerprint TEXT,
    transaction_id TEXT UNIQUE,
    status TEXT DEFAULT 'completed',
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_ad_rewards_user ON public.ad_rewards(user_id);
CREATE INDEX IF NOT EXISTS idx_ad_rewards_created ON public.ad_rewards(created_at);
CREATE INDEX IF NOT EXISTS idx_ad_rewards_ip ON public.ad_rewards(ip_address);

-- Referrals Table
CREATE TABLE IF NOT EXISTS public.referrals (
    id BIGSERIAL PRIMARY KEY,
    referrer_id TEXT NOT NULL REFERENCES public.users(uid) ON DELETE CASCADE,
    referred_uid TEXT,
    referred_email TEXT,
    earnings DECIMAL(10,4) DEFAULT 0,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_referrals_referrer ON public.referrals(referrer_id);

-- Withdrawals Table
CREATE TABLE IF NOT EXISTS public.withdrawals (
    id BIGSERIAL PRIMARY KEY,
    user_id TEXT NOT NULL REFERENCES public.users(uid) ON DELETE CASCADE,
    amount DECIMAL(12,4) NOT NULL,
    method TEXT DEFAULT 'binance_pay',
    binance_id TEXT,
    binance_email TEXT,
    status TEXT DEFAULT 'pending',
    approved_at TIMESTAMPTZ,
    rejected_at TIMESTAMPTZ,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_withdrawals_user ON public.withdrawals(user_id);
CREATE INDEX IF NOT EXISTS idx_withdrawals_status ON public.withdrawals(status);

-- Transactions Table
CREATE TABLE IF NOT EXISTS public.transactions (
    id BIGSERIAL PRIMARY KEY,
    user_id TEXT NOT NULL REFERENCES public.users(uid) ON DELETE CASCADE,
    amount DECIMAL(12,4) NOT NULL,
    type TEXT NOT NULL,
    description TEXT,
    status TEXT DEFAULT 'completed',
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_transactions_user ON public.transactions(user_id);

-- Activation Logs
CREATE TABLE IF NOT EXISTS public.activation_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id TEXT NOT NULL REFERENCES public.users(uid) ON DELETE CASCADE,
    key TEXT NOT NULL,
    result TEXT,
    ip TEXT,
    device TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_activation_logs_user ON public.activation_logs(user_id);

-- Plans Table
CREATE TABLE IF NOT EXISTS public.plans (
    id BIGSERIAL PRIMARY KEY,
    name TEXT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    daily_earnings DECIMAL(10,2),
    features JSONB DEFAULT '[]',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Settings Table
CREATE TABLE IF NOT EXISTS public.settings (
    id BIGSERIAL PRIMARY KEY,
    key TEXT UNIQUE NOT NULL,
    value TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW(),
    updated_at TIMESTAMPTZ DEFAULT NOW()
);

-- Insert default settings
INSERT INTO public.settings (key, value) VALUES
    ('site_name', 'EARNNOVA'),
    ('min_withdrawal', '1.00'),
    ('referral_bonus', '0.10'),
    ('daily_ad_limit', '20'),
    ('ad_cooldown', '30'),
    ('default_ad_reward', '0.01'),
    ('activation_bonus', '0.05')
ON CONFLICT (key) DO NOTHING;

-- Device Logs for fraud detection
CREATE TABLE IF NOT EXISTS public.device_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id TEXT REFERENCES public.users(uid) ON DELETE CASCADE,
    fingerprint TEXT,
    ip_address TEXT,
    user_agent TEXT,
    action TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_device_logs_user ON public.device_logs(user_id);

-- Activity Logs
CREATE TABLE IF NOT EXISTS public.activity_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id TEXT,
    action TEXT,
    details TEXT,
    ip_address TEXT,
    user_agent TEXT,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

CREATE INDEX IF NOT EXISTS idx_activity_logs_user ON public.activity_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_activity_logs_created ON public.activity_logs(created_at);

-- Row Level Security (RLS)
ALTER TABLE public.users ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.ad_rewards ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.referrals ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.withdrawals ENABLE ROW LEVEL SECURITY;
ALTER TABLE public.transactions ENABLE ROW LEVEL SECURITY;

-- RLS Policies
CREATE POLICY "Users can view own data" ON public.users
    FOR SELECT USING (auth.uid()::text = uid);

CREATE POLICY "Users can update own data" ON public.users
    FOR UPDATE USING (auth.uid()::text = uid);

CREATE POLICY "Anyone can insert users" ON public.users
    FOR INSERT WITH CHECK (true);

CREATE POLICY "Users can view own ad rewards" ON public.ad_rewards
    FOR SELECT USING (auth.uid()::text = user_id);

CREATE POLICY "Users can view own referrals" ON public.referrals
    FOR SELECT USING (auth.uid()::text = referrer_id);

CREATE POLICY "Users can view own withdrawals" ON public.withdrawals
    FOR SELECT USING (auth.uid()::text = user_id);

CREATE POLICY "Users can view own transactions" ON public.transactions
    FOR SELECT USING (auth.uid()::text = user_id);

-- Functions
CREATE OR REPLACE FUNCTION public.get_user_stats(p_uid TEXT)
RETURNS TABLE (
    total_earnings DECIMAL,
    total_withdrawals DECIMAL,
    referral_count BIGINT,
    ads_watched BIGINT
) LANGUAGE plpgsql AS $$
BEGIN
    RETURN QUERY
    SELECT
        COALESCE((SELECT SUM(amount) FROM public.transactions WHERE user_id = p_uid AND type = 'ad_reward'), 0),
        COALESCE((SELECT SUM(amount) FROM public.withdrawals WHERE user_id = p_uid AND status = 'approved'), 0),
        (SELECT COUNT(*) FROM public.referrals WHERE referrer_id = p_uid),
        (SELECT COUNT(*) FROM public.ad_rewards WHERE user_id = p_uid);
END;
$$;
