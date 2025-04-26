# Debugging Broken Buttons in WordPress Plugin Admin UI

## Problem Statement
- **Buttons titled:** "Add Text Field", "Add Media Field", "Add Toggle Switch" do nothing when clicked in the admin UI.
- **No errors** in the browser console.
- **No console logs** from event handlers, even after adding `console.log` to JS functions.
- **You can create feeds, but cannot add fields/toggles dynamically.**

---

## Step-by-Step Debug Checklist

### 1. Are We Targeting the Right Buttons?
- **Button Labels in HTML:**
  - "Add Text Field"
  - "Add Media Field"
  - "Add Toggle Switch"
- **Button IDs in HTML:**
  - `add-text-field-button`
  - `add-media-field-button`
  - `add-toggle-switch-button`
- **Are these the buttons you see in the DOM?**
  - Use browser dev tools: Inspect the button and confirm the ID matches.

### 2. Is the JS File Loaded?
- Open browser Dev Tools > Network tab > Filter by JS.
- Refresh the admin page.
- **Is `admin-scripts.js` loaded?**
  - What is its size? (Should not be 0 bytes)
  - Any 404s?
- Open the Sources tab, search for `addTextField` in loaded scripts.

### 3. Are Event Listeners Being Attached?
- In Dev Tools console, run:
  ```js
  typeof addTextField
  typeof addMediaField
  typeof addToggleSwitch
  ```
  - Should return `function` for each if handlers are global.
- Add `console.log('listener attached')` right after each `addEventListener` in your code.
- Reload and check if you see these logs.

### 4. Are You Using the Correct Event Type and Attachment?
- Are you using `addEventListener('click', ...)` on the right button element?
- Is the button disabled, covered, or inside a form that is being submitted?

### 5. Is There a JavaScript Error Before Listener Attachment?
- Add a `console.log('JS loaded')` at the very top of `admin-scripts.js`.
- If you do not see this log, the file is not loaded or is failing early.

### 6. Is the JS File Loaded on the Correct Page?
- Is the enqueue logic conditional on a specific admin page slug?
- Try forcing the script to load on ALL admin pages as a test.

### 7. Are There Conflicting Scripts or Styles?
- Is another script removing or replacing the buttons?
- Is MDC or another library re-rendering the DOM after your listeners attach?

### 8. Is the Button Actually Clickable?
- In Dev Tools, right-click the button > Force Element State > :active.
- Try triggering the click handler manually in the console:
  ```js
  document.getElementById('add-text-field-button').click()
  ```
- Does this trigger anything?

---

## Next Steps
1. **Confirm the button IDs in the DOM match the IDs in your JS.**
2. **Confirm `admin-scripts.js` is loaded and executed on the page.**
3. **Add a `console.log('JS loaded')` at the top of your JS file and reload.**
4. **Check for any early returns or fatal errors in the JS file.**
5. **Try attaching a click handler directly from the browser console:**
   ```js
   document.getElementById('add-text-field-button').addEventListener('click', function(){alert('clicked!')})
   ```
   - Click the button. If you see the alert, the DOM and browser are fine, but your plugin JS is not running or not attaching the handler.

---

## If Nothing Works
- The issue is almost certainly that your JS file is NOT being loaded or executed on this page.
- Double-check the PHP `wp_enqueue_script` logic and file paths.
- Try hardcoding the enqueue to always run on all admin pages as a test.
- If you still see nothing, try renaming the JS file, clearing browser cache, and reloading.

---

## Attachments
- Screenshot of the admin page UI (provided)

---

## Summary
If you follow these steps and still cannot get a simple button click to trigger **any** JS, your JS file is not loaded or executed. This is a script loading/enqueue issue, not an event or DOM issue.

---

*Update this file with your findings as you debug further.*
