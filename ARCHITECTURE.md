# Technical Rationale - FibProductLabel

## Implementation Decisions

### Data Model
- **ManyToMany Association**: Chosen over custom fields for high-performance indexing and scalability.
- **Prefixing**: All entities and tables use the `fib_` prefix to prevent collisions.
- **Translations**: Fully translatable `name` field via `product_label_translation`.

### Visibility & Logic
- **ProductLabelVisibilityService**: Centralizes filtering (active, date ranges) and sorting (priority). Decoupled from subscribers for reusability.
- **DateTime Handling**: Uses `DateTimeImmutable` for thread-safe date comparisons.

### Cache Invalidation
- **Strategy**: Uses `EntityWrittenContainerEvent` for efficient change detection.
- **Verification**: 
    - **Manual**: Change label -> `bin/console cache:clear:delayed` -> verify storefront.
    - **Technical**: Integration test against `CacheInvalidator`.

### Infrastructure
- **Docker DX**: Standalone environment in plugin root for zero-config onboarding.
- **CI/CD**: `shopware-cli` integration for automated validation and ZIP creation.
