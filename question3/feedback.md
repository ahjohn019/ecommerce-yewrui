# Order Feedback

## Index

### Before Changes
- `index()` fetched all orders with `Order::all()`.
- It looped through every order manually.
- It queried the user inside the loop with `User::find(...)`, which created an N+1 query problem.
- It returned a plain JSON response directly from the controller.
- It did not support pagination.
- It did not support query parameters or filters.
- Validation for index input was not separated into a request class.

### After Changes
- `index()` now uses `OrderIndexRequest` for validated query parameters.
- `index()` supports filters such as:
  - `user_id`
  - `status`
  - `min_total`
  - `max_total`
  - `from_date`
  - `to_date`
  - `per_page`
- The service now paginates the results instead of loading everything at once.
- The service applies filters in the query before pagination.
- The related `user` is eager-loaded with `with('user:id,name')`.
- The response uses `OrderResource` and the shared response service.

### Improvements Needed Before Changes
- Avoid loading all records at once for list endpoints.
- Avoid querying related models inside a loop.
- Use eager loading to reduce database queries.
- Move validation rules into a form request.
- Add pagination for better performance and API usability.
- Add filter support so clients can request only the data they need.
- Keep controller logic thin by moving query logic into a service.

## Create

### Before Changes
- `create()` accepted raw request data directly.
- Validation was not separated into a dedicated form request.
- The order insert was not wrapped in a transaction.
- It included an extra raw SQL query that did not contribute to the response.
- The controller handled too much of the request flow.

### After Changes
- `store()` now uses `StoreOrderRequest`.
- The controller passes validated data to the service.
- The service wraps the order creation in `DB::transaction(...)`.
- The unnecessary raw SQL query was removed.
- The created order response uses `OrderResource`.
- The response goes through the shared response service.

### Improvements Needed Before Changes
- Use a form request for validation.
- Remove unnecessary queries that slow down the request.
- Use a transaction to avoid partial writes if something fails.
- Keep the controller focused on orchestration, not business logic.

## Delete

### Before Changes
- `destroy()` deleted the order directly without transaction safety.
- It used a basic controller-level response.
- It did not use the shared response service pattern.
- The orders table did not include soft deletes.
- The `Order` model did not use the `SoftDeletes` trait.

### After Changes
- `destroy()` now uses the service layer.
- The delete operation is wrapped in `DB::transaction(...)`.
- The response uses the shared response service.
- The endpoint returns a consistent success message.
- The orders migration includes `softDeletes()`.
- The `Order` model now uses `SoftDeletes`.

### Improvements Needed Before Changes
- Use the service layer for consistency with the rest of the app.
- Wrap write operations in a transaction when failure should roll back.
- Keep API responses consistent across endpoints.
- Add soft deletes in the migration and model when records should be recoverable.

## Data Model

### Before Changes
- There was no orders migration in the project.
- The order table structure was incomplete for the controller and model.
- Soft delete support was missing from the database schema.

### After Changes
- A new orders migration was added with:
  - `user_id`
  - `total`
  - `status`
  - `timestamps()`
  - `softDeletes()`
- The `Order` model was updated to match the schema.
- The relationship to `User` is defined in the model.

### Improvements Needed Before Changes
- Keep the database schema aligned with the model and controller.
- Add soft delete support when records should not be removed permanently.

## Summary
- The refactor improves performance, readability, validation, and response consistency.
- It also makes the `index`, `create`, and `delete` actions easier to maintain and extend later.
- The data model now supports soft deletes for safer record management.
