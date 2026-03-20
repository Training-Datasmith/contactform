# Architecture: contactform

## Purpose

A PrestaShop native module providing a contact form widget that customers can submit from the storefront. It sends the message to the shop's configured email address and optionally sends a confirmation email to the customer.

## Directory Structure

```
contactform.php              — Module entry point: install/uninstall hooks, email sending logic, widget rendering
views/
  templates/
    widget/                  — Smarty template for the contact form widget

tests/
  php/phpstan/               — PHPStan configuration and bootstrap for static analysis

upgrade/
  upgrade-4.1.0.php          — Database/configuration migration for version 4.1.0
  upgrade-4.4.1.php          — Migration for version 4.4.1
```

## Key Design Decisions

- **WidgetInterface** — implements PrestaShop's `WidgetInterface` so the form can be embedded in any hook position via the widget system.
- **Configurable email behaviour** — `CONTACTFORM_SEND_CONFIRMATION_EMAIL` and `CONTACTFORM_SEND_NOTIFICATION_EMAIL` toggles are stored as PrestaShop configuration values.
- **Single-class module** — all logic lives in `contactform.php` following PrestaShop module conventions; no separate service layer.

## Extension Points

- Override the Smarty template in a child theme to customise form appearance.
- Hook into PrestaShop's `actionContactFormSubmitBefore` / `actionContactFormSubmitAfter` events to add custom validation or processing.
