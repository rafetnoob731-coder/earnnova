# EARNNOVA — Complete Visual Design Specification v4.0

> **Last Updated:** 2026-06-09
> **Status:** Final Design Document
> **Scope:** Full-stack earning ecosystem — Premium Fintech UI/UX, PWA, Admin Panel

---

## Table of Contents
1. [Homepage](#01-homepage)
2. [Login Page](#02-login-page)
3. [Registration Page](#03-registration-page)
4. [Dashboard](#04-dashboard)
5. [Watch Ads Page](#05-watch-ads-page)
6. [Missions Page](#06-missions-page)
7. [Referral Page](#07-referral-page)
8. [Withdrawal Page](#08-withdrawal-page)
9. [Analytics Page](#09-analytics-page)
10. [Plans Page](#10-plans-page)
11. [Profile Page](#11-profile-page)
12. [Notifications Center](#12-notifications-center)
13. [Settings Page](#13-settings-page)
14. [Admin Dashboard](#14-admin-dashboard)
15. [Mobile Navigation System](#15-mobile-navigation-system)
16. [Visual Enhancements](#-visual-enhancements-across-all-pages)

---

## 01. HOMEPAGE

### Hero Section
- Full-screen aurora background (cyan → violet → coral gradient, slow 20s animation)
- 3D floating logo (rotates slightly on mouse move, parallax depth)
- Headline: "Earn Smarter. Grow Faster." (72px desktop, gradient text: cyan to violet)
- Subheadline: "Join 1M+ users earning crypto with ads, referrals & missions" (20px, white/80%)
- Dual CTA buttons:
  - **Primary:** "Get Started" — cyan gradient, pill shape, pulse animation, icon: rocket
  - **Secondary:** "Watch Demo" — glass bordered, hover: solid gradient
- Trust bar: "Trustpilot 4.8 ★ | 1M+ Downloads | $2.5M+ Paid Out" (animated counters)

### How It Works Section (3 steps)
- Step cards: Glassmorphic, numbered 01-02-03, hover: lift + glow
  - 01: Watch Ads → Earn instantly
  - 02: Refer Friends → Get 10% forever
  - 03: Complete Missions → Unlock bonuses
- Connecting lines: Animated dashed lines between steps

### Live Earnings Ticker
- Marquee component: "• John just earned $0.50 • Sarah withdrew $200 • Alex referred 3 friends •" (real-time feed)

### Stats Counter Row (3 columns)
- $2.5M+ — Total Paid (count-up animation)
- 1,247 — Active Now (live counter)
- 47s — Avg. Withdrawal Time (real-time)

### Testimonials (horizontal scroll)
- Glass cards: Avatar + username + earnings screenshot + rating stars
- Auto-scroll: Infinite loop, pauses on hover
- CTA: "Join 50,000+ happy earners" button

### Footer
- Logo + tagline: "The future of earning"
- Quick links: Platform, Resources, Support, Legal
- Social icons: Telegram, Twitter, Discord, Instagram (hover: scale + color transition)
- App store badges: Glassmorphic, Google Play + App Store
- Newsletter signup: Email field + "Notify Me" button

---

## 02. LOGIN PAGE

### Background
- Animated particle network (floating dots connected by lines, interactive on mouse move)
- Gradient overlay: Dark blue to deep purple (opacity 0.8)

### Login Card (centered, 480px max-width)
- Glassmorphism: Blur 20px, border 1px white/10%, shadow 0 25px 50px -12px black/25%
- Logo (32px) + "Welcome Back" (gradient text, 28px)
- Email/Phone field: Icon, floating label, real-time validation
- Password field: Eye toggle, forgot password link
- "Remember me for 30 days" checkbox
- Login button: Full width, gradient cyan→violet, ripple, loading spinner
- Divider: "Or continue with" (with lines)
- Social buttons: Google (white), Apple (dark), Telegram (blue)
- Register link: "New to EARNNOVA? Create account" + arrow
- Security note: "🔒 Secured by SSL • 2FA available"

---

## 03. REGISTRATION PAGE

### Step Progress Indicator
- 3 circles with connecting line: Account (1) → Verify (2) → Complete (3)
- Active: Gradient circle + pulse; Completed: Green checkmark

### Step 1: Account Setup
- Full name (real-time validation, auto-capitalize)
- Email (format + availability check, debounced)
- Phone (country code dropdown with flags)
- Referral code (auto-filled from URL, optional)
- Password (strength meter 0-100%, red→yellow→green)
- Confirm password (match indicator)
- Terms checkbox (required, links to modal)
- Continue button (gradient, enabled only when valid)

### Step 2: Verification
- Title: "Verify your identity"
- OTP input: 6 boxes, auto-focus next, auto-submit
- Resend code: Disabled for 60s with countdown
- Method toggle: SMS / Email (segmented control)
- Back button, Verify button (gradient loading spinner)

### Step 3: Welcome
- Animated confetti (canvas, bursts on load)
- Success icon: Large green checkmark (draw animation)
- Welcome message: "Welcome to EARNNOVA, Alex!"
- Bonus card: "$5 Welcome Bonus Claimed!" (glass card)
- Avatar selection: 6 presets (circular, hover scale)
- Interest chips: Crypto, Gaming, Freelancing, Investing
- "Go to Dashboard" button: Gradient, pulsing

---

## 04. DASHBOARD

### Header (sticky, glassmorphic)
- Greeting: "Good morning, Alex" (personalized, 20px)
- Date/time badge: "Monday, March 18 • 10:23 AM"
- Notification bell: Icon with badge count (3), dropdown
- Avatar: 40px circle → profile dropdown

### Balance Hero Card (3D tilt effect)
- Glass card with inner glow
- Total Balance: "$12,450.89" (SF Mono, 48px, gradient text)
- Sub-labels: "Lifetime Earnings" + "Today: +$48.20" (green, arrow)
- Eye toggle: Hide/show balance
- Quick actions row: Deposit, Withdraw, Buy Plan
- Progress to next tier: "Elite: $2,549 to go" (progress bar 78%)

### Earnings Grid (2×2)
- Today's Earnings — $48.20 (+12%)
- Referral Earnings — $1,240 (15 referrals)
- Active Plan — "Premium Pro" (23 days left)
- Withdrawal Status — "Pending: $200" + "Completed: $5,000"

### Earnings Graph (full width)
- Header: "Earnings Overview" + Day/Week/Month toggle
- Area chart: Gradient fill, animated draw line
- Interactive tooltip, date labels

### Recent Activity (scrollable)
- Icon circle (color-coded), text, time, status badge
- "View All" link

### Active Missions Preview (horizontal scroll)
- 3 mission cards (120px): title, reward, progress
- "View All Missions" button

### Quick Action Bar (floating, fixed bottom on mobile)
- 4 buttons: Watch Ads, Refer, Missions, Withdraw

---

## 05. WATCH ADS PAGE

### Header
- Title: "Watch Ads & Earn"
- Today's earnings: "$4.80 / $10.00" (progress bar 48%)
- Daily limit badge

### Ad Categories Tabs
- All, Gaming, Finance, Shopping, Crypto
- Active: Underline gradient + bold

### Ad Cards Grid (2 columns)
- Thumbnail 16:9, video preview on hover
- Advertiser logo, reward amount, category badge
- Watch button (gradient, full width)
- Cooldown timer if applicable

### Premium Ad Section (gold border)
- "⭐ Double Rewards", "$1.00 per ad"
- Limited spots: "Only 3 left today"
- Gold gradient button with shine animation

### Daily Streak Tracker
- "7 Day Streak 🔥 x1.5 Multiplier Active"
- Progress bar, celebration effects

### Daily Bonus Chest (floating)
- 3D chest icon with shake animation
- Claim button, random amount preview
- Cooldown timer

### Earnings Animation
- Coins fly from card to top balance
- Toast notification: "+$0.50 added"

---

## 06. MISSIONS PAGE

### Header
- Title: "Daily Missions"
- XP bar: "Level 12 • 2,450/3,000 XP to Level 13"

### Mission Categories (segmented control)
- Daily | Weekly | Special | Premium
- Animated underline between categories

### Featured Mission (hero card, 3D)
- Gold border + glow
- Title: "⭐ Double Rewards Weekend"
- Reward: "$5.00 + 200 XP"
- Live countdown
- Progress + claim button

### Mission Cards Grid (vertical)
- Title, reward pill, difficulty badge
- Progress bar (animated fill)
- Action button: "Go to Ads" / "Claim Reward"
- Time remaining badge

### Achievements Section (horizontal scroll)
- 12/50 unlocked badges
- Circular, locked (grayscale) / unlocked (color + glow)
- Rarity borders: Common, Rare, Epic, Legendary

### Leaderboard Preview
- Top 3 today with earnings
- Your rank: "#127 of 12,340"
- Prize pool: "$500 Daily Pool"
- "View Full Leaderboard" button

---

## 07. REFERRAL PAGE

### Referral Hero Card
- Title: "Invite Friends, Earn Forever"
- Referral link field + Copy button (checkmark animation)
- QR code (128×128, downloadable)
- Share buttons: Telegram, WhatsApp, Twitter, Facebook, Copy Link

### Referral Stats Dashboard (4 cards)
- Total Referrals: 47 (+12)
- Active Referrals: 32 (68% rate)
- Lifetime Earnings: $1,240 (+$340)
- Pending Commissions: $86

### Commission Structure Visual (interactive tree)
- 3 tiers: You → T1 (10%) → T2 (5%) → T3 (2.5%)
- Zoomable, nodes show earnings

### Referral Leaderboard
- Top referrers with counts and earnings
- Your position + next tier reward

### Referral Tree Visualization (canvas)
- Network graph: nodes = users, lines = relationships
- Interactive: drag, zoom, click for details
- Color-coded tiers

### Invite Friends Section
- Import contacts (Telegram, phone)
- Bulk invite, personal message template
- Invite history (last 10, with status)

---

## 08. WITHDRAWAL PAGE

### Wallet Overview (glass card)
- Total Balance, Available, Minimum, Fee
- Progress to daily limit

### Withdrawal Form (glass card)
- Amount input: Numeric keypad, real-time fee calc
- Network selector: BEP20, TRC20, ERC20 (logo + fee)
- Address field: Paste detection, save checkbox
- Withdrawal preview: You send → Fee → You receive → ETA
- Submit button: "Request Withdrawal" (gradient)
- Confirmation modal: Address preview + 2FA

### Transaction History (filterable)
- Chips: All | Pending | Completed | Failed
- Each: Date, amount (red/green), status badge, TXID
- Status timeline animation
- Load more, Export CSV

### Security Features (info card)
- 2FA required, Address whitelist, Large withdrawal alert

---

## 09. ANALYTICS PAGE

### Time Period Selector
- Today | This Week | This Month | Custom Range (date picker)

### Key Metrics Row (4 cards)
- Total Earnings (+23%), Total Referrals (+12)
- Ads Watched (+8%), Withdrawals (+15%)
- Each: value, % change, sparkline

### Earnings Breakdown (stacked bar)
- Ads (cyan), Referrals (violet), Missions (coral)
- Interactive legend toggle, hover values
- Toggle: absolute or percentages

### Referral Performance (line chart + metrics)
- New referrals over time, conversion rate 24%
- Avg referral value $26.40, best tier

### Ad Performance Analytics
- Daily bar chart, avg reward per ad, best category
- Peak earning hours heatmap

### Geographic Distribution (world map)
- Heatmap overlay, top countries table
- Interactive: click country to filter

### Export Reports
- Format: PDF, CSV, Excel
- Schedule: "Email me weekly report"
- Download button

---

## 10. PLANS PAGE

### Billing Toggle
- Monthly / Yearly (save 20%) — animated slide

### Plan Cards Grid (3 columns)
- **Basic ($9.99/mo):** Glass, +10% rewards, 5 missions, 5% referral, 2% fee
- **Pro ($29.99/mo) — Most Popular:** Gradient border, glow, +25% rewards, unlimited missions, 10% referral, 0.5% fee, priority support, PRO badge
- **Elite ($99.99/mo) — 👑 Elite:** Gold border, crown, +50% rewards, 15% referral, 0% fee, 24/7 support, $20 monthly bonus, ELITE badge

### Comparison Table (expandable)
- Features × Plans, checkmarks

### Enterprise Section
- Custom plans for 10+ users, "Contact Sales"

### FAQ (accordion)
- Plan changes, cancellation, free trial

---

## 11. PROFILE PAGE

### Profile Header
- 100px avatar with gradient border + camera edit icon
- Username @alex_crypto, member since badge
- "Edit Profile" button (outline, pencil)

### Personal Information (card, 2-column grid)
- Full name (editable), Email (verified badge), Phone (+ verify)
- Date of birth, Country (dropdown)
- Save changes button (appears after edit)

### Verification Status
- KYC Level 1: Basic — Verified ($500/day)
- KYC Level 2: Advanced — Pending ($10,000/day)
- Upgrade button + progress bar (50%)

### Security Settings
- Last login info, 2FA status + Manage
- Change password modal, Device management

### Payment Methods
- Linked addresses (truncated + default badge)
- Add new address, Default method selector

### Notification Preferences (toggles)
- Email, SMS, Push — minimum amount alert, sound effects

### Account Actions (red card)
- Delete account (red outline, confirmation modal)
- Data export (GDPR)

---

## 12. NOTIFICATIONS CENTER

### Header (sticky)
- Title, "Mark all as read" button
- Filter chips: All | Unread | Earnings | System | Promo

### Date Groups
- Today, Yesterday, This Week

### Notification Cards
- Icon circle (color-coded), title, time, status dot
- Action button if applicable
- Swipe actions (mobile): mark read / delete

### Real-time WebSocket
- Toast banner from top, auto-dismiss
- Sound, bell badge updates

### Empty State
- Illustration, "All caught up!", "Browse Missions" button

### Settings Gear → redirects to Profile → Notifications

---

## 13. SETTINGS PAGE

### Sidebar / Segmented Control
- Account, Security, Notifications, Appearance, Language, Help, About

### Account Settings
- Change email + verification, Change phone + OTP
- Timezone (auto-detected), Custom referral slug

### Security & Privacy
- Password change (strength meter)
- 2FA: QR setup, backup codes, toggle
- Active sessions + remote logout, Login alerts
- Privacy toggles (balance visibility, data sharing)

### Notifications
- Email alerts: Daily summary, Weekly report, Withdrawals
- Push: New referral, Mission completed, Offers
- In-app sounds with preview

### Appearance
- Theme: Dark | Light | System (live preview)
- Accent color picker (cyan, violet, coral, gold)
- Font size, Reduce motion toggle

### Language & Region
- App language dropdown (RTL auto-detect for Arabic)
- Currency display (USD, EUR, GBP, NGN, INR)
- Date/time format

### Help & Support
- FAQ accordion, Contact form, Live chat (Telegram)
- Report problem, Video tutorials

### About
- Version, Build, License, Terms, Privacy, Open source

### Floating Save Button
- Appears on unsaved changes, confirmation toast

---

## 14. ADMIN DASHBOARD

### Header (glass, sticky)
- Welcome, Live metrics ("🟢 1,247 online"), System health
- Admin avatar dropdown

### Key Metrics Row (4 cards)
- Total users (+12%), Active today (+8%)
- Total paid (+23%), Platform revenue (+15%)
- Each with sparkline

### Quick Action Bar
- Announcement, Export data, User search, Settings

### User Analytics (chart card)
- New signups area chart (30 days)
- Retention cohort table, Geographic heatmap
- Device breakdown pie chart

### Revenue Analytics
- Stacked area: Ads 45%, Subscriptions 35%, Fees 20%
- Projected revenue (30 days), ARPU $15.50, LTV $124

### Fraud Monitoring (real-time, red accent)
- Alert list: suspicious withdrawals, multiple accounts, VPN
- Manual review queue, Auto-approval rules

### Withdrawal Management (table)
- Pending: 45 items ($12,450)
- Batch actions, Individual approve/reject/view
- Search, Export CSV

### Referral Management
- Top referrers table, Flagged referrals
- Commission adjustment, Bonus campaigns

### Advertisement Management
- Active campaigns, today's stats (views, CPM, avg reward)
- Create campaign modal (name, budget, targeting, reward, creative, schedule)

### Content Management
- Missions builder, Announcements push, FAQ editor

### Security Center
- Failed logins chart, 2FA adoption (67%)
- Audit log, IP whitelist

### User Management (searchable table)
- Search by name/email/ID
- Actions: View, Adjust balance, Ban, Impersonate
- Bulk: Export, Send message, Suspend

### Reports
- Quick reports (Daily/Weekly/Monthly)
- Schedule toggle, Custom report generator

---

## 15. MOBILE NAVIGATION SYSTEM

### Bottom Tab Bar (fixed, 72px)
- 5 tabs: 🏠 Home, 📺 Watch, 🎯 Missions, 👥 Referral, 👤 Profile
- Active: gradient line + label slide-up
- Haptic feedback, safe area inset

### Swipe Gestures
- Swipe right: back, Swipe left: next
- Swipe down: pull to refresh, Swipe up: bottom sheet

### Touch Targets (min 44×44px)
- Buttons 48×48, 8px min spacing, opacity 0.7 on press

### Modal Sheets (bottom, 80% height)
- Drag handle, drag to expand/dismiss
- Examples: withdrawal confirm, address form, mission details

### Context Menus (long press)
- Balance, Transaction, Avatar, Notification

### Pull to Refresh
- Cyan spinner, haptic feedback, "Updated just now"

### Loading States
- Skeleton shimmer, top progress bar, gradient spinners

### Empty States
- Custom SVG illustration, title, subtitle, CTA button

### Toast Notifications
- Bottom (above tab bar) or top (below header)
- 3s duration, Success/Error/Info, optional action button
- Swipe to dismiss

### Navigation Transitions
- Push: slide from right, Pop: slide to right
- Modal: slide up, Tab: crossfade + icon bounce

### Accessibility
- VoiceOver labels, Reduce motion, Dynamic type up to 31pt
- Increased tap targets

### Offline Mode (PWA)
- Cached pages, offline indicator, sync when online

---

## 🎨 Visual Enhancements Across All Pages

### Glassmorphism Standard
- `backdrop-filter: blur(20px)`, `rgba(255,255,255,0.05)` bg
- Border: `1px solid rgba(255,255,255,0.1)`
- Shadow: `0 8px 32px rgba(0,0,0,0.1)`

### Neumorphism Accents
- Soft inner shadows on pressed buttons
- Embossed text on active states
- Raised cards on hover

### Gradient Library
- **Cyan-Violet:** `linear-gradient(135deg, #00F0FF, #7C3AED)`
- **Coral-Mint:** `linear-gradient(135deg, #FF0066, #00E5B2)`
- **Gold:** `linear-gradient(135deg, #FFD700, #FF8C00)`
- **Aurora:** `linear-gradient(125deg, #00F0FF20, #7C3AED20, #FF006620)`

### 3D Depth
- Parallax layers: Background (1×), particles (1.2×), cards (1.5×)
- Mouse move: subtle rotation on hero cards
- Scroll: fade + scale animations

### Micro-animations
- Hover: scale 1.02, shadow increase
- Tap: scale 0.95 (elastic bounce)
- Success: checkmark draw + confetti
- Notification: bell shake + badge pop

---

## ✅ Production-Ready Compliance

| Requirement | Specification |
|-------------|---------------|
| Responsive | 320px → 1920px |
| Theme | Dark mode first, light fallback |
| Accessibility | WCAG 2.1 AAA |
| Performance | Lazy loading, skeleton states, 60fps |
| Security | Input validation, CSRF, rate limiting |
| PWA | Offline support, push notifications, installable |

---

> **Design Philosophy:** Enterprise-premium fintech with bank-level UX, Web3 aesthetics, and global scalability. Optimized for high conversion, user retention, and trust signals throughout the earning journey.
