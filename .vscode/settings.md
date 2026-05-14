- .vscode/settings.json is a project-local Visual Studio Code configuration
  file that applies editor settings only to that specific workspace, so
  everyone opening the repo gets consistent behavior (like lint rules,
  formatting defaults, language-specific options, and extension behavior)
  without changing their global VS Code preferences; in your case, it tells
  VS Code’s built-in CSS/SCSS/LESS validators to ignore unknown at-rules so
  Tailwind-specific directives such as @apply, @theme, and @source are not
  incorrectly flagged in this project.
