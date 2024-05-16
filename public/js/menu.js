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
