# Fib Product Label Plugin

Manage and display product labels (e.g., "Sale", "New") with priority and validity logic.

## ✨ Features
- **Flexible Labels**: Custom names, colors, and priority.
- **Validity Periods**: Automatic display based on `validFrom` and `validTo`.
- **Performance**: High-speed indexing via M2M relations and granular cache invalidation.
- **Automation**: Daily scheduled task to deactivate expired labels.

## 💻 Developer Experience (DX)
- **Zero-Config Docker**: Run `docker-compose up -d` in the plugin root.
- **Composer Scripts**: 
    - `composer run phpstan` (Max Level)
    - `composer run phpunit`
    - `composer run csfixer`  
- **CI/CD**: Automatic validation and release creation via GitHub Actions.

## 🚀 Quick Start
```bash
# Start dev environment
docker-compose up -d

# Check progress
docker logs -f fib_product_label_dev
```
The plugin will be installed and the Administration built automatically.

## 📈 Future Improvements
- **Advanced Admin UI**: Make label list fully sortable and searchable.
- **Granular Visibility**: Configuration to show/hide labels in specific components (PDP, Listing, Search, Wishlist).
- **Rule Builder Integration**: Dynamic visibility based on complex conditions.
- **Icon Support**: Allow selecting and displaying Material Icons within labels.
- **Storefront Filtering**: Make labels filterable in product listings.
- **Elasticsearch (ES)**: Enable ES indexing for labels to ensure high-performance filtering.

---
*For technical details and architecture rationale, see [ARCHITECTURE.md](./ARCHITECTURE.md).*
