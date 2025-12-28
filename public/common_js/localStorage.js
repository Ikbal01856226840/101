

function generateHash(str) {
    let hash = 5381; // Initial hash value
    for (let i = 0; i < str.length; i++) {
        // Shift left and add the character code
        hash = (hash * 33) ^ str.charCodeAt(i);
    }
    // Convert to a positive integer
    return (hash >>> 0).toString(16); // Return as a hex string
}

let hrefkey = generateHash($("#report_header_title").text()+window.location.href);
function setStorage(name, value) {
    try {
        localStorage.setItem(`${hrefkey}_${name}`, value);
    } catch (error) {
        console.error('Error saving to localStorage:', error);
    }
}

function getStorage(name, selector = null, type = 'input') {
    let data = localStorage.getItem(`${hrefkey}_${name}`) || '';
    if (selector && data) {
        if (type == "input") {
            $(selector).val(data).trigger('change');
        } else if (type == 'checkbox') {
            $(selector).prop('checked', data == 'true');
        }

    }
    return data;
}

function getRemoveItem(name, selector = null, type = 'input') {
    let data = localStorage.removeItem(`${hrefkey}_${name}`) || '';
    if (selector && data) {
        if (type == "input") {
            $(selector).val(data).trigger('change');
        } else if (type == 'checkbox') {
            $(selector).prop('checked', data == 'true');
        }

    }
    return data;
}