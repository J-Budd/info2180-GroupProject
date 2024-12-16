

        document.getElementById('new-contact').addEventListener('click', () => {
            setActiveLink('new-contact');
            document.querySelector('.new-contact-form').style.display = 'block'; // Show the form
            document.getElementById('contacts-table').style.display = 'none';
            document.getElementById('users-table').style.display = 'none';
            document.querySelector('.filter-tabs').style.display = 'none';
            document.querySelector('.add-contact').style.display = 'none'; // Hide the Add Contact button
            document.querySelector('.add-user').style.display = 'none';
        });


        document.getElementById('add-contact').addEventListener('click', () => {
            setActiveLink('new-contact');
            document.querySelector('.add-user').style.display = 'none';
            document.querySelector('.new-contact-form').style.display = 'block'; // Show the form
            document.getElementById('contacts-table').style.display = 'none';
            document.getElementById('users-table').style.display = 'none';
            document.querySelector('.filter-tabs').style.display = 'none';
            document.querySelector('.add-contact').style.display = 'none'; // Hide the Add Contact button
        });


        fetchContacts('all', document.querySelector('.filter-tabs a')); // Default filter on page load
        document.addEventListener('DOMContentLoaded', () => {
            loadContacts('all'); // Load contacts on page load
        });

        
        

        // Fetch contacts based on the filter
        async function fetchContacts(filter = 'all', element) {
            const contactsBody = document.getElementById('contacts-body');

            // Set active class on the clicked filter and remove it from others
            const filterTabs = document.querySelectorAll('.filter-tabs a');
            filterTabs.forEach(tab => tab.classList.remove('active'));
            element.classList.add('active');

            try {
                const response = await fetch(`fetch_contacts.php?filter=${filter}`);
                if (!response.ok) {
                    throw new Error('Failed to fetch contacts');
                }
                const contacts = await response.json();
                displayContacts(contacts);
            } catch (error) {
                console.error(error);
                contactsBody.innerHTML = '<tr><td colspan="5">Error loading contacts.</td></tr>';
            }
        }

        // Display contacts in table
        function displayContacts(contacts) {
            const contactsBody = document.getElementById('contacts-body');
            if (contacts.length === 0) {
                contactsBody.innerHTML = '<tr><td colspan="5">No contacts found.</td></tr>';
                return;
            }
            contactsBody.innerHTML = contacts.map(contact => `
                <tr>
                    <td>${contact.firstname} ${contact.lastname}</td>
                    <td>${contact.email}</td>
                    <td>${contact.company}</td>
                    <td>
                        <span class="badge ${contact.type === 'Sales Lead' ? 'sales' : 'support'}">
                            ${contact.type}
                        </span>
                    </td>
                    <td><a href="#" class="view-link" onclick="fetchContactDetails(${contact.id})">View</a></td>
                </tr>
            `).join('');
        }

        // Fetch and display user list
        document.getElementById('users-link').addEventListener('click', () => {
            setActiveLink('users-link');

            document.querySelector('.add-user').style.display = 'inline-block';
            document.querySelector('.new-contact-form').style.display = 'none';
            document.getElementById('contacts-table').style.display = 'none';
            document.getElementById('users-table').style.display = 'table'; // Ensure the Users table is displayed

            document.querySelector('.add-contact').style.display = 'none'; // Hide Add Contact button
            document.querySelector('.filter-tabs').style.display = 'none'; // Hide filters
            loadUsers(); // Call function to load the users
        });

        // When switching back to contacts, reset filter-tabs visibility
        document.getElementById('home-link').addEventListener('click', () => {
            document.querySelector('.add-user').style.display = 'none';
            document.getElementById('contacts-table').style.display = 'block';
            document.getElementById('users-table').style.display = 'none';

            // Show filter-tabs again when Contacts are displayed
            document.querySelector('.filter-tabs').style.display = 'flex';
        });

        // Load users
        async function loadUsers() {
            document.getElementById('.users-body').style.display = 'inline-block';   
        }


        function setActiveLink(linkId) {
            const links = document.querySelectorAll('.filter-tabs a, .sidebar ul li a');
            links.forEach(link => link.classList.remove('active'));
            document.getElementById(linkId).classList.add('active');
        }

        // Fetch contact details by ID
        


// Fetch contact details by ID
async function fetchContactDetails(contactId) {
    const contactDetailsSection = document.getElementById('contact-details-section');
    contactDetailsSection.style.display = 'block'; // Show the details section
    document.querySelector('.add-user').style.display = 'none';
    document.querySelector('.new-contact-form').style.display = 'none';
    document.getElementById('contacts-table').style.display = 'none';
    document.getElementById('users-table').style.display = 'none';
    document.querySelector('.add-contact').style.display = 'none'; // Hide Add Contact button
    document.querySelector('.filter-tabs').style.display = 'none'; // Hide filters

    try {
        // Make the fetch request
        const response = await fetch(`fetch_contact_details.php?id=${contactId}`);
        
        // Log the raw response for debugging
        const text = await response.text(); 
        console.log('Raw Response:', text); 

        // Check if the response is valid JSON
        let contact;
        try {
            contact = JSON.parse(text); // Try parsing JSON
        } catch (error) {
            throw new Error('Error parsing JSON: ' + error.message);
        }

        // Check if the response contains an error
        if (contact.error) {
            throw new Error(contact.error);
        }

        // If no errors, display the contact details
        displayContactDetails(contact);
    } catch (error) {
        // Handle any errors during fetch or JSON parsing
        console.error(error);
        contactDetailsSection.innerHTML = `<p>Error loading contact details: ${error.message}</p>`;
    }
}

// Function to display the contact details in the details section
// Function to display the contact details in the details section
// Function to display the contact details in the details section
function displayContactDetails(contact) {
    const contactDetailsSection = document.getElementById('contact-details-section');
    
    // Split the notes string into an array using the '|' delimiter
    const notesList = contact.notes ? contact.notes.split('|') : []; // Split notes string if available, else empty array
    
    contactDetailsSection.innerHTML = `
        <button id="close-details-btn" style="margin-bottom: 20px;">Close</button> <!-- Close button -->
        <h2>${contact.title} ${contact.firstname} ${contact.lastname}</h2>
        <p>Email: ${contact.email}</p>
        <p>Phone: ${contact.telephone}</p>
        <p>Company: ${contact.company}</p>
        <p>Assigned To: ${contact.assigned_to}</p>
        <p>Created On: ${new Date(contact.created_at).toLocaleString()}</p>
        <p>Updated On: ${new Date(contact.updated_at).toLocaleString()}</p>
        <h3>Notes:</h3>
        <ul>
            ${notesList.length > 0 ? notesList.map(note => `<li>${note}</li>`).join('') : '<li>No notes available</li>'}
        </ul>
        <h3>Add a Note:</h3>
        <form action="add_note.php" method="POST" id = "add-note-form">
            <textarea name="note" id="note" required></textarea>
            <input type="hidden" name="contact_id" value="${contact.id}">
            <button type="submit">Add Note</button>
        </form>
        
        <!-- Buttons for updating the contact type and assigning to user -->
        <button id="switch-type-btn" style="margin-top: 20px;">Switch Type</button>
        <button id="assign-user-btn" style="margin-top: 10px;">Assign to Current User</button>
    `;

    // Add event listener to the close button
    document.getElementById('close-details-btn').addEventListener('click', () => {
        closeContactDetails(); // Close the details and show the contacts table
    });

    const addNoteForm = document.getElementById('add-note-form');
    addNoteForm.addEventListener('submit', async function (event) {
        event.preventDefault(); // Prevent the default form submission behavior

        const formData = new FormData(addNoteForm);
        try {
            // Send the form data to the server using fetch
            const response = await fetch('add_note.php', {
                method: 'POST',
                body: formData,
            });

            if (response.ok) {
                // Fetch the updated contact details
                const updatedContact = await fetchContactDetails(contact.id);
                displayContactDetails(updatedContact); // Re-render with updated data
            } else {
                alert('Failed to add the note. Please try again.');
            }
        } catch (error) {
            // console.error('Error adding note:', error);
            // alert('An error occurred while adding the note.');
        }
    });

    // Add event listener for switching the contact type
    document.getElementById('switch-type-btn').addEventListener('click', () => {
        switchContactType(contact.id, contact.type);
    });

    // Add event listener for assigning the contact to the current user
    document.getElementById('assign-user-btn').addEventListener('click', () => {
        assignContactToUser(contact.id);
    });
}

// Fetch contact details by ID
async function fetchContactDetails(contactId) {
    const contactDetailsSection = document.getElementById('contact-details-section');
    contactDetailsSection.style.display = 'block'; // Show the details section
    document.querySelector('.add-user').style.display = 'none';
    document.querySelector('.new-contact-form').style.display = 'none';
    document.getElementById('contacts-table').style.display = 'none';
    document.getElementById('users-table').style.display = 'none';
    document.querySelector('.add-contact').style.display = 'none'; // Hide Add Contact button
    document.querySelector('.filter-tabs').style.display = 'none'; // Hide filters

    try {
        // Make the fetch request
        const response = await fetch(`fetch_contact_details.php?id=${contactId}`);
        if (!response.ok) {
            throw new Error('Failed to fetch contact details');
        }

        const contact = await response.json();
        if (contact.error) {
            throw new Error(contact.error);
        }

        // If no errors, display the contact details
        displayContactDetails(contact);
    } catch (error) {
        // Handle any errors during fetch or JSON parsing
        console.error(error);
        contactDetailsSection.innerHTML = `<p>Error loading contact details: ${error.message}</p>`;
    }
}


// Function to switch the contact type between 'Sales Lead' and 'Support'
async function switchContactType(contactId, currentType) {
    const newType = currentType === 'Sales Lead' ? 'Support' : 'Sales Lead';

    try {
        const response = await fetch('switch_contact_type.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                contact_id: contactId,
                new_type: newType,
            }),
        });

        const result = await response.json();
        if (result.success) {
            alert(`Contact type successfully changed to ${newType}`);
            // Reload the contact details after changing the type
            fetchContactDetails(contactId);
        } else {
            alert('Failed to change contact type');
        }
    } catch (error) {
        console.error(error);
        alert('Error changing contact type');
    }
}

// Function to assign the contact to the current user
async function assignContactToUser(contactId) {
    try {
        const response = await fetch('assign_contact_to_user.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                contact_id: contactId,
                user_id: currentUserId, // Assume `currentUserId` is globally available or fetched dynamically
            }),
        });

        const result = await response.json();
        if (result.success) {
            alert('Contact successfully assigned to current user');
            // Reload the contact details after assigning the user
            fetchContactDetails(contactId);
        } else {
            alert('Failed to assign contact to user');
        }
    } catch (error) {
        console.error(error);
        alert('Error assigning contact to user');
    }
}


// Function to close the contact details section and show the contact table
function closeContactDetails() {
    // Hide the contact details section
    document.getElementById('contact-details-section').style.display = 'none';

    // Show the contact table
    // document.getElementById('contacts-table').style.display = 'table'; // Ensure the contacts table is visible again
    window.location.href = 'dashboard.php';
    // document.querySelector('.filter-tabs').style.display = 'flex'; // Show filter tabs again (if needed)

}

