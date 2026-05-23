# HayBTech for Magento 2

Official Magento 2 payment module for HayBTech -- accept mobile money payments on your Magento store.

[![Magento](https://img.shields.io/badge/Magento-2.4+-EC6737.svg)](https://magento.com/)
[![PHP](https://img.shields.io/badge/PHP-8.1+-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

---

## Features

- Seamless redirect-based checkout via HayBTech hosted payment page
- Automatic webhook verification with HMAC-SHA256 signature validation
- Full support for Test and Live modes
- Zero external Composer dependencies (embedded SDK)
- Compatible with Magento multi-store and multi-currency setups

---

## Requirements

| Requirement | Version  |
|:------------|:---------|
| Magento     | 2.4+     |
| PHP         | 8.1+     |

The module depends on the following core Magento modules:

- `Magento_Sales`
- `Magento_Payment`
- `Magento_Checkout`

---

## Installation

### Manual Installation

1. Download or clone this repository.
2. Copy the module to your Magento installation:

```bash
cp -r haybtech-magento/ <magento-root>/app/code/HayBTech/Payment/
```

3. Enable the module and run setup:

```bash
php bin/magento module:enable HayBTech_Payment
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

### Via Composer (Private Repository)

```bash
composer require haybtech/magento-payment
php bin/magento setup:upgrade
php bin/magento setup:di:compile
php bin/magento cache:flush
```

---

## Configuration

1. Log in to your **Magento Admin Panel**.
2. Navigate to **Stores > Configuration > Sales > Payment Methods**.
3. Locate **HayBTech** in the payment methods list.
4. Configure the following fields:

| Field              | Description                                               |
|:-------------------|:----------------------------------------------------------|
| **Enabled**        | Enable or disable the payment method                      |
| **Title**          | Display name shown to customers during checkout           |
| **Secret Key (Test)** | Your test secret key (`sk_test_...`) from the HayBTech dashboard |
| **Secret Key (Live)** | Your live secret key (`sk_live_...`) from the HayBTech dashboard |
| **Webhook Secret** | Your webhook signing secret (`whsec_...`)                 |
| **Mode**           | Toggle between Test and Live                              |

5. Click **Save Config** and flush the cache.

---

## Webhook Setup

1. In your **HayBTech Dashboard**, navigate to **Settings > Webhooks**.
2. Add a new endpoint:

```
https://your-store.com/haybtech/webhook/notify
```

3. Copy the webhook secret and paste it into the Magento configuration.

The module automatically verifies webhook signatures using HMAC-SHA256 to ensure the integrity and authenticity of every notification.

---

## How It Works

1. Customer selects **HayBTech** at checkout.
2. Magento creates the order and redirects to the HayBTech hosted payment page.
3. Customer completes payment via their preferred mobile money provider.
4. HayBTech sends a signed webhook notification to your Magento store.
5. The module verifies the signature and updates the order status automatically.

---

## Supported Payment Providers

| Provider                 | Countries                               |
|:-------------------------|:----------------------------------------|

---

## Module Structure

```
HayBTech/Payment/
  registration.php              # Magento module registration
  etc/
    module.xml                  # Module declaration and dependencies
  Model/
    HayBTech.php                # Payment method model (redirect logic)
  lib/
    sdk/                        # Embedded HayBTech PHP SDK (zero dependencies)
      HayBTech.php
      HayBTechClient.php
      HayBTechResponse.php
      Webhook.php
      Resources/
        Payments.php
        Webhooks.php
      Exceptions/
        ApiException.php
        HayBTechException.php
        SignatureException.php
```

---

## Security

- **Webhook Signature Verification**: Every incoming notification is validated using HMAC-SHA256 with constant-time comparison.
- **Zero External Dependencies**: The embedded SDK uses only native PHP functions. No Composer packages, no supply chain risk.
- **Secret Masking**: API keys are masked in logs and debug output.
- **Payload Size Limit**: Webhook payloads are capped at 1 MB to prevent memory exhaustion.
- **CRLF Guard**: Prevents HTTP header injection via malformed API keys.

---

## Troubleshooting

| Issue                        | Solution                                                                 |
|:-----------------------------|:-------------------------------------------------------------------------|
| Module not appearing         | Run `php bin/magento setup:upgrade && php bin/magento cache:flush`        |
| Webhooks returning 403       | Verify your webhook secret matches the one in the HayBTech dashboard     |
| Orders not updating          | Check `var/log/system.log` for webhook processing errors                 |
| Payment method not displayed | Ensure the module is enabled and the store currency is set to XOF        |

---

MIT License
