# Contributing to FibProductLabel

First off, thank you for considering contributing to this plugin! It's people like you that make it better for everyone.

## 🛠 Local Development Setup

We use a Docker-based environment to ensure consistency.

1. **Start the environment**:
   ```bash
   docker-compose up -d
   ```
2. **Access the shell**:
   ```bash
   docker exec -it fib_product_label_dev bash
   ```
3. **Run Quality Tools**:
   Before submitting a PR, please ensure all checks pass:
   ```bash
   composer run csfixer:check
   composer run phpstan
   composer run phpunit
   ```

## 📜 Coding Standards

To keep the codebase clean and maintainable, please follow these rules:

- **PHP Standards**: We follow PSR-12 and Shopware 6 coding conventions.
- **Static Analysis**: PHPStan must pass at **Level Max**.
- **Naming**: Use descriptive names for variables and methods. Comments should explain the *why*, not the *what*.
- **Commits**: Please use descriptive commit messages.

## 🧪 Pull Request Process

1. **Branching**: Create a feature branch from `master` (e.g., `feature/your-feature-name`).
2. **Tests**: Add unit or integration tests for any new logic or bug fixes.
3. **Documentation**: If you change features, update the `README.md` or `ARCHITECTURE.md` accordingly.
4. **Self-Review**: Run the quality tools locally one last time before pushing.

## 🛡 Security

If you find a security vulnerability, please refer to our [SECURITY.md](./SECURITY.md) and do not open a public issue.

---
Thank you for your help in making this plugin better!
