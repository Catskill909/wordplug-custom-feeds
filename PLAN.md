# WordPress Custom REST API Feed Plugin Plan

---

## 📝 Work Log & Feature Status (as of 2025-04-25)

### Current Status
- **All core plugin functions now work!**
- Text, Media, and Toggle fields can be added, edited, and deleted dynamically.
- Delete buttons and media uploader are fully functional.
- Toggle field logic works, but UI needs improvement.

### UI/UX Improvement Tasks (Expert Designer Refresh)
- [ ] **Feed Title Field:** Shrink the input box width and improve placeholder behavior (placeholder should disappear when typing, label should be clear and accessible).
- [ ] **Button Colors:** Update button colors for better readability and contrast, especially on "Add" and "Remove" buttons.
- [ ] **Toggle Switches:** Replace the current toggle UI with a visible switch that clearly shows "ON" and "OFF" states, and uses those text values (not just true/false or "-").
- [ ] **General Styling:** Apply a modern, visually engaging design throughout the admin UI. Improve spacing, font sizes, and overall layout for clarity and usability.
- [ ] **Accessibility:** Ensure all UI controls are accessible and screen-reader friendly.

**Comprehensive Step-by-Step UI/UX Improvement Plan:**

**Step 1: Field Sizing and Label/Placeholder Clarity**

**Implementation:**
- Wrapped the Feed Title field in a `.feed-title-wrapper` div for targeted styling.
- Removed `style="width: 100%"` from the label and set width via CSS.
- Ensured the MDC floating label is present and accessible.
- Added custom CSS in `admin/css/admin-style.css` to set max-width and responsiveness for the field.

**Testing Checklist:**
- [ ] Field is visually balanced and not overly wide.
- [ ] Label floats above input when typing or focused, never overlapping.
- [ ] Placeholder text is clear and disappears on focus.
- [x] Document all recent changes and fixes in PLAN.md

**2025-04-26:**
- Documented and reviewed the `add_admin_menu()` method in `class-admin-menu.php`.
- Added a docblock and improved inline comments for clarity and maintainability.

Proceed to Step 2 after confirming checklist.

**Step 2: Button Color and Contrast**
- [ ] Update all buttons (Add, Remove, Create Feed, etc.) to use Material Design color classes with high contrast (e.g., primary for main actions, error for remove/delete).
- [ ] Ensure button text is always readable against the background.
- [ ] Test: Visually inspect buttons in light/dark modes and confirm accessibility contrast ratios.

**Step 3: Toggle Switch Redesign**
- [ ] Replace the current toggle UI with a true Material Design Switch component.
- [ ] Add visible "ON" and "OFF" labels next to the switch, and ensure the value saved is "on" or "off" (not true/false or "-").
- [ ] Test: Toggle switches update visually and in saved config, and are obvious to all users.

**Step 4: Layout, Spacing, and Grouping**
- [ ] Use cards or panels to group related field types (text, media, toggles) for visual clarity.
- [ ] Add consistent spacing between sections and controls.
- [ ] Use responsive design so the admin UI works on all screen sizes.
- [ ] Test: UI remains usable and visually appealing on desktop, tablet, and mobile.

**Step 5: General Styling and Visual Polish**
- [ ] Apply a modern color palette and update font sizes for better readability.
- [ ] Add subtle shadows, dividers, or backgrounds to separate sections.
- [ ] Use icons where appropriate (e.g., trash for remove, plus for add).
- [ ] Test: UI feels modern, engaging, and visually distinct from default WordPress admin.

**Step 6: Accessibility**
- [ ] Ensure all interactive elements are keyboard accessible (tab, enter, space).
- [ ] Add ARIA labels/roles as needed for screen readers.
- [ ] Confirm color choices meet WCAG AA contrast standards.
- [ ] Test: Use keyboard and screen reader to complete all admin tasks.

**Step 7: User Feedback and Error Handling**
- [ ] Add inline error messages for required fields and invalid input.
- [ ] Show success messages/snackbars after saving or deleting.
- [ ] Test: Users always know when actions succeed or fail.

**Step 8: Iterative Testing and Feedback**
- [ ] After each step, test thoroughly and gather feedback from real users or stakeholders.
- [ ] Adjust the plan as needed based on usability findings.

### Bug Fixes

### [2025-04-26] Session Review & Outstanding Issues

#### Progress This Session
- **Major UI Cleanup:** Removed all duplicate legacy/un-styled field sections from feed-edit-page.php. Only Material Design (MDC) styled controls remain.
- **Button Consistency:** All "Remove" buttons now use a unified `.remove-feed-item-button` class for solid red background, white text, and strong contrast. MDC/inline styles removed for clarity and accessibility.
- **Bug Fix:** Feed config now loads correctly when editing an existing feed. Defensive checks and debug logging added to prevent data loss (see above).
- **Text Fields:** Text fields save/load as expected in both create and edit views.

#### Newly Discovered Bugs/Styling Issues
- **Toggle Switches:** Toggles added in the create feed view do not appear when editing that feed. (Data not loaded or not rendered in edit view.)
- **Media Fields:** Media fields appear in edit view, but the current image/file name is not displayed (no visual feedback of what is selected).
- **General:** Some minor style inconsistencies remain (e.g., spacing, label alignment).

#### Next Steps / Action Items
- [ ] Fix: Ensure toggle switches are loaded and rendered correctly in the edit feed view.
- [ ] Fix: Display the current image/file name for each media field in the edit view.
- [ ] **UI/UX:** Redesign the toggle switch interface to use a Material Design HTML/CSS toggle (as shown in the reference image). When off, display the word "off" next to the switch. The value stored in the feed config must be exactly "on" or "off" (not true/false).
- [ ] Review: Audit and polish spacing, label alignment, and general visual polish for all field types.
- [ ] Test: Confirm all field types (text, media, toggles) save and load as expected for both new and existing feeds.
- [ ] Accessibility: Review keyboard/screen-reader accessibility for all controls.
- [ ] Document: Continue updating PLAN.md as bugs are fixed and features improved.

### [2025-04-26] Feed Edit Data Loss Bug
- Fixed a critical bug where saved feed configuration was not loaded when editing an existing feed.
- Added defensive checks in both `render_edit_page()` and `handle_save_feed()` to ensure `$feed_config_raw` is always an array, preventing data loss if the meta or POST data is missing or malformed.
- Added debug logging (when `WP_DEBUG` is enabled) in `handle_save_feed()` to log the incoming and sanitized config for easier future troubleshooting.
- Rationale: Ensures the UI always loads the correct fields and prevents accidental data wipes due to unexpected data types.

### [2025-04-26] Admin UI/UX Improvements
- Fixed floating label for Feed Title: label now floats when field is pre-filled or on focus, and placeholder is removed to prevent overlap with user-typed text.
- Softened Remove button color to Material error color with improved contrast and accessibility.
- Improved toggle switch markup for accessibility: proper ARIA roles, keyboard navigation, and visual feedback.
- Added logic to prevent duplicate admin interface rendering by removing extra `.feed-edit-card` elements.
- Added focus outlines for Remove buttons and improved general accessibility.

### Current Bugs & Planned Fixes

- **Feed Title Placeholder:** Placeholder text for Feed Title overlaps with input text. The MDC floating label is not floating properly when the user types. **Planned Fix:** Ensure the label floats above when typing or when the field has a value.
- **Duplicate Admin Interface:** There are two interfaces in the admin panel. **Planned Fix:** Remove the duplicate unstyled interface, keep only the new Material Design styled interface.
- **Remove Button Styling:** The remove button for fields is too harsh (black on red) and not readable. **Planned Fix:** Restyle to a softer, more accessible color scheme (e.g., outlined red with white background, readable text).
- **Toggle Switch:** The toggle switch is not a real HTML switch. **Planned Fix:** Implement a real MDC/HTML switch that visually shows on/off and updates the field value accordingly.
- **Edit Feed Bug:** When editing a feed, no saved content is loaded into the form fields. **Planned Fix:** Ensure all saved data is pre-filled for editing, including text, media, and toggle fields.

### To-Do List / Next Dev Steps

- [ ] Continue with UI/UX improvements (button layout, styling, accessibility) and test all changes together after implementation.
- [x] **MAJOR BUG FIXED (2025-04-26):** Editing an existing feed now loads all saved data (title, fields, toggles, media) into the edit interface. The bug was caused by redundant variable initialization in the view, which was removed. Variables are now set in `render_edit_page()` and passed to the view so the form is properly pre-filled.
- [x] **UI/UX IMPROVEMENT (2025-04-26):** The Add New Feed button is now a modern, prominent Material Design button, centered above the table for improved clarity and user experience.
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