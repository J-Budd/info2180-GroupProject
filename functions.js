// ajax_functions.js - Handle AJAX for contacts and notes management

// Helper function for making AJAX requests
async function makeRequest(url, method = 'GET', data = null) {
    const options = {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
    };

    if (data) {
        options.body = JSON.stringify(data);
    }

    const response = await fetch(url, options);
    return response.json();
}

// Contact Management Functions
async function createContact(contactData) {
    const result = await makeRequest('contact_management.php', 'POST', { ...contactData, action: 'create' });
    alert(result.message);
    if (result.success) fetchContacts();
}

async function updateContact(contactData) {
    const result = await makeRequest('contact_management.php', 'POST', { ...contactData, action: 'update' });
    alert(result.message);
    if (result.success) fetchContacts();
}

async function deleteContact(contactId) {
    const result = await makeRequest('contact_management.php', 'POST', { id: contactId, action: 'delete' });
    alert(result.message);
    if (result.success) fetchContacts();
}

async function fetchContacts() {
    const result = await makeRequest('contact_management.php');
    if (result.success) {
        renderContacts(result.data);
    } else {
        alert(result.message);
    }
}

function renderContacts(contacts) {
    const container = document.getElementById('contact-list');
    container.innerHTML = '';
    contacts.forEach(contact => {
        const contactElement = document.createElement('div');
        contactElement.className = 'contact-item';
        contactElement.innerHTML = `
            <p>${contact.firstname} ${contact.lastname}</p>
            <button onclick="editContact(${contact.id})">Edit</button>
            <button onclick="deleteContact(${contact.id})">Delete</button>
        `;
        container.appendChild(contactElement);
    });
}

// Notes Management Functions
async function createNote(noteData) {
    const result = await makeRequest('notes_management.php', 'POST', { ...noteData, action: 'create' });
    alert(result.message);
    if (result.success) fetchNotes(noteData.contact_id);
}

async function updateNote(noteData) {
    const result = await makeRequest('notes_management.php', 'POST', { ...noteData, action: 'update' });
    alert(result.message);
    if (result.success) fetchNotes(noteData.contact_id);
}

async function deleteNote(noteId, contactId) {
    const result = await makeRequest('notes_management.php', 'POST', { id: noteId, action: 'delete' });
    alert(result.message);
    if (result.success) fetchNotes(contactId);
}

async function fetchNotes(contactId) {
    const result = await makeRequest(`notes_management.php?contact_id=${contactId}`);
    if (result.success) {
        renderNotes(result.data);
    } else {
        alert(result.message);
    }
}

function renderNotes(notes) {
    const container = document.getElementById('notes-list');
    container.innerHTML = '';
    notes.forEach(note => {
        const noteElement = document.createElement('div');
        noteElement.className = 'note-item';
        noteElement.innerHTML = `
            <p>${note.comment}</p>
            <button onclick="editNote(${note.id})">Edit</button>
            <button onclick="deleteNote(${note.id}, ${note.contact_id})">Delete</button>
        `;
        container.appendChild(noteElement);
    });
}
