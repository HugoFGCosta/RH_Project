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

    toggle(updateState = true) {
        this.isCollapsed ? this.expand(updateState) : this.collapse(updateState);
    }

    collapse(updateState = true) {
        if (updateState) localStorage.setItem('sidebarState', 'collapsed');
        menu.classList.add('active');
        main.classList.add('active');
        content.classList.add('active');
        this.arrowImg.src = 'images/menu-arrow-open.svg';
        rootListItems.forEach(item => toggleSubMenu(item, false));
    }

    expand(updateState = true) {
        if (updateState) localStorage.setItem('sidebarState', 'expanded');
        menu.classList.remove('active');
        main.classList.remove('active');
        content.classList.remove('active');
        this.arrowImg.src = 'images/menu-arrow-closed.svg';
    }
}

const sidebar = new Sidebar();

function hoverLink() {
    this.classList.add("hovered");
}

function leaveLink() {
    this.classList.remove("hovered");
}

function toggleSubMenu(item, open = undefined) {
    const dropdown = item.querySelector('.dropdown-main');
    const dropdownContent = item.nextElementSibling;
    const arrow = dropdown ? dropdown.querySelector('img') : null;

    if (dropdown && dropdownContent) {
        if (open == null) {
            dropdownContent.classList.toggle('expanded-item');
        } else {
            const isExpanded = dropdownContent.classList.contains('expanded-item');
            if (open && !isExpanded) {
                dropdownContent.classList.add('expanded-item');
            } else if (!open && isExpanded) {
                dropdownContent.classList.remove('expanded-item');
            }
        }
        if (arrow) {
            if (dropdownContent.classList.contains('expanded-item')) {
                arrow.src = 'images/up-arrow.svg';
            } else {
                arrow.src = 'images/dropdown-arrow.svg';
            }
        }
    }
}

function closeAllSubMenus() {
    document.querySelectorAll('.menu .expanded-item').forEach(item => {
        item.classList.remove('expanded-item');
        const dropdown = item.previousElementSibling;
        const arrow = dropdown ? dropdown.querySelector('img') : null;
        if (arrow) arrow.src = 'images/dropdown-arrow.svg';
    });
}

function navigateToRoute(listItem) {
    const href = listItem.querySelector('a').href;
    const dropdown = listItem.querySelector('.dropdown-main');

    if (dropdown) {
        if (!sidebar.isCollapsed) {
            const dropdownContent = listItem.nextElementSibling;
            if (dropdownContent.classList.contains('expanded-item')) {
                toggleSubMenu(listItem, false);
            } else {
                closeAllSubMenus();
                toggleSubMenu(listItem, true);
            }
        } else {
            sidebar.expand();
        }
    } else {
        if (sidebar.isCollapsed) {
            sidebar.expand(false);
            setTimeout(() => {
                if (href) {
                    window.location.href = href;
                }
            }, 500);
            setTimeout(() => sidebar.collapse(), 2000);
        } else {
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

    const dropdown = this.querySelector('.dropdown-main');
    if (!dropdown) {
        clearSelected();
        this.classList.add("selected");
        localStorage.setItem('selectedMenuItem', this.id);
    }

    navigateToRoute(this);
}

function selectSubMenuLink(e) {
    e.stopPropagation();
}

allListItems.forEach(item => {
    item.addEventListener('mouseover', hoverLink);
    item.addEventListener('mouseout', leaveLink);
    item.addEventListener('click', selectLink);
});

dailyTasksListItems.forEach(item => {
    item.addEventListener('click', selectSubMenuLink);
});

document.addEventListener('DOMContentLoaded', () => {
    const selectedId = localStorage.getItem('selectedMenuItem');
    if (selectedId) {
        const selectedItem = document.getElementById(selectedId);
        if (selectedItem) {
            selectedItem.classList.add("selected");
        }
    }

    if (sidebar.isCollapsed) {
        sidebar.collapse();
    }
});

function clearSelected() {
    rootListItems.forEach(item => item.classList.remove("selected"));
    localStorage.removeItem('selectedMenuItem');
}

toggle.onclick = function() {
    sidebar.toggle();
};

const userLink = document.querySelector('.user a');
if (userLink) {
    userLink.addEvent
}
