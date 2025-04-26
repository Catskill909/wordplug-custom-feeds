/**
 * Custom JavaScript for the WordPlug Custom Feeds admin interface.
 * Handles MDC Web component initialization and dynamic UI interactions.
 */
// Production-ready: no debug alerts or highlights
// Add your real event listeners and logic for the admin buttons below

console.log('JS loaded: admin-scripts.js');
// --- WordPlug Custom Feeds Admin Scripts ---
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const feedConfigContainer = document.getElementById('feed-config-container');
    if (!feedConfigContainer) {
        // Not present on this page (e.g., feed list) — skip attaching listeners.
        return;
    }
    console.log('[WordPlug] feed-config-container found. Attaching listeners.');

    const textFieldsList = document.getElementById('text-fields-list');
    const mediaFieldsList = document.getElementById('media-fields-list');
    const toggleSwitchesList = document.getElementById('toggle-switches-list');

    setTimeout(function () {
        const addTextFieldButton = document.getElementById('add-text-field-button');
        const addMediaFieldButton = document.getElementById('add-media-field-button');
        const addToggleSwitchButton = document.getElementById('add-toggle-switch-button');
        if (addTextFieldButton) {
            addTextFieldButton.addEventListener('click', function(e) {
                console.log('[WordPlug] Add Text Field button pressed');
                addTextField(e);
            });
            console.log('[WordPlug] Listener attached: addTextFieldButton');
        } else {
            console.warn('[WordPlug] add-text-field-button NOT FOUND');
        }
        if (addMediaFieldButton) {
            addMediaFieldButton.addEventListener('click', function(e) {
                console.log('[WordPlug] Add Media Field button pressed');
                addMediaField(e);
            });
            console.log('[WordPlug] Listener attached: addMediaFieldButton');
        } else {
            console.warn('[WordPlug] add-media-field-button NOT FOUND');
        }
        if (addToggleSwitchButton) {
            addToggleSwitchButton.addEventListener('click', function(e) {
                console.log('[WordPlug] Add Toggle Switch button pressed');
                addToggleSwitch(e);
            });
            console.log('[WordPlug] Listener attached: addToggleSwitchButton');
        } else {
            console.warn('[WordPlug] add-toggle-switch-button NOT FOUND');
        }
    }, 0);

    // Remainder of the original logic (helper functions, removeItem, etc.) remains unchanged

    /**
     * Finds the next available index for a new item in a list.
     * Accounts for items potentially being removed.
     * @param {HTMLElement} listContainer - The container element holding the items.
     * @returns {number} The next available index.
     */
    function getNextIndex(listContainer) {
        if (!listContainer) return 0;
        const items = listContainer.querySelectorAll('.feed-config-item');
        let maxIndex = -1;
        items.forEach(item => {
            const index = parseInt(item.dataset.index, 10);
            if (!isNaN(index) && index > maxIndex) {
                maxIndex = index;
            }
        });
        return maxIndex + 1;
    }

    /**
     * Removes the placeholder paragraph if it exists.
     * @param {HTMLElement} listContainer - The container element.
     */
    function removePlaceholder(listContainer) {
        if (!listContainer) return;
        const placeholder = listContainer.querySelector('p.no-items-placeholder');
        if (placeholder) {
            placeholder.remove();
        }
    }

    /**
     * Adds a placeholder paragraph if the list is empty (only contains non-element nodes or the placeholder itself).
     * @param {HTMLElement} listContainer - The container element.
     * @param {string} placeholderText - The text for the placeholder.
     */
    function addPlaceholderIfEmpty(listContainer, placeholderText) {
        if (!listContainer) return;
        // Check if there are any element children other than the placeholder itself
        const hasRealChildren = Array.from(listContainer.children).some(
            child => child.nodeType === 1 && !child.classList.contains('no-items-placeholder')
        );
        if (!hasRealChildren) {
            // Remove any existing placeholder first to avoid duplicates
            removePlaceholder(listContainer);
            // Add the new placeholder
            const placeholder = document.createElement('p');
            placeholder.classList.add('no-items-placeholder');
            placeholder.innerHTML = `<i>${placeholderText}</i>`;
            listContainer.appendChild(placeholder);
        }
    }

    // --- Text Field Functions ---

    /**
     * Adds a new text field row.
     */
function addTextField() {
    console.log('addTextField called');
        if (!textFieldsList) return;
        removePlaceholder(textFieldsList);

        const index = getNextIndex(textFieldsList);
        const newItem = document.createElement('div');
        newItem.classList.add('feed-config-item');
        newItem.dataset.index = index;
        newItem.style.marginBottom = '15px';
        newItem.style.padding = '10px';
        newItem.style.border = '1px solid #eee';

        // Use textarea for the value field
        newItem.innerHTML = `
			<div style="display: flex; align-items: flex-start; gap: 10px; margin-bottom: 10px;">
				<label style="flex-grow: 1;">
					<span>Field Key</span>
					<input type="text" name="feed_config[text_fields][${index}][key]" required>
				</label>
				<label style="flex-grow: 2;">
					<span>Field Value</span>
					<textarea name="feed_config[text_fields][${index}][value]" rows="3"></textarea>
				</label>
			</div>
			<button type="button" class="remove-feed-item-button" style="background-color: red;">
				<span>Remove</span>
			</button>
		`;
        textFieldsList.appendChild(newItem);
    }

    // --- Media Field Functions ---

    /**
     * Adds a new media field row.
     */
function addMediaField() {
    console.log('addMediaField called');
        if (!mediaFieldsList) return;
        removePlaceholder(mediaFieldsList);

        const index = getNextIndex(mediaFieldsList);
        const newItem = document.createElement('div');
        newItem.classList.add('feed-config-item');
        newItem.dataset.index = index;
        newItem.style.marginBottom = '15px';
        newItem.style.padding = '10px';
        newItem.style.border = '1px solid #eee';

        newItem.innerHTML = `
			<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
				<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--dense" data-mdc-auto-init="MDCTextField" style="flex-grow: 1;">
					<span class="mdc-notched-outline">
						<span class="mdc-notched-outline__leading"></span>
						<span class="mdc-notched-outline__notch">
							<span class="mdc-floating-label">Field Key</span>
						</span>
						<span class="mdc-notched-outline__trailing"></span>
					</span>
					<input type="text" class="mdc-text-field__input" name="feed_config[media_fields][${index}][key]" required>
				</label>
				<div class="media-field-controls" style="flex-grow: 2;">
					<input type="hidden" class="media-field-value" name="feed_config[media_fields][${index}][value]" value="">
					<button type="button" class="mdc-button mdc-button--outlined select-media-button">
						<span class="mdc-button__ripple"></span>
						<span class="mdc-button__label">Select Media</span>
					</button>
					<span class="selected-media-info" style="margin-left: 10px; font-style: italic;">No media selected</span>
				</div>
			</div>
			<button type="button" class="mdc-button mdc-button--outlined mdc-button--dense remove-feed-item-button" style="--mdc-theme-primary: red;">
				<span class="mdc-button__ripple"></span>
				<span class="mdc-button__label">Remove</span>
			</button>
		`;
        mediaFieldsList.appendChild(newItem);
        mdc.autoInit(newItem); // Initialize MDC components within the new item
    }

    /**
     * Opens the WordPress Media Uploader.
     * @param {Event} e - The click event object.
     */
    function openMediaUploader(e) {
        // The button is the target of the delegated event listener
        const button = e.currentTarget; // The element the listener was attached to (document)
        const targetButton = e.target.closest('.select-media-button'); // The actual button clicked

        if (!targetButton) return; // Click wasn't on a select media button or its child

        const itemContainer = targetButton.closest('.feed-config-item');
        if (!itemContainer) return;
        const valueInput = itemContainer.querySelector('.media-field-value');
        const infoSpan = itemContainer.querySelector('.selected-media-info');
        if (!valueInput || !infoSpan) return;

        // Ensure wp.media exists
        if (typeof wp === 'undefined' || typeof wp.media === 'undefined') {
            console.error('WordPress media uploader script (wp.media) not loaded or available.');
            alert('Media uploader is not available. Please ensure WordPress media scripts are enqueued for this page.');
            return;
        }

        // Create a new media frame
        let mediaUploader = wp.media({
            title: 'Select Media',
            button: {
                text: 'Use this media'
            },
            multiple: false // Only allow single selection
        });

        // When a file is selected, run a callback.
        mediaUploader.on('select', function () {
            const attachment = mediaUploader.state().get('selection').first().toJSON();
            valueInput.value = attachment.url; // Store the URL
            // Display filename, fallback to truncated URL
            const filename = attachment.filename || attachment.url.split('/').pop();
            infoSpan.textContent = filename.length > 30 ? filename.substring(0, 27) + '...' : filename; // Truncate long names
            infoSpan.title = attachment.url; // Set title attribute for full URL on hover
        });

        // Open the uploader
        mediaUploader.open();
    }


    // --- Toggle Switch Functions ---

    /**
     * Adds a new toggle switch row.
     */
function addToggleSwitch() {
    console.log('addToggleSwitch called');
        if (!toggleSwitchesList) return;
        removePlaceholder(toggleSwitchesList);

        const index = getNextIndex(toggleSwitchesList);
        const newItem = document.createElement('div');
        newItem.classList.add('feed-config-item');
        newItem.dataset.index = index;
        newItem.style.marginBottom = '15px';
        newItem.style.padding = '10px';
        newItem.style.border = '1px solid #eee';

        newItem.innerHTML = `
			<div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
				<label class="mdc-text-field mdc-text-field--outlined mdc-text-field--dense" data-mdc-auto-init="MDCTextField" style="flex-grow: 1;">
					<span class="mdc-notched-outline">
						<span class="mdc-notched-outline__leading"></span>
						<span class="mdc-notched-outline__notch">
							<span class="mdc-floating-label">Toggle Key</span>
						</span>
						<span class="mdc-notched-outline__trailing"></span>
					</span>
					<input type="text" class="mdc-text-field__input" name="feed_config[toggles][${index}][key]" required>
				</label>
				<div class="mdc-form-field" style="flex-grow: 0;">
					<div class="mdc-switch mdc-switch--unselected" data-mdc-auto-init="MDCSwitch">
						<div class="mdc-switch__track"></div>
						<div class="mdc-switch__handle-track">
							<div class="mdc-switch__handle">
								<div class="mdc-switch__shadow">
									<div class="mdc-elevation-overlay"></div>
								</div>
								<div class="mdc-switch__ripple"></div>
								<div class="mdc-switch__icons">
									<svg class="mdc-switch__icon mdc-switch__icon--on" viewBox="0 0 24 24"><path d="M19.69,5.23L8.96,15.96l-4.23-4.23L2.96,13.5l6,6L21.46,7L19.69,5.23z"/></svg>
									<svg class="mdc-switch__icon mdc-switch__icon--off" viewBox="0 0 24 24"><path d="M20 13H4v-2h16v2z"/></svg>
								</div>
							</div>
						</div>
					</div>
					<label style="margin-left: 10px;">Default Off / On</label>
					<!-- Hidden input to store the actual value (on/off) -->
					<input type="hidden" class="toggle-value" name="feed_config[toggles][${index}][value]" value="off">
				</div>
			</div>
			<button type="button" class="mdc-button mdc-button--outlined mdc-button--dense remove-feed-item-button" style="--mdc-theme-primary: red;">
				<span class="mdc-button__ripple"></span>
				<span class="mdc-button__label">Remove</span>
			</button>
		`;
        toggleSwitchesList.appendChild(newItem);
        mdc.autoInit(newItem); // Initialize MDC components

        // Attach listener specifically to the new switch's instance
        const switchElement = newItem.querySelector('.mdc-switch');
        if (switchElement && switchElement.MDCSwitch) {
            switchElement.MDCSwitch.listen('change', handleSwitchChange);
        } else {
            console.warn('Could not find or initialize MDCSwitch instance for new toggle:', newItem);
        }
    }

    /**
     * Handles the change event for a toggle switch.
     * Updates the hidden input value.
     * @param {Event} event - The MDC Switch change event, where event.target is the MDCSwitch instance.
     */
    function handleSwitchChange(event) {
        const switchInstance = event.target; // MDCSwitch instance
        if (!switchInstance || typeof switchInstance.selected === 'undefined') {
            console.error('Invalid switch event target:', event.target);
            return;
        }

        // Find the closest parent item container
        const itemContainer = switchInstance.root.closest('.feed-config-item');
        if (itemContainer) {
            const hiddenInput = itemContainer.querySelector('.toggle-value');
            if (hiddenInput) {
                hiddenInput.value = switchInstance.selected ? 'on' : 'off';
                // console.log(`Switch ${itemContainer.dataset.index} changed to: ${hiddenInput.value}`); // Optional debug log
            } else {
                console.error('Could not find hidden input for switch:', itemContainer);
            }
        } else {
            // This might happen if the switch isn't nested correctly
            console.error('Could not find item container for switch:', switchInstance.root);
        }
    }

    // --- General Functions ---

    function removeItem(e) {
        const targetButton = e.target.closest('.remove-feed-item-button');
        if (!targetButton) {
            console.warn('[WordPlug] removeItem: No .remove-feed-item-button found from event target', e.target);
            return;
        }
        const itemToRemove = targetButton.closest('.feed-config-item');
        if (!itemToRemove) {
            console.warn('[WordPlug] removeItem: No .feed-config-item ancestor found for remove button', targetButton);
            return;
        }
        const listContainer = itemToRemove.parentElement;
        console.log('[WordPlug] Removing item:', itemToRemove, 'from container:', listContainer);
        itemToRemove.remove();
        // Add placeholder back if list becomes empty
        if (listContainer) {
            if (listContainer.id === 'text-fields-list') {
                addPlaceholderIfEmpty(listContainer, 'No text fields added yet.');
            } else if (listContainer.id === 'media-fields-list') {
                addPlaceholderIfEmpty(listContainer, 'No media fields added yet.');
            } else if (listContainer.id === 'toggle-switches-list') {
                addPlaceholderIfEmpty(listContainer, 'No toggle switches added yet.');
            }
        }
    }

    // Use event delegation for remove buttons and media buttons on the container
    // This handles clicks on items present initially or added later
    feedConfigContainer.addEventListener('click', function (e) {
        console.log('[WordPlug] feedConfigContainer click handler fired', e.target);
        // Handle Remove Button clicks
        const removeBtn = e.target.closest('.remove-feed-item-button');
        if (removeBtn) {
            console.log('[WordPlug] Remove button clicked', removeBtn);
            removeItem(e);
        }
        // Handle Select Media Button clicks
        if (e.target.closest('.select-media-button')) {
            openMediaUploader(e);
        }
    });

    // Attach listeners directly to existing switches rendered by PHP
    // This needs to happen *after* mdc.autoInit() has run and component instances are created
    if (toggleSwitchesList) {
        toggleSwitchesList.querySelectorAll('.mdc-switch').forEach(switchElement => {
            // Check if the MDC component instance exists on the element
            if (switchElement.MDCSwitch) {
                // Attach the listener using the instance's method
                switchElement.MDCSwitch.listen('change', handleSwitchChange);
            } else {
                // This might happen if autoInit failed or the element structure is wrong
                console.warn('MDCSwitch instance not found on initial switch element:', switchElement);
            }
        });
    }

    // Check initial placeholders on load
    addPlaceholderIfEmpty(textFieldsList, 'No text fields added yet.');
    addPlaceholderIfEmpty(mediaFieldsList, 'No media fields added yet.');
    addPlaceholderIfEmpty(toggleSwitchesList, 'No toggle switches added yet.');


}); // End DOMContentLoaded