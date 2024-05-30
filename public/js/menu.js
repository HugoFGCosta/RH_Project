"use strict";

let list = document.querySelectorAll('.menu li');

function hoverLink() {
    this.classList.add("hovered");
}

function leaveLink() {
    this.classList.remove("hovered");
}

function selectLink() {
    clearSelected();
    this.classList.add("selected");
    localStorage.setItem('selectedMenuItem', this.id);
}

list.forEach((item) => {
    item.addEventListener('mouseover', hoverLink);
    item.addEventListener('mouseout', leaveLink);
    item.addEventListener('click', selectLink);
});

document.addEventListener('DOMContentLoaded', () => {
    const selectedId = localStorage.getItem('selectedMenuItem');
    if (selectedId) {
        const selectedItem = document.getElementById(selectedId);
        selectedItem?.classList.add("selected");
    }

    const sidebarState = localStorage.getItem('sidebarState');
    if (sidebarState === 'collapsed') {
        menu.classList.add('active');
        main.classList.add('active');
        content.classList.add('active');
    }
});

function clearSelected() {
    list.forEach((item) => {
        item.classList.remove("selected");
    });
    localStorage.removeItem('selectedMenuItem');
}

let toggle = document.querySelector('.toggle');
let menu = document.querySelector('.menu');
let main = document.querySelector('.main');
let content = document.querySelector('.content-area');

toggle.onclick = function() {
    menu.classList.toggle('active');
    main.classList.toggle('active');
    content.classList.toggle('active');

    const isCollapsed = menu.classList.contains('active');
    localStorage.setItem('sidebarState', isCollapsed ? 'collapsed' : 'expanded');
};

const userLink = document.querySelector('.user a');
if (userLink) {
    userLink.addEventListener('click', function(event) {
        clearSelected();
    });
}


document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded and parsed");
    var entryExitButton = document.getElementById('entryExitButton');

    function updateButtonStatus(status) {
        if (status === 'in') {
            entryExitButton.textContent = 'Saída';
            entryExitButton.classList.add('btn-out');
            entryExitButton.classList.remove('btn-in');
        } else {
            entryExitButton.textContent = 'Entrada';
            entryExitButton.classList.add('btn-in');
            entryExitButton.classList.remove('btn-out');
        }
    }

    function fetchStatusAndUpdateButton() {
        fetch(presenceStatusUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log("Status fetched:", data);
                updateButtonStatus(data.status);
            })
            .catch(error => {
                console.error('Erro ao verificar status:', error);
            });
    }

    fetchStatusAndUpdateButton();

    entryExitButton.addEventListener('click', function(e) {
        e.preventDefault();
        var currentTime = new Date().toISOString().slice(0, 19).replace('T', ' ');

        fetch(presenceStatusUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                console.log("Status fetched after click:", data);
                if (data.status === 'out') {
                    if (!document.getElementById('first_start').value) {
                        document.getElementById('first_start').value = currentTime;
                    } else if (!document.getElementById('second_start').value) {
                        document.getElementById('second_start').value = currentTime;
                    }
                } else if (data.status === 'in') {
                    if (!document.getElementById('first_end').value) {
                        document.getElementById('first_end').value = currentTime;
                    } else if (!document.getElementById('second_end').value) {
                        document.getElementById('second_end').value = currentTime;
                    }
                }

                console.log("Submitting form with data:", {
                    first_start: document.getElementById('first_start').value,
                    first_end: document.getElementById('first_end').value,
                    second_start: document.getElementById('second_start').value,
                    second_end: document.getElementById('second_end').value,
                });

                e.target.form.submit();
            })
            .catch(error => {
                console.error('Erro ao verificar status:', error);
                alert('Erro ao verificar status: ' + error.message);
            });
    });
});




/**
 * Toggle menu visibility
 */

document.addEventListener('DOMContentLoaded', function () {
    let menuArrowOpen = document.getElementById('menu-arrow-open');

    menuArrowOpen.onclick = function () {
        if (menuArrowOpen.src.includes('menu-arrow-open')) {
            menuArrowOpen.src = 'images/menu-arrow-closed.svg';
            document.getElementById('menu').style.display = 'none';
            document.getElementById('main').style.width = '100%';
        } else {
            menuArrowOpen.src = 'images/menu-arrow-open.svg';
            document.getElementById('menu').style.display = 'block'; // or any other appropriate display property
            document.getElementById('main').style.width = '80%'; // adjust width as needed
        }
    };
});

