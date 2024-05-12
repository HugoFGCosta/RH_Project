"use strict";
let list = document.querySelectorAll('.menu li');

function activeLink() {
    list.forEach((item) =>
    {
        item.classList.remove("hovered");
    });
    this.classList.add("hovered");
}

list.forEach((item) => item.addEventListener('mouseover', activeLink));

//toggle do menu
let toggle = document.querySelector('.toggle');
let menu = document.querySelector('.menu');
let main = document.querySelector('.main');

toggle.onclick = function() {
    menu.classList.toggle('active');
    main.classList.toggle('active');
}
