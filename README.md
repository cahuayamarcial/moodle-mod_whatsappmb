# WhatsApp by Marbot — Moodle activity module

[![Moodle](https://img.shields.io/badge/Moodle-4.1%2B-orange)](https://moodle.org)
[![License](https://img.shields.io/badge/license-GPL%20v3-blue)](LICENSE)
[![Release](https://img.shields.io/badge/release-1.0.4-green)](https://github.com/cahuayamarcial/moodle-mod_whatsappmb/releases)

Adds a **WhatsApp activity** to Moodle courses. Teachers configure either a personal contact number (with optional pre-filled message) or a group invite link, and students join the conversation with a single click — directly from the course page.

![Preview](https://img001.prntscr.com/file/img001/V0zqdx7-RGmdMdkGdGj80A.gif)

## Features

- **Personal contact** — WhatsApp number with optional default message that opens the chat pre-filled
- **Group invite** — Direct link to a WhatsApp group
- **Server-side redirect** — Uses `view.php`; no inline JavaScript, no popup blockers
- **Activity logs** — Triggers the `course_module_viewed` event for analytics and reporting
- **Completion tracking** — Supports "complete when viewed"
- **Backup & restore** — Full Moodle 2 backup/restore support
- **Privacy** — Stores no personal data (`null_provider`)
- **i18n** — English and Spanish

## Requirements

| Component | Version |
|---|---|
| Moodle  | 4.1 or newer |
| PHP     | 7.4 or newer |

## Installation

### From the Moodle Plugins directory (recommended)

1. Site administration → Plugins → Install plugins
2. Search for **WhatsApp by Marbot** and install
3. Run the upgrade

### From ZIP

1. Download the latest release: [github.com/cahuayamarcial/moodle-mod_whatsappmb/releases](https://github.com/cahuayamarcial/moodle-mod_whatsappmb/releases/latest)
2. Site administration → Plugins → Install plugins → Upload the ZIP

### Manual

```sh
cd moodle/mod
git clone https://github.com/cahuayamarcial/moodle-mod_whatsappmb.git whatsappmb
```

Then run Site administration → Notifications.

## Usage

1. Turn editing on in a course → **Add an activity or resource** → **WhatsApp**
2. Pick **Link type**:
   - *Personal number*: enter a phone number in international format (e.g. `+59171502211`) and an optional default message
   - *Group link*: paste a WhatsApp group invite (`https://chat.whatsapp.com/...`)
3. Save — students see a clickable activity that opens WhatsApp Web or the WhatsApp app

## Capabilities

| Capability                       | Role default              |
|----------------------------------|---------------------------|
| `mod/whatsappmb:addinstance`     | Editing teacher, Manager  |
| `mod/whatsappmb:view`            | All users                 |

## Changelog

### 1.0.4

- Removed inline JavaScript from `whatsappmb_get_coursemodule_info`; replaced with a clean server-side redirect via `view.php`
- Added `whatsappmb_view()` helper that triggers the viewed event and updates completion state
- Enabled `FEATURE_COMPLETION_TRACKS_VIEWS`
- `index.php` now follows the standard `mod_*` convention
- Fixed backup placeholder typo that prevented link decoding on restore
- Added missing GPL headers and PHPDoc across all classes and functions
- Translated all comments to English
- Bumped minimum supported Moodle to 4.1
- Plugin passes `phpcs --standard=moodle` with zero errors

### 1.0.0

- Initial release
- Personal contact with pre-filled message
- Group invite link

## License

GNU GPL v3 — see [LICENSE](LICENSE).

## Author

**Marcial Cahuaya** ([Marbot](https://github.com/cahuayamarcial))

Issues and suggestions: [github.com/cahuayamarcial/moodle-mod_whatsappmb/issues](https://github.com/cahuayamarcial/moodle-mod_whatsappmb/issues)
