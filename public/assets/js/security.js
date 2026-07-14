/**
 * GiftShop Security - Safe Clipboard Copy
 * Mitigates clipboard hijacking/poisoning attacks where hidden text containing newlines
 * or terminal commands is sneaked into copied contents.
 */
document.addEventListener('copy', function(e) {
    var selection = window.getSelection();
    if (!selection || selection.toString() === '') return;

    var text = selection.toString();

    // Identify if the active element is marked for safe copying
    var activeElement = document.activeElement;
    var isSafeCopyField = activeElement && (
        activeElement.classList.contains('safe-copy') || 
        activeElement.closest('.safe-copy') !== null ||
        activeElement.getAttribute('data-safe-copy') !== null
    );

    // If it's a designated safe-copy container or short copied text (like coupons/order numbers)
    if (isSafeCopyField || text.length < 500) {
        // Strip control characters, tabs, carriage returns, and newlines to prevent terminal auto-execute
        var sanitizedText = text.replace(/[\r\n\t\x00-\x1F]/g, ' ').replace(/\s+/g, ' ').trim();
        if (sanitizedText !== text) {
            e.clipboardData.setData('text/plain', sanitizedText);
            e.preventDefault();
        }
    }
});

/**
 * Programmatic secure copy helper.
 * Securely writes a text string to the clipboard after cleaning potential injection sequences.
 */
function safeCopyToClipboard(text) {
    if (typeof text !== 'string') return;
    var sanitized = text.replace(/[\r\n\t\x00-\x1F]/g, ' ').replace(/\s+/g, ' ').trim();
    
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(sanitized).catch(function(err) {
            console.error('Failed to copy to clipboard safely: ', err);
        });
    } else {
        var textarea = document.createElement('textarea');
        textarea.value = sanitized;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();
        try {
            document.execCommand('copy');
        } catch (err) {
            console.error('Fallback copy failed: ', err);
        }
        document.body.removeChild(textarea);
    }
}
