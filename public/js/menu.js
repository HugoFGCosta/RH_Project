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
        console.log("Collapsing sidebar");
        if (updateState) localStorage.setItem('sidebarState', 'collapsed');
        menu.classList.add('active');
        main.classList.add('active');
        content.classList.add('active');
        this.arrowImg.src = '/images/menu-arrow-open.svg';
        rootListItems.forEach(item => toggleSubMenu(item, false));
    }

    expand(updateState = true) {
        console.log("Expanding sidebar");
        if (updateState) localStorage.setItem('sidebarState', 'expanded');
        menu.classList.remove('active');
        main.classList.remove('active');
        content.classList.remove('active');
        this.arrowImg.src = '/images/menu-arrow-closed.svg';
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
        console.log("Toggling submenu for item", item, open);
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
                arrow.src = '/images/up-arrow.svg';
            } else {
                arrow.src = '/images/dropdown-arrow.svg';
            }
        }
    } else {
        console.log("Dropdown or dropdownContent not found for item", item);
    }
}

function closeAllSubMenus() {
    document.querySelectorAll('.menu .expanded-item').forEach(item => {
        item.classList.remove('expanded-item');
        const dropdown = item.previousElementSibling;
        const arrow = dropdown ? dropdown.querySelector('img') : null;
        if (arrow) arrow.src = '/images/dropdown-arrow.svg';
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
    e.stopPropagation(); // Prevent the click event from bubbling up to parent elements
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
    userLink.addEventListener('click', function(event) {
        clearSelected();
    });
}

//manipulação do botão de entrada e saída
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM fully loaded and parsed");
    const entryExitButton = document.getElementById('entryExitButton');
    const presenceStatusUrl = '/get-status';

    function updateButtonStatus(status, shift) {
        if (entryExitButton) {
            entryExitButton.classList.remove('btn-in', 'btn-out', 'btn-completed');
            if (status === 'in') {
                entryExitButton.textContent = 'Saída';
                entryExitButton.classList.add('btn-out');
            } else if (status === 'completed') {
                entryExitButton.textContent = 'Presença Completa';
                entryExitButton.disabled = true;
                entryExitButton.classList.add('btn-completed');
            } else {
                entryExitButton.textContent = 'Entrada';
                entryExitButton.classList.add('btn-in');
                entryExitButton.dataset.shift = shift;
            }
        }
    }

    function fetchStatusAndUpdateButton() {
        fetch(presenceStatusUrl)
            .then(response => response.json())
            .then(data => {
                console.log("Status fetched:", data);
                updateButtonStatus(data.status, data.shift);
            })
            .catch(error => {
                console.error('Erro ao verificar status:', error);
            });
    }

    fetchStatusAndUpdateButton();

    if (entryExitButton) {
        entryExitButton.addEventListener('click', function(e) {
            e.preventDefault();

            // Desativa o botão imediatamente
            entryExitButton.disabled = true;
            console.log("Botão desativado imediatamente após clique");

            const currentTime = new Date().toISOString().slice(0, 19).replace('T', ' ');

            fetch(presenceStatusUrl)
                .then(response => response.json())
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
                        // Reativa o botão porque não houve submissão
                        entryExitButton.disabled = false;
                        console.log("Botão reativado após status completo");
                        return;
                    }

                    console.log("Submitting form with data:", {
                        first_start: document.getElementById('first_start').value,
                        first_end: document.getElementById('first_end').value,
                        second_start: document.getElementById('second_start').value,
                        second_end: document.getElementById('second_end').value,
                    });

                    // Submete o formulário via AJAX
                    const formData = new FormData();
                    formData.append('first_start', document.getElementById('first_start').value);
                    formData.append('first_end', document.getElementById('first_end').value);
                    formData.append('second_start', document.getElementById('second_start').value);
                    formData.append('second_end', document.getElementById('second_end').value);
                    formData.append('_token', document.querySelector('input[name="_token"]').value);

                    fetch(e.target.form.action, {
                        method: 'POST',
                        body: formData
                    })
                        .then(response => response.text()) // Mudança aqui para pegar a resposta como texto
                        .then(responseText => {
                            try {
                                const data = JSON.parse(responseText);
                                console.log('Success:', data);
                                fetchStatusAndUpdateButton(); // Atualiza o botão após a submissão
                                // Desativa o botão por 5 minutos
                                setTimeout(() => {
                                    entryExitButton.disabled = false;
                                    fetchStatusAndUpdateButton(); // Atualiza o botão após 5 minutos
                                    console.log("Botão reativado após 5 minutos");
                                }, 300000); // 300000 ms = 5 minutes
                            } catch (error) {
                                console.error('Erro ao analisar JSON:', error);
                                console.error('Resposta do servidor:', responseText);
                                alert('Erro ao submeter o formulário: ' + error.message);
                                // Reativa o botão em caso de erro
                                entryExitButton.disabled = false;
                                console.log("Botão reativado após erro");
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                            alert('Erro ao submeter o formulário: ' + error.message);
                            // Reativa o botão em caso de erro
                            entryExitButton.disabled = false;
                            console.log("Botão reativado após erro");
                        });
                })
                .catch(error => {
                    console.error('Erro ao verificar status:', error);
                    alert('Erro ao verificar status: ' + error.message);
                    // Reativa o botão em caso de erro
                    entryExitButton.disabled = false;
                    console.log("Botão reativado após erro");
                });
        });
    }
});


document.addEventListener('DOMContentLoaded', function() {
    toggle.onclick = function() {
        sidebar.toggle();
    };
});
