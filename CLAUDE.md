# CLAUDE.md — workspace-website-getmatic

Dieser Workspace ist für die Entwicklung und laufende Optimierung der **getmatic-Firmenwebsite**.

---

## Was das hier ist

Dieser Workspace begleitet die getmatic-Website dauerhaft. Die Website ist kein einmaliges Projekt — sie lebt und wird kontinuierlich weiterentwickelt. Hier werden Änderungen geplant, umgesetzt und dokumentiert.

**getmatic** ist ein selbstständiges Unternehmen im Bereich Elektrotechnik, Automatisierung und Programmierung (SPS / TIA Portal S7, Antriebe, Raspberry Pi). Kunden aus Industrie, Pharma und Medizin.

---

## Workspace ≠ Subagent

- **Workspace** (dieser Ordner): organisiert die Arbeit an der Website
- **Subagent** (`.claude/agents/<name>.md`): spezialisierter KI-Helfer für Claude — optional, für den Anfang nicht nötig

---

## Warum `/create-plan` vor `/implement`?

Bei nicht trivialen Aufgaben **nicht** direkt „bau das mal" sagen:

1. `/create-plan <anforderung>` → strukturierter Plan in `plans/`
2. Plan prüfen und anpassen
3. `/implement <plan-pfad>` → umsetzen

Ein 2.000-Token-Plan verhindert einen 30.000-Token-Fehlschlag.

---

## Workspace-Struktur

```
.
├── CLAUDE.md              # Diese Datei
├── .claude/
│   ├── commands/          # /prime, /create-plan, /implement, /shutdown
│   ├── agents/            # Optionale Subagents
│   └── skills/            # Lokale Skills (skill-creator, mcp-integration; getmatic-website noch nicht erstellt — siehe plans/2026-06-02-website-creator-skills-aufbauen.md)
├── context/               # Kontext über Projekt, Ziele, Stand
├── plans/                 # Implementierungspläne
├── outputs/               # Texte, Assets, Deliverables
├── reference/             # Design-Referenzen, Code-Patterns, Vorlagen
└── scripts/               # Automatisierungsskripte
```

---

## Commands

### /prime
Session initialisieren — Kontext laden, Bereitschaft bestätigen. Immer zu Beginn ausführen.

### /create-plan [anforderung]
Plan erstellen vor größeren Änderungen (neue Seiten, Features, Redesign, SEO-Optimierungen).
Beispiel: `/create-plan Kontaktformular hinzufügen`

### /implement [plan-pfad]
Plan aus `plans/` umsetzen.

### /shutdown
Session beenden — aufräumen, committen.

---

## Kommunikation

- Sprache: Deutsch und Englisch
- Ton: Knapp und direkt
- Detailtiefe: Minimal

---

## Kritische Anweisung

Nach jeder Änderung prüfen, ob `CLAUDE.md` oder `context/` aktualisiert werden muss. Diese Datei ist die Single Source of Truth für jede neue Session.

---

## Session-Workflow

1. `/prime` — Kontext laden
2. Arbeiten — direkt oder via Commands
3. `/create-plan` — vor größeren Änderungen
4. `/implement` — Plan umsetzen
5. `/shutdown` — aufräumen, committen
