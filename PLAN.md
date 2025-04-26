# WordPress Custom REST API Feed Plugin Plan

---

## 📝 Work Log & Feature Status (as of 2025-04-25)

### Current Status
- **Text Field Creation:** Works fine. Fields can be added and saved.
- **Delete Button:** Present, but does nothing (needs implementation).
- **Media Field:**
    - Cannot choose any WordPress media (media uploader not working).
    - Media button does nothing (needs implementation).
- **Toggle Field:**
    - UI present, but not functional as a true toggle.
    - Should be a visual on/off toggle (MDC Switch or similar) and allow a field title.
    - Value should be 'on' or 'off'.

### To-Do List / Next Dev Steps
- [ ] Fix: The edit screen currently traps the user until they save. Improve the edit flow so users can easily navigate back to the main admin view without saving, or consider merging edit and list views for better UX. (New development task)
- [ ] Fix: The edit button for created feeds does not bring up saved content in the admin UI. The JSON feed is correct, but there is currently no way to edit existing feeds from the admin interface.
- [ ] Implement delete functionality for fields (delete button should remove the field row from UI and config).
- [ ] Implement WordPress Media Uploader integration for media fields (button should open media library, allow selection, and save URL/ID).
- [ ] Implement a visual toggle (on/off) for toggle fields using MDC Switch, and allow assigning a field title.
- [ ] Ensure all field types (text, media, toggle) can be added, edited, and deleted dynamically.
- [ ] Polish UI for better usability and feedback (error/success messages, loading states, etc).
- [ ] Test saving and loading of all field types in feed config meta.
- [ ] REST API: Ensure output JSON matches structure and includes all field types and values.
- [ ] Update documentation and help text in the admin UI as features are completed.

---

**Project Goal:** Create a WordPress plugin allowing administrators to define custom REST API endpoints (feeds). Each feed will output configurable text fields, media library URLs, and toggle switch states in a structured JSON format. The admin interface will use Material Components for the Web (MDC Web) for styling and provide a list of created feeds with clickable endpoint URLs.

**Key Decisions:**

1.  **Material Design:** Integrate Material Components for the Web (MDC Web) directly for the admin UI.
2.  **JSON Structure:**
    ```json
    {
      "title": "Feed Title",
      "fields": {
        "fieldName": "value",
        "mediaFieldName": "media_url"
      },
      "toggles": {
        "toggleName": "on" // or "off"
      }
    }
    ```
3.  **Security:** Endpoints will be publicly accessible.
4.  **Compatibility:** Target the latest stable WordPress version.

**Plan Details:**

1.  **Plugin Structure:**
    ```mermaid
    graph TD
        A[wordplug/] --> B(wordplug.php);
        A --> C(includes/);
        A --> D(admin/);
        A --> E(assets/);
        A --> F(languages/);

        C --> C1(class-custom-post-type.php);
        C --> C2(class-rest-api.php);
        C --> C3(helpers.php);

        D --> D1(class-admin-menu.php);
        D --> D2(views/);
        D --> D3(js/);
        D --> D4(css/);

        D2 --> D2a(feed-list-page.php);
        D2 --> D2b(feed-edit-page.php);

        D3 --> D3a(admin-scripts.js);
        D3 --> D3b(mdc-integration.js);

        D4 --> D4a(admin-styles.css);

        E --> E1(mdc-web/); % Placeholder for MDC Web library files
        E --> E2(images/);

        F --> F1(wordplug.pot);
    ```
    *   `wordplug.php`: Main plugin file (headers, activation/deactivation hooks).
    *   `includes/`: Core PHP classes (Custom Post Type, REST API logic).
    *   `admin/`: Admin-specific functionality (menu pages, views, JS, CSS).
    *   `assets/`: Frontend assets (MDC Web library, images).
    *   `languages/`: Translation files.

2.  **Technology Stack:**
    *   **Backend:** PHP (Latest stable WordPress coding standards).
    *   **Frontend (Admin):** JavaScript (ES6+), Material Components for the Web (MDC Web), CSS3.
    *   **WordPress APIs:** Settings API, REST API, Custom Post Types, Media Library API, Options API (potentially for storing feed configurations if not using CPT meta).

3.  **Data Storage:**
    *   Utilize a **Custom Post Type (CPT)** named `custom_feed`.
    *   **Feed Title:** Stored as the CPT `post_title`.
    *   **Configurable Fields & Toggles:** Stored as post meta associated with the `custom_feed` CPT. A structured format (e.g., serialized array or JSON) will be used to store the definitions (field names, types, toggle names) and their values for each feed post.
        *   *Meta Key Example:* `_feed_config` (stores field/toggle definitions)
        *   *Meta Key Example:* `_feed_values` (stores actual text/URL values and toggle states)

4.  **Admin Interface (MDC Web):**
    *   **Main Page:** Add a top-level admin menu item ("Custom Feeds"). This page will display a list of created feeds using MDC Data Table. Each row will show the Feed Title and the generated REST API endpoint URL (clickable, opens in new tab). An "Add New" button (MDC Button) will lead to the edit page.
    *   **Edit/Add Page:**
        *   Use MDC Text Fields for the Feed Title.
        *   Implement a dynamic section using JavaScript and MDC components (Buttons, Text Fields, Switches) to add/remove/configure:
            *   **Text Fields:** Input for field name/key, input for default value.
            *   **Media Fields:** Input for field name/key, MDC Button to launch the WordPress Media Uploader, display selected media URL/ID.
            *   **Toggle Switches:** Input for toggle name/key, MDC Switch component for default state (on/off).
        *   Save/Update button (MDC Button).

5.  **REST API Implementation:**
    *   Register a custom REST API namespace (e.g., `wordplug/v1`).
    *   Register dynamic routes based on the created feeds. A potential route structure: `/wp-json/wordplug/v1/feed/{feed_slug}` or `/wp-json/wordplug/v1/feed/{feed_id}`.
    *   The route callback function will:
        *   Retrieve the corresponding `custom_feed` post based on the slug or ID.
        *   Fetch the feed configuration and values from post meta.
        *   Construct the JSON output according to the defined structure.
        *   Set appropriate headers (e.g., `Content-Type: application/json`).
        *   Return the `WP_REST_Response`.
    *   Ensure the `permission_callback` returns `__return_true` for public accessibility.

6.  **Asset Enqueueing:**
    *   Conditionally enqueue MDC Web CSS and JS library files *only* on the plugin's admin pages.
    *   Enqueue custom admin JavaScript (`admin-scripts.js`, `mdc-integration.js`) for MDC component initialization and dynamic field management.
    *   Enqueue custom admin CSS (`admin-styles.css`) for layout and adjustments.

7.  **Development Steps:**
    1.  Set up the basic plugin structure and main file (`wordplug.php`).
    2.  Implement the `custom_feed` Custom Post Type (`includes/class-custom-post-type.php`).
    3.  Create the admin menu and basic list/edit page structure (`admin/class-admin-menu.php`, `admin/views/`).
    4.  Integrate MDC Web: Download/include the library, enqueue base CSS/JS (`admin/class-admin-menu.php`, `assets/mdc-web/`).
    5.  Build the Feed List page UI using MDC Data Table (`admin/views/feed-list-page.php`, `admin/js/admin-scripts.js`).
    6.  Develop the Feed Edit page UI:
        *   Title field (MDC Text Field).
        *   Dynamic sections for adding/managing Text, Media, and Toggle fields using MDC components and JavaScript (`admin/views/feed-edit-page.php`, `admin/js/admin-scripts.js`).
        *   Integrate WordPress Media Uploader.
    7.  Implement saving/updating feed configuration and values to post meta (`admin/class-admin-menu.php` or a dedicated save handler).
    8.  Implement the REST API endpoint registration and data retrieval logic (`includes/class-rest-api.php`).
    9.  Refine styling and JavaScript interactions (`admin/css/admin-styles.css`, `admin/js/`).
    10. Add internationalization support (`languages/`).
    11. Testing and Refinement.