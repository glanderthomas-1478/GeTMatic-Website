# Plan: Website-Creator-Skills aufbauen

**Erstellt:** 2026-06-02
**Status:** Entwurf
**Anforderung:** Relevante Skills installieren/erstellen, damit Claude als vollständiger Website-Creator für getmatic fungieren kann (Design, Code-Generierung, SEO, Content, Deployment-Guidance)

---

## Überblick

### Was dieser Plan erreicht

Dieser Plan baut einen vollständigen Website-Creator-Stack für den getmatic-Workspace auf. Nach Umsetzung kennt Claude in jeder Session die Marke, den Tech-Stack, die Zielgruppe und die SEO-Anforderungen der getmatic-Website — ohne jedes Mal neu erklärt zu werden.

### Warum das wichtig ist

Aktuell fehlt Claude jeder getmatic-spezifische Kontext für die Website-Arbeit. Design-Entscheidungen, Texte und Code werden generisch. Mit einem dedizierten `getmatic-website`-Skill plus Referenz-Bibliothek wird jede Session sofort produktiv — konsistente Markenstimme, passender Tech-Stack, SEO-ready.

---

## Aktueller Zustand

### Relevante bestehende Struktur

- `.claude/skills/skill-creator/` — Skill-Erstellungs-Toolchain (init, package, validate)
- `.claude/skills/mcp-integration/` — Beispiel für lokale Skill-Struktur
- `context/current-data.md` — Tech-Stack-Sektion leer (`<!-- Bei Bedarf ausfüllen -->`)
- `reference/` — leer, keine Design- oder Code-Referenzen
- System-Skills verfügbar: `frontend-design`, `ui-ux-pro-max` (bereits aktiv, kein lokales Install nötig)

### Lücken oder Probleme, die adressiert werden

1. **Tech-Stack unbekannt** — kein Framework/Hosting dokumentiert → Claude kann keinen passenden Code generieren
2. **Keine Brand-Guidelines** — Farben, Fonts, Tone-of-Voice fehlen → generische Designs
3. **Kein SEO-Workflow** — kein Checklisten-System für Seiten-Optimierung
4. **Keine Content-Vorlagen** — Texte müssen jedes Mal von Null geschrieben werden
5. **Kein Deployment-Kontext** — Hosting-spezifische Anweisungen fehlen

---

## Vorgeschlagene Änderungen

### Zusammenfassung der Änderungen

- Tech-Stack und Brand-Info in `context/current-data.md` eintragen (User-Aktion)
- Einen custom Skill `getmatic-website` erstellen mit Brand, Zielgruppe, Ton, Design-System
- SEO-Referenzdatei in `reference/` anlegen (Checkliste + Meta-Tag-Templates)
- Content-Vorlagen in `reference/` anlegen (Leistungsseiten, Hero-Texte, CTAs)
- `context/current-data.md` mit Tech-Stack befüllen

### Neue Dateien erstellen

| Dateipfad | Zweck |
|---|---|
| `.claude/skills/getmatic-website/SKILL.md` | Master-Skill: Brand, Tech-Stack, Zielgruppe, Design-Tokens — lädt getmatic-Kontext in jede Session |
| `.claude/skills/getmatic-website/references/brand.md` | Farben, Fonts, Tone-of-Voice, verbotene Stilmittel |
| `.claude/skills/getmatic-website/references/seo.md` | SEO-Checkliste, Meta-Tag-Templates, Core Web Vitals Hints |
| `.claude/skills/getmatic-website/references/content-templates.md` | Vorlagen: Hero, Leistungsseiten, CTA, About, Kontakt |
| `.claude/skills/getmatic-website/references/deployment.md` | Hosting-spezifische Hinweise, Build-Kommandos, Cache-Strategien |

### Zu ändernde Dateien

| Dateipfad | Änderungen |
|---|---|
| `context/current-data.md` | Tech-Stack, Hosting, CMS, aktuelle To-dos eintragen |
| `context/business-info.md` | Brand-Farben und Design-System-Hinweis ergänzen (kurz) |

### Zu löschende Dateien

Keine.

---

## Design-Entscheidungen

### Getroffene Schlüsselentscheidungen

1. **Ein Custom-Skill statt vieler kleiner**: Statt `seo-skill` + `content-skill` + `brand-skill` separat → ein `getmatic-website`-Skill mit Referenzdateien. Spart Kontext-Budget, alles getmatic-spezifisch.
2. **System-Skills `frontend-design` und `ui-ux-pro-max` NICHT duplizieren**: Diese sind bereits global verfügbar. Im SKILL.md nur darauf hinweisen — kein eigenes Frontend-Design-Regelwerk.
3. **Referenzdateien statt SKILL.md-Body**: Details in `references/` auslagern — SKILL.md bleibt schlank (<100 Zeilen), Claude lädt nur was gerade gebraucht wird.
4. **Tech-Stack-Lücke zuerst schließen**: Ohne bekannten Stack macht Code-Generierung keinen Sinn. User muss `current-data.md` ausfüllen, bevor der Skill voll nutzbar ist.

### Betrachtete Alternativen

- **Marketplace-Skills installieren** (z.B. `seo-audit` von addyosmani): Verworfen — generische Skills ohne getmatic-Kontext. Besser: eigene Referenzdatei mit denselben Checklisten, aber angepasst.
- **Alles in CLAUDE.md**: Zu viel statischer Kontext in jeder Session. Skills laden on-demand.

### Offene Fragen — USER-INPUT NÖTIG vor Implementierung

1. **Tech-Stack**: Welches Framework/CMS nutzt die Website? (WordPress, Webflow, Next.js, plain HTML, etc.)
2. **Hosting**: Wo läuft die Website? (Hetzner, Netlify, Vercel, shared Hosting, etc.)
3. **Brand-Farben**: Gibt es definierte Primär-/Sekundärfarben? Logo-Farben?
4. **Schriften**: Welche Fonts werden genutzt?
5. **Domain**: Wie lautet die URL? (für SEO-Referenzen)

---

## Schritt-für-Schritt-Aufgaben

### Schritt 1: Tech-Stack und Brand in context/current-data.md eintragen

Vor der Skill-Erstellung die fehlenden Infos dokumentieren, damit der Skill echten Inhalt bekommt.

**Aktionen:**

- `context/current-data.md` öffnen
- Abschnitt "Tech-Stack" ausfüllen: Framework, Hosting, CMS, Build-Tool
- Abschnitt "Offene Aufgaben" mit aktuellen To-dos befüllen
- Brand-Farben und Fonts ergänzen (kurze Liste reicht)

**Betroffene Dateien:**

- `context/current-data.md`

---

### Schritt 2: Skill initialisieren via init_skill.py

Das Skill-Creator-Tooling nutzen, um die korrekte Verzeichnisstruktur zu generieren.

**Aktionen:**

- Ausführen: `python .claude/skills/skill-creator/scripts/init_skill.py getmatic-website --path .claude/skills --resources references`
- Generierte Vorlage prüfen: `.claude/skills/getmatic-website/SKILL.md` existiert

**Betroffene Dateien:**

- `.claude/skills/getmatic-website/SKILL.md` (neu, generiert)
- `.claude/skills/getmatic-website/references/` (neu, leer)

---

### Schritt 3: SKILL.md schreiben

Den Skill-Body mit getmatic-spezifischem Kontext befüllen. SKILL.md bleibt schlank — Details kommen in Referenzdateien.

**Aktionen:**

- Frontmatter setzen:
  ```yaml
  ---
  name: getmatic-website
  description: >
    Website-Entwicklung und -Optimierung für die getmatic-Firmenwebsite.
    Verwenden bei: Seiten bauen, Texte schreiben, SEO optimieren, Design-Entscheidungen,
    Code generieren, Deployment-Fragen. Enthält Brand-Guidelines, Tech-Stack,
    Zielgruppen-Kontext und Content-Vorlagen für getmatic (Elektrotechnik,
    Automatisierung, SPS, Raspberry Pi — Kunden: Industrie, Pharma, Medizin).
  ---
  ```
- Body schreiben (max. 80 Zeilen):
  - Abschnitt: Zielgruppe & Ton (3-4 Zeilen)
  - Abschnitt: Tech-Stack (aus current-data.md übernehmen)
  - Abschnitt: Referenzen (Links zu brand.md, seo.md, content-templates.md, deployment.md mit "wann lesen")
  - Abschnitt: System-Skills hinweisen (`frontend-design`, `ui-ux-pro-max` für UI-Arbeit nutzen)

**Betroffene Dateien:**

- `.claude/skills/getmatic-website/SKILL.md`

---

### Schritt 4: references/brand.md erstellen

Marken-Kontext für konsistente Design- und Text-Entscheidungen.

**Aktionen:**

- Datei erstellen mit folgenden Abschnitten:
  - **Farben**: Primär, Sekundär, Akzent, Hintergrund (Hex-Werte aus current-data.md)
  - **Typografie**: Heading-Font, Body-Font, Mono-Font
  - **Ton der Website**: professionell, technisch fundiert, direkt — keine Marketing-Floskeln
  - **Verbotene Stilmittel**: keine Stock-Photo-Ästhetik, kein "wir sind leidenschaftlich", kein Inter+purple-gradient (Default-AI-Look)
  - **Zielgruppen-Personas**: Techniker (will Specs), Entscheider (will Referenzen + Vertrauen)

**Betroffene Dateien:**

- `.claude/skills/getmatic-website/references/brand.md`

---

### Schritt 5: references/seo.md erstellen

SEO-Checkliste und Templates für die getmatic-Website.

**Aktionen:**

- Datei erstellen mit:
  - **On-Page-Checkliste**: Title-Tag (max 60 Zeichen), Meta-Description (max 155), H1 einmalig, Alt-Tags, interne Links
  - **Meta-Tag-Templates**: Für Leistungsseiten, Homepage, Kontaktseite — mit getmatic-Keywords (SPS, TIA Portal, Automatisierung, Elektrotechnik)
  - **Core Web Vitals Hints**: LCP, CLS, INP — Faustregeln je nach Tech-Stack
  - **Strukturierte Daten**: LocalBusiness-Schema für getmatic (Name, Adresse, Leistungen)
  - **Keyword-Cluster**: SPS-Programmierung, TIA Portal S7, Automatisierungstechnik, Raspberry Pi Industrie

**Betroffene Dateien:**

- `.claude/skills/getmatic-website/references/seo.md`

---

### Schritt 6: references/content-templates.md erstellen

Wiederverwendbare Textbausteine für die häufigsten Website-Bereiche.

**Aktionen:**

- Datei erstellen mit Vorlagen für:
  - **Hero-Section**: Headline-Formel (Nutzen + Zielgruppe), Subheadline, CTA-Text
  - **Leistungsseiten-Template**: Einleitung → Problem → Lösung (getmatic) → Technische Details → Referenz-Erwähnung → CTA
  - **Über-uns-Abschnitt**: Struktur für Vertrauen + Kompetenz ohne Eigenlob
  - **CTA-Varianten**: Kontakt, Anfrage, Mehr erfahren — Formulierungen für technische Zielgruppe
  - **Branchen-Abschnitt**: Wie man Industrie / Pharma / Medizin-Expertise kommuniziert

**Betroffene Dateien:**

- `.claude/skills/getmatic-website/references/content-templates.md`

---

### Schritt 7: references/deployment.md erstellen

Hosting- und Deployment-spezifische Hinweise (wird nach Schritt 1 mit realem Stack befüllt).

**Aktionen:**

- Datei erstellen mit Abschnitten je nach Tech-Stack (Platzhalter bis Schritt 1 abgeschlossen):
  - Build-Kommandos
  - Deployment-Workflow (lokal → staging → prod)
  - Cache-Invalidierung
  - Backup-Strategie
  - Typische Fehler beim Deploy

**Betroffene Dateien:**

- `.claude/skills/getmatic-website/references/deployment.md`

---

### Schritt 8: Skill validieren und paketieren

Qualität prüfen und Skill in verteilbare Form bringen.

**Aktionen:**

- Validate: `python .claude/skills/skill-creator/scripts/quick_validate.py .claude/skills/getmatic-website`
- Fehler beheben falls vorhanden
- Package: `python .claude/skills/skill-creator/scripts/package_skill.py .claude/skills/getmatic-website`
- `.skill`-Datei in `outputs/` ablegen (für Backup/Export)

**Betroffene Dateien:**

- `.claude/skills/getmatic-website/` (alle Dateien)
- `outputs/getmatic-website.skill`

---

### Schritt 9: context/current-data.md finalisieren

Alle neuen Infos persistent im Kontext dokumentieren.

**Aktionen:**

- Tech-Stack-Abschnitt vollständig ausfüllen
- Abschnitt "Skill-Stack" ergänzen: Welche Skills sind aktiv und wofür

**Betroffene Dateien:**

- `context/current-data.md`

---

## Verbindungen & Abhängigkeiten

### Dateien, die diesen Bereich referenzieren

- `CLAUDE.md` — beschreibt die Workspace-Struktur, kein direktes Update nötig
- `context/current-data.md` — liefert Inputs für SKILL.md und brand.md

### Nötige Updates für Konsistenz

- `context/current-data.md` muss VOR der Skill-Erstellung ausgefüllt sein (Schritt 1)
- Nach Skill-Erstellung: `context/current-data.md` Abschnitt "Skill-Stack" aktualisieren

### Auswirkungen auf bestehende Workflows

- `/prime` lädt weiterhin denselben Kontext — keine Änderung
- `getmatic-website`-Skill wird künftig automatisch ausgelöst, wenn Website-Aufgaben angefragt werden
- `frontend-design` und `ui-ux-pro-max` bleiben als System-Skills aktiv — ergänzend, nicht ersetzend

---

## Validierungs-Checkliste

- [ ] `context/current-data.md` enthält Tech-Stack und Hosting
- [ ] `.claude/skills/getmatic-website/SKILL.md` existiert mit korrektem Frontmatter
- [ ] Alle 4 Referenzdateien existieren und sind befüllt (brand, seo, content-templates, deployment)
- [ ] `quick_validate.py` läuft ohne Fehler durch
- [ ] Skill wird ausgelöst wenn man sagt "baue mir die Kontaktseite"
- [ ] Skill wird ausgelöst wenn man sagt "optimiere den SEO der Homepage"
- [ ] Brand-Farben und Fonts sind in brand.md korrekt eingetragen
- [ ] `context/current-data.md` hat Abschnitt "Aktiver Skill-Stack"

---

## Erfolgskriterien

Die Implementierung ist abgeschlossen, wenn:

1. `getmatic-website`-Skill automatisch in Website-Aufgaben greift und getmatic-spezifischen Kontext mitbringt (Farben, Ton, Tech-Stack)
2. SEO-Checkliste aus `references/seo.md` bei Seiten-Optimierungen anwendbar ist ohne externe Suche
3. Content-Vorlagen in `references/content-templates.md` direkt für neue Seiten nutzbar sind
4. `frontend-design` + `getmatic-website` zusammen konsistente, nicht-generische UI-Vorschläge liefern

---

## Notizen

- **Wichtigste Voraussetzung:** Tech-Stack und Brand-Infos müssen vom User kommen — ohne diese bleibt der Skill ein Platzhalter. Schritt 1 ist User-Aktion, nicht Claude-Aktion.
- **System-Skills nutzen:** `frontend-design` (277k Installs) und `ui-ux-pro-max` sind bereits stark — der `getmatic-website`-Skill ergänzt sie mit Kontext, konkurriert nicht.
- **Später erweiterbar:** Wenn Referenzprojekte dokumentiert sind, können diese als `references/case-studies.md` ergänzt werden.
- **Marketplace-Skills** wie `seo-audit` (addyosmani/web-quality-skills) können zusätzlich installiert werden, sobald der Tech-Stack bekannt ist — für jetzt reicht die eigene SEO-Referenzdatei.

### Quellen zur Recherche

- [Frontend Design Skill — Anthropic official](https://claudemarketplaces.com/skills/anthropics/skills/frontend-design)
- [Best Claude Code Skills 2026 — Firecrawl](https://www.firecrawl.dev/blog/best-claude-code-skills)
- [Marketing & SEO Skills — Claude Marketplaces](https://claudemarketplaces.com/skills/category/marketing-seo)
- [SEO Audit Skill — addyosmani](https://claudemarketplaces.com/skills/addyosmani/web-quality-skills/seo)
