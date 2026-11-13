# ZebLearn LMS — Razorpay Payments Integration & Setup Guide

This document provides instructions for configuring and verifying Razorpay payments in the ZebLearn Learning Management System.

---

## 1. Razorpay Account Setup
1. Sign up or log in to the official [Razorpay Dashboard](https://dashboard.razorpay.com/).
2. Complete business KYC verification for live activations.
3. Switch the environment toggle in the top-right to **Test Mode** during staging and testing.

---

## 2. API Keys & Environment Configuration
1. In the Razorpay Dashboard, navigate to **Account & Settings &rarr; API Keys**.
2. Click **Generate Key** to produce your `Key ID` and `Key Secret`.
3. Add these to your local `.env` file (never commit live keys to git):

```env
# Razorpay Payment Gateway
RAZORPAY_KEY_ID=rzp_test_YOUR_KEY_ID
RAZORPAY_KEY_SECRET=YOUR_KEY_SECRET
RAZORPAY_WEBHOOK_SECRET=YOUR_WEBHOOK_SECRET
```

4. Configuration is registered in `config/services.php`:
   - `services.razorpay.key`
   - `services.razorpay.secret`
   - `services.razorpay.webhook_secret`

---

## 3. Webhook Setup
1. In the Razorpay Dashboard, navigate to **Account & Settings &rarr; Webhooks**.
2. Click **Add New Webhook**.
3. **Webhook URL**: `https://your-domain.com/webhooks/razorpay`
4. **Secret**: Enter a strong random secret and save it in `RAZORPAY_WEBHOOK_SECRET`.
5. **Active Events**: Select:
   - `payment.captured`
   - `order.paid`
   - `payment.failed`

---

## 4. Local Webhook Testing
To test webhooks locally:
1. Use the Razorpay CLI or an ngrok tunnel:
   ```bash
   ngrok http 8000
   ```
2. Set the Webhook URL in Razorpay Dashboard to `https://your-tunnel.ngrok-free.app/webhooks/razorpay`.
3. The endpoint is automatically exempt from browser CSRF in `bootstrap/app.php` and requires valid HMAC SHA256 signature verification in the `X-Razorpay-Signature` header.

---

## 5. How to Test Checkout Without Real Charges
1. Make sure `RAZORPAY_KEY_ID` starts with `rzp_test_`.
2. As an authenticated student, navigate to any available course and click checkout:
   - URL: `/checkout/{course-slug}`
3. Click **Pay via Razorpay**:
   - Razorpay opens in modal test mode.
   - For Card: Use Razorpay test card numbers (e.g. `4012 0010 0000 2603`, valid future expiry, CVV `111`).
   - For UPI: Select Success simulation.
   - For NetBanking: Select any test bank and click **Success**.
4. Upon payment success, the frontend callback triggers server-side signature verification at `POST /checkout/{course-slug}/verify`.
5. Once verified, the student is automatically enrolled and immediately redirected to the course learning area.

---

## 6. Production Checklist
- [ ] Switch Razorpay Dashboard to **Live Mode**.
- [ ] Generate Live API Keys (`rzp_live_...`).
- [ ] Configure Live `RAZORPAY_KEY_ID` and `RAZORPAY_KEY_SECRET` in production environment secrets.
- [ ] Set up production Webhook endpoint with SSL (`https://`) and configure `RAZORPAY_WEBHOOK_SECRET`.
- [ ] Verify that manual admin enrollment approval remains operational for corporate/invoice-based enrollments.
- [ ] Ensure `APP_DEBUG=false` in production.

