-- ============================================
-- EARNNOVA v2.0 - Gamification Schema
-- ============================================

-- User Ranks Configuration
CREATE TABLE IF NOT EXISTS public.ranks (
    id BIGSERIAL PRIMARY KEY,
    name TEXT UNIQUE NOT NULL,
    level INTEGER UNIQUE NOT NULL,
    min_xp BIGINT DEFAULT 0,
    badge_color TEXT DEFAULT '#4361ee',
    daily_bonus_multiplier DECIMAL(3,2) DEFAULT 1.00,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Insert default ranks
INSERT INTO public.ranks (name, level, min_xp, badge_color, daily_bonus_multiplier) VALUES
    ('Bronze', 1, 0, '#cd7f32', 1.00),
    ('Silver', 2, 500, '#c0c0c0', 1.10),
    ('Gold', 3, 2000, '#ffd700', 1.25),
    ('Diamond', 4, 5000, '#b9f2ff', 1.50),
    ('Platinum', 5, 10000, '#e5e4e2', 1.75),
    ('Elite', 6, 25000, '#ff6b35', 2.00),
    ('Legend', 7, 50000, '#ffd700', 3.00)
ON CONFLICT (name) DO NOTHING;

-- User XP & Progression
ALTER TABLE public.users ADD COLUMN IF NOT EXISTS xp BIGINT DEFAULT 0;
ALTER TABLE public.users ADD COLUMN IF NOT EXISTS level INTEGER DEFAULT 1;
ALTER TABLE public.users ADD COLUMN IF NOT EXISTS rank_id INTEGER REFERENCES public.ranks(id);
ALTER TABLE public.users ADD COLUMN IF NOT EXISTS streak_days INTEGER DEFAULT 0;
ALTER TABLE public.users ADD COLUMN IF NOT EXISTS last_daily_date TIMESTAMPTZ;
ALTER TABLE public.users ADD COLUMN IF NOT EXISTS total_missions_completed INTEGER DEFAULT 0;
ALTER TABLE public.users ADD COLUMN IF NOT EXISTS achievements JSONB DEFAULT '[]';

-- Daily Missions
CREATE TABLE IF NOT EXISTS public.missions (
    id BIGSERIAL PRIMARY KEY,
    title TEXT NOT NULL,
    description TEXT,
    type TEXT NOT NULL, -- 'watch_ads', 'referrals', 'tasks', 'login', 'earnings'
    requirement INTEGER NOT NULL, -- how many to complete
    xp_reward INTEGER DEFAULT 50,
    coin_reward DECIMAL(10,4) DEFAULT 0.00,
    is_daily BOOLEAN DEFAULT TRUE,
    is_active BOOLEAN DEFAULT TRUE,
    icon TEXT DEFAULT '⭐',
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Default daily missions
INSERT INTO public.missions (title, description, type, requirement, xp_reward, coin_reward, icon) VALUES
    ('Ad Watcher', 'Watch 10 ads today', 'watch_ads', 10, 100, 0.05, '📺'),
    ('Super Viewer', 'Watch 20 ads today', 'watch_ads', 20, 250, 0.10, '👁️'),
    ('Social Butterfly', 'Refer 1 new user today', 'referrals', 1, 200, 0.10, '👥'),
    ('Referral Master', 'Refer 3 new users today', 'referrals', 3, 500, 0.25, '🌟'),
    ('Task Completer', 'Complete 3 tasks today', 'tasks', 3, 150, 0.08, '📋'),
    ('Earning Goal', 'Earn $0.50 today', 'earnings', 50, 300, 0.15, '💰'),
    ('Daily Login', 'Login to the platform', 'login', 1, 50, 0.02, '🔐')
ON CONFLICT DO NOTHING;

-- User Mission Progress
CREATE TABLE IF NOT EXISTS public.user_missions (
    id BIGSERIAL PRIMARY KEY,
    user_id TEXT NOT NULL REFERENCES public.users(uid) ON DELETE CASCADE,
    mission_id INTEGER NOT NULL REFERENCES public.missions(id) ON DELETE CASCADE,
    progress INTEGER DEFAULT 0,
    completed BOOLEAN DEFAULT FALSE,
    completed_at TIMESTAMPTZ,
    date_assigned DATE DEFAULT CURRENT_DATE,
    UNIQUE(user_id, mission_id, date_assigned)
);

-- Achievements
CREATE TABLE IF NOT EXISTS public.achievements (
    id BIGSERIAL PRIMARY KEY,
    name TEXT UNIQUE NOT NULL,
    description TEXT,
    icon TEXT DEFAULT '🏆',
    xp_reward INTEGER DEFAULT 100,
    coin_reward DECIMAL(10,4) DEFAULT 0.00,
    requirement_type TEXT, -- 'xp_total', 'referrals_total', 'ads_total', 'earnings_total', 'streak_days'
    requirement_value BIGINT,
    is_hidden BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Insert default achievements
INSERT INTO public.achievements (name, description, icon, xp_reward, coin_reward, requirement_type, requirement_value) VALUES
    ('First Steps', 'Watch your first ad', '👣', 50, 0.01, 'ads_total', 1),
    ('Getting Started', 'Watch 100 ads total', '🚀', 200, 0.10, 'ads_total', 100),
    ('Ad Enthusiast', 'Watch 1,000 ads total', '💎', 1000, 1.00, 'ads_total', 1000),
    ('Ad Master', 'Watch 10,000 ads total', '👑', 5000, 5.00, 'ads_total', 10000),
    ('Social Starter', 'Refer your first user', '🌟', 100, 0.05, 'referrals_total', 1),
    ('Network Builder', 'Refer 10 users', '🌐', 500, 0.50, 'referrals_total', 10),
    ('Influencer', 'Refer 100 users', '🔥', 2000, 2.00, 'referrals_total', 100),
    ('Earning Newbie', 'Earn your first $1', '💰', 50, 0.05, 'earnings_total', 100),
    ('Earning Pro', 'Earn $100 total', '💵', 1000, 1.00, 'earnings_total', 10000),
    ('Earning Whale', 'Earn $1,000 total', '🐳', 5000, 5.00, 'earnings_total', 100000),
    ('Loyal Member', '7-day login streak', '📅', 200, 0.20, 'streak_days', 7),
    ('Dedicated', '30-day login streak', '🔥', 1000, 1.00, 'streak_days', 30),
    ('Unstoppable', '100-day login streak', '💪', 5000, 5.00, 'streak_days', 100),
    ('XP Hunter', 'Earn 1,000 XP', '🏹', 200, 0.20, 'xp_total', 1000),
    ('XP Champion', 'Earn 10,000 XP', '⚡', 2000, 2.00, 'xp_total', 10000),
    ('XP Legend', 'Earn 100,000 XP', '🌟', 10000, 10.00, 'xp_total', 100000)
ON CONFLICT (name) DO NOTHING;

-- Spin Wheel Rewards
CREATE TABLE IF NOT EXISTS public.spin_wheel_rewards (
    id BIGSERIAL PRIMARY KEY,
    label TEXT NOT NULL,
    type TEXT NOT NULL, -- 'xp', 'coins', 'bonus', 'free_spin'
    value DECIMAL(10,4) NOT NULL,
    probability DECIMAL(5,2) NOT NULL, -- percentage 0-100
    color TEXT DEFAULT '#4361ee',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMPTZ DEFAULT NOW()
);

INSERT INTO public.spin_wheel_rewards (label, type, value, probability, color) VALUES
    ('10 XP', 'xp', 10, 25.00, '#4361ee'),
    ('25 XP', 'xp', 25, 20.00, '#7209b7'),
    ('50 XP', 'xp', 50, 15.00, '#00b4d8'),
    ('$0.01', 'coins', 0.01, 15.00, '#06d6a0'),
    ('$0.05', 'coins', 0.05, 10.00, '#ffd166'),
    ('$0.10', 'coins', 0.10, 5.00, '#ef476f'),
    ('100 XP', 'xp', 100, 4.00, '#e5e4e2'),
    ('$0.25', 'coins', 0.25, 3.00, '#ffd700'),
    ('Free Spin', 'free_spin', 1, 2.00, '#118ab2'),
    ('$1.00 JACKPOT!', 'coins', 1.00, 1.00, '#ff6b35')
ON CONFLICT DO NOTHING;

-- User Spin History
CREATE TABLE IF NOT EXISTS public.spin_history (
    id BIGSERIAL PRIMARY KEY,
    user_id TEXT NOT NULL REFERENCES public.users(uid) ON DELETE CASCADE,
    reward_id INTEGER REFERENCES public.spin_wheel_rewards(id),
    reward_label TEXT,
    reward_type TEXT,
    reward_value DECIMAL(10,4),
    created_at TIMESTAMPTZ DEFAULT NOW()
);

-- Daily Rewards (Calendar)
CREATE TABLE IF NOT EXISTS public.daily_rewards (
    id BIGSERIAL PRIMARY KEY,
    day_number INTEGER NOT NULL,
    xp_reward INTEGER DEFAULT 50,
    coin_reward DECIMAL(10,4) DEFAULT 0.00,
    icon TEXT DEFAULT '🎁',
    UNIQUE(day_number)
);

INSERT INTO public.daily_rewards (day_number, xp_reward, coin_reward, icon) VALUES
    (1, 50, 0.01, '🎁'),
    (2, 75, 0.02, '🎁'),
    (3, 100, 0.03, '🎁'),
    (4, 150, 0.05, '🎁'),
    (5, 200, 0.08, '🎁'),
    (6, 250, 0.10, '🎁'),
    (7, 500, 0.25, '🌟')
ON CONFLICT (day_number) DO NOTHING;

-- User Daily Claim Log
CREATE TABLE IF NOT EXISTS public.daily_claims (
    id BIGSERIAL PRIMARY KEY,
    user_id TEXT NOT NULL REFERENCES public.users(uid) ON DELETE CASCADE,
    day_number INTEGER NOT NULL,
    xp_earned INTEGER DEFAULT 0,
    coins_earned DECIMAL(10,4) DEFAULT 0,
    claim_date DATE DEFAULT CURRENT_DATE,
    UNIQUE(user_id, day_number, claim_date)
);

-- Add XP tracking fields to activity
ALTER TABLE public.activity_logs ADD COLUMN IF NOT EXISTS xp_earned INTEGER DEFAULT 0;
ALTER TABLE public.activity_logs ADD COLUMN IF NOT EXISTS metadata JSONB DEFAULT '{}';

-- Indexes for performance
CREATE INDEX IF NOT EXISTS idx_user_missions_user ON public.user_missions(user_id);
CREATE INDEX IF NOT EXISTS idx_user_missions_date ON public.user_missions(date_assigned);
CREATE INDEX IF NOT EXISTS idx_spin_history_user ON public.spin_history(user_id);
CREATE INDEX IF NOT EXISTS idx_daily_claims_user ON public.daily_claims(user_id);
CREATE INDEX IF NOT EXISTS idx_users_xp ON public.users(xp DESC);
CREATE INDEX IF NOT EXISTS idx_users_level ON public.users(level DESC);
