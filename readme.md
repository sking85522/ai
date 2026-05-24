# Hritik AI - Advanced Agentic Engine

An autonomous, modular AI engine built with PHP, designed to understand and execute complex software development tasks from natural language commands.

## ✨ Features

Hritik AI can perform a wide range of agentic tasks, including:

-   **🤖 Project Management:**
    -   `plan project <goal>`: Creates a complete project plan with milestones.
    -   `create project <name> with files <a,b,c>`: Scaffolds a new project structure.
    -   `deploy project <name>`: Packages and deploys a project.
-   **✍️ Code & Documentation:**
    -   `audit file <path>`: Reviews a file for quality and potential issues.
    -   `optimize file <path>`: Applies automated optimizations to code.
    -   `generate readme`: Generates a README file for the project.
    -   `document folder <name>`: Creates documentation for a specific folder.
-   **🔍 Research & Debugging:**
    -   `research <topic>`: Performs technical research on a given topic.
    -   `debug <error message>`: Analyzes an error and suggests a fix.
    -   `test file <path>`: Executes a PHP file as a test.
-   **👁️ System Visualization:**
    -   `show map`: Generates an ASCII tree of the project architecture.
    -   `audit system`: Checks the connections of core modules.
-   **🧠 Advanced Capabilities:**
    -   `evolve system`: Initiates a self-evolution process.
    -   `spawn agent <name> specialized in <domain>`: Creates new specialized AI agents.
    -   `collaborate <agent1>, <agent2> on <task>`: Manages collaboration between agents.
    -   `enable ai in <project_path>`: Injects an API bridge into another project.

## 🏗️ Architecture

The engine is designed with a modular and extensible architecture:

-   **`core/Engine/AgenticCore.php`**: The heart of the AI. It parses user commands and delegates tasks to the appropriate tool.
-   **`core/Tools/`**: A suite of specialized tools that handle the actual logic for file manipulation, deployment, documentation, planning, and more. Each tool is a self-contained class responsible for a specific domain.
-   **`modules/`**: The project integrates a powerful set of PHP libraries for scientific computing, machine learning, and data analysis, including:
    -   `numphp` (NumPy-like)
    -   `sciphp` (SciPy-like)
    -   `mlphp` (Machine Learning)
    -   `neuralphp` (Neural Networks)
    -   `pandaphp` (Pandas-like)
    -   ...and many more.

## � How to Run

To start interacting with Hritik AI, run the console application from your terminal:

```bash
php console.php
```

On this XAMPP setup, `php` may not be available in PATH. Use:

```bat
.\run_console.bat
```

Or run PHP directly:

```bat
H:\xampp\php\php.exe console.php
```

This will launch the interactive AI shell, where you can start giving commands.

## 🐍 Note on `hritik_ai_py`

The `hritik_ai_py` directory is a temporary helper module used during the development of the main PHP engine. It will be removed once the PHP version is feature-complete.
