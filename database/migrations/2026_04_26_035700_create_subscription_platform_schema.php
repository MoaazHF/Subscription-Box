<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS "pgcrypto"');

        DB::unprepared(<<<'SQL'
CREATE TABLE IF NOT EXISTS roles (
    id SMALLSERIAL PRIMARY KEY,
    name VARCHAR(30) UNIQUE NOT NULL
);

INSERT INTO roles (name) VALUES
    ('subscriber'),
    ('warehouse_staff'),
    ('driver'),
    ('admin')
ON CONFLICT (name) DO NOTHING;

DO $$
DECLARE
    subscriber_role_id SMALLINT;
BEGIN
    SELECT id INTO subscriber_role_id
    FROM roles
    WHERE name = 'subscriber'
    LIMIT 1;

    ALTER TABLE users
        ADD COLUMN IF NOT EXISTS role_id SMALLINT;

    UPDATE users
    SET role_id = subscriber_role_id
    WHERE role_id IS NULL;

    EXECUTE format(
        'ALTER TABLE users ALTER COLUMN role_id SET DEFAULT %s',
        subscriber_role_id
    );

    ALTER TABLE users
        ALTER COLUMN role_id SET NOT NULL;

    IF NOT EXISTS (
        SELECT 1
        FROM pg_constraint
        WHERE conname = 'users_role_id_foreign'
    ) THEN
        ALTER TABLE users
            ADD CONSTRAINT users_role_id_foreign
            FOREIGN KEY (role_id) REFERENCES roles(id);
    END IF;
END $$;

CREATE INDEX IF NOT EXISTS idx_users_role ON users(role_id);

CREATE TABLE IF NOT EXISTS delivery_zones (
    id SMALLSERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    region VARCHAR(100),
    country CHAR(2) NOT NULL,
    is_serviceable BOOLEAN DEFAULT TRUE
);

CREATE TABLE IF NOT EXISTS subscription_plans (
    id SMALLSERIAL PRIMARY KEY,
    name VARCHAR(20) UNIQUE NOT NULL,
    price_monthly NUMERIC(8,2) NOT NULL,
    max_items SMALLINT NOT NULL,
    max_weight_g INTEGER NOT NULL,
    features JSONB,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_subscription_plan_price CHECK (price_monthly >= 0),
    CONSTRAINT chk_subscription_plan_max_items CHECK (max_items > 0),
    CONSTRAINT chk_subscription_plan_max_weight CHECK (max_weight_g > 0)
);

INSERT INTO subscription_plans (name, price_monthly, max_items, max_weight_g, features)
VALUES
    ('basic', 9.99, 3, 1000, '{"swaps":0,"addons":false,"priority_support":false}'::jsonb),
    ('standard', 19.99, 5, 2000, '{"swaps":2,"addons":true,"priority_support":false}'::jsonb),
    ('premium', 34.99, 8, 3500, '{"swaps":5,"addons":true,"priority_support":true}'::jsonb)
ON CONFLICT (name) DO NOTHING;

CREATE TABLE IF NOT EXISTS addresses (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL,
    delivery_zone_id SMALLINT,
    street VARCHAR(255) NOT NULL,
    city VARCHAR(100) NOT NULL,
    region VARCHAR(100),
    country CHAR(2) NOT NULL,
    postal_code VARCHAR(20),
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_addr_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_addr_zone FOREIGN KEY (delivery_zone_id) REFERENCES delivery_zones(id)
);

CREATE TABLE IF NOT EXISTS subscriptions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL,
    plan_id SMALLINT NOT NULL,
    address_id UUID,
    status VARCHAR(20) NOT NULL DEFAULT 'active',
    start_date DATE NOT NULL,
    next_billing_date DATE,
    remaining_billing_days SMALLINT DEFAULT 0,
    auto_renew BOOLEAN DEFAULT TRUE,
    eco_shipping BOOLEAN DEFAULT FALSE,
    loyalty_points INTEGER DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_subscription_status CHECK (status IN ('active', 'paused', 'cancelled', 'suspended', 'gift')),
    CONSTRAINT chk_subscription_remaining_days CHECK (remaining_billing_days >= 0),
    CONSTRAINT chk_subscription_loyalty_points CHECK (loyalty_points >= 0),
    CONSTRAINT fk_sub_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_sub_plan FOREIGN KEY (plan_id) REFERENCES subscription_plans(id),
    CONSTRAINT fk_sub_address FOREIGN KEY (address_id) REFERENCES addresses(id)
);

CREATE TABLE IF NOT EXISTS payments (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    subscription_id UUID NOT NULL,
    amount NUMERIC(10,2) NOT NULL,
    currency CHAR(3) DEFAULT 'USD',
    tax_amount NUMERIC(8,2) DEFAULT 0.00,
    status VARCHAR(20) NOT NULL,
    gateway_ref VARCHAR(100),
    gateway_reason_code VARCHAR(50),
    retry_count SMALLINT DEFAULT 0,
    next_retry_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_payment_amount CHECK (amount >= 0),
    CONSTRAINT chk_payment_tax_amount CHECK (tax_amount >= 0),
    CONSTRAINT chk_payment_retry_count CHECK (retry_count >= 0),
    CONSTRAINT chk_payment_status CHECK (status IN ('pending', 'success', 'failed', 'suspended', 'refunded')),
    CONSTRAINT fk_pay_sub FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS allergen_tags (
    id SMALLSERIAL PRIMARY KEY,
    name VARCHAR(50) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS user_allergens (
    user_id UUID NOT NULL,
    allergen_tag_id SMALLINT NOT NULL,
    PRIMARY KEY (user_id, allergen_tag_id),
    CONSTRAINT fk_ua_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    CONSTRAINT fk_ua_allergen FOREIGN KEY (allergen_tag_id) REFERENCES allergen_tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS items (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(150) NOT NULL,
    description TEXT,
    weight_g INTEGER NOT NULL,
    size_category VARCHAR(20) NOT NULL DEFAULT 'medium',
    unit_price NUMERIC(8,2) NOT NULL,
    stock_qty INTEGER NOT NULL DEFAULT 0,
    is_limited_edition BOOLEAN DEFAULT FALSE,
    limited_stock INTEGER,
    supplier VARCHAR(100),
    origin_country CHAR(2),
    sourcing_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_item_weight CHECK (weight_g > 0),
    CONSTRAINT chk_item_price CHECK (unit_price >= 0),
    CONSTRAINT chk_item_stock CHECK (stock_qty >= 0),
    CONSTRAINT chk_item_limited_stock CHECK (limited_stock IS NULL OR limited_stock >= 0),
    CONSTRAINT chk_item_size CHECK (size_category IN ('small', 'medium', 'large'))
);

CREATE TABLE IF NOT EXISTS item_allergens (
    item_id UUID NOT NULL,
    allergen_tag_id SMALLINT NOT NULL,
    PRIMARY KEY (item_id, allergen_tag_id),
    CONSTRAINT fk_ia_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
    CONSTRAINT fk_ia_allergen FOREIGN KEY (allergen_tag_id) REFERENCES allergen_tags(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS boxes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    subscription_id UUID NOT NULL,
    period_month SMALLINT NOT NULL,
    period_year SMALLINT NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'open',
    lock_date DATE NOT NULL,
    theme VARCHAR(100),
    total_weight_g INTEGER DEFAULT 0,
    shipping_tier VARCHAR(20) DEFAULT 'standard',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (subscription_id, period_month, period_year),
    CONSTRAINT chk_box_period_month CHECK (period_month BETWEEN 1 AND 12),
    CONSTRAINT chk_box_total_weight CHECK (total_weight_g >= 0),
    CONSTRAINT chk_box_status CHECK (status IN ('open', 'locked', 'picking', 'packed', 'shipped', 'delivered')),
    CONSTRAINT chk_box_shipping_tier CHECK (shipping_tier IN ('standard', 'heavy', 'oversized')),
    CONSTRAINT fk_box_sub FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS box_customisations (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    box_id UUID UNIQUE NOT NULL,
    swap_allowed BOOLEAN DEFAULT TRUE,
    theme_preference VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bc_box FOREIGN KEY (box_id) REFERENCES boxes(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS box_items (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    box_id UUID NOT NULL,
    item_id UUID NOT NULL,
    quantity SMALLINT NOT NULL DEFAULT 1,
    is_addon BOOLEAN DEFAULT FALSE,
    is_swapped BOOLEAN DEFAULT FALSE,
    is_surprise BOOLEAN DEFAULT FALSE,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (box_id, item_id),
    CONSTRAINT chk_box_item_quantity CHECK (quantity > 0),
    CONSTRAINT fk_bi_box FOREIGN KEY (box_id) REFERENCES boxes(id) ON DELETE CASCADE,
    CONSTRAINT fk_bi_item FOREIGN KEY (item_id) REFERENCES items(id)
);

CREATE TABLE IF NOT EXISTS drivers (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID UNIQUE NOT NULL,
    vehicle_number VARCHAR(20),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_drv_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS warehouse_staff (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID UNIQUE NOT NULL,
    warehouse_location VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ws_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS deliveries (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    box_id UUID UNIQUE NOT NULL,
    driver_id UUID,
    address_id UUID NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    tracking_number VARCHAR(100),
    estimated_delivery DATE,
    actual_delivery TIMESTAMP NULL,
    delivery_instructions TEXT,
    stops_remaining SMALLINT,
    eco_dispatch BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_delivery_status CHECK (status IN ('pending', 'picking', 'packed', 'shipped', 'out_for_delivery', 'delivered', 'undeliverable')),
    CONSTRAINT chk_delivery_stops_remaining CHECK (stops_remaining IS NULL OR stops_remaining >= 0),
    CONSTRAINT fk_del_box FOREIGN KEY (box_id) REFERENCES boxes(id) ON DELETE CASCADE,
    CONSTRAINT fk_del_driver FOREIGN KEY (driver_id) REFERENCES drivers(id),
    CONSTRAINT fk_del_address FOREIGN KEY (address_id) REFERENCES addresses(id)
);

CREATE TABLE IF NOT EXISTS claims (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    subscription_id UUID NOT NULL,
    delivery_id UUID NOT NULL,
    item_id UUID,
    type VARCHAR(20) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    photo_url VARCHAR(500),
    description TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_at TIMESTAMP NULL,
    resolved_by UUID,
    CONSTRAINT chk_claim_type CHECK (type IN ('damaged', 'missing')),
    CONSTRAINT chk_claim_status CHECK (status IN ('pending', 'approved', 'rejected', 'escalated')),
    CONSTRAINT fk_cl_sub FOREIGN KEY (subscription_id) REFERENCES subscriptions(id),
    CONSTRAINT fk_cl_delivery FOREIGN KEY (delivery_id) REFERENCES deliveries(id),
    CONSTRAINT fk_cl_item FOREIGN KEY (item_id) REFERENCES items(id),
    CONSTRAINT fk_cl_admin FOREIGN KEY (resolved_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS referrals (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    referrer_id UUID NOT NULL,
    referee_id UUID,
    referral_code VARCHAR(20) UNIQUE NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    reward_applied BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    confirmed_at TIMESTAMP NULL,
    CONSTRAINT chk_referral_status CHECK (status IN ('pending', 'confirmed', 'rejected')),
    CONSTRAINT fk_ref_referrer FOREIGN KEY (referrer_id) REFERENCES users(id),
    CONSTRAINT fk_ref_referee FOREIGN KEY (referee_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS rewards (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL,
    type VARCHAR(30) NOT NULL,
    amount NUMERIC(8,2),
    points INTEGER,
    description VARCHAR(255),
    is_applied BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    applied_at TIMESTAMP NULL,
    CONSTRAINT chk_reward_type CHECK (type IN ('account_credit', 'free_box', 'loyalty_points', 'anniversary_item')),
    CONSTRAINT chk_reward_amount CHECK (amount IS NULL OR amount >= 0),
    CONSTRAINT chk_reward_points CHECK (points IS NULL OR points >= 0),
    CONSTRAINT fk_rew_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS promo_codes (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    code VARCHAR(30) UNIQUE NOT NULL,
    discount_type VARCHAR(20) NOT NULL,
    discount_value NUMERIC(8,2) NOT NULL,
    max_uses INTEGER,
    used_count INTEGER DEFAULT 0,
    expires_at TIMESTAMP NULL,
    created_by UUID,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_discount_type CHECK (discount_type IN ('percent', 'fixed')),
    CONSTRAINT chk_discount_value CHECK (discount_value >= 0),
    CONSTRAINT chk_percent_value CHECK (discount_type <> 'percent' OR discount_value <= 100),
    CONSTRAINT chk_max_uses CHECK (max_uses IS NULL OR max_uses >= 0),
    CONSTRAINT chk_used_count CHECK (used_count >= 0),
    CONSTRAINT fk_pc_creator FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS promo_code_usages (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    promo_code_id UUID NOT NULL,
    user_id UUID NOT NULL,
    used_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (promo_code_id, user_id),
    CONSTRAINT fk_pcu_code FOREIGN KEY (promo_code_id) REFERENCES promo_codes(id) ON DELETE CASCADE,
    CONSTRAINT fk_pcu_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS gift_subscriptions (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    purchaser_id UUID NOT NULL,
    recipient_user_id UUID,
    recipient_email VARCHAR(255) NOT NULL,
    recipient_name VARCHAR(100),
    plan_id SMALLINT NOT NULL,
    duration_months SMALLINT NOT NULL,
    activation_code VARCHAR(64) UNIQUE NOT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending_payment',
    subscription_id UUID,
    personal_message TEXT,
    purchased_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activated_at TIMESTAMP NULL,
    scheduled_send_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    CONSTRAINT chk_gift_duration CHECK (duration_months > 0),
    CONSTRAINT chk_gift_status CHECK (status IN ('pending_payment', 'pending_activation', 'active', 'expired')),
    CONSTRAINT fk_gs_purchaser FOREIGN KEY (purchaser_id) REFERENCES users(id),
    CONSTRAINT fk_gs_recipient FOREIGN KEY (recipient_user_id) REFERENCES users(id),
    CONSTRAINT fk_gs_plan FOREIGN KEY (plan_id) REFERENCES subscription_plans(id),
    CONSTRAINT fk_gs_sub FOREIGN KEY (subscription_id) REFERENCES subscriptions(id)
);

CREATE TABLE IF NOT EXISTS social_posts (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL,
    box_id UUID NOT NULL,
    caption TEXT,
    photo_url VARCHAR(500),
    visibility VARCHAR(20) NOT NULL DEFAULT 'public',
    loyalty_points_awarded INTEGER DEFAULT 0,
    is_deleted BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_post_visibility CHECK (visibility IN ('public', 'private')),
    CONSTRAINT chk_social_loyalty_points CHECK (loyalty_points_awarded >= 0),
    CONSTRAINT fk_sp_user FOREIGN KEY (user_id) REFERENCES users(id),
    CONSTRAINT fk_sp_box FOREIGN KEY (box_id) REFERENCES boxes(id)
);

CREATE TABLE IF NOT EXISTS notifications (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    user_id UUID NOT NULL,
    type VARCHAR(20) NOT NULL,
    subject VARCHAR(255),
    body TEXT,
    status VARCHAR(20) DEFAULT 'queued',
    sent_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_notification_type CHECK (type IN ('email', 'sms', 'push')),
    CONSTRAINT chk_notification_status CHECK (status IN ('queued', 'sent', 'failed')),
    CONSTRAINT fk_notif_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS audit_logs (
    id BIGSERIAL PRIMARY KEY,
    user_id UUID,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id UUID,
    ip_address VARCHAR(45),
    metadata JSONB,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_al_user FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS flash_sales (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    name VARCHAR(150) NOT NULL,
    plan_id SMALLINT,
    discount_percent SMALLINT NOT NULL,
    stock_limit INTEGER,
    claimed_count INTEGER DEFAULT 0,
    start_at TIMESTAMP NOT NULL,
    end_at TIMESTAMP NOT NULL,
    created_by UUID NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_fs_discount_percent CHECK (discount_percent BETWEEN 1 AND 100),
    CONSTRAINT chk_fs_stock_limit CHECK (stock_limit IS NULL OR stock_limit >= 0),
    CONSTRAINT chk_fs_claimed_count CHECK (claimed_count >= 0),
    CONSTRAINT chk_fs_dates CHECK (end_at > start_at),
    CONSTRAINT fk_fs_plan FOREIGN KEY (plan_id) REFERENCES subscription_plans(id),
    CONSTRAINT fk_fs_creator FOREIGN KEY (created_by) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS retention_offers (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    subscription_id UUID NOT NULL,
    offer_type VARCHAR(30) NOT NULL,
    offer_value VARCHAR(100) NOT NULL,
    cancellation_reason VARCHAR(255),
    presented_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    accepted BOOLEAN DEFAULT FALSE,
    accepted_at TIMESTAMP NULL,
    CONSTRAINT chk_retention_offer_type CHECK (offer_type IN ('discount', 'frequency_change', 'plan_downgrade')),
    CONSTRAINT fk_ro_sub FOREIGN KEY (subscription_id) REFERENCES subscriptions(id) ON DELETE CASCADE
);

CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_users_updated_at ON users;
CREATE TRIGGER trg_users_updated_at BEFORE UPDATE ON users
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_addresses_updated_at ON addresses;
CREATE TRIGGER trg_addresses_updated_at BEFORE UPDATE ON addresses
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_subscription_plans_updated_at ON subscription_plans;
CREATE TRIGGER trg_subscription_plans_updated_at BEFORE UPDATE ON subscription_plans
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_subscriptions_updated_at ON subscriptions;
CREATE TRIGGER trg_subscriptions_updated_at BEFORE UPDATE ON subscriptions
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_payments_updated_at ON payments;
CREATE TRIGGER trg_payments_updated_at BEFORE UPDATE ON payments
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_items_updated_at ON items;
CREATE TRIGGER trg_items_updated_at BEFORE UPDATE ON items
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_boxes_updated_at ON boxes;
CREATE TRIGGER trg_boxes_updated_at BEFORE UPDATE ON boxes
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_box_customisations_updated_at ON box_customisations;
CREATE TRIGGER trg_box_customisations_updated_at BEFORE UPDATE ON box_customisations
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_drivers_updated_at ON drivers;
CREATE TRIGGER trg_drivers_updated_at BEFORE UPDATE ON drivers
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_warehouse_staff_updated_at ON warehouse_staff;
CREATE TRIGGER trg_warehouse_staff_updated_at BEFORE UPDATE ON warehouse_staff
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_deliveries_updated_at ON deliveries;
CREATE TRIGGER trg_deliveries_updated_at BEFORE UPDATE ON deliveries
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_promo_codes_updated_at ON promo_codes;
CREATE TRIGGER trg_promo_codes_updated_at BEFORE UPDATE ON promo_codes
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_social_posts_updated_at ON social_posts;
CREATE TRIGGER trg_social_posts_updated_at BEFORE UPDATE ON social_posts
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_notifications_updated_at ON notifications;
CREATE TRIGGER trg_notifications_updated_at BEFORE UPDATE ON notifications
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

DROP TRIGGER IF EXISTS trg_flash_sales_updated_at ON flash_sales;
CREATE TRIGGER trg_flash_sales_updated_at BEFORE UPDATE ON flash_sales
FOR EACH ROW EXECUTE FUNCTION set_updated_at();

CREATE INDEX IF NOT EXISTS idx_addresses_user ON addresses(user_id);
CREATE INDEX IF NOT EXISTS idx_addresses_zone ON addresses(delivery_zone_id);
CREATE INDEX IF NOT EXISTS idx_sub_user ON subscriptions(user_id);
CREATE INDEX IF NOT EXISTS idx_sub_status ON subscriptions(status);
CREATE INDEX IF NOT EXISTS idx_sub_billing ON subscriptions(next_billing_date);
CREATE INDEX IF NOT EXISTS idx_pay_sub ON payments(subscription_id);
CREATE INDEX IF NOT EXISTS idx_pay_status ON payments(status);
CREATE INDEX IF NOT EXISTS idx_pay_retry_failed ON payments(next_retry_at) WHERE status = 'failed';
CREATE INDEX IF NOT EXISTS idx_item_name ON items(name);
CREATE INDEX IF NOT EXISTS idx_item_stock ON items(stock_qty);
CREATE INDEX IF NOT EXISTS idx_box_sub ON boxes(subscription_id);
CREATE INDEX IF NOT EXISTS idx_box_period ON boxes(period_year, period_month);
CREATE INDEX IF NOT EXISTS idx_box_status ON boxes(status);
CREATE INDEX IF NOT EXISTS idx_del_status ON deliveries(status);
CREATE INDEX IF NOT EXISTS idx_del_updated ON deliveries(updated_at);
CREATE INDEX IF NOT EXISTS idx_del_driver ON deliveries(driver_id);
CREATE INDEX IF NOT EXISTS idx_del_tracking ON deliveries(tracking_number);
CREATE INDEX IF NOT EXISTS idx_claim_sub ON claims(subscription_id);
CREATE INDEX IF NOT EXISTS idx_claim_delivery ON claims(delivery_id);
CREATE INDEX IF NOT EXISTS idx_claim_status ON claims(status);
CREATE INDEX IF NOT EXISTS idx_al_user ON audit_logs(user_id);
CREATE INDEX IF NOT EXISTS idx_al_entity ON audit_logs(entity_type, entity_id);
CREATE INDEX IF NOT EXISTS idx_al_created ON audit_logs(created_at);
CREATE INDEX IF NOT EXISTS idx_ref_code ON referrals(referral_code);
CREATE INDEX IF NOT EXISTS idx_ref_referrer ON referrals(referrer_id);
CREATE INDEX IF NOT EXISTS idx_gs_code ON gift_subscriptions(activation_code);
CREATE INDEX IF NOT EXISTS idx_gs_purchaser ON gift_subscriptions(purchaser_id);
CREATE INDEX IF NOT EXISTS idx_sp_public ON social_posts(visibility, created_at);
CREATE INDEX IF NOT EXISTS idx_notifications_user ON notifications(user_id);
CREATE INDEX IF NOT EXISTS idx_notifications_status ON notifications(status);
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        DB::unprepared(<<<'SQL'
DROP TRIGGER IF EXISTS trg_flash_sales_updated_at ON flash_sales;
DROP TRIGGER IF EXISTS trg_notifications_updated_at ON notifications;
DROP TRIGGER IF EXISTS trg_social_posts_updated_at ON social_posts;
DROP TRIGGER IF EXISTS trg_promo_codes_updated_at ON promo_codes;
DROP TRIGGER IF EXISTS trg_deliveries_updated_at ON deliveries;
DROP TRIGGER IF EXISTS trg_warehouse_staff_updated_at ON warehouse_staff;
DROP TRIGGER IF EXISTS trg_drivers_updated_at ON drivers;
DROP TRIGGER IF EXISTS trg_box_customisations_updated_at ON box_customisations;
DROP TRIGGER IF EXISTS trg_boxes_updated_at ON boxes;
DROP TRIGGER IF EXISTS trg_items_updated_at ON items;
DROP TRIGGER IF EXISTS trg_payments_updated_at ON payments;
DROP TRIGGER IF EXISTS trg_subscriptions_updated_at ON subscriptions;
DROP TRIGGER IF EXISTS trg_subscription_plans_updated_at ON subscription_plans;
DROP TRIGGER IF EXISTS trg_addresses_updated_at ON addresses;
DROP TRIGGER IF EXISTS trg_users_updated_at ON users;

DROP FUNCTION IF EXISTS set_updated_at();

DROP TABLE IF EXISTS retention_offers;
DROP TABLE IF EXISTS flash_sales;
DROP TABLE IF EXISTS audit_logs;
DROP TABLE IF EXISTS notifications;
DROP TABLE IF EXISTS social_posts;
DROP TABLE IF EXISTS gift_subscriptions;
DROP TABLE IF EXISTS promo_code_usages;
DROP TABLE IF EXISTS promo_codes;
DROP TABLE IF EXISTS rewards;
DROP TABLE IF EXISTS referrals;
DROP TABLE IF EXISTS claims;
DROP TABLE IF EXISTS deliveries;
DROP TABLE IF EXISTS warehouse_staff;
DROP TABLE IF EXISTS drivers;
DROP TABLE IF EXISTS box_items;
DROP TABLE IF EXISTS box_customisations;
DROP TABLE IF EXISTS boxes;
DROP TABLE IF EXISTS item_allergens;
DROP TABLE IF EXISTS items;
DROP TABLE IF EXISTS user_allergens;
DROP TABLE IF EXISTS allergen_tags;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS subscriptions;
DROP TABLE IF EXISTS addresses;
DROP TABLE IF EXISTS subscription_plans;
DROP TABLE IF EXISTS delivery_zones;
ALTER TABLE users DROP CONSTRAINT IF EXISTS users_role_id_foreign;
ALTER TABLE users DROP COLUMN IF EXISTS role_id;
DROP TABLE IF EXISTS roles;
SQL);
    }
};
