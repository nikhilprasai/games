// Wait for the DOM to fully load
document.addEventListener('DOMContentLoaded', function() {
    // Highlight active link in the sidebar
    document.querySelectorAll('.sidebar a').forEach(link => {
        link.addEventListener('click', function() {
            // Remove 'active' class from all links
            document.querySelectorAll('.sidebar a').forEach(l => l.classList.remove('active'));
            // Add 'active' class to the clicked link
            this.classList.add('active');
        });
    });

    // Handle deposit form submission
    const depositForm = document.getElementById('deposit-form');
    if (depositForm) {
        depositForm.addEventListener('submit', function(event) {
            // Allow the form to submit directly to submit_deposit.php
        });
    }

    // Handle FreePlay deposit form submission
    const freeplayForm = document.querySelector('form[action="freeplay_deposit.php"]');
    if (freeplayForm) {
        freeplayForm.addEventListener('submit', function(event) {
            // Allow the form to submit directly to FreePlay deposit processing
        });
    }

    // Handle cashout form submission
    const cashoutForm = document.querySelector('form[action="submit_cashout.php"]');
    if (cashoutForm) {
        cashoutForm.addEventListener('submit', function(event) {
            // Allow the form to submit directly to submit_cashout.php for processing
        });
    }
});

// Function to filter recent deposits based on search input
function filterDeposits() {
    const searchInput = document.getElementById('search-bar').value.toLowerCase();
    const depositRows = document.querySelectorAll('#recent-deposits tbody tr');

    depositRows.forEach(row => {
        const fbName = row.cells[0].textContent.toLowerCase();
        if (fbName.includes(searchInput)) {
            row.style.display = ''; // Show entry
        } else {
            row.style.display = 'none'; // Hide entry
        }
    });
}

// Function to filter players on FreePlay Deposit page
function filterPlayers() {
    const searchInput = document.getElementById('search-bar').value.toLowerCase();
    const playerList = document.querySelectorAll('#player-list li');

    playerList.forEach(player => {
        const playerName = player.textContent.toLowerCase();
        if (playerName.includes(searchInput)) {
            player.style.display = ''; // Show entry
        } else {
            player.style.display = 'none'; // Hide entry
        }
    });
}

// Function to filter FreePlay deposits on FreePlay History page
function filterFreeplayHistory() {
    const searchInput = document.getElementById('search-bar').value.toLowerCase();
    const depositRows = document.querySelectorAll('#freeplay-history tbody tr');

    depositRows.forEach(row => {
        const fbName = row.cells[0].textContent.toLowerCase();
        if (fbName.includes(searchInput)) {
            row.style.display = ''; // Show entry
        } else {
            row.style.display = 'none'; // Hide entry
        }
    });
}

// Function to filter cashout history based on search input
function filterCashouts() {
    const searchInput = document.getElementById('search-bar').value.toLowerCase();
    const cashoutRows = document.querySelectorAll('#cashout-history tbody tr');

    cashoutRows.forEach(row => {
        const fbName = row.cells[0].textContent.toLowerCase();
        if (fbName.includes(searchInput)) {
            row.style.display = ''; // Show entry
        } else {
            row.style.display = 'none'; // Hide entry
        }
    });
}

// Animate the stats when the dashboard page loads
window.onload = function() {
    // Check if we are on the dashboard page
    if (document.title === "Dashboard") {
        const stats = document.querySelectorAll('.stat-box p');
        stats.forEach((stat, index) => {
            setTimeout(() => {
                stat.style.opacity = 1;
                stat.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    stat.style.transform = 'scale(1)';
                }, 300);
            }, index * 300); // Stagger the animations
        });
    } else {
        // Ensure the stats are reset on the home page
        const stats = document.querySelectorAll('.stat-box p');
        stats.forEach(stat => {
            stat.style.opacity = 1; // Ensure opacity is set to 1
            stat.style.transform = 'scale(1)'; // Reset scale
        });
    }
};

// Function to open the edit modal for deposits
function openModal(id, fbName, depositAmount, bonusAmount, game) {
    document.getElementById('edit-id').value = id; // Set the hidden input value to the ID
    document.getElementById('edit-fb-name').value = fbName; // Set the input value to the selected name
    document.getElementById('edit-deposit-amount').value = depositAmount; // Set the input value to the selected deposit amount
    document.getElementById('edit-bonus-amount').value = bonusAmount; // Set the input value to the selected bonus amount
    document.getElementById('edit-game').value = game; // Set the input value to the selected game
    document.getElementById('editModal').style.display = "block"; // Show the modal
}

// Function to close the modal
function closeModal() {
    document.getElementById('editModal').style.display = "none"; // Hide the modal
}

// Close the modal if the user clicks outside of it
window.onclick = function(event) {
    const modal = document.getElementById('editModal');
    if (event.target == modal) {
        closeModal();
    }
};

// Function to toggle the sidebar visibility
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.querySelector('.overlay');
    sidebar.classList.toggle('active'); // Toggle the active class
    overlay.style.display = sidebar.classList.contains('active') ? 'block' : 'none'; // Show/hide overlay
}

// Example JavaScript to toggle sidebar
const hamburger = document.querySelector('.hamburger');
const sidebar = document.querySelector('.sidebar');

hamburger.addEventListener('click', () => {
    sidebar.classList.toggle('active');
});
document.getElementById('logout-link').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent the default link action
    const confirmation = confirm("Do you really want to logout?");
    if (confirmation) {
        window.location.href = 'logout.php'; // Redirect to logout if confirmed
    }
});
