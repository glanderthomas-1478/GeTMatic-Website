# Onboarding — Claude Workspace Vorlage

> Tiefgang-Erklärung der Konzepte. Lies das einmal in Ruhe durch, danach weißt du, wie du mit diesem Workspace produktiv bist. Für den schnellen Einstieg reicht `CLAUDE.md` und ein `/prime`-Aufruf.

---

## Inhalt

1. [Was ist ein Workspace?](#1-was-ist-ein-workspace)
2. [Warum du `context/` ausfüllen solltest](#2-warum-du-context-ausfüllen-solltest)
3. [Spezialisierte Workspaces: ein Ordner pro Thema](#3-spezialisierte-workspaces-ein-ordner-pro-thema)
4. [Workspaces vs. Subagents](#4-workspaces-vs-subagents)
5. [Plan-first: warum `/create-plan` vor `/implement`?](#5-plan-first-warum-create-plan-vor-implement)
6. [Shell-Aliase](#6-shell-aliase)
7. [Troubleshooting](#7-troubleshooting)

---

## 1. Was ist ein Workspace?

Ein **Workspace** ist in dieser Vorlage ein Projektordner, der Claude Code bei jeder Session automatisch mit Kontext versorgt. Formal besteht er aus:

- **`CLAUDE.md`** im Wurzelverzeichnis — wird von Claude Code automatisch geladen, sobald du im Ordner eine Session startest
- **`.claude/`** mit Commands, Skills und optionalen Subagents
- **`context/`** mit vier Dateien, die dich und dein Projekt beschreiben
- **`plans/`, `outputs/`, `reference/`, `scripts/`** als Organisationsstruktur

Der Begriff „Workspace" ist in Claude Code kein offizielles Feature, sondern eine Konvention. Was Claude wirklich automatisch liest, ist die `CLAUDE.md`. Alles andere ist Workspace-Disziplin — Struktur, die du dir selbst und Claude gönnst, damit beide in jeder Session sofort orientiert sind.

**Der Kernvorteil:** Du musst Claude nicht bei jeder Session neu erklären, wer du bist und was du vorhast. Das steht bereits in `context/` und wird über `/prime` geladen.

---

## 2. Warum du `context/` ausfüllen solltest

Ohne ausgefüllten Kontext liefert Claude generische Antworten. Mit ausgefülltem Kontext liefert Claude zugeschnittene Antworten. So einfach ist der Unterschied.

Die vier Dateien im Ordner `context/`:

| Datei | Beantwortet die Frage | Zeitaufwand beim Ausfüllen |
|-------|----------------------|----------------------------|
| `personal-info.md` | Wer bist du? Was ist deine Rolle? Wie arbeitest du? | ~5 Min |
| `business-info.md` | In welchem Projekt/Unternehmen/Kontext arbeitest du? | ~5 Min |
| `strategy.md` | Woran arbeitest du gerade? Was sind deine Ziele? | ~5 Min |
| `current-data.md` | Welche Metriken, Daten, Stände sind aktuell relevant? | laufend |

**Gesamtaufwand beim ersten Mal: ca. 15 Minuten.** Diese Zeit bekommst du in jeder Folge-Session mehrfach zurück, weil Claude sofort weiß, worum es geht, statt dich mit Rückfragen zu behelligen.

**Regel:** Halte die Dateien aktuell. Veralteter Kontext ist schlimmer als kein Kontext — Claude wird auf Basis falscher Annahmen Vorschläge machen. Nach größeren Projektänderungen 5 Minuten investieren, damit `context/` wieder stimmt.

---

## 3. Spezialisierte Workspaces: ein Ordner pro Thema

Der entscheidende Trick: Du kannst diese Vorlage **beliebig oft klonen** — ein Ordner pro Thema.

### Beispielhafte Struktur auf deiner Festplatte

```
~/Claude_workspace/
├── workspace-kundenprojekt-alpha/    # Für ein konkretes Kundenprojekt
├── workspace-lernprojekt-rust/       # Für dein Nebenbei-Lernprojekt
├── workspace-marketing-kampagne-q2/  # Für eine befristete Kampagne
└── workspace-privat-dokumentation/   # Für private Doku/Notizen
```

Jeder Ordner ist ein eigenständiger Workspace mit eigener `CLAUDE.md`, eigenem `context/`, eigenen `plans/` und `outputs/`. Wenn du Claude im Ordner `workspace-kundenprojekt-alpha/` startest, weiß er präzise: „Hier geht es um Projekt Alpha, nicht um Rust-Lernen."

### Warum ist das besser als ein großer Workspace?

- **Keine Kontextvermischung.** Claude bekommt nur, was zum aktuellen Thema gehört.
- **Klare Historie.** Pläne und Outputs pro Thema getrennt, leicht auffindbar.
- **Parallele Bearbeitung.** Themen laufen unabhängig, du kannst zwischen ihnen springen ohne Claude zu „resetten".
- **Ende eines Themas.** Thema abgeschlossen → Ordner archivieren oder löschen. Kein Aufräumen in einem Monster-Workspace.

### Was das **nicht** ist

Das ist **kein Subagent-System.** Du erzeugst nicht mehrere KI-Helfer, sondern mehrere Projektkontexte für dich. Mehr dazu im nächsten Abschnitt.

---

## 4. Workspaces vs. Subagents

Zwei verschiedene Werkzeuge, die gern verwechselt werden.

| Merkmal                  | Workspace                                    | Subagent                                     |
|--------------------------|----------------------------------------------|----------------------------------------------|
| Wo liegt er?             | Ein Projektordner auf deiner Festplatte      | `.claude/agents/<name>.md` innerhalb eines Workspaces |
| Was organisiert er?      | **Dich als Mensch** (Kontext, Pläne, Outputs) | **Claude als Assistent** (spezialisierte Rolle) |
| Hat er eigenen Kontext?  | Ja — via `CLAUDE.md` + `context/`            | Ja — eigenes Kontext-Fenster, getrennt vom Haupt-Claude |
| Wer aktiviert ihn?       | Du, indem du `claude` im Ordner startest     | Der Haupt-Claude, wenn eine Teilaufgabe passt |
| Anzahl typisch           | Ein Workspace pro Thema (mehrere parallel)   | Null bis wenige pro Workspace, nur wenn nötig |
| Erstkonfig-Aufwand       | ~15 Min (context/ ausfüllen)                 | Pro Subagent: System-Prompt + Tool-Auswahl überlegen |

### Wann brauche ich einen Subagent?

Fast nie zu Beginn. Erst wenn eine **wiederkehrende Teilaufgabe** auftaucht, für die du ein stabiles Profil haben willst. Beispiele:

- Ein „Code-Reviewer"-Subagent, der in jedem Workspace-Projekt identisch prüft
- Ein „Recherche-Agent", der nur Web-Tools hat und nur Fakten sammelt
- Ein „Test-Runner", der ausschließlich Tests ausführen und Ergebnisse berichten darf

Format einer Subagent-Datei (YAML-Frontmatter + System-Prompt):

```markdown
---
name: code-reviewer
description: Prüft geänderten Code auf Qualität, Stil und Sicherheit.
tools: [Read, Grep, Bash]
---

Du bist ein strenger Code-Reviewer. Lies den geänderten Code in diesem Projekt,
prüfe auf Sicherheitslücken, ineffizienten Code und Stilbrüche...
```

Offizielle Doku: <https://docs.claude.com/en/docs/claude-code/sub-agents>

### Faustregel

Startest du ein neues Thema → neuer **Workspace**.
Willst du Claude eine wiederkehrende Rolle geben → **Subagent** innerhalb des Workspaces.

---

## 5. Plan-first: warum `/create-plan` vor `/implement`?

Dies ist die wichtigste Disziplin in diesem Workspace. Einmal verinnerlicht, sparst du Stunden und Token.

### Das Problem ohne Plan

Du sagst: „Bau mir einen neuen Command, der X tut."

Claude legt los, schreibt 30.000 Tokens Code, Dokumentation, Integration. Am Ende stellst du fest: „Nee, X sollte eigentlich Y tun, und Z hatte ich mir anders gedacht." Jetzt musst du entweder korrigieren lassen (weitere 20.000 Tokens) oder verwerfen.

**Verbranntes Budget: 50.000+ Tokens für ein Ergebnis, das nicht passt.**

### Die Plan-first-Lösung

```
/create-plan Neuen Command bauen, der X tut
```

Claude schreibt einen **Plan** von ~2.000 Tokens in `plans/YYYY-MM-DD-neuer-command.md`. Der Plan enthält:

- Überblick & Begründung
- Aktuellen Zustand & Lücken
- Vorgeschlagene Änderungen (Dateien, Strukturen)
- Design-Entscheidungen mit Alternativen
- Schritt-für-Schritt-Aufgaben
- Validierungs-Checkliste

Du liest den Plan. Du entdeckst: „X sollte Y sein." Du lässt den Plan anpassen (wenige hundert Tokens) oder passt ihn selbst an. Erst dann:

```
/implement plans/2026-04-23-neuer-command.md
```

Claude setzt den geprüften Plan um — zielgerichtet, ohne Zickzack.

### Rechenbeispiel

| Ansatz                       | Tokens gesamt | Ergebnispassung |
|------------------------------|---------------|-----------------|
| Direkt implementieren, passt | ~30.000       | ok              |
| Direkt implementieren, passt nicht, Korrektur | ~50.000+      | ok nach Korrektur |
| Plan erst, dann implementieren | ~32.000       | ok im ersten Wurf |

Der Plan „kostet" kaum mehr als ein guter direkter Lauf, **rettet dich aber zuverlässig vor dem Fehlschlag-Szenario.** Auf Opus oder Sonnet ist das spürbar Geld.

### Wann darfst du Plan überspringen?

- Trivialen Änderungen (eine Zeile korrigieren, Typo fixen, ein File lesen)
- Kurze Analyse- oder Lese-Aufgaben ohne Schreibzugriff
- Reine Rückfragen

Bei **allem, was mehrere Dateien anfasst oder neue Strukturen anlegt**, lohnt sich `/create-plan`.

### Der Workflow im Überblick

```
1. Aufgabe fällt dir ein
2. /create-plan <knappe Beschreibung>
3. Plan lesen, Fragen/Korrekturen mit Claude besprechen
4. /implement plans/YYYY-MM-DD-<name>.md
5. Validieren (Plan enthält Checkliste)
6. /shutdown am Session-Ende
```

---

## 6. Shell-Aliase

Zwei Aliase beschleunigen den Start. Details in `shell-aliases.md`. Kurzfassung:

```bash
alias cs='claude "/prime"'                                  # Sichere Variante
alias cr='claude --dangerously-skip-permissions "/prime"'   # Schnelle Variante
```

Beide starten Claude und führen `/prime` automatisch aus. `cs` fragt bei sensiblen Aktionen, `cr` vertraut Claude blind — nutze `cr` nur, wenn du weißt was du tust.

---

## 7. Troubleshooting

### „Claude weiß meinen Namen nicht / antwortet generisch"

→ `context/` ist leer oder nicht ausgefüllt. Prüfe, ob alle `<!-- AUSFÜLLEN -->`-Marker beantwortet sind, und rufe `/prime` erneut auf.

### „Mein Command wird nicht gefunden"

→ Du bist im falschen Ordner. `/prime` und andere Commands funktionieren nur, wenn du Claude im Wurzelverzeichnis des Workspaces gestartet hast.

### „Ich will einen Subagent, weiß aber nicht wie"

→ Siehe `.claude/agents/README.md` und die offizielle Doku <https://docs.claude.com/en/docs/claude-code/sub-agents>. Für den Start reicht eine Markdown-Datei mit YAML-Frontmatter in `.claude/agents/`.

### „Mein Plan war schlecht, Claude hat Mist gebaut"

→ Das ist ein Feature, kein Bug. Plan lesen **bevor** `/implement` — das ist der ganze Punkt. Plan anpassen, dann erneut implementieren.

### „Wie viele Workspaces soll ich haben?"

→ So viele wie Themen. Ein einziger Workspace für alles wird zum Chaos — thematische Trennung hält den Kontext scharf. Faustregel: Wenn ein Thema mehrere Sessions ziehen wird, bekommt es einen eigenen Workspace.

### „Muss ich `/shutdown` am Ende ausführen?"

→ Nein, aber empfohlen. Der Command räumt temporäre Dateien auf, aktualisiert `CLAUDE.md` bei Änderungen und committet/pusht (falls Git konfiguriert ist). Ohne `/shutdown` bleibt der Workspace auch nicht zurück — aber es ist ein sauberer Abschluss.

---

_Fragen zum Workspace oder Verbesserungsvorschläge gehören in deine `notes/`-Datei (falls du eine anlegst) oder in einen `/create-plan`, der die Vorlage erweitert._
