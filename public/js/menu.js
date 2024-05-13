"use strict";

let list = document.querySelectorAll('.menu li');

function hoverLink() {
    this.classList.add("hovered");
}

function leaveLink() {
    this.classList.remove("hovered");
}

function selectLink() {
    list.forEach((item) => {
        item.classList.remove("selected");
    });
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
        if (selectedItem) {
            selectedItem.classList.add("selected");
        }
    }
});

let toggle = document.querySelector('.toggle');
let menu = document.querySelector('.menu');
let main = document.querySelector('.main');
let content = document.querySelector('.content-area');

toggle.onclick = function() {
    menu.classList.toggle('active');
    main.classList.toggle('active');
    content.classList.toggle('active');
}
