"use strict";

let toggle = document.querySelector('.toggle');
let menu = document.querySelector('.menu');
let main = document.querySelector('.main');
let content = document.querySelector('.content-area');

let allListItems = document.querySelectorAll('.menu ul li');
let rootListItems = document.querySelectorAll('.menu > ul > li');
let dailyTasksListItems = document.querySelectorAll('.menu > #daily-tasks-content > li');

class Sidebar {

    arrowImg = toggle.querySelector('#menu-arrow-open');

    get isCollapsed() {
        return localStorage.getItem('sidebarState') === 'collapsed';
    }

    toggle(updateState = true){
        this.isCollapsed
            ? this.expand(updateState)
            : this.collapse(updateState)
    }

    collapse(updateState = true) {
        updateState && localStorage.setItem('sidebarState', 'collapsed');
        menu.classList.add('active');
        main.classList.add('active');
        content.classList.add('active');
        this.arrowImg.src = 'images/menu-arrow-open.svg';
        rootListItems.forEach((item) => toggleSubMenu(item, false));
    }

    expand(updateState = true) {
        updateState && localStorage.setItem('sidebarState', 'expanded');
        menu.classList.remove('active');
        main.classList.remove('active');
        content.classList.remove('active');
        this.arrowImg.src = 'images/menu-arrow-closed.svg';
    }
}


const sidebar = new Sidebar()

function hoverLink() {
    this.classList.add("hovered");
}

function leaveLink() {
    this.classList.remove("hovered");
}


function toggleSubMenu(item, open = undefined) {
    const dropdown = item.querySelector('.dropdown-main')
    const dropdownContent = item.nextElementSibling

    if(dropdown && dropdownContent){
        if(open == null) {

            dropdownContent.classList.toggle('expanded-item');
        }else{
            const isExpanded = dropdownContent.classList.contains('expanded-item')
            open && !isExpanded && dropdownContent.classList.add('expanded-item');
            !open && isExpanded && dropdownContent.classList.remove('expanded-item');
        }
    }
}

function navigateToRoute (listItem) {

    const href = listItem.querySelector('a').href;

    const dropdown = listItem && listItem.querySelector('.dropdown-main')

    if(dropdown){
        if(!sidebar.isCollapsed){
            toggleSubMenu(listItem);
        }
        else{
            sidebar.expand()
        }
    }
    else {
        if (sidebar.isCollapsed) {

            sidebar.expand(false)

            setTimeout(() => {
                if (href) {
                    window.location.href = href;
                }

            }, 500);

            setTimeout(() => sidebar.collapse(), 2000);

        }else {
            if (href) {
                window.location.href = href;
            }
        }
    }
}

function selectLink(e) {
    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    const dropdown = this.querySelector('.dropdown-main')

    if(!dropdown){
        clearSelected();
        this.classList.add("selected");
        localStorage.setItem('selectedMenuItem', this.id);
    }

    navigateToRoute(this)
}


allListItems.forEach((item) => {
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

    if (sidebar.isCollapsed) {
        sidebar.collapse()
    }
});

function clearSelected() {
    rootListItems.forEach((item) => item.classList.remove("selected"));
    localStorage.removeItem('selectedMenuItem');
}

toggle.onclick = function() {
    sidebar.toggle()
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
    var presenceStatusUrl = '/get-status';

    function updateButtonStatus(status, shift) {
        if (entryExitButton) {
            if (status === 'in') {
                entryExitButton.textContent = 'Saída';
                entryExitButton.classList.add('btn-out');
                entryExitButton.classList.remove('btn-in');
            } else if (status === 'completed') {
                entryExitButton.textContent = 'Presença Completa';
                entryExitButton.disabled = true;
                entryExitButton.classList.add('btn-completed');
            } else {
                entryExitButton.textContent = 'Entrada';
                entryExitButton.classList.add('btn-in');
                entryExitButton.classList.remove('btn-out');
                entryExitButton.dataset.shift = shift;
            }
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
                updateButtonStatus(data.status, data.shift);
            })
            .catch(error => {
                console.error('Erro ao verificar status:', error);
            });
    }

    fetchStatusAndUpdateButton();

    entryExitButton && entryExitButton.addEventListener('click', function(e) {
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
                    if (!document.getElementById('first_start').value && data.shift === 'first') {
                        document.getElementById('first_start').value = currentTime;
                    } else if (!document.getElementById('second_start').value && data.shift === 'second') {
                        document.getElementById('second_start').value = currentTime;
                    }
                } else if (data.status === 'in') {
                    if (!document.getElementById('first_end').value && data.shift === 'first') {
                        document.getElementById('first_end').value = currentTime;
                    } else if (!document.getElementById('second_end').value && data.shift === 'second') {
                        document.getElementById('second_end').value = currentTime;
                    }
                } else if (data.status === 'completed') {
                    alert('Presença já registrada completamente para hoje.');
                    return;
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




// The function is called when the document is loaded and toggles the menu visibility
// when the menu arrow is clicked by changing the image and the display style of the menu
document.addEventListener('DOMContentLoaded', function () {

    toggle.onclick = function () {
        sidebar.toggle()
    };
});
