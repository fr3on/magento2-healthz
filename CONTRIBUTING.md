# Contributing to Magento 2 Healthz

Thank you for your interest in contributing to this project!

## How to Contribute

1.  **Report Bugs**: If you find a bug, please create an issue on GitHub.
2.  **Suggest Features**: Have an idea? Create an issue to discuss it.
3.  **Submit Pull Requests**:
    *   Fork the repository.
    *   Create a feature branch (`git checkout -b feature/my-feature`).
    *   Ensure all code follows the project's standards.
    *   Run tests (`vendor/bin/phpunit`).
    *   Commit your changes (`git commit -m 'Add some feature'`).
    *   Push to the branch (`git push origin feature/my-feature`).
    *   Create a Pull Request.

## Standards

-   Follow PSR-12 coding standard.
-   Ensure all checks implement `Fr3on\Healthz\Model\Check\CheckInterface`.
-   Add unit tests for any new checks or logic.
-   Keep controllers light and focused on JSON output.

## Code of Conduct

Please be respectful and professional in all interactions.
