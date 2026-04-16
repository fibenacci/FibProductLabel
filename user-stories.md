# User Stories – FibProductLabel

Each story includes acceptance criteria that must be directly covered by tests. Tests are written **before or in parallel with implementation** (TDD/BDD).

---

## US-01: Creating a Label

**User Story Narrative**
As a: Shop Owner
Action: Create a new product label in the administration panel
So that: I can visually identify products and highlight specific features to customers.

**User Acceptance Criteria**
Persona: Shop Administrator
Given: I am logged into the administration panel and have the `fib_product_label.creator` permission.
When: I navigate to the Product Label module, fill in the mandatory "Name" (translated), select a "Color" using the hex code picker, and set a "Priority" and "Validity Period".
Then: The label is saved in the database, a success message is displayed, and I am redirected to the details view of the newly created label.

---

## US-02: Assigning Labels to Products

**User Story Narrative**
As a: Shop Owner
Action: Assign labels to specific products on their detail pages
So that: Those products are correctly categorized and identified with the intended visual labels.

**User Acceptance Criteria**
Persona: Content Manager
Given: A product exists and at least one active product label is available in the system.
When: I open the "Product Labels" tab on the product detail page and select one or more labels from the multi-select field and save the product.
Then: The assignments are correctly persisted in the system, and I can see the selected labels assigned to the product upon reloading.

---

## US-03: Displaying Labels in the Storefront

**User Story Narrative**
As a: Customer
Action: View product labels on product cards in listings and in the Buy widget
So that: I can quickly identify product features, promotions, or special statuses.

**User Acceptance Criteria**
Persona: Storefront Customer
Given: I am browsing the storefront and looking at products that have active labels within their validity period assigned.
When: I view a product listing page or a product detail page.
Then: I see the assigned labels displayed with their configured color, sorted by priority (descending), and only those that are currently active and valid.

---

## US-04: Automatically Deactivate Expired Labels

**User Story Narrative**
As a: Shop Owner
Action: Have the system automatically deactivate labels once their validity period has passed
So that: I don't have to manually monitor and disable expired promotions or labels.

**User Acceptance Criteria**
Persona: System / Cronjob
Given: There are active product labels in the system whose "Valid To" date is in the past.
When: The scheduled task `fib_product_label.deactivate_expired` is executed.
Then: All expired labels are set to `active = false` in the database, and labels without a "Valid To" date remain untouched.

---

## US-05: Cache Invalidation on Label Changes

**User Story Narrative**
As a: Shop Owner
Action: Automatically invalidate the cache for affected products and categories when labels change
So that: Customers always see the most up-to-date label information without sacrificing overall site performance.

**User Acceptance Criteria**
Persona: System
Given: Products and listings are cached to ensure high performance.
When: A product label is updated or its assignment to a product or category is changed.
Then: Only the cache tags associated with the affected products and categories are invalidated, while the rest of the cache remains intact.
