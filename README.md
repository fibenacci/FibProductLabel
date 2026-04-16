# Fib Product Label Plugin for Shopware 6

## 🚀 Overview
The **Fib Product Label** plugin allows shop administrators to create, manage, and assign labels (e.g., "New", "Sale", "Exclusive") to products. These labels are displayed prominently in the storefront listing and on product detail pages, following priority and validity date rules.

---

## 🛠 Design Decisions & Architecture

### 1. Implementation Rationale
The current architecture is designed for scalability, performance, and developer efficiency:

- **ManyToMany Association for Assignments**: To handle large catalogs, I implemented a true M2M relation instead of simple custom fields. This ensures that indexing remains performant even with thousands of products and allows for complex database-level queries (e.g., "Give me all products with label X").
- **Encapsulated Visibility Logic**: The `ProductLabelVisibilityService` centralizes all filtering (active status, date ranges) and sorting logic. By separating this from the Storefront Subscriber, the logic becomes easily testable and can be reused for future API endpoints or CLI commands.
- **Modern Shopware 6.6+ Frontend Architecture**: 
    - **Functional Date Filtering**: Since Shopware 6.6+ (Vue 3) no longer supports the pipe syntax for filters, I implemented the `dateFilter` as a computed helper. This ensures reliable and localized date formatting within the administration list view.
    - **Explicit Service Tagging**: To guarantee repository availability in a production-like container environment, the services are registered with explicit entity attributes.
- **Self-Contained Developer Experience (DX)**: The inclusion of a `docker-compose.yml` directly in the plugin root minimizes onboarding time. It provides a "ready-to-code" environment where all necessary steps (Admin-Build, Plugin-Activation) are automated.
- **Proactive Resource Management**: The `ScheduledTask` for deactivating expired labels ensures that the storefront doesn't have to perform expensive date calculations on every request for labels that have already expired.

### 2. Data Model (`fib_product_label`)
- **Prefixing**: I used the `fib_` prefix for the entity and database tables to prevent collisions with the Shopware core or other plugins.
- **ManyToMany Association**: As mentioned above, this enables high-performance indexing and efficient data handling.
- **Translation Support**: The `name` field is fully translatable via a separate `product_label_translation` table.

### 3. Business Logic (`ProductLabelVisibilityService`)
- **Separation of Concerns**: Visibility logic is isolated from event subscribers.
- **Date Handling**: Validity is checked against `DateTimeImmutable` for predictable behavior.

### 4. Storefront Integration
- **Twig Extensions**: Used `sw_extends` for surgical injection into `buy-widget` and `product-badges`.
- **BEM SCSS**: Styling follows the Block Element Modifier convention.

### 5. Bonus Features (Quality & Performance)
- **Cache Invalidation**: I implemented a `ProductLabelCacheInvalidatorSubscriber` following official [Shopware 6 caching guidelines](https://developer.shopware.com/docs/guides/plugins/plugins/framework/caching/).
    - **Why `EntityWrittenContainerEvent`?**: This is the most efficient way to capture all changes (creates, updates, deletes) across multiple entities (labels, translations, and product-assignments) in a single request.
    - **Verification Strategy**: I would verify the cache invalidation in two ways:
        - **Functional (Manual)**: Call a cached PDP or listing page, change a label or mapping in the administration, and then run `bin/console cache:clear:delayed`. This is necessary because Shopware processes invalidations delayed by default.
        - **Technical (Integration Test)**: Use an integration test against the `CacheInvalidator` to verify that the subscriber invalidates the expected cache tags (e.g., `product-{id}`) when changes occur.
    - **Performance**: By using the `CacheInvalidator` service and standard tag generation patterns, we ensure that storefront users always see the most up-to-date labels without sacrificing the performance benefits of the HTTP cache.
- **Scheduled Task**: A background task runs once a day to keep the database lean.

---

## 🧪 Testing Strategy

The plugin follows a multi-layer testing approach:
- **PHPUnit Integration**: Verifies DAL integrity.
- **PHPUnit Unit**: Validates business logic using mocks.
- **PHPStan (Max Level)**: Static analysis at the highest level to prevent type errors.
- **Playwright E2E**: Browser tests for administration and storefront flows.

---

## ⚙️ Setup & Installation

### Option 1: Quick Start (Docker - Recommended)
The plugin is self-contained. You can start a complete development environment directly from the plugin root:

1. **Start the environment**:
   ```bash
   docker-compose up -d
   ```
2. **Access the shop**: 
   Once the setup is complete (check logs with `docker logs -f fib_product_label_dev`), the shop is available at `http://localhost`. The plugin is automatically installed, activated, and the administration is built.

### Option 2: Manual Installation
If you already have a running Shopware 6 environment:

1. **Place the plugin** in `custom/plugins/FibProductLabel`.
2. **Install & Activate**:
   ```bash
   bin/console plugin:refresh
   bin/console plugin:install --activate FibProductLabel
   ```
3. **Build Administration** (to register the new module):
   ```bash
   bin/build-administration.sh
   ```
4. **Clear Cache**:
   ```bash
   bin/console cache:clear
   ```

---

## 📈 Future Improvements
Given more time, I would:
- **Bulk Actions**: Add support for bulk assignment of labels in the product list.
- **Dynamic Rules**: Integrate with the Shopware Rule Builder to automatically assign labels based on rules (e.g., "Stock < 10").
- **UI Enhancements**: Implement a color preview in the product detail tab multi-select.
