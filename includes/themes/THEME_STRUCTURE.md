# Nexpell Theme Structure

Ein Theme liegt vollständig in:

`includes/themes/<slug>/`

Empfohlene Struktur:

```text
includes/themes/<slug>/
|-- theme.json
|-- index.php
|-- header.php
|-- footer.php
|-- templates/
|-- css/
|-- js/
|-- images/
|-- fonts/
`-- assets/
    |-- css/
    |-- js/
    |-- img/
    `-- vendor/
```

Hinweise:

- `theme.json` ist das Manifest für Name, Layout und Assets.
- `index.php` ist das Theme-Layout.
- `templates/` enthält HTML-Templates für `Template::loadTemplate()`.
- Externe Templates wie BootstrapMade können ihre `assets/` und `assets/vendor/`
  Struktur innerhalb des Theme-Ordners behalten.
- Das aktive Theme wird in `settings_themes` gespeichert.
- Theme-spezifische Optionen liegen in `settings_theme_options`.
