# Prime

> Lade den Workspace-Kontext und bringe Claude auf den Stand deiner Arbeit. Beim ersten Start (solange `context/` noch Platzhalter enthält) wechselt dieser Command in den **Onboarding-Modus** und erklärt dir die Grundlagen.

## Ausführen

```
ls -la
find . -type f -name "*.md" | head -20
grep -l "AUSFÜLLEN" context/*.md 2>/dev/null
```

## Lesen

- `CLAUDE.md`
- `ONBOARDING.md` (nur relevant, wenn Onboarding-Modus greift)
- `./context` (alle vier Dateien: personal-info, business-info, strategy, current-data)

## Moduswahl

**Wenn der `grep` auf `"AUSFÜLLEN"` Treffer liefert → Onboarding-Modus.**
**Wenn keine Treffer mehr → Standard-Modus.**

---

## Onboarding-Modus (erster Start, context/ noch nicht ausgefüllt)

Gib dem Benutzer in klarer, ruhiger Form Folgendes aus — in dieser Reihenfolge. Halte den Ton einladend, nicht belehrend. Benutze Du-Form. Ziel: der Benutzer versteht in 2 Minuten, was er vor sich hat und was er tun soll.

### 1. Willkommen

Kurz begrüßen und sagen, dass dies ein **Claude Workspace** ist — ein vorbereiteter Projektordner, der Claude bei jeder Session in die richtige Spur bringt.

### 2. Workspaces ≠ Subagents

Erkläre in 3–4 Zeilen den Unterschied (ohne Jargon zu überladen):

- **Workspace** = dieser Ordner hier. Eine Organisationsstruktur für dich als Mensch. Enthält `CLAUDE.md` (automatisch geladen), `context/` (wer du bist, was du tust) und Commands wie `/prime`, `/create-plan`, `/implement`, `/shutdown`.
- **Subagent** = ein spezialisierter KI-Helfer mit eigenem Kontext und eigenen Tools. Liegt unter `.claude/agents/<name>.md`. Wird vom Haupt-Claude bei Bedarf aufgerufen. Für den Start **nicht nötig**.

Hinweis: „Workspaces organisieren den Menschen, Subagents erweitern Claude." Für Details siehe `ONBOARDING.md`.

### 3. Spezialisierte Themen-Workspaces

Erkläre, dass diese Vorlage für **beliebig viele Themen parallel** genutzt werden kann. Jedes Thema bekommt einen eigenen Ordner (z.B. `workspace-marketing/`, `workspace-kundenprojekt-x/`, `workspace-lernprojekt-rust/`) mit eigener `CLAUDE.md` und eigenem `context/`. Claude weiß in jeder Session genau, in welchem Workspace er ist — das ist deine Hauptwaffe gegen „Claude schreibt generisch". Das ist **kein** Subagent-System.

### 4. `context/` ausfüllen ist Pflicht, nicht Zierde

Sag klar: ohne ausgefüllte `context/`-Dateien arbeitet Claude blind und liefert generische Antworten. Die vier Dateien sind in ca. 15 Minuten ausgefüllt — diese Zeit bekommst du in jeder Folge-Session mehrfach zurück. Die vier Dateien:

- `personal-info.md` — wer du bist, was deine Rolle ist
- `business-info.md` — Organisation/Projekt/Kontext, in dem du arbeitest
- `strategy.md` — woran du gerade arbeitest, welche Ziele du verfolgst
- `current-data.md` — Metriken, aktueller Stand, Datenquellen

### 5. `/create-plan` vor `/implement` — Token-Disziplin

Erkläre die Arbeitslogik:

- Bei größeren Aufgaben **erst** `/create-plan <anforderung>` → Claude erstellt einen billigen, strukturierten Plan (wenige Tokens).
- Du prüfst und korrigierst den Plan, bevor Tokens in die Umsetzung fließen.
- Dann `/implement <plan-pfad>` → Claude setzt den geprüften Plan um.
- Ohne Plan verbrennt Claude sonst leicht viele Tokens in eine Richtung, die dir nicht zusagt — und du zahlst doppelt (einmal Fehlversuch, einmal Korrektur). Rechne mit einem Verhältnis von ca. 1:10 bis 1:20 zwischen Plan und Implementierung. Ein schlechter 30.000-Token-Implementierungslauf kostet dich deutlich mehr als ein 2.000-Token-Plan.

### 6. Dein nächster Schritt

Sage explizit: „Öffne die vier Dateien in `context/`, fülle die `<!-- AUSFÜLLEN: ... -->`-Stellen aus, speichere und rufe `/prime` erneut auf. Danach bin ich auf dich eingestellt und wir können loslegen."

Abschließend: Angebot, Fragen zu `ONBOARDING.md`, `CLAUDE.md` oder dem Workflow direkt zu beantworten.

---

## Standard-Modus (context/ ist ausgefüllt)

Nach Lektüre von `CLAUDE.md` und allen vier `context/`-Dateien liefere:

1. Eine kurze Zusammenfassung, wer der Benutzer ist, wofür dieser Workspace ist und was deine Rolle ist
2. Verständnis der Workspace-Struktur und des Zwecks jedes Abschnitts
3. Welche Commands verfügbar sind
4. Zusammenfassung der aktuellen Strategien und Prioritäten aus `strategy.md`
5. Bestätigung, dass du bereit bist, den Benutzer bei der Verfolgung dieser Ziele zu unterstützen

Halte die Zusammenfassung prägnant — keine Ausführlichkeit, die der Benutzer in den Kontextdateien schon selbst gelesen hat.
