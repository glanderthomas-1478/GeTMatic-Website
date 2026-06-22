# Subagents (optional)

Dieser Ordner ist der Ablageort für **projektlokale Subagents**. Er ist bewusst leer — Subagents sind kein Pflichtbestandteil des Workspaces, sondern ein optionales Werkzeug.

## Was ist ein Subagent?

Ein Subagent ist ein **spezialisierter KI-Assistent innerhalb dieses Workspaces**. Er hat:

- einen **eigenen System-Prompt** (klare Aufgabe, z.B. „Code-Reviewer", „Recherche-Agent", „Test-Runner")
- ein **eigenes Kontext-Fenster**, getrennt vom Haupt-Claude
- **eigene Tool-Restrictions** (z.B. nur Lesen, oder nur bestimmte Befehle)

Der Haupt-Claude ruft Subagents bei passenden Aufgaben auf. Sie „entlasten" den Hauptkontext und bringen spezialisiertes Verhalten mit.

## Wann brauchst du einen Subagent?

Erst dann, wenn eine **wiederkehrende Teilaufgabe** auftaucht, die du immer gleich beschreibst (z.B. „Lies diesen Code und prüfe auf Sicherheitslücken"). Solange du das nicht hast, lass den Ordner leer.

Ein Subagent ist **nicht dasselbe** wie ein eigener Workspace. Siehe `ONBOARDING.md` im Wurzelverzeichnis für den Unterschied.

## Format

Jeder Subagent ist eine Markdown-Datei mit YAML-Frontmatter:

```markdown
---
name: mein-reviewer
description: Prüft geänderten Code auf Qualität und Stil.
tools: [Read, Grep, Bash]
---

Du bist ein Code-Reviewer. Lies den geänderten Code in diesem Projekt ...
```

Dateiname frei wählbar, Endung `.md`.

## Doku

Offizielle Claude Code Subagent-Dokumentation:
<https://docs.claude.com/en/docs/claude-code/sub-agents>
